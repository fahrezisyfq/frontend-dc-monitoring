import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import test from 'node:test';
import vm from 'node:vm';

test('Dashboard charts update once per event and release instances on removal or navigation', async () => {
    const events = new Map();
    const hooks = new Map();
    const cards = [{ isConnected: true }, { isConnected: true }];
    const created = [0, 0];
    const updated = [0, 0];
    const cleaned = [0, 0];
    const context = vm.createContext({
        document: {
            readyState: 'loading',
            addEventListener(name, callback) {
                assert.ok(!events.has(name), `Duplicate listener: ${name}`);
                events.set(name, callback);
            },
            querySelectorAll(selector) {
                const card = cards[Number(selector)];
                return card.isConnected ? [card] : [];
            },
        },
        Livewire: { hook: (name, callback) => hooks.set(name, callback) },
        queueMicrotask: (callback) => callback(),
    });
    const source = await readFile(new URL('../../resources/js/pages/dashboard/lifecycle.js', import.meta.url), 'utf8');
    vm.runInContext(source.replace('export function', 'function'), context);
    const register = vm.runInContext('registerDashboardChart', context);
    cards.forEach((card, index) => register(String(index), () => {
        created[index]++;
        return { update: () => updated[index]++, cleanup: () => cleaned[index]++ };
    }));

    events.get('DOMContentLoaded')();
    events.get('livewire:init')();
    events.get('livewire:navigated')();
    hooks.get('morphed')();
    assert.deepEqual(created, [1, 1]);
    assert.deepEqual(updated, [3, 3]);

    cards[0].isConnected = false;
    hooks.get('morph.removed')();
    assert.deepEqual(cleaned, [1, 0]);
    events.get('livewire:navigating')();
    assert.deepEqual(cleaned, [1, 1]);

    cards[0].isConnected = true;
    events.get('livewire:navigated')();
    assert.deepEqual(created, [2, 2]);
    assert.deepEqual(updated, [4, 5]);
});
