function hashHandler() {
    return {
        hash: window.location.hash,
        modalOpen: false,
        checkHash(hash) {
            return this.hash === '#' + hash;
        },
        init() {
            window.addEventListener('hashchange', () => {
                this.hash = window.location.hash;
            }, false);
            this.hash = window.location.hash;
        }
    }
}
