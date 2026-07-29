const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

export function beforeAfterCompare(initialPercent = 50) {
    return {
        position: initialPercent,
        dragging: false,

        init() {
            this.$nextTick(() => this.syncBeforeImageWidth());
            this._onResize = () => this.syncBeforeImageWidth();
            window.addEventListener('resize', this._onResize, { passive: true });
        },

        destroy() {
            window.removeEventListener('resize', this._onResize);
        },

        syncBeforeImageWidth() {
            const track = this.$refs.track;
            const before = this.$refs.beforeImg;
            if (! track || ! before) {
                return;
            }
            before.style.width = `${track.offsetWidth}px`;
        },

        startDrag(event) {
            this.dragging = true;
            this.$refs.track?.setPointerCapture?.(event.pointerId);
            this.updateFromEvent(event);
        },

        onDrag(event) {
            if (!this.dragging) {
                return;
            }
            this.updateFromEvent(event);
        },

        endDrag(event) {
            this.dragging = false;
            try {
                this.$refs.track?.releasePointerCapture?.(event.pointerId);
            } catch {
                /* pointer already released */
            }
        },

        updateFromEvent(event) {
            const track = this.$refs.track;
            if (!track) {
                return;
            }
            const rect = track.getBoundingClientRect();
            const x = clamp(event.clientX - rect.left, 0, rect.width);
            this.position = rect.width ? (x / rect.width) * 100 : this.position;
        },

        nudge(delta) {
            this.position = clamp(this.position + delta, 5, 95);
        },

        onKeydown(event) {
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                this.nudge(-4);
            } else if (event.key === 'ArrowRight') {
                event.preventDefault();
                this.nudge(4);
            }
        },
    };
}
