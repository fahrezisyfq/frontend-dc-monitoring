import test from 'node:test';
import assert from 'node:assert/strict';

globalThis.document = { addEventListener() {} };
const { dataCenterCreate } = await import('../../../resources/js/pages/data-centers/tambah-data-center/create.js');

function page() {
    const state = dataCenterCreate({
        form: { name: 'DC Uji', company: '', province: '', city: '', district: '', services: [], it_energy: '', total_energy: '', water: '' },
        companies: { 1: { name: 'Company', nib: '0000000000001' } }, areas: { 1: 'Area' },
        steps: { 1: 'Identity', 2: 'Capacity', 3: 'Energy', 4: 'Certificates', 5: 'Confirmation' },
        mainFields: { name: ['Nama', 'text'] }, capacityFields: { reporting_year: ['Tahun', 'year'], land_area: ['Luas', 'number'] },
        energyFields: Object.fromEntries(['it_energy', 'total_energy', 'water'].map(key => [key, [key, 'number']])),
        certificateFields: { name: ['Nama', 'text'], issuer: ['Penerbit', 'text'], issued_at: ['Terbit', 'date'], expires_at: ['Kedaluwarsa', 'date'] },
        regions: { Bali: { timezone: 'WITA', cities: { Denpasar: { Selatan: 'DEMO' } } } },
        statuses: ['Operational'], services: ['Rack'], categories: ['Nasional'],
    });
    state.watchers = {};
    state.$watch = (key, fn) => { state.watchers[key] = fn; };
    state.$nextTick = fn => fn();
    state.$refs = { stepTitle: { focus() {} } };
    state.$root = { querySelector: () => null };
    state.$dispatch = () => {};
    state.calls = [];
    state.$wire = { submit: async payload => { state.calls.push(payload); return { success: true }; } };
    state.init();
    return state;
}

test('navigation, company and certificate edits stay local and retain the draft', () => {
    const s = page();
    s.next(); s.form.land_area = '2.5'; s.goToStep(4); s.addCertificate();
    s.certificates[0].name = 'ISO'; s.addCertificate(); s.removeCertificate(0); s.addCertificate();
    assert.deepEqual(Object.keys(s.certificates), ['1', '2']);
    s.backTo(1);
    s.newCompany = { name: 'New', nib: '1234567890123' }; s.addCompany();
    assert.equal(s.form.company, '2');
    assert.equal(s.newCompanies[2].name, 'New');
    assert.equal(s.form.land_area, '2.5');
    assert.equal(s.calls.length, 0);
    s.newCompany = { name: 'Duplicate', nib: '1234567890123' }; s.addCompany();
    assert.ok(s.errorFor('newCompany.nib'));
});

test('dependent locations reset locally and expose derived PUM/timezone', () => {
    const s = page();
    Object.assign(s.form, { province: 'Bali', city: 'Denpasar', district: 'Selatan' });
    assert.equal(s.pum, 'DEMO'); assert.equal(s.timezone, 'WITA');
    s.watchers['form.province']();
    assert.equal(s.form.city, ''); assert.equal(s.form.district, ''); assert.equal(s.pum, '');
});

test('ratios handle decimals, zero, invalid and inconsistent totals', () => {
    const s = page();
    Object.assign(s.form, { it_energy: '3', total_energy: '4', water: '0' });
    assert.equal(s.pue, '1.33'); assert.equal(s.wue, '0');
    s.form.total_energy = '2'; assert.equal(s.pue, ''); assert.equal(s.validateStep(3), false);
    s.form.it_energy = '0'; assert.equal(s.wue, '');
    s.form.it_energy = '1'; s.form.water = '-1'; assert.equal(s.wue, '');
    s.form.water = '1e999'; assert.equal(s.validateStep(3), false);
});

test('final confirmation cannot bypass frontend validation', async () => {
    const s = page();
    s.step = 5; s.form.reporting_year = '26';
    await s.submit(); assert.equal(s.step, 2); assert.equal(s.calls.length, 0);
    s.form.reporting_year = '2026'; s.addCertificate(); s.certificates[0].name = 'ISO'; s.step = 5;
    await s.submit(); assert.equal(s.step, 4); assert.ok(s.errorFor('certificates.0.issuer'));
    s.certificates[0].issuer = 'Issuer';
    Object.assign(s.certificates[0], { issued_at: '2026-02-02', expires_at: '2026-01-01' });
    assert.equal(s.validateStep(4), false);
});

test('submit sends one snapshot and blocks repeated submissions while busy', async () => {
    const s = page(); let resolve;
    s.$wire.submit = payload => { s.calls.push(payload); return new Promise(done => { resolve = done; }); };
    s.step = 5;
    const pending = s.submit(); await s.submit(); s.goToStep(1); s.addCertificate();
    assert.equal(s.calls.length, 1); assert.equal(s.step, 5); assert.deepEqual(s.certificates, {});
    s.form.name = 'Changed'; assert.equal(s.calls[0].form.name, 'DC Uji');
    resolve({ success: true }); await pending; assert.equal(s.busy, false);
});

test('server validation and network failures preserve draft and permit retry', async () => {
    const s = page(); s.step = 5;
    s.$wire.submit = async () => ({ success: false, step: 3, errors: { 'form.water': ['Server error'] } });
    await s.submit(); assert.equal(s.step, 3); assert.equal(s.errorFor('form.water'), 'Server error');
    assert.equal(s.form.name, 'DC Uji'); assert.equal(s.busy, false);
    s.step = 5; s.$wire.submit = async () => { throw new Error('offline'); };
    await s.submit(); assert.ok(s.message); assert.equal(s.busy, false);
});
