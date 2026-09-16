/**
 * Web Audio API Sound Synthesizer for ACF Spin Wheel
 * Procedural audio generation with zero external asset dependencies
 */

class SpinWheelAudio {
    constructor() {
        this.ctx = null;
        this.enabled = true;
        this.lastTickTime = 0;
        this.minTickInterval = 40; // ms minimum between ticks to prevent stuttering
    }

    /**
     * Lazily initialize AudioContext on user interaction
     */
    initContext() {
        if (!this.ctx && (window.AudioContext || window.webkitAudioContext)) {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            this.ctx = new AudioCtx();
        }
        if (this.ctx && this.ctx.state === 'suspended') {
            this.ctx.resume().catch(() => {});
        }
    }

    /**
     * Toggle sound on/off
     */
    toggle() {
        this.enabled = !this.enabled;
        return this.enabled;
    }

    /**
     * Play a mechanical tick sound when a slice peg passes the pointer
     */
    playTick(volume = 0.25) {
        if (!this.enabled) return;

        const nowMs = performance.now();
        if (nowMs - this.lastTickTime < this.minTickInterval) {
            return;
        }
        this.lastTickTime = nowMs;

        try {
            this.initContext();
            if (!this.ctx) return;

            const t = this.ctx.currentTime;

            // Short pitch blip for realistic mechanical click
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();

            osc.type = 'triangle';
            osc.frequency.setValueAtTime(650, t);
            osc.frequency.exponentialRampToValueAtTime(180, t + 0.025);

            gain.gain.setValueAtTime(volume, t);
            gain.gain.exponentialRampToValueAtTime(0.001, t + 0.025);

            osc.connect(gain);
            gain.connect(this.ctx.destination);

            osc.start(t);
            osc.stop(t + 0.025);
        } catch (e) {
            // Audio context not allowed or unsupported
        }
    }

    /**
     * Play a pleasant celebration fanfare chime when a winner is determined
     */
    playFanfare() {
        if (!this.enabled) return;

        try {
            this.initContext();
            if (!this.ctx) return;

            const t = this.ctx.currentTime;
            // Chord notes: C5, E5, G5, C6 arpeggiated
            const notes = [
                { freq: 523.25, time: 0.00, dur: 0.35 },
                { freq: 659.25, time: 0.12, dur: 0.35 },
                { freq: 783.99, time: 0.24, dur: 0.45 },
                { freq: 1046.50, time: 0.36, dur: 0.90 },
            ];

            notes.forEach(n => {
                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(n.freq, t + n.time);

                gain.gain.setValueAtTime(0, t + n.time);
                gain.gain.linearRampToValueAtTime(0.28, t + n.time + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.001, t + n.time + n.dur);

                osc.connect(gain);
                gain.connect(this.ctx.destination);

                osc.start(t + n.time);
                osc.stop(t + n.time + n.dur);
            });
        } catch (e) {
            // Audio context not allowed or unsupported
        }
    }
}

window.SpinWheelAudio = SpinWheelAudio;
