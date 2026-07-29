export function teamCarousel(count = 1) {
    return {
        index: 0,
        offset: 0,
        count,
        cardWidth: 0,
        gap: 24,
        visible: 1,
        get canPrev() {
            return this.index > 0;
        },
        get canNext() {
            return this.index < Math.max(0, this.count - this.visible);
        },
        init() {
            this.measure();
            window.addEventListener('resize', () => this.measure(), { passive: true });
        },
        measure() {
            const card = this.$refs.track?.querySelector('.team-card');
            if (!card) {
                return;
            }
            this.cardWidth = card.getBoundingClientRect().width;
            const viewport = this.$refs.viewport?.getBoundingClientRect().width || 0;
            this.visible = Math.max(1, Math.floor((viewport + this.gap) / (this.cardWidth + this.gap)));
            this.index = Math.min(this.index, Math.max(0, this.count - this.visible));
            this.offset = this.index * (this.cardWidth + this.gap);
        },
        prev() {
            if (!this.canPrev) return;
            this.index -= 1;
            this.offset = this.index * (this.cardWidth + this.gap);
        },
        next() {
            if (!this.canNext) return;
            this.index += 1;
            this.offset = this.index * (this.cardWidth + this.gap);
        },
    };
}
