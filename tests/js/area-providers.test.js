import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const blade = readFileSync(new URL('../../resources/views/livewire/pages/areas/providers.blade.php', import.meta.url), 'utf8');
const definition = blade.match(/x-data="([\s\S]*?)"/)[1];
const selectionExpression = blade.match(/@area-provider-added.stop="([^"]+)"/)[1]
    .replaceAll('{{ $selection }}', 'state.ids');

function panel() {
    const ui = new Function(`return (${definition})`)();
    const state = { ids: ['1'], calls: 0, locked: false, focused: false };
    ui.$wire = { addProvider: async () => { state.calls++; return { success: true, id: '3' }; } };
    ui.$dispatch = (name, detail) => {
        assert.equal(name, 'area-provider-added');
        new Function('state', '$event', selectionExpression)(state, { detail });
    };
    ui.$nextTick = callback => callback();
    ui.$refs = { providerToggle: { focus: () => { state.focused = true; } } };
    ui.providerOpen = true;
    ui.providerDraft = { name: 'New', service: 'Internet' };
    return { ui, state, lock: value => { state.locked = value; } };
}

test('new provider is appended without losing existing selections and closes inline form', async () => {
    const { ui, state, lock } = panel();
    await ui.saveProvider(lock);
    assert.deepEqual(state.ids, ['1', '3']);
    assert.equal(ui.providerOpen, false);
    assert.deepEqual(ui.providerDraft, { name: '', service: '' });
    assert.equal(state.locked, false);
    assert.equal(state.focused, true);
});

test('cancel clears the inline draft without requests or selection changes', () => {
    const { ui, state } = panel();
    ui.resetProvider();
    assert.deepEqual(state.ids, ['1']);
    assert.equal(state.calls, 0);
    assert.equal(ui.providerOpen, false);
});

test('validation and network failure retain input and selection for retry', async () => {
    const { ui, state, lock } = panel();
    ui.$wire.addProvider = async () => ({ success: false, errors: { name: ['Duplicate'] } });
    await ui.saveProvider(lock);
    assert.deepEqual(ui.providerErrors, { name: ['Duplicate'] });
    assert.equal(ui.providerDraft.name, 'New');
    assert.equal(ui.providerOpen, true);
    assert.deepEqual(state.ids, ['1']);
    ui.$wire.addProvider = async () => { throw new Error('Offline'); };
    await ui.saveProvider(lock);
    assert.ok(ui.providerMessage);
    assert.equal(state.locked, false);
});

test('pending addition blocks duplicate requests and late responses after removal', async () => {
    const { ui, state, lock } = panel();
    let resolve;
    ui.$wire.addProvider = () => { state.calls++; return new Promise(done => { resolve = done; }); };
    const pending = ui.saveProvider(lock);
    assert.equal(state.locked, true);
    await ui.saveProvider(lock);
    assert.equal(state.calls, 1);
    ui.destroy();
    resolve({ success: true, id: '3' });
    await pending;
    assert.deepEqual(state.ids, ['1']);
    assert.equal(state.locked, false);
});
