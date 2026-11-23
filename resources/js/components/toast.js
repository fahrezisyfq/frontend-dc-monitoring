export function toastNotifications() {
    return {
        queue: [],
        current: null,
        visible: false,
        timer: null,
        exitTimer: null,

        enqueue(notification) {
            if (
                !["success", "error"].includes(notification?.type) ||
                typeof notification.message !== "string"
            )
                return;
            this.queue.push({
                type: notification.type,
                message: notification.message,
            });
            if (!this.current) this.next();
        },

        next() {
            this.current = this.queue.shift() ?? null;
            if (!this.current) return;
            this.$nextTick(() => {
                if (!this.current) return;
                this.visible = true;
                this.timer = setTimeout(
                    () => this.dismiss(),
                    this.current.type === "error" ? 5000 : 3000,
                );
            });
        },

        dismiss() {
            if (!this.visible) return;
            clearTimeout(this.timer);
            this.visible = false;
            this.exitTimer = setTimeout(() => {
                this.current = null;
                this.next();
            }, 200);
        },

        clear() {
            clearTimeout(this.timer);
            clearTimeout(this.exitTimer);
            this.queue = [];
            this.current = null;
            this.visible = false;
        },

        destroy() {
            this.clear();
        },
    };
}

document.addEventListener("alpine:init", () => {
    window.Alpine.data("toastNotifications", toastNotifications);
});
