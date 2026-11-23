import * as d3 from "d3";
import { registerDashboardChart } from "./lifecycle";

const statuses = [
    { name: "Planning", color: "var(--color-yellow)" },
    { name: "Construction", color: "var(--color-cyan)" },
    { name: "Operational", color: "var(--color-primary)" },
    { name: "Decommissioned", color: "var(--color-neutral-500)" },
];
const number = new Intl.NumberFormat("id-ID", { maximumFractionDigits: 1 });
let tooltipId = 0;

function prepareData(counts) {
    const data = statuses.map((status) => {
        const count = Number(counts[status.name] ?? 0);
        return {
            ...status,
            count: Number.isFinite(count) ? Math.max(0, count) : 0,
        };
    });
    const total = d3.sum(data, (status) => status.count);

    return {
        total,
        data: data.map((status) => ({
            ...status,
            percent: total ? (status.count / total) * 100 : 0,
        })),
    };
}

function describe(status) {
    return `${status.name}: ${number.format(status.count)} pusat data (${number.format(status.percent)}%)`;
}

function createChart(card) {
    const root = d3.select(card.querySelector("[data-status-chart]"));
    const tooltip = root
        .select("[data-status-tooltip]")
        .attr("id", `status-tooltip-${++tooltipId}`);
    const svg = root
        .select("[data-status-canvas]")
        .selectAll("svg")
        .data([null])
        .join("svg")
        .attr("viewBox", "-80 -80 160 160")
        .attr("class", "block size-full")
        .attr("role", "group")
        .attr("aria-label", "Distribusi status pusat data");
    const arc = d3.arc().innerRadius(48).outerRadius(68);
    let signature;

    function hide() {
        tooltip.classed("hidden", true);
    }

    function show(event, slice) {
        hide();
        tooltip.text(describe(slice.data)).classed("hidden", false);
    }

    function render() {
        const source = card.querySelector("[data-status-counts]").textContent;
        if (source === signature) return;
        signature = source;
        const { data, total } = prepareData(JSON.parse(source));
        hide();
        root.select("[data-status-empty]").classed("hidden", total !== 0);

        svg.selectAll("circle")
            .data(total ? [] : [null])
            .join("circle")
            .attr("r", 58)
            .attr("fill", "none")
            .attr("stroke", "var(--color-neutral-100)")
            .attr("stroke-width", 20);
        const slices = svg
            .selectAll("path")
            .data(
                d3
                    .pie()
                    .sort(null)
                    .value((status) => status.count)(data)
                    .filter((slice) => slice.value > 0),
                (slice) => slice.data.name,
            )
            .join("path")
            .attr("d", arc)
            .attr("fill", (slice) => slice.data.color)
            .attr("stroke", "var(--color-white)")
            .attr("stroke-width", 1)
            .attr("tabindex", 0)
            .attr("role", "img")
            .attr("aria-label", (slice) => describe(slice.data))
            .attr("aria-describedby", tooltip.attr("id"))
            .attr(
                "class",
                "cursor-default outline-none transition-opacity duration-150 motion-reduce:transition-none hover:opacity-80 focus:opacity-80",
            )
            .on("pointerenter.status", show)
            .on("pointerleave.status", () => {
                const focused = svg.node().querySelector("path:focus");
                if (focused)
                    show(
                        { currentTarget: focused },
                        d3.select(focused).datum(),
                    );
                else hide();
            })
            .on("focus.status", show)
            .on("blur.status", hide)
            .on("click.status", show)
            .on("keydown.status", (event) => {
                if (event.key === "Escape") hide();
            });
        slices.order();

        svg.selectAll("text")
            .data([
                {
                    value: number.format(total),
                    y: 0,
                    className:
                        "fill-neutral-900 text-2xl font-semibold tabular-nums",
                },
                {
                    value: "pusat data",
                    y: 19,
                    className: "fill-neutral-500 text-xs",
                },
            ])
            .join("text")
            .attr("text-anchor", "middle")
            .attr("y", (label) => label.y)
            .attr("class", (label) => label.className)
            .attr("pointer-events", "none")
            .text((label) => label.value);

        const rows = root
            .select("[data-status-legend]")
            .selectAll("li")
            .data(data, (status) => status.name)
            .join((enter) => {
                const row = enter
                    .append("li")
                    .attr(
                        "class",
                        "col-span-2 grid grid-cols-subgrid items-center text-xs",
                    );
                const label = row
                    .append("span")
                    .attr(
                        "class",
                        "flex min-w-0 items-center gap-2 wrap-break-word text-neutral-700",
                    );
                label
                    .append("span")
                    .attr("data-swatch", "")
                    .attr("class", "size-2 shrink-0 rounded-full")
                    .attr("aria-hidden", "true");
                label.append("span").attr("data-label", "");
                const value = row
                    .append("span")
                    .attr(
                        "class",
                        "grid grid-cols-[auto_5ch] items-baseline gap-1 whitespace-nowrap text-right tabular-nums",
                    );
                value
                    .append("span")
                    .attr("data-count", "")
                    .attr("class", "font-medium text-neutral-900");
                value
                    .append("span")
                    .attr("data-percent", "")
                    .attr("class", "font-normal text-neutral-500");
                return row;
            });
        rows.select("[data-swatch]").style(
            "background-color",
            (status) => status.color,
        );
        rows.select("[data-label]").text((status) => status.name);
        rows.select("[data-count]").text((status) =>
            number.format(status.count),
        );
        rows.select("[data-percent]").text(
            (status) => `(${Math.round(status.percent)}%)`,
        );
    }

    return {
        update: render,
        cleanup() {
            svg.selectAll("path").on(".status", null);
            svg.remove();
            tooltip.text("").classed("hidden", true);
        },
    };
}

registerDashboardChart("[data-dashboard-status]", createChart);
