// Use a unique name for each drawer. Alpine and Livewire can dispatch:
// drawer:open { name: 'record-detail', id: recordId }
// drawer:close { name: 'record-detail' }
// Populate Livewire properties before opening; only the selected ID is retained.
// Focus trapping, scroll locking and their cleanup use Alpine's existing focus plugin.
export function drawer(options = {}) {
    return {
        isOpen: false,
        selectedId: null,
        disposed: false,

        openDrawer(detail = {}) {
            if (this.disposed || !detail || detail.name !== options.name) return;

            this.selectedId = detail.id ?? null;
            if (this.isOpen) return;

            this.isOpen = true;
            this.$dispatch('drawer:opened', { name: options.name, id: this.selectedId });
        },

        closeDrawer() {
            if (!this.isOpen) return;

            this.isOpen = false;
            this.$dispatch('drawer:closed', { name: options.name, id: this.selectedId });
            // Keep slot content intact throughout the leave transition.
        },

        handleClose(detail = {}) {
            if (detail?.name === options.name) this.closeDrawer();
        },

        handleEscape(event) {
            if (options.closeOnEscape !== false && !event.defaultPrevented
                && this.$refs.panel.contains(document.activeElement)) {
                this.closeDrawer();
            }
        },

        handleOutside() {
            if (options.closeOnOutside !== false) this.closeDrawer();
        },

        destroy() {
            this.disposed = true;
            this.isOpen = false;
            this.selectedId = null;
        },
    };
}

document.addEventListener('alpine:init', () => {
    window.Alpine.data('drawer', drawer);
});
