// Shared ownership of synchronous Dashboard charts and their Livewire cleanup.
const charts = [];

function initializeCharts() {
    for (const { selector, create, instances } of charts) {
        for (const [card, chart] of instances) {
            if (!card.isConnected) {
                chart.cleanup();
                instances.delete(card);
            }
        }

        document.querySelectorAll(selector).forEach((card) => {
            if (!instances.has(card)) instances.set(card, create(card));
            instances.get(card).update();
        });
    }
}

export function registerDashboardChart(selector, create) {
    charts.push({ selector, create, instances: new Map() });
    if (document.readyState !== "loading") initializeCharts();
}

document.addEventListener("DOMContentLoaded", initializeCharts);
document.addEventListener("livewire:navigated", initializeCharts);
document.addEventListener("livewire:navigating", () => {
    charts.forEach(({ instances }) => {
        instances.forEach((chart) => chart.cleanup());
        instances.clear();
    });
});
document.addEventListener("livewire:init", () => {
    Livewire.hook("morphed", initializeCharts);
    Livewire.hook("morph.removed", () => queueMicrotask(initializeCharts));
});
