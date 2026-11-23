import assert from 'node:assert/strict';
import test from 'node:test';

globalThis.document = { addEventListener() {} };
const { toastNotifications } = await import('../../resources/js/components/toast.js');

function setup(t) {
    const timers = new Map();
    let id = 0;
    t.mock.method(globalThis, 'setTimeout', (callback, delay) => {
        timers.set(++id, { callback, delay });
        return id;
    });
    t.mock.method(globalThis, 'clearTimeout', (timer) => timers.delete(timer));
    const state = toastNotifications();
    state.$nextTick = (callback) => callback();
    const run = (timer) => {
        const entry = timers.get(timer);
        timers.delete(timer);
        entry.callback();
    };
    return { state, timers, run };
}

test('Success expires after 3s, leaves for 200ms, then queued error receives its full 5s', (t) => {
    const { state, timers, run } = setup(t);
    state.enqueue({ type: 'success', message: 'Saved' });
    state.enqueue({ type: 'error', message: 'Failed' });
    assert.equal(state.current.message, 'Saved');
    assert.equal(timers.get(state.timer).delay, 3000);
    run(state.timer);
    assert.equal(state.visible, false);
    assert.equal(state.current.message, 'Saved');
    assert.equal(timers.get(state.exitTimer).delay, 200);
    run(state.exitTimer);
    assert.equal(state.current.message, 'Failed');
    assert.equal(state.visible, true);
    assert.equal(timers.get(state.timer).delay, 5000);
    run(state.timer);
    run(state.exitTimer);
    assert.equal(state.current, null);
    assert.equal(timers.size, 0);
});

test('Manual close cancels auto-dismiss and repeated clicks do not skip queued notifications', (t) => {
    const { state, timers, run } = setup(t);
    state.enqueue({ type: 'success', message: 'First' });
    state.enqueue({ type: 'success', message: 'Second' });
    state.dismiss();
    state.dismiss();
    assert.equal(timers.size, 1);
    run(state.exitTimer);
    assert.equal(state.current.message, 'Second');
    assert.equal(timers.get(state.timer).delay, 3000);
});

test('Navigation and removal clear timers and queued notifications; remount starts empty', (t) => {
    const { state, timers } = setup(t);
    state.enqueue({ type: 'success', message: 'Old page' });
    state.enqueue({ type: 'error', message: 'Queued' });
    state.clear();
    assert.equal(state.visible, false);
    assert.equal(state.current, null);
    assert.deepEqual(state.queue, []);
    assert.equal(timers.size, 0);
    state.enqueue({ type: 'success', message: 'Leaving' });
    state.dismiss();
    state.destroy();
    assert.equal(timers.size, 0);
    assert.equal(toastNotifications().current, null);
});
