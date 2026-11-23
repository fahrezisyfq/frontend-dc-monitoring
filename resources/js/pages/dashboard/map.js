import * as d3 from "d3";

const zoneByCode = {
    11: "WIB",
    12: "WIB",
    13: "WIB",
    14: "WIB",
    15: "WIB",
    16: "WIB",
    17: "WIB",
    18: "WIB",
    19: "WIB",
    21: "WIB",
    31: "WIB",
    32: "WIB",
    33: "WIB",
    34: "WIB",
    35: "WIB",
    36: "WIB",
    61: "WIB",
    62: "WIB",
    51: "WITA",
    52: "WITA",
    53: "WITA",
    63: "WITA",
    64: "WITA",
    65: "WITA",
    71: "WITA",
    72: "WITA",
    73: "WITA",
    74: "WITA",
    75: "WITA",
    76: "WITA",
    81: "WIT",
    82: "WIT",
    91: "WIT",
    92: "WIT",
    93: "WIT",
    94: "WIT",
    95: "WIT",
    96: "WIT",
};
const zoneColors = { WIB: "#7FB8DC", WITA: "#79C6B2", WIT: "#DDBD62" };
const instances = new Map();
let geography;
let navigationVersion = 0;

async function getGeography() {
    geography ??= fetch("/data/indonesia-provinces.geojson").then(
        (response) => {
            if (!response.ok) throw new Error("GeoJSON tidak dapat dimuat");
            return response.json();
        },
    );
    return geography;
}

function countCentersByZone(centers, geojson) {
    const counts = { WIB: 0, WITA: 0, WIT: 0 };
    for (const center of centers) {
        const provinceName = center.city.split(", ").at(-1);
        const province = geojson.features.find(
            (feature) =>
                feature.properties.name.toLowerCase() ===
                    provinceName.toLowerCase() ||
                (provinceName === "DKI Jakarta" &&
                    feature.properties.code === "31"),
        );
        if (!province)
            throw new Error(`Provinsi tidak ditemukan: ${provinceName}`);
        counts[zoneByCode[province.properties.code]]++;
    }

    return counts;
}

function createMap(card, geojson) {
    const unknown = geojson.features.filter(
        (feature) => !zoneByCode[feature.properties.code],
    );
    if (geojson.features.length !== 38 || unknown.length) {
        throw new Error(
            `Klasifikasi provinsi tidak lengkap: ${unknown.map((feature) => feature.properties.name).join(", ")}`,
        );
    }

    const centers = JSON.parse(
        card.querySelector("[data-map-centers]").textContent,
    );
    const valid = centers.filter(
        (center) =>
            Number.isFinite(Number(center.latitude)) &&
            Number.isFinite(Number(center.longitude)),
    );
    const canvas = card.querySelector("[data-map-canvas]");
    const svg = d3.select(card.querySelector("svg"));
    const tooltip = document.createElement("div");
    tooltip.className =
        "fixed z-50 hidden max-w-64 rounded-lg border border-neutral-300 bg-white p-3 text-sm text-neutral-900 shadow-lg";
    tooltip.setAttribute("role", "tooltip");
    document.body.append(tooltip);
    let active;

    function hide() {
        active = undefined;
        tooltip.classList.add("hidden");
    }

    function show(event, center) {
        active = center;
        tooltip.replaceChildren();
        const title = document.createElement("strong");
        title.textContent = center.name;
        const detail = document.createElement("div");
        detail.textContent = `${center.city} · ${center.status} · ${center.load} MW`;
        tooltip.append(title, detail);
        tooltip.classList.remove("hidden");
        const bounds = event.currentTarget.getBoundingClientRect();
        const box = tooltip.getBoundingClientRect();
        tooltip.style.left = `${Math.max(8, Math.min(window.innerWidth - box.width - 8, bounds.left + bounds.width / 2 - box.width / 2))}px`;
        tooltip.style.top = `${Math.max(8, Math.min(window.innerHeight - box.height - 8, bounds.top - box.height - 8))}px`;
    }

    function draw() {
        const width = Math.max(canvas.clientWidth, 240);
        const height = Math.max(150, Math.round(width * 0.43));
        svg.attr("viewBox", `0 0 ${width} ${height}`);
        const projection = d3.geoMercator().fitExtent(
            [
                [12, 12],
                [width - 12, height - 12],
            ],
            geojson,
        );
        const path = d3.geoPath(projection);
        svg.selectAll("*").remove();
        svg.selectAll("path")
            .data(geojson.features)
            .join("path")
            .attr("d", path)
            .attr(
                "fill",
                (feature) => zoneColors[zoneByCode[feature.properties.code]],
            )
            .attr("stroke", "#fff")
            .attr("stroke-width", 0.7);
        const markers = svg
            .selectAll("g[data-marker]")
            .data(valid, (center) => center.name)
            .join("g")
            .attr("data-marker", "")
            .attr(
                "transform",
                (center) =>
                    `translate(${projection([Number(center.longitude), Number(center.latitude)])})`,
            )
            .attr("tabindex", 0)
            .attr("role", "button")
            .attr(
                "aria-label",
                (center) =>
                    `${center.name}, ${center.city}, ${center.status}, ${center.load} MW`,
            );
        markers.append("circle").attr("r", 10).attr("fill", "transparent");
        markers
            .append("circle")
            .attr("r", 4)
            .attr("fill", "#00ACE0")
            .attr("stroke", "#fff")
            .attr("stroke-width", 1.5)
            .style("pointer-events", "none");
        markers
            .on("pointerenter", (event, center) => {
                if (event.pointerType === "mouse") show(event, center);
            })
            .on("pointerleave", (event) => {
                if (event.pointerType === "mouse") hide();
            })
            .on("focus", show)
            .on("blur", hide)
            .on("click", (event, center) => {
                event.stopPropagation();
                active === center && event.pointerType !== "mouse"
                    ? hide()
                    : show(event, center);
            })
            .on("keydown", (event, center) => {
                if (event.key === "Enter" || event.key === " ") {
                    event.preventDefault();
                    show(event, center);
                }
            });
    }

    const counts = countCentersByZone(valid, geojson);
    card.querySelectorAll("[data-zone-count]").forEach((element) => {
        element.textContent = counts[element.dataset.zoneCount];
    });
    draw();
    const observer = new ResizeObserver(draw);
    observer.observe(canvas);
    const close = (event) => {
        if (!card.contains(event.target)) hide();
    };
    const escape = (event) => {
        if (event.key === "Escape") hide();
    };
    document.addEventListener("click", close);
    document.addEventListener("keydown", escape);
    return () => {
        observer.disconnect();
        document.removeEventListener("click", close);
        document.removeEventListener("keydown", escape);
        tooltip.remove();
    };
}

async function initializeMaps() {
    const version = navigationVersion;
    for (const [card, cleanup] of instances) {
        if (!card.isConnected) {
            cleanup();
            instances.delete(card);
        }
    }
    const cards = document.querySelectorAll("[data-dashboard-map]");
    if (!cards.length) return;
    try {
        const geojson = await getGeography();
        if (version !== navigationVersion) return;
        cards.forEach((card) => {
            if (!instances.has(card) && card.isConnected)
                instances.set(card, createMap(card, geojson));
        });
    } catch (error) {
        if (version !== navigationVersion) return;
        console.error(error);
        cards.forEach((card) => {
            card.querySelector("[data-map-canvas]").textContent =
                "Peta tidak dapat dimuat.";
        });
    }
}

document.addEventListener("DOMContentLoaded", initializeMaps);
document.addEventListener("livewire:navigated", initializeMaps);
document.addEventListener("livewire:navigating", () => {
    navigationVersion++;
    instances.forEach((cleanup) => cleanup());
    instances.clear();
});
document.addEventListener("livewire:init", () =>
    Livewire.hook("morph.updated", initializeMaps),
);
