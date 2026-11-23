import * as d3 from "d3";
import { registerDashboardChart } from "./lifecycle";

const number = new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 });
let tooltipId = 0;

// Preserve the existing Blade chart's year mapping and cumulative series.
export function prepareTrend(counts) {
    return counts.map((value, index) => ({ year: 2021 + index, value }));
}

function roundedBarPath(left, top, barWidth, bottom) {
    const radius = Math.min(4, barWidth / 2, (bottom - top) / 2);
    const path = d3.path();
    path.moveTo(left, bottom);
    path.lineTo(left, top + radius);
    path.quadraticCurveTo(left, top, left + radius, top);
    path.lineTo(left + barWidth - radius, top);
    path.quadraticCurveTo(left + barWidth, top, left + barWidth, top + radius);
    path.lineTo(left + barWidth, bottom);
    path.closePath();
    return path.toString();
}

function createChart(card) {
    const canvas = card.querySelector("[data-trend-canvas]");
    const tooltip = d3
        .select(canvas.querySelector("[data-trend-tooltip]"))
        .attr("id", `trend-tooltip-${++tooltipId}`);
    const svg = d3
        .select(canvas)
        .selectAll("svg")
        .data([null])
        .join("svg")
        .attr("class", "block h-auto w-full")
        .attr("role", "group")
        .attr(
            "aria-label",
            "Perkembangan jumlah pusat data kumulatif per tahun",
        );
    const grid = svg.append("g").attr("aria-hidden", "true");
    const xAxis = svg.append("g").attr("aria-hidden", "true");
    const yAxis = svg.append("g").attr("aria-hidden", "true");
    const bars = svg.append("g");
    const xLabel = svg
        .append("text")
        .text("Tahun")
        .attr("text-anchor", "middle");
    const yLabel = svg
        .append("text")
        .text("Jumlah pusat data (unit)")
        .attr("text-anchor", "middle");
    for (const label of [xLabel, yLabel])
        label.attr("class", "fill-neutral-700 text-xs");
    let series = [];
    let signature;
    let width = 0;

    const hide = () => tooltip.classed("hidden", true);
    const describe = (point) =>
        `${point.year}: ${number.format(point.value)} pusat data`;
    const show = (event, point) =>
        tooltip.text(describe(point)).classed("hidden", false);

    function draw() {
        hide();
        const data = series;
        const known = data.filter((point) => Number.isFinite(point.value));
        const empty = !known.length;
        d3.select(canvas.querySelector("[data-trend-empty]")).classed(
            "hidden",
            !empty,
        );
        svg.classed("hidden", empty);
        if (empty || width <= 0) return;

        const height = 216;
        const margin = { top: 16, right: 12, bottom: 56, left: 48 };
        const right = Math.max(margin.left + 1, width - margin.right);
        const bottom = height - margin.bottom;
        const x = d3
            .scaleBand()
            .domain(data.map((point) => point.year))
            .range([margin.left, right])
            .padding(0.4);
        const barWidth = Math.min(32, x.bandwidth());
        const y = d3
            .scaleLinear()
            .domain([0, d3.max(known, (point) => point.value) || 1])
            .nice(4)
            .range([bottom, margin.top]);
        const ticks = y.ticks(4).filter(Number.isInteger);
        const years = data.map((point) => point.year);
        const rotateLabels = x.step() < 44;
        svg.attr("viewBox", `0 0 ${width} ${height}`);
        xAxis
            .attr("transform", `translate(0,${bottom})`)
            .call(
                d3
                    .axisBottom(x)
                    .tickValues(years)
                    .tickFormat(String)
                    .tickSize(0)
                    .tickPadding(12),
            );
        xAxis
            .selectAll("text")
            .attr("transform", rotateLabels ? "rotate(-45)" : null)
            .attr("text-anchor", rotateLabels ? "end" : "middle")
            .attr("dx", rotateLabels ? "-0.5em" : null)
            .attr("dy", rotateLabels ? "0.5em" : "0.71em");
        yAxis
            .attr("transform", `translate(${margin.left},0)`)
            .call(
                d3
                    .axisLeft(y)
                    .tickValues(ticks)
                    .tickFormat(number.format)
                    .tickSize(0)
                    .tickPadding(10),
            );
        for (const axis of [xAxis, yAxis]) {
            axis.attr("color", "var(--color-neutral-300)")
                .attr("font-family", "inherit")
                .attr("font-size", 12);
            axis.selectAll("text").attr("fill", "var(--color-neutral-700)");
            axis.select(".domain").remove();
        }
        grid.selectAll("line")
            .data(ticks)
            .join("line")
            .attr("x1", margin.left)
            .attr("x2", right)
            .attr("y1", y)
            .attr("y2", y)
            .attr("stroke", "var(--color-neutral-300)")
            .attr("stroke-opacity", 0.5);
        bars.selectAll("path")
            .data(known, (point) => point.year)
            .join("path")
            .attr("d", (point) =>
                roundedBarPath(
                    x(point.year) + (x.bandwidth() - barWidth) / 2,
                    y(point.value),
                    barWidth,
                    bottom,
                ),
            )
            .attr("fill", "var(--color-primary)")
            .attr("tabindex", 0)
            .attr("role", "img")
            .attr("aria-label", describe)
            .attr("aria-describedby", tooltip.attr("id"))
            .attr(
                "class",
                "cursor-default transition-opacity duration-200 ease-out hover:opacity-80 focus:opacity-80 focus:outline-none motion-reduce:transition-none",
            )
            .on("pointerenter.trend", show)
            .on("pointerleave.trend", () => {
                const focused = bars.node().querySelector("path:focus");
                if (focused) show(null, d3.select(focused).datum());
                else hide();
            })
            .on("focus.trend", show)
            .on("blur.trend", hide)
            .on("click.trend", show)
            .on("keydown.trend", (event) => {
                if (event.key === "Escape") hide();
            });
        xLabel.attr("x", (margin.left + right) / 2).attr("y", height - 6);
        yLabel.attr(
            "transform",
            `translate(14,${(margin.top + bottom) / 2}) rotate(-90)`,
        );
    }

    function update() {
        const source = card.querySelector("[data-trend-source]").textContent;
        if (signature === source) return;
        signature = source;
        series = prepareTrend(JSON.parse(source));
        draw();
    }

    const observer = new ResizeObserver(([entry]) => {
        const nextWidth = Math.floor(entry.contentRect.width);
        if (nextWidth !== width) {
            width = nextWidth;
            draw();
        }
    });
    observer.observe(canvas);

    return {
        update,
        cleanup() {
            observer.disconnect();
            bars.selectAll("path").on(".trend", null);
            svg.remove();
            hide();
        },
    };
}

registerDashboardChart("[data-dashboard-trend]", createChart);
