import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import test from 'node:test';
import vm from 'node:vm';

test('Map navigation releases tooltips and ignores pending initialization from the previous page', async () => {
    const events = new Map();
    const card = { isConnected: true };
    let resolveGeography;
    const geography = new Promise((resolve) => { resolveGeography = resolve; });
    let created = 0;
    let cleaned = 0;
    const context = vm.createContext({
        document: {
            addEventListener(name, callback) {
                assert.ok(!events.has(name), `Duplicate listener: ${name}`);
                events.set(name, callback);
            },
            querySelectorAll: () => [card],
        },
        fetch: () => geography,
        console,
        createTestMap: () => {
            created++;
            return () => { cleaned++; };
        },
    });
    const source = await readFile(new URL('../../resources/js/pages/dashboard/map.js', import.meta.url), 'utf8');
    vm.runInContext(source.replace("import * as d3 from 'd3';", ''), context);
    vm.runInContext('createMap = createTestMap', context);

    const pending = events.get('livewire:navigated')();
    events.get('livewire:navigating')();
    resolveGeography({ ok: true, json: async () => ({ features: [] }) });
    await pending;
    assert.equal(created, 0);

    await events.get('livewire:navigated')();
    await events.get('livewire:navigated')();
    assert.equal(created, 1);

    events.get('livewire:navigating')();
    assert.equal(cleaned, 1);
    events.get('livewire:navigating')();
    assert.equal(cleaned, 1);

    await events.get('livewire:navigated')();
    assert.equal(created, 2);
});
