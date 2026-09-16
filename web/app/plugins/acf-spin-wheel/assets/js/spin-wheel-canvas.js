/**
 * HTML5 Canvas Wheel Renderer and Physics Engine
 * ACF Spin Wheel
 */

class SpinWheelCanvas {
    constructor(canvas, options = {}) {
        this.canvas = canvas;
        this.ctx = canvas.getContext('2d');
        this.options = Object.assign({
            onTick: () => {},
            onWinner: () => {},
            onSpinStart: () => {},
        }, options);

        this.slices = [];
        this.currentRotation = 0; // Current angle in radians
        this.isSpinning = false;
        this.animationId = null;

        // Pointer location is at 12 o'clock (top center: 3 * Math.PI / 2)
        this.pointerAngle = 1.5 * Math.PI;

        this.init();
    }

    init() {
        this.resize();
        window.addEventListener('resize', () => this.resize());
    }

    /**
     * Handle High-DPI screen resizing
     */
    resize() {
        if (!this.canvas) return;
        const rect = this.canvas.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;
        const size = Math.min(rect.width, rect.height) || 500;

        this.canvas.width = size * dpr;
        this.canvas.height = size * dpr;

        this.width = size;
        this.height = size;
        this.centerX = (size * dpr) / 2;
        this.centerY = (size * dpr) / 2;
        this.radius = ((size * dpr) / 2) * 0.94;

        this.dpr = dpr;
        this.draw();
    }

    /**
     * Update slices array and redraw
     */
    setSlices(slices) {
        this.slices = Array.isArray(slices) ? slices : [];
        this.draw();
    }

    /**
     * Calculate total weight of all slices
     */
    getTotalWeight() {
        return this.slices.reduce((sum, slice) => sum + (slice.weight || 1), 0) || 1;
    }

    /**
     * Truncate text with ellipsis if exceeding max width
     */
    truncateText(text, maxWidth, font) {
        this.ctx.font = font;
        if (this.ctx.measureText(text).width <= maxWidth) {
            return text;
        }
        let truncated = text;
        while (truncated.length > 1 && this.ctx.measureText(truncated + '...').width > maxWidth) {
            truncated = truncated.slice(0, -1);
        }
        return truncated + '...';
    }

    /**
     * Determine best contrast color (white or dark)
     */
    getContrastColor(hexColor) {
        if (!hexColor || hexColor.charAt(0) !== '#') return '#FFFFFF';
        const hex = hexColor.replace('#', '');
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
        return (yiq >= 170) ? '#1E293B' : '#FFFFFF';
    }

    /**
     * Main Wheel Draw
     */
    draw() {
        if (!this.ctx) return;

        const ctx = this.ctx;
        const width = this.canvas.width;
        const height = this.canvas.height;
        const cx = this.centerX;
        const cy = this.centerY;
        const r = this.radius;

        ctx.clearRect(0, 0, width, height);

        if (this.slices.length === 0) {
            // Empty wheel placeholder
            ctx.save();
            ctx.beginPath();
            ctx.arc(cx, cy, r, 0, 2 * Math.PI);
            ctx.fillStyle = '#E5E7EB';
            ctx.fill();
            ctx.lineWidth = 4 * this.dpr;
            ctx.strokeStyle = '#D1D5DB';
            ctx.stroke();

            ctx.fillStyle = '#6B7280';
            ctx.font = `bold ${16 * this.dpr}px sans-serif`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('Enter slices to begin', cx, cy);
            ctx.restore();
            return;
        }

        const totalWeight = this.getTotalWeight();
        let currentAngle = this.currentRotation;

        ctx.save();

        // 1. Draw outer wheel border & shadow
        ctx.beginPath();
        ctx.arc(cx, cy, r + 4 * this.dpr, 0, 2 * Math.PI);
        ctx.fillStyle = '#1E293B';
        ctx.fill();

        // 2. Draw each slice
        this.slices.forEach((slice, index) => {
            const weight = slice.weight || 1;
            const sliceAngle = (weight / totalWeight) * (2 * Math.PI);
            const endAngle = currentAngle + sliceAngle;

            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, r, currentAngle, endAngle);
            ctx.closePath();

            // Fill slice
            ctx.fillStyle = slice.color || '#3B82F6';
            ctx.fill();

            // Slice boundary stroke
            ctx.lineWidth = 2 * this.dpr;
            ctx.strokeStyle = '#FFFFFF';
            ctx.stroke();

            // Slice decorative outer peg
            const pegAngle = currentAngle;
            const pegX = cx + (r - 8 * this.dpr) * Math.cos(pegAngle);
            const pegY = cy + (r - 8 * this.dpr) * Math.sin(pegAngle);
            ctx.beginPath();
            ctx.arc(pegX, pegY, 3 * this.dpr, 0, 2 * Math.PI);
            ctx.fillStyle = '#FFFFFF';
            ctx.fill();

            // 3. Draw Slice Label
            ctx.save();
            ctx.translate(cx, cy);
            const midAngle = currentAngle + sliceAngle / 2;
            ctx.rotate(midAngle);

            // Compute responsive font size based on slice count and length
            const count = this.slices.length;
            let fontSize = 16;
            if (count > 24) fontSize = 10;
            else if (count > 16) fontSize = 12;
            else if (count > 10) fontSize = 14;
            else fontSize = 17;

            const font = `bold ${fontSize * this.dpr}px sans-serif`;
            ctx.font = font;
            ctx.textAlign = 'right';
            ctx.textBaseline = 'middle';

            const textColor = this.getContrastColor(slice.color);
            ctx.fillStyle = textColor;

            // Subtle text shadow for high readability
            if (textColor === '#FFFFFF') {
                ctx.shadowColor = 'rgba(0, 0, 0, 0.4)';
                ctx.shadowBlur = 4 * this.dpr;
            } else {
                ctx.shadowColor = 'rgba(255, 255, 255, 0.6)';
                ctx.shadowBlur = 3 * this.dpr;
            }

            const maxTextWidth = r * 0.62;
            const labelText = this.truncateText(slice.label || `Option ${index + 1}`, maxTextWidth, font);

            ctx.fillText(labelText, r * 0.88, 0);
            ctx.restore();

            currentAngle = endAngle;
        });

        // 3. Outer rim highlights
        ctx.beginPath();
        ctx.arc(cx, cy, r, 0, 2 * Math.PI);
        ctx.lineWidth = 6 * this.dpr;
        ctx.strokeStyle = '#FFFFFF';
        ctx.stroke();

        ctx.restore();
    }

    /**
     * Get the slice that is currently under the pointer
     */
    getCurrentWinner() {
        if (!this.slices.length) return null;

        const totalWeight = this.getTotalWeight();
        // Normalize rotation to [0, 2*PI)
        const rot = (this.currentRotation % (2 * Math.PI) + 2 * Math.PI) % (2 * Math.PI);

        // Pointer is at 1.5 * Math.PI (12 o'clock)
        // Wheel relative angle:
        let relativeAngle = (this.pointerAngle - rot) % (2 * Math.PI);
        if (relativeAngle < 0) {
            relativeAngle += 2 * Math.PI;
        }

        let accumulatedAngle = 0;
        for (let i = 0; i < this.slices.length; i++) {
            const slice = this.slices[i];
            const sliceAngle = ((slice.weight || 1) / totalWeight) * (2 * Math.PI);
            if (relativeAngle >= accumulatedAngle && relativeAngle < accumulatedAngle + sliceAngle) {
                return slice;
            }
            accumulatedAngle += sliceAngle;
        }

        return this.slices[this.slices.length - 1];
    }

    /**
     * Spin the wheel with realistic physics and smooth deceleration
     */
    spin() {
        if (this.isSpinning || this.slices.length < 2) return;

        this.isSpinning = true;
        this.options.onSpinStart();

        const startRotation = this.currentRotation;
        // 5 to 8 full rotations plus random angle
        const minRotations = 5;
        const extraRotations = Math.random() * 3.5;
        const randomExtraAngle = Math.random() * 2 * Math.PI;
        const totalDelta = (minRotations + extraRotations) * 2 * Math.PI + randomExtraAngle;

        // Spin duration between 5.5s and 6.5s
        const duration = 5500 + Math.random() * 1000;
        const startTime = performance.now();

        const totalWeight = this.getTotalWeight();
        let lastSliceIndex = -1;

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Cubic-quartic deceleration curve for suspenseful finish
            const easeOut = 1 - Math.pow(1 - progress, 3.5);

            this.currentRotation = startRotation + totalDelta * easeOut;

            // Detect slice transition to trigger tick sound & pointer vibration
            const currentWinner = this.getCurrentWinner();
            const currentIndex = this.slices.indexOf(currentWinner);
            if (currentIndex !== -1 && currentIndex !== lastSliceIndex) {
                lastSliceIndex = currentIndex;
                this.options.onTick();
            }

            this.draw();

            if (progress < 1) {
                this.animationId = requestAnimationFrame(animate);
            } else {
                this.isSpinning = false;
                const winner = this.getCurrentWinner();
                this.options.onWinner(winner);
            }
        };

        this.animationId = requestAnimationFrame(animate);
    }
}

window.SpinWheelCanvas = SpinWheelCanvas;
