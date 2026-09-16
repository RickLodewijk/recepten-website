/**
 * Lightweight Canvas Confetti Effect for ACF Spin Wheel
 */

class SpinWheelConfetti {
    constructor(canvasElement) {
        this.canvas = canvasElement;
        this.ctx = this.canvas ? this.canvas.getContext('2d') : null;
        this.particles = [];
        this.animationId = null;
        this.colors = ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#FBBF24', '#06B6D4'];
        this.isActive = false;

        this.handleResize = this.resize.bind(this);
    }

    resize() {
        if (!this.canvas) return;
        const rect = this.canvas.parentElement ? this.canvas.parentElement.getBoundingClientRect() : { width: 500, height: 400 };
        this.canvas.width = rect.width;
        this.canvas.height = rect.height;
    }

    /**
     * Launch confetti burst
     */
    start(count = 90) {
        if (!this.canvas || !this.ctx) return;
        this.stop();
        this.resize();
        this.isActive = true;

        this.particles = [];
        const width = this.canvas.width;
        const height = this.canvas.height;

        for (let i = 0; i < count; i++) {
            this.particles.push({
                x: width / 2 + (Math.random() - 0.5) * 60,
                y: height * 0.35 + (Math.random() - 0.5) * 40,
                vx: (Math.random() - 0.5) * 12,
                vy: -Math.random() * 10 - 4,
                size: Math.random() * 8 + 6,
                color: this.colors[Math.floor(Math.random() * this.colors.length)],
                rotation: Math.random() * 360,
                rotationSpeed: (Math.random() - 0.5) * 12,
                opacity: 1,
                decay: Math.random() * 0.005 + 0.005,
                shape: Math.random() > 0.4 ? 'rect' : 'circle',
            });
        }

        window.addEventListener('resize', this.handleResize);
        this.loop();
    }

    /**
     * Animation loop
     */
    loop() {
        if (!this.isActive || !this.ctx) return;

        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        let activeCount = 0;
        const gravity = 0.35;
        const friction = 0.98;

        for (let i = 0; i < this.particles.length; i++) {
            const p = this.particles[i];

            if (p.opacity <= 0.02) continue;
            activeCount++;

            p.vx *= friction;
            p.vy += gravity;
            p.x += p.vx;
            p.y += p.vy;
            p.rotation += p.rotationSpeed;
            p.opacity -= p.decay;

            this.ctx.save();
            this.ctx.globalAlpha = Math.max(0, p.opacity);
            this.ctx.translate(p.x, p.y);
            this.ctx.rotate((p.rotation * Math.PI) / 180);
            this.ctx.fillStyle = p.color;

            if (p.shape === 'rect') {
                this.ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
            } else {
                this.ctx.beginPath();
                this.ctx.arc(0, 0, p.size / 2, 0, Math.PI * 2);
                this.ctx.fill();
            }

            this.ctx.restore();
        }

        if (activeCount > 0) {
            this.animationId = requestAnimationFrame(this.loop.bind(this));
        } else {
            this.stop();
        }
    }

    /**
     * Stop and clear
     */
    stop() {
        this.isActive = false;
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
            this.animationId = null;
        }
        if (this.ctx && this.canvas) {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        }
        window.removeEventListener('resize', this.handleResize);
    }
}

window.SpinWheelConfetti = SpinWheelConfetti;
