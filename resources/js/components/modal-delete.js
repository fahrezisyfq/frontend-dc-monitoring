// Open with: $dispatch('modal-delete:open', { name: 'records', id: 1, dataName: 'Record' }).
// The configured Livewire action receives the ID and returns { success: true } on success,
// or { success: false, message: '...' } on failure. Authorization stays in Livewire.
export function modalDelete(options = {}) {
    return {
        isOpen: false,
        busy: false,
        selectedId: null,
        dataName: "",
        title: "",
        message: "",
        error: "",
        trigger: null,
        disposed: false,

        openModal(detail = {}) {
            if (
                !detail ||
                this.disposed ||
                this.isOpen ||
                this.busy ||
                detail.name !== (options.name ?? "delete")
            )
                return;

            const id = detail.id ?? options.dataId;
            if (
                !(
                    (typeof id === "string" && id.trim() !== "") ||
                    (typeof id === "number" && Number.isFinite(id))
                )
            )
                return;

            this.selectedId = id;
            this.trigger = document.activeElement;
            this.dataName = detail.dataName ?? options.dataName ?? "";
            this.title = detail.title ?? options.title ?? "Hapus Data";
            this.message =
                detail.message ??
                options.message ??
                "Apakah Anda yakin ingin menghapus data ini?";
            this.error = "";
            this.isOpen = true;
            this.$dispatch("modal-delete:opened", { name: options.name });
        },

        closeModal() {
            if (this.busy || !this.isOpen) return;

            this.isOpen = false;
            // Keep the displayed content intact during the leave transition.
            this.selectedId = null;
            this.$dispatch("modal-delete:closed", {
                name: options.name,
                triggerRemoved: !this.trigger?.isConnected,
            });
        },

        async confirmDelete() {
            if (
                this.disposed ||
                this.busy ||
                !this.isOpen ||
                this.selectedId === null
            )
                return;

            this.error = "";
            if (
                typeof options.action !== "string" ||
                options.action.trim() === ""
            ) {
                this.error = "Aksi hapus belum dikonfigurasi.";
                return;
            }

            this.busy = true;

            try {
                const result = await this.$wire.$call(
                    options.action,
                    this.selectedId,
                );
                if (this.disposed) return;

                if (result?.success === true) {
                    this.busy = false;
                    this.closeModal();
                } else {
                    this.error =
                        result?.message ||
                        "Data belum berhasil dihapus. Silakan coba lagi.";
                }
            } catch {
                if (this.disposed) return;
                this.error =
                    "Proses belum berhasil. Periksa koneksi lalu coba lagi.";
                this.$dispatch("toast", {
                    type: "error",
                    message: "Terjadi kesalahan. Silakan coba lagi.",
                });
            } finally {
                this.busy = false;
            }
        },

        destroy() {
            this.disposed = true;
            this.trigger = null;
        },
    };
}

document.addEventListener("alpine:init", () => {
    window.Alpine.data("modalDelete", modalDelete);
});
