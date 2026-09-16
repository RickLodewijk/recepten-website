/**
 * Main Frontend Application Controller for ACF Spin Wheel
 */

document.addEventListener('DOMContentLoaded', () => {
    const config = window.acfSpinWheelData || {};
    const i18n = config.i18n || {};
    const palette = config.defaultPalette || [
        '#EF4444', '#F97316', '#F59E0B', '#10B981',
        '#06B6D4', '#3B82F6', '#6366F1', '#8B5CF6',
        '#EC4899', '#14B8A6', '#84CC16', '#E11D48'
    ];

    // DOM Elements
    const rootEl = document.getElementById('acf-wheel-app');
    if (!rootEl) return;

    const canvasEl = document.getElementById('acf-wheel-canvas');
    const pointerEl = document.getElementById('acf-wheel-pointer');
    const spinBtn = document.getElementById('acf-wheel-spin-btn');
    const titleInput = document.getElementById('acf-wheel-title-input');
    const entriesInput = document.getElementById('acf-wheel-entries-input');
    const slicesCountBadge = document.getElementById('acf-wheel-slices-count');
    const soundToggleBtn = document.getElementById('acf-wheel-sound-toggle');
    const soundIcon = document.getElementById('acf-wheel-sound-icon');
    const soundLabel = soundToggleBtn ? soundToggleBtn.querySelector('.acf-wheel-sound-label') : null;

    const btnShuffle = document.getElementById('acf-wheel-btn-shuffle');
    const btnSort = document.getElementById('acf-wheel-btn-sort');
    const btnSave = document.getElementById('acf-wheel-btn-save');
    const btnNew = document.getElementById('acf-wheel-btn-new');
    const btnShare = document.getElementById('acf-wheel-btn-share');
    const btnClone = document.getElementById('acf-wheel-btn-clone');

    const statusBar = document.getElementById('acf-wheel-status-bar');
    const statusText = document.getElementById('acf-wheel-status-text');

    const sharedBanner = document.getElementById('acf-wheel-shared-banner');
    const savedSection = document.getElementById('acf-wheel-saved-section');
    const savedGrid = document.getElementById('acf-wheel-saved-grid');
    const savedCountBadge = document.getElementById('acf-wheel-saved-count');

    // Modals
    const winnerModal = document.getElementById('acf-wheel-winner-modal');
    const winnerText = document.getElementById('acf-wheel-winner-text');
    const winnerClose = document.getElementById('acf-wheel-winner-close');
    const btnSpinAgain = document.getElementById('acf-wheel-btn-spin-again');
    const btnRemoveWinner = document.getElementById('acf-wheel-btn-remove-winner');
    const btnWinnerDismiss = document.getElementById('acf-wheel-btn-winner-dismiss');
    const confettiCanvas = document.getElementById('acf-wheel-confetti-canvas');

    const btnResetWheel = document.getElementById('acf-wheel-btn-reset-wheel');
    const removedCountSpan = document.getElementById('acf-wheel-removed-count');
    const btnStageReset = document.getElementById('acf-wheel-stage-reset');
    const removedStageCountSpan = btnStageReset ? btnStageReset.querySelector('.acf-wheel-removed-stage-count') : null;

    const eliminatedBox = document.getElementById('acf-wheel-eliminated-box');
    const eliminatedBoxCount = document.getElementById('acf-wheel-eliminated-box-count');
    const eliminatedChips = document.getElementById('acf-wheel-eliminated-chips');
    const btnResetAll = document.getElementById('acf-wheel-btn-reset-all');

    const loginModal = document.getElementById('acf-wheel-login-modal');
    const loginClose = document.getElementById('acf-wheel-login-close');
    const loginDismiss = document.getElementById('acf-wheel-login-dismiss');

    const shareModal = document.getElementById('acf-wheel-share-modal');
    const shareInput = document.getElementById('acf-wheel-share-input');
    const shareClose = document.getElementById('acf-wheel-share-close');
    const btnCopyModal = document.getElementById('acf-wheel-btn-copy-modal');

    const toastContainer = document.getElementById('acf-wheel-toasts');

    // State Variables
    let activeWheelId = null;
    let activeShareToken = config.currentShareToken || null;
    let activeShareUrl = null;
    let isReadOnly = false;
    let tickTimeout = null;
    let originalEntries = [];
    let removedEntries = [];
    let lastWinnerSlice = null;

    /**
     * Build robust REST API URL supporting both plain (?rest_route=) and pretty permalinks
     */
    function getApiUrl(endpoint, params = {}) {
        let base = config.restUrl || '';
        if (!base.endsWith('/')) {
            base += '/';
        }
        const cleanEndpoint = endpoint.replace(/^\//, '');
        let url = base + cleanEndpoint;

        const queryParts = [];
        for (const [key, val] of Object.entries(params)) {
            if (val !== undefined && val !== null && val !== '') {
                queryParts.push(`${encodeURIComponent(key)}=${encodeURIComponent(val)}`);
            }
        }

        if (queryParts.length > 0) {
            url += (url.includes('?') ? '&' : '?') + queryParts.join('&');
        }

        return url;
    }

    /**
     * Headers helper: Only send X-WP-Nonce for logged-in users.
     * Sending stale nonces as a guest causes WordPress core rest_cookie_check_errors to return 403.
     */
    function getAuthHeaders(includeJson = false) {
        const headers = {};
        if (includeJson) {
            headers['Content-Type'] = 'application/json';
        }
        if (config.isLoggedIn && config.nonce) {
            headers['X-WP-Nonce'] = config.nonce;
        }
        return headers;
    }

    // Sub-modules
    const audio = new SpinWheelAudio();
    const confetti = new SpinWheelConfetti(confettiCanvas);

    // Initialize Canvas Wheel
    const wheelCanvas = new SpinWheelCanvas(canvasEl, {
        onTick: () => {
            audio.playTick();
            if (pointerEl) {
                pointerEl.classList.add('is-ticking');
                clearTimeout(tickTimeout);
                tickTimeout = setTimeout(() => {
                    pointerEl.classList.remove('is-ticking');
                }, 60);
            }
        },
        onSpinStart: () => {
            if (spinBtn) spinBtn.disabled = true;
        },
        onWinner: (winnerSlice) => {
            if (spinBtn) spinBtn.disabled = false;
            audio.playFanfare();
            lastWinnerSlice = winnerSlice;
            showWinnerModal(winnerSlice ? winnerSlice.label : 'Winner');
        }
    });

    /**
     * Show Toast Notification
     */
    function showToast(message, type = 'info', duration = 3200) {
        if (!toastContainer) return;
        const toast = document.createElement('div');
        toast.className = `acf-wheel-toast acf-wheel-toast-${type}`;

        const icon = type === 'success' ? '✅' : (type === 'error' ? '⚠️' : 'ℹ️');
        toast.innerHTML = `<span>${icon}</span> <span>${message}</span>`;

        toastContainer.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(12px)';
            setTimeout(() => toast.remove(), 250);
        }, duration);
    }

    /**
     * Helpers for Reset Rad & Original Entries
     */
    function captureOriginalEntriesIfEmpty() {
        if (originalEntries.length === 0 && entriesInput) {
            originalEntries = entriesInput.value.split('\n').map(l => l.trim()).filter(l => l.length > 0);
        }
    }

    /**
     * Session State Management (LocalStorage)
     */
    function saveSessionState() {
        try {
            const state = {
                wheelId: activeWheelId,
                token: activeShareToken,
                title: titleInput ? titleInput.value : '',
                activeEntries: entriesInput ? entriesInput.value : '',
                originalEntries: originalEntries,
                removedEntries: removedEntries
            };
            localStorage.setItem('acf_spin_active_state', JSON.stringify(state));
        } catch (e) {}
    }

    function restoreSessionState() {
        try {
            const raw = localStorage.getItem('acf_spin_active_state');
            if (!raw) return false;
            const state = JSON.parse(raw);
            if (!state || (!state.activeEntries && (!state.originalEntries || state.originalEntries.length === 0))) return false;

            activeWheelId = state.wheelId || null;
            activeShareToken = state.token || null;
            if (titleInput && state.title) titleInput.value = state.title;

            originalEntries = Array.isArray(state.originalEntries) ? state.originalEntries : [];
            removedEntries = Array.isArray(state.removedEntries) ? state.removedEntries : [];

            if (entriesInput && state.activeEntries) {
                entriesInput.value = state.activeEntries;
            } else if (entriesInput && originalEntries.length > 0) {
                const elimSet = new Set(removedEntries);
                entriesInput.value = originalEntries.filter(e => !elimSet.has(e)).join('\n');
            }

            updateWheelFromInput();
            updateResetUI();
            if (activeWheelId) {
                highlightActiveCard(activeWheelId);
                if (statusBar && statusText) {
                    statusBar.style.display = 'flex';
                    statusText.textContent = `Active wheel: "${titleInput ? titleInput.value : ''}"`;
                }
            }
            return true;
        } catch (e) {
            return false;
        }
    }

    function updateResetUI() {
        const count = removedEntries.length;
        if (removedCountSpan) removedCountSpan.textContent = count;
        if (removedStageCountSpan) removedStageCountSpan.textContent = count;
        if (btnResetWheel) btnResetWheel.style.display = count > 0 ? 'inline-flex' : 'none';
        if (btnStageReset) btnStageReset.style.display = count > 0 ? 'inline-flex' : 'none';

        if (eliminatedBox) {
            eliminatedBox.style.display = count > 0 ? 'block' : 'none';
        }
        if (eliminatedBoxCount) {
            eliminatedBoxCount.textContent = count;
        }
        if (eliminatedChips) {
            eliminatedChips.innerHTML = '';
            removedEntries.forEach(entry => {
                const chip = document.createElement('span');
                chip.className = 'acf-wheel-chip';
                chip.innerHTML = `
                    <span class="acf-wheel-chip-label">${escapeHtml(entry)}</span>
                    <button type="button" class="acf-wheel-chip-btn" title="Zet &quot;${escapeHtml(entry)}&quot; terug in het rad" aria-label="Zet terug">↩</button>
                `;
                chip.querySelector('.acf-wheel-chip-btn').addEventListener('click', (e) => {
                    e.stopPropagation();
                    restoreSingleEntry(entry);
                });
                eliminatedChips.appendChild(chip);
            });
        }
    }

    function restoreSingleEntry(entry) {
        const idx = removedEntries.indexOf(entry);
        if (idx !== -1) {
            removedEntries.splice(idx, 1);
        }
        const current = entriesInput ? entriesInput.value.split('\n').map(l => l.trim()).filter(l => l.length > 0) : [];
        if (!current.includes(entry)) {
            current.push(entry);
            if (entriesInput) entriesInput.value = current.join('\n');
        }
        updateWheelFromInput();
        updateResetUI();
        saveSessionState();
        showToast(`"${entry}" is teruggezet in het rad.`, 'info', 2000);
    }

    function resetWheelEntries() {
        if (!entriesInput) return;
        const allSet = new Set(originalEntries);
        removedEntries.forEach(r => allSet.add(r));
        const fullList = Array.from(allSet);
        if (fullList.length === 0) return;

        entriesInput.value = fullList.join('\n');
        originalEntries = fullList;
        removedEntries = [];
        updateWheelFromInput();
        updateResetUI();
        saveSessionState();
        showToast(i18n.wheelReset || 'Rad hersteld! Alle opties doen weer mee.', 'success', 2500);
    }

    /**
     * Parse Entries from Textarea into Slice Objects
     */
    function parseEntries(text) {
        const lines = text.split('\n')
            .map(line => line.trim())
            .filter(line => line.length > 0);

        return lines.map((line, i) => {
            return {
                label: line,
                color: palette[i % palette.length],
                weight: 1
            };
        });
    }

    /**
     * Update Wheel from Textarea
     */
    function updateWheelFromInput() {
        const slices = parseEntries(entriesInput.value);
        wheelCanvas.setSlices(slices);

        if (slicesCountBadge) {
            slicesCountBadge.textContent = `${slices.length} ${slices.length === 1 ? 'slice' : 'slices'}`;
        }
    }

    /**
     * Winner Modal Handling
     */
    function showWinnerModal(name) {
        if (winnerText) winnerText.textContent = name;
        if (btnRemoveWinner) {
            btnRemoveWinner.textContent = `🗑️ ${i18n.removeWinner || 'Haal eruit'}`;
        }
        if (winnerModal) winnerModal.style.display = 'flex';
        confetti.start();
    }

    function hideWinnerModal() {
        if (winnerModal) winnerModal.style.display = 'none';
        confetti.stop();
    }

    if (winnerClose) winnerClose.addEventListener('click', hideWinnerModal);
    if (btnWinnerDismiss) btnWinnerDismiss.addEventListener('click', hideWinnerModal);
    if (btnSpinAgain) {
        btnSpinAgain.addEventListener('click', () => {
            hideWinnerModal();
            setTimeout(() => wheelCanvas.spin(), 200);
        });
    }

    /**
     * Remove Winner action ("Haal eruit")
     */
    if (btnRemoveWinner) {
        btnRemoveWinner.addEventListener('click', () => {
            if (!lastWinnerSlice || !lastWinnerSlice.label) {
                hideWinnerModal();
                return;
            }

            captureOriginalEntriesIfEmpty();

            const winnerLabel = lastWinnerSlice.label.trim();
            if (!removedEntries.includes(winnerLabel)) {
                removedEntries.push(winnerLabel);
            }

            // Remove first matching line from textarea
            const lines = entriesInput.value.split('\n');
            const idx = lines.findIndex(line => line.trim() === winnerLabel);
            if (idx !== -1) {
                lines.splice(idx, 1);
            }
            entriesInput.value = lines.join('\n');

            // Ensure originalEntries has all items
            const fullSet = new Set([...originalEntries, ...removedEntries]);
            originalEntries = Array.from(fullSet);

            updateWheelFromInput();
            updateResetUI();
            saveSessionState();
            hideWinnerModal();

            const msg = (i18n.winnerRemoved || '"%s" is uit het rad gehaald.').replace('%s', winnerLabel);
            showToast(msg, 'info', 3200);
        });
    }

    if (btnResetWheel) btnResetWheel.addEventListener('click', resetWheelEntries);
    if (btnStageReset) btnStageReset.addEventListener('click', resetWheelEntries);
    if (btnResetAll) btnResetAll.addEventListener('click', resetWheelEntries);

    /**
     * Auth / Login Modal Handling
     */
    function showLoginModal() {
        if (loginModal) loginModal.style.display = 'flex';
    }
    function hideLoginModal() {
        if (loginModal) loginModal.style.display = 'none';
    }
    if (loginClose) loginClose.addEventListener('click', hideLoginModal);
    if (loginDismiss) loginDismiss.addEventListener('click', hideLoginModal);

    /**
     * Share Modal Handling
     */
    function showShareModal(url) {
        if (shareInput) shareInput.value = url;
        if (shareModal) shareModal.style.display = 'flex';
        if (shareInput) {
            shareInput.focus();
            shareInput.select();
        }
    }
    function hideShareModal() {
        if (shareModal) shareModal.style.display = 'none';
    }
    if (shareClose) shareClose.addEventListener('click', hideShareModal);
    if (btnCopyModal) {
        btnCopyModal.addEventListener('click', () => {
            if (!shareInput) return;
            navigator.clipboard.writeText(shareInput.value)
                .then(() => showToast(i18n.linkCopied || 'Share link copied!', 'success'))
                .catch(() => showToast(i18n.copyFailed || 'Failed to copy link', 'error'));
        });
    }

    // Close modals clicking outside
    [winnerModal, loginModal, shareModal].forEach(modal => {
        if (!modal) return;
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                if (modal === winnerModal) hideWinnerModal();
                else if (modal === loginModal) hideLoginModal();
                else if (modal === shareModal) hideShareModal();
            }
        });
    });

    /**
     * Sound Toggle
     */
    if (soundToggleBtn) {
        soundToggleBtn.addEventListener('click', () => {
            const isEnabled = audio.toggle();
            if (soundIcon) soundIcon.textContent = isEnabled ? '🔊' : '🔇';
            if (soundLabel) soundLabel.textContent = isEnabled ? 'Sound On' : 'Muted';
        });
    }

    /**
     * Event Listeners for Editor
     */
    if (entriesInput) {
        entriesInput.addEventListener('input', () => {
            const currentActive = entriesInput.value.split('\n').map(l => l.trim()).filter(l => l.length > 0);
            const activeSet = new Set(currentActive);
            // If user re-added an eliminated item to the textarea, remove it from removedEntries
            removedEntries = removedEntries.filter(r => !activeSet.has(r));

            const fullSet = new Set([...originalEntries, ...currentActive, ...removedEntries]);
            originalEntries = Array.from(fullSet);

            updateWheelFromInput();
            updateResetUI();
            saveSessionState();
        });
    }

    if (spinBtn) {
        spinBtn.addEventListener('click', () => {
            if (parseEntries(entriesInput.value).length < 2) {
                showToast(i18n.emptyNotice || 'Please enter at least 2 options.', 'error');
                return;
            }
            wheelCanvas.spin();
        });
    }

    if (canvasEl) {
        canvasEl.addEventListener('click', (e) => {
            // If clicking canvas directly (outside center spin button)
            if (!wheelCanvas.isSpinning && parseEntries(entriesInput.value).length >= 2) {
                wheelCanvas.spin();
            }
        });
    }

    /**
     * Quick Tools: Shuffle & Sort
     */
    if (btnShuffle) {
        btnShuffle.addEventListener('click', () => {
            const lines = entriesInput.value.split('\n').filter(l => l.trim().length > 0);
            for (let i = lines.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [lines[i], lines[j]] = [lines[j], lines[i]];
            }
            entriesInput.value = lines.join('\n');
            updateWheelFromInput();
            showToast('Entries shuffled', 'info', 1800);
        });
    }

    if (btnSort) {
        btnSort.addEventListener('click', () => {
            const lines = entriesInput.value.split('\n').filter(l => l.trim().length > 0);
            lines.sort((a, b) => a.localeCompare(b, undefined, { sensitivity: 'base' }));
            entriesInput.value = lines.join('\n');
            updateWheelFromInput();
            showToast('Entries sorted alphabetically', 'info', 1800);
        });
    }

    /**
     * LocalStorage helpers for guest wheels
     */
    function getGuestWheels() {
        try {
            return JSON.parse(localStorage.getItem('acf_spin_guest_wheels') || '[]');
        } catch (e) {
            return [];
        }
    }

    function saveGuestWheelLocally(wheelData) {
        let list = getGuestWheels();
        list = list.filter(w => w.id !== wheelData.id && w.token !== wheelData.token);
        list.unshift(wheelData);
        if (list.length > 50) list = list.slice(0, 50);
        try {
            localStorage.setItem('acf_spin_guest_wheels', JSON.stringify(list));
        } catch (e) {}
    }

    function removeGuestWheelLocally(wheelId, token) {
        let list = getGuestWheels();
        list = list.filter(w => w.id !== wheelId && w.token !== token);
        try {
            localStorage.setItem('acf_spin_guest_wheels', JSON.stringify(list));
        } catch (e) {}
    }

    /**
     * Primary Action: Save Wheel
     */
    async function saveWheel() {
        const canSave = config.isLoggedIn || !config.requireLoginToSave;
        if (!canSave) {
            showLoginModal();
            return;
        }

        const activeList = parseEntries(entriesInput.value).map(s => s.label);
        // Master allEntries includes originalEntries, current activeList, and removedEntries
        const fullSet = new Set([...originalEntries, ...activeList, ...removedEntries]);
        const allEntries = Array.from(fullSet);

        if (allEntries.length === 0) {
            showToast(i18n.emptyNotice || 'Please enter at least one entry.', 'error');
            return;
        }

        const title = (titleInput && titleInput.value.trim()) ? titleInput.value.trim() : 'My Spin Wheel';
        const baseUrl = window.location.origin + window.location.pathname;

        // UI Loading state
        const saveText = document.getElementById('acf-wheel-save-btn-text');
        const spinner = btnSave ? btnSave.querySelector('.acf-wheel-spinner') : null;
        if (btnSave) btnSave.disabled = true;
        if (spinner) spinner.style.display = 'inline-block';
        if (saveText) saveText.textContent = i18n.saving || 'Saving...';

        try {
            const response = await fetch(getApiUrl('save'), {
                method: 'POST',
                headers: getAuthHeaders(true),
                body: JSON.stringify({
                    id: activeWheelId,
                    token: activeShareToken,
                    title: title,
                    entries: allEntries, // All entries including spun ones for wheel_slices in ACF
                    all_entries: allEntries,
                    eliminated_entries: removedEntries, // Preserves which ones have been spun!
                    base_url: baseUrl
                })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || i18n.saveFailed || 'Save failed');
            }

            activeWheelId = data.id;
            activeShareToken = data.token;
            activeShareUrl = data.share_url;
            originalEntries = allEntries;

            saveSessionState();

            // Remember in localStorage if guest
            if (!config.isLoggedIn) {
                saveGuestWheelLocally({
                    id: data.id,
                    title: data.title,
                    token: data.token,
                    share_url: data.share_url,
                    slice_count: (data.slices || []).length,
                    slices: data.slices,
                    raw_entries: allEntries.join('\n'),
                    eliminated_entries: removedEntries,
                    updated_at: new Date().toISOString(),
                    date_human: 'Just now'
                });
            }

            if (statusBar && statusText) {
                statusBar.style.display = 'flex';
                statusText.textContent = `Saved just now`;
            }

            showToast(i18n.savedSuccess || 'Wheel saved successfully!', 'success');
            loadSavedWheels(); // Refresh saved wheels list
        } catch (err) {
            showToast(err.message || i18n.saveFailed || 'Failed to save wheel.', 'error');
        } finally {
            if (btnSave) btnSave.disabled = false;
            if (spinner) spinner.style.display = 'none';
            if (saveText) saveText.textContent = i18n.saveWheel || 'Save Wheel';
        }
    }

    if (btnSave) {
        btnSave.addEventListener('click', saveWheel);
    }

    /**
     * Primary Action: New Wheel
     */
    if (btnNew) {
        btnNew.addEventListener('click', () => {
            activeWheelId = null;
            activeShareToken = null;
            activeShareUrl = null;

            if (titleInput) titleInput.value = 'My Spin Wheel';
            if (entriesInput) {
                entriesInput.value = [
                    'Pizza', 'Burgers', 'Sushi', 'Pasta',
                    'Tacos', 'Salad', 'Curry', 'BBQ'
                ].join('\n');
            }

            originalEntries = [
                'Pizza', 'Burgers', 'Sushi', 'Pasta',
                'Tacos', 'Salad', 'Curry', 'BBQ'
            ];
            removedEntries = [];
            updateResetUI();

            updateWheelFromInput();
            saveSessionState();

            if (statusBar) statusBar.style.display = 'none';
            highlightActiveCard(null);

            // Clean query param if any
            const url = new URL(window.location);
            if (url.searchParams.has('wheel')) {
                url.searchParams.delete('wheel');
                window.history.pushState({}, '', url.pathname);
            }

            showToast('New wheel ready!', 'info', 2000);
        });
    }

    /**
     * Primary Action: Copy Share Link
     */
    if (btnShare) {
        btnShare.addEventListener('click', async () => {
            if (activeShareUrl) {
                showShareModal(activeShareUrl);
                navigator.clipboard.writeText(activeShareUrl)
                    .then(() => showToast(i18n.linkCopied || 'Share link copied!', 'success'))
                    .catch(() => {});
            } else {
                const canSave = config.isLoggedIn || !config.requireLoginToSave;
                if (!canSave) {
                    showLoginModal();
                } else {
                    showToast('Saving wheel to generate link...', 'info', 2000);
                    await saveWheel();
                    if (activeShareUrl) {
                        showShareModal(activeShareUrl);
                    }
                }
            }
        });
    }

    /**
     * Clone / Create My Own (from shared banner)
     */
    if (btnClone) {
        btnClone.addEventListener('click', () => {
            isReadOnly = false;
            activeWheelId = null;
            activeShareToken = null;
            activeShareUrl = null;

            if (sharedBanner) sharedBanner.style.display = 'none';
            if (titleInput) {
                titleInput.disabled = false;
                titleInput.value = (titleInput.value || 'Shared Wheel') + ' (Copy)';
            }
            if (entriesInput) entriesInput.disabled = false;
            if (btnSave) btnSave.disabled = false;
            if (btnShuffle) btnShuffle.disabled = false;
            if (btnSort) btnSort.disabled = false;

            const url = new URL(window.location);
            url.searchParams.delete('wheel');
            window.history.pushState({}, '', url.pathname);

            showToast('Wheel copied into editor. You can now edit and save it!', 'success', 4000);
        });
    }

    /**
     * Highlight Active Card in Grid
     */
    function highlightActiveCard(wheelId) {
        if (!savedGrid) return;
        const cards = savedGrid.querySelectorAll('.acf-wheel-card');
        cards.forEach(card => {
            if (wheelId && parseInt(card.getAttribute('data-wheel-id'), 10) === parseInt(wheelId, 10)) {
                card.classList.add('is-active-wheel');
            } else {
                card.classList.remove('is-active-wheel');
            }
        });
    }

    /**
     * Load Saved Wheels Grid (All published wheels globally available to everyone)
     */
    async function loadSavedWheels() {
        if (!savedGrid) return;

        const baseUrl = window.location.origin + window.location.pathname;

        try {
            let wheels = [];
            const response = await fetch(getApiUrl('my-wheels', { base_url: baseUrl }));

            if (response.ok) {
                const data = await response.json();
                if (data.success && Array.isArray(data.wheels)) {
                    wheels = data.wheels;
                }
            } else {
                throw new Error(`HTTP ${response.status}`);
            }

            if (savedCountBadge) savedCountBadge.textContent = wheels.length;

            if (wheels.length === 0) {
                savedGrid.innerHTML = `
                    <div class="acf-wheel-empty-state">
                        <div class="acf-wheel-empty-icon">🎡</div>
                        <h4>${i18n.noSavedWheels || "You haven't saved any wheels yet."}</h4>
                        <p>Customize your options above and click "Save Wheel" to access them here anytime!</p>
                    </div>
                `;
                return;
            }

            savedGrid.innerHTML = '';

            wheels.forEach(wheel => {
                const card = document.createElement('div');
                card.className = `acf-wheel-card ${activeWheelId === wheel.id ? 'is-active-wheel' : ''}`;
                card.setAttribute('data-wheel-id', wheel.id);

                // Build mini color stripe
                const stripeHtml = (wheel.slices || []).slice(0, 10).map(s => {
                    return `<div class="acf-wheel-card-stripe-slice" style="background:${s.color || '#3B82F6'};"></div>`;
                }).join('');

                const elimCount = (wheel.eliminated_entries || []).length;
                const badgeText = elimCount > 0 
                    ? `${wheel.slice_count} slices (${elimCount} gedraaid)` 
                    : `${wheel.slice_count} slices`;

                card.innerHTML = `
                    <div class="acf-wheel-card-stripe">${stripeHtml}</div>
                    <div class="acf-wheel-card-body">
                        <div class="acf-wheel-card-title-row">
                            <h4 class="acf-wheel-card-title">${escapeHtml(wheel.title)}</h4>
                            <span class="acf-wheel-card-badge">${badgeText}</span>
                        </div>
                        <div class="acf-wheel-card-meta">Updated ${wheel.date_human || wheel.updated_at}</div>
                        <div class="acf-wheel-card-actions">
                            <button type="button" class="acf-wheel-btn acf-wheel-btn-primary acf-wheel-btn-sm btn-load" title="Load into editor">
                                📥 Load
                            </button>
                            <button type="button" class="acf-wheel-btn acf-wheel-btn-outline acf-wheel-btn-sm btn-copy" title="Copy shareable link">
                                🔗 Share
                            </button>
                            <button type="button" class="acf-wheel-btn acf-wheel-btn-danger acf-wheel-btn-sm acf-wheel-btn-icon-only btn-delete" title="Delete wheel">
                                🗑️
                            </button>
                        </div>
                    </div>
                `;

                // Load action
                card.querySelector('.btn-load').addEventListener('click', () => {
                    activeWheelId = wheel.id;
                    activeShareToken = wheel.token;
                    activeShareUrl = wheel.share_url;

                    if (titleInput) {
                        titleInput.value = wheel.title;
                        titleInput.disabled = false;
                    }

                    originalEntries = wheel.raw_entries ? wheel.raw_entries.split('\n').map(l => l.trim()).filter(l => l.length > 0) : [];
                    removedEntries = Array.isArray(wheel.eliminated_entries) ? [...wheel.eliminated_entries] : [];

                    // Filter out already eliminated entries for active canvas & textarea
                    const elimSet = new Set(removedEntries);
                    const remaining = originalEntries.filter(l => !elimSet.has(l));

                    if (entriesInput) {
                        entriesInput.value = (remaining.length > 0 ? remaining : originalEntries).join('\n');
                        entriesInput.disabled = false;
                    }

                    if (btnShuffle) btnShuffle.disabled = false;
                    if (btnSort) btnSort.disabled = false;
                    if (btnSave) btnSave.disabled = false;

                    if (sharedBanner) sharedBanner.style.display = 'none';

                    updateWheelFromInput();
                    updateResetUI();
                    saveSessionState();
                    highlightActiveCard(wheel.id);

                    if (statusBar && statusText) {
                        statusBar.style.display = 'flex';
                        statusText.textContent = `Loaded "${wheel.title}"`;
                    }

                    // Smooth scroll up to editor
                    window.scrollTo({
                        top: rootEl.getBoundingClientRect().top + window.pageYOffset - 40,
                        behavior: 'smooth'
                    });

                    showToast(`Loaded "${wheel.title}"`, 'success', 2200);
                });

                // Copy share link action
                card.querySelector('.btn-copy').addEventListener('click', () => {
                    navigator.clipboard.writeText(wheel.share_url)
                        .then(() => showToast(i18n.linkCopied || 'Share link copied to clipboard!', 'success'))
                        .catch(() => showToast(i18n.copyFailed || 'Failed to copy link', 'error'));
                });

                // Delete action
                card.querySelector('.btn-delete').addEventListener('click', async () => {
                    if (!confirm(i18n.deleteConfirm || 'Are you sure you want to delete this wheel?')) {
                        return;
                    }

                    if (!config.isLoggedIn) {
                        removeGuestWheelLocally(wheel.id, wheel.token);
                    }

                    try {
                        await fetch(getApiUrl(`delete/${wheel.id}`), {
                            method: 'DELETE',
                            headers: getAuthHeaders()
                        });
                    } catch (e) {}

                    // Remove card animation
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        card.remove();
                        if (activeWheelId === wheel.id) {
                            activeWheelId = null;
                            activeShareToken = null;
                            activeShareUrl = null;
                            if (statusBar) statusBar.style.display = 'none';
                        }
                        loadSavedWheels();
                    }, 200);

                    showToast(i18n.deletedSuccess || 'Wheel deleted', 'info');
                });

                savedGrid.appendChild(card);
            });
        } catch (e) {
            console.error('Failed to load saved wheels:', e);
            if (savedGrid) {
                savedGrid.innerHTML = `
                    <div class="acf-wheel-empty-state">
                        <div class="acf-wheel-empty-icon">⚠️</div>
                        <h4>${i18n.loadFailed || 'Kon opgeslagen wielen niet laden.'}</h4>
                        <button type="button" class="acf-wheel-btn acf-wheel-btn-outline acf-wheel-btn-sm" id="acf-wheel-btn-retry-load">
                            🔄 ${i18n.retry || 'Opnieuw proberen'}
                        </button>
                    </div>
                `;
                const retryBtn = document.getElementById('acf-wheel-btn-retry-load');
                if (retryBtn) {
                    retryBtn.addEventListener('click', () => loadSavedWheels());
                }
            }
        }
    }

    /**
     * Load Public Shared Wheel
     */
    async function loadPublicWheel(token) {
        try {
            const response = await fetch(getApiUrl(`public/${encodeURIComponent(token)}`));
            if (!response.ok) {
                showToast('Shared wheel not found or deleted.', 'error');
                return;
            }

            const data = await response.json();
            if (!data.success) return;

            // Shared wheels load ready to spin or edit as a new wheel copy
            activeWheelId = null;
            activeShareToken = null;
            activeShareUrl = window.location.href;

            if (titleInput) {
                titleInput.value = data.title || 'Shared Wheel';
                titleInput.disabled = false;
            }

            if (entriesInput) {
                entriesInput.value = data.raw_entries || '';
                entriesInput.disabled = false;
            }

            originalEntries = data.raw_entries ? data.raw_entries.split('\n').map(l => l.trim()).filter(l => l.length > 0) : [];
            removedEntries = Array.isArray(data.eliminated_entries) ? [...data.eliminated_entries] : [];

            const elimSet = new Set(removedEntries);
            const remaining = originalEntries.filter(l => !elimSet.has(l));

            if (titleInput) {
                titleInput.value = data.title || 'Shared Wheel';
                titleInput.disabled = false;
            }

            if (entriesInput) {
                entriesInput.value = (remaining.length > 0 ? remaining : originalEntries).join('\n');
                entriesInput.disabled = false;
            }

            updateWheelFromInput();
            updateResetUI();
            saveSessionState();

            // Ensure editor controls remain fully accessible
            if (btnShuffle) btnShuffle.disabled = false;
            if (btnSort) btnSort.disabled = false;
            if (btnSave) btnSave.disabled = false;

            // Update banner details
            const bannerTitle = document.getElementById('acf-wheel-banner-title');
            if (bannerTitle) {
                bannerTitle.textContent = `Viewing: "${data.title}"`;
            }

            if (sharedBanner) {
                sharedBanner.style.display = 'flex';
            }

            showToast(`Loaded shared wheel "${data.title}"`, 'info', 3000);
        } catch (e) {
            showToast('Unable to load shared wheel.', 'error');
        }
    }

    /**
     * Utility: Escape HTML
     */
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Initialize App: Restore active session state if available, else load default
    let restored = false;
    if (!config.currentShareToken) {
        restored = restoreSessionState();
    }

    if (!restored) {
        updateWheelFromInput();
        captureOriginalEntriesIfEmpty();
    }

    if (config.currentShareToken) {
        loadPublicWheel(config.currentShareToken);
    }

    loadSavedWheels();
});
