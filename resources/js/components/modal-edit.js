// Mount inside the owning Livewire component and use a unique modal name per page.
// Open: $dispatch('modal-edit:open', { name: 'records', id: recordId }).
// loadAction(id) fills Livewire properties and resets its validation errors.
// Without loadAction, populate those properties in Livewire BEFORE dispatching the event,
// unless opting into the local draft mode below.
// Both actions return { success: true }, or { success: false, message: '...' }.
// By default, slot fields use wire:model; Alpine never copies or resets their values.
// Optional useDraft mode accepts detail.data for x-model="draft.field" slots and
// calls action(draft, id). It displays returned errors without validating locally.
export function modalEdit(options = {}) {
    return {
        isOpen: false,
        busy: false,
        loading: false,
        ready: false,
        selectedId: null,
        title: "",
        description: "",
        error: "",
        trigger: null,
        disposed: false,
        draft: {},
        errors: {},

        async openModal(detail = {}) {
            if (
                !detail ||
                this.disposed ||
                this.isOpen ||
                this.busy ||
                detail.name !== (options.name ?? "edit")
            )
                return;

            const id = detail.id;
            if (
                !(
                    (typeof id === "string" && id.trim() !== "") ||
                    (typeof id === "number" && Number.isFinite(id))
                )
            )
                return;

            this.trigger = document.activeElement;
            this.selectedId = id;
            this.title = detail.title ?? options.title ?? "Edit Data";
            this.description = detail.description ?? options.description ?? "";
            this.error = "";
            this.errors = {};
            if (options.useDraft)
                this.draft = JSON.parse(JSON.stringify(detail.data ?? {}));
            this.ready = false;
            this.isOpen = true;
            this.$dispatch("modal-edit:opened", { name: options.name });

            if (!options.loadAction) {
                this.ready = true;
                this.focusForm();
                return;
            }

            this.busy = true;
            this.loading = true;

            try {
                const result = await this.$wire.$call(options.loadAction, id);
                if (this.disposed) return;

                this.ready = result?.success === true;
                if (!this.ready)
                    this.error =
                        result?.message ||
                        "Data belum berhasil dimuat. Tutup modal lalu coba lagi.";
            } catch {
                if (!this.disposed)
                    this.error =
                        "Data belum berhasil dimuat. Tutup modal lalu coba lagi.";
            } finally {
                if (!this.disposed) {
                    this.busy = false;
                    this.loading = false;
                    this.focusForm();
                }
            }
        },

        focusForm() {
            this.$nextTick(() => {
                if (this.disposed || !this.isOpen) return;

                if (this.ready) this.$focus.within(this.$refs.fields).first();
                else this.$refs.cancel.focus();
            });
        },

        closeModal() {
            if (this.busy || !this.isOpen) return;

            this.isOpen = false;
            this.selectedId = null;
            for (const field of options.clearFields ?? [])
                this.draft[field] = "";
            this.$dispatch("modal-edit:closed", { name: options.name });
            // Leave field values to Livewire, including during the leave transition.
            this.$nextTick(() => {
                if (!this.disposed && !this.isOpen && this.trigger?.isConnected)
                    this.trigger.focus();
            });
        },

        errorFor(field) {
            return this.errors[`form.${field}`]?.[0] ?? "";
        },

        async submit() {
            if (
                this.disposed ||
                this.busy ||
                !this.isOpen ||
                !this.ready ||
                this.selectedId === null
            )
                return;

            this.error = "";
            this.errors = {};
            if (
                typeof options.action !== "string" ||
                options.action.trim() === ""
            ) {
                this.error = "Aksi simpan belum dikonfigurasi.";
                return;
            }

            this.busy = true;

            try {
                const args = options.useDraft
                    ? [{ ...this.draft }, this.selectedId]
                    : [this.selectedId];
                const result = await this.$wire.$call(options.action, ...args);
                if (this.disposed) return;

                // Validation failures may resolve without a result: only explicit success closes.
                if (result?.success === true) {
                    this.busy = false;
                    this.closeModal();
                } else {
                    this.errors = result?.errors ?? {};
                    this.error =
                        result?.message ??
                        (Object.keys(this.errors).length
                            ? ""
                            : "Perubahan belum tersimpan. Periksa isian form lalu coba lagi.");
                    this.$nextTick(() =>
                        this.$refs.fields
                            .querySelector('[aria-invalid="true"]')
                            ?.focus(),
                    );
                }
            } catch {
                if (!this.disposed) {
                    this.error =
                        "Proses belum berhasil. Periksa koneksi lalu coba lagi.";
                    this.$dispatch("toast", {
                        type: "error",
                        message: "Terjadi kesalahan. Silakan coba lagi.",
                    });
                }
            } finally {
                if (!this.disposed) this.busy = false;
            }
        },

        destroy() {
            this.disposed = true;
            this.trigger = null;
            this.draft = {};
        },
    };
}

document.addEventListener("alpine:init", () => {
    window.Alpine.data("modalEdit", modalEdit);
});
