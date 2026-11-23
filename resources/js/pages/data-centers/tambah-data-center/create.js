const clone = value => JSON.parse(JSON.stringify(value));
const blank = value => value === '' || value === null || value === undefined;
const numeric = value => !blank(value) && /^[-+]?(?:\d+\.?\d*|\.\d+)(?:e[-+]?\d+)?$/i.test(String(value)) && Number.isFinite(Number(value));

export function dataCenterCreate(config) {
    return {
        form: clone(config.form),
        companies: clone(config.companies),
        areas: config.areas,
        steps: config.steps,
        certificates: {},
        newCompanies: {},
        newCompany: { name: '', nib: '' },
        nextCertificateId: 0,
        step: 1,
        visitedSteps: [1],
        errors: {},
        busy: false,
        disposed: false,
        message: '',

        init() {
            this.$watch('form.province', () => { this.form.city = ''; this.form.district = ''; });
            this.$watch('form.city', () => { this.form.district = ''; });
            for (const field of Object.keys(config.energyFields)) {
                this.$watch(`form.${field}`, () => this.validateStep(3));
            }
        },

        get cities() { return config.regions[this.form.province]?.cities ?? {}; },
        get companyOptions() { return Object.fromEntries(Object.entries(this.companies).map(([id, company]) => [id, `${company.name} — NIB ${company.nib}`])); },
        get provinceOptions() { return Object.fromEntries(Object.keys(config.regions).map(value => [value, value])); },
        get cityOptions() { return Object.fromEntries(Object.keys(this.cities).map(value => [value, value])); },
        get districtOptions() { return Object.fromEntries(Object.keys(this.districts).map(value => [value, value])); },
        get districts() { return this.cities[this.form.city] ?? {}; },
        get pum() { return this.districts[this.form.district] ?? ''; },
        get timezone() { return this.pum ? config.regions[this.form.province]?.timezone ?? '' : ''; },
        get pue() { return this.ratio('total_energy'); },
        get wue() { return this.ratio('water'); },
        get filledCertificates() {
            return Object.values(this.certificates).filter(row => Object.values(row).some(value => String(value ?? '').trim() !== ''));
        },
        get stepDescription() {
            return {
                1: 'Field bertanda * wajib diisi. Field lainnya dapat dikosongkan untuk simulasi.',
                2: 'Masukkan angka nonnegatif; gunakan titik untuk desimal dan tahun 4 digit.',
                3: 'Masukkan konsumsi tahunan energi dan air untuk periode pelaporan yang sama. PUE dan WUE dihitung otomatis.',
                4: 'Sertifikasi opsional. Jika diisi, nama sertifikasi dan lembaga penerbit wajib dilengkapi.',
                5: 'Periksa kembali seluruh data. Gunakan Ubah untuk memperbaiki isian.',
            }[this.step];
        },

        ratio(key) {
            const it = this.form.it_energy, value = this.form[key];
            if (!numeric(it) || !numeric(value) || Number(it) <= 0 || Number(value) < 0
                || (key === 'total_energy' && Number(value) < Number(it))) return '';
            const ratio = Number(value) / Number(it);
            return Number.isFinite(ratio) ? String(Math.round((ratio + Number.EPSILON) * 100) / 100) : '';
        },
        display(value, unit = '') { return blank(value) ? '—' : String(value) + (unit ? ` ${unit}` : ''); },
        errorFor(path) {
            const key = Object.keys(this.errors).find(key => key === path || key.startsWith(`${path}.`));
            return key ? this.errors[key]?.[0] ?? '' : '';
        },
        fieldId(path) { return `dc-${path.replaceAll('.', '-')}`; },
        connectField(element, path) {
            // Certificate rows are cloned by Alpine, so connect each label to its unique input.
            const label = element.querySelector('label');
            if (label) label.htmlFor = this.fieldId(path);
        },
        focusStep() { this.$nextTick(() => { if (!this.disposed) this.$refs.stepTitle.focus({ preventScroll: true }); }); },
        focusError() {
            this.$nextTick(() => {
                if (this.disposed) return;
                const panel = this.$root.querySelector(`[data-step="${this.step}"]`);
                const input = panel?.querySelector('[aria-invalid="true"]');
                if (input) input.focus(); else this.$refs.stepTitle.focus({ preventScroll: true });
            });
        },
        goToStep(step) {
            if (this.busy || !this.steps[step] || step === this.step) return;
            this.step = Number(step);
            if (!this.visitedSteps.includes(this.step)) this.visitedSteps.push(this.step);
            this.errors = {};
            this.message = '';
            this.focusStep();
        },
        backTo(step) { if (step < this.step) this.goToStep(step); },
        next() {
            if (this.busy) return;
            if (!this.validateStep(this.step)) { this.focusError(); return; }
            this.goToStep(this.step + 1);
        },
        addCompany() {
            if (this.busy) return;
            const company = { name: this.newCompany.name.trim(), nib: this.newCompany.nib.trim() };
            delete this.errors['newCompany.name']; delete this.errors['newCompany.nib'];
            if (!company.name) this.errors['newCompany.name'] = ['Nama Perusahaan wajib diisi.'];
            else if (company.name.length > 255) this.errors['newCompany.name'] = ['Nama Perusahaan terlalu panjang (maksimal 255 karakter).'];
            if (!/^\d{13}$/.test(company.nib)) this.errors['newCompany.nib'] = ['NIB harus terdiri dari 13 digit angka.'];
            else if (Object.values(this.companies).some(row => row.nib === company.nib)) this.errors['newCompany.nib'] = ['NIB sudah tersedia.'];
            if (this.errorFor('newCompany')) return;
            const id = Math.max(0, ...Object.keys(this.companies).map(Number)) + 1;
            this.companies[id] = company;
            this.newCompanies[id] = company;
            this.form.company = String(id);
            this.newCompany = { name: '', nib: '' };
            this.$dispatch('dc-company-added');
        },
        addCertificate() {
            if (this.busy) return;
            this.certificates[this.nextCertificateId++] = Object.fromEntries(Object.keys(config.certificateFields).map(key => [key, '']));
        },
        removeCertificate(index) {
            if (this.busy) return;
            delete this.certificates[index];
            this.errors = {};
        },

        validateStep(step) {
            const errors = {};
            const add = (key, message) => { errors[key] = [message]; };
            const fields = { 1: config.mainFields, 2: config.capacityFields, 3: config.energyFields }[step] ?? {};
            for (const [key, [label, type]] of Object.entries(fields)) {
                const value = this.form[key];
                if (type === 'text') {
                    if (!String(value ?? '').trim()) add(`form.${key}`, `${label} wajib diisi.`);
                    else if (String(value).length > 255) add(`form.${key}`, `${label} terlalu panjang (maksimal 255 karakter).`);
                } else if (!blank(value)) {
                    if (type === 'year' && !/^[1-9]\d{3}$/.test(String(value))) add(`form.${key}`, `${label} harus berupa tahun 4 digit.`);
                    if (type === 'number') {
                        if (!numeric(value)) add(`form.${key}`, `${label} harus berupa angka.`);
                        else if (Number(value) < 0) add(`form.${key}`, `${label} tidak boleh negatif.`);
                    }
                }
            }
            if (step === 1) {
                const options = {
                    company: Object.keys(this.companies), status: config.statuses,
                    province: Object.keys(config.regions), city: Object.keys(this.cities), district: Object.keys(this.districts),
                    ai_ready: ['Ya', 'Tidak'], area: Object.keys(this.areas),
                };
                for (const [key, values] of Object.entries(options)) {
                    if (!blank(this.form[key]) && !values.map(String).includes(String(this.form[key]))) add(`form.${key}`, 'Pilih salah satu opsi yang tersedia.');
                }
                if (!Array.isArray(this.form.services) || this.form.services.some(value => !config.services.includes(value))) {
                    add('form.services', 'Pilih salah satu opsi yang tersedia.');
                }
            }
            if (step === 3 && numeric(this.form.it_energy) && numeric(this.form.total_energy)
                && Number(this.form.it_energy) >= 0 && Number(this.form.total_energy) < Number(this.form.it_energy)) {
                add('form.total_energy', 'Konsumsi energi listrik total tidak boleh lebih kecil daripada konsumsi energi listrik IT.');
            }
            if (step === 4) {
                const validDate = value => /^\d{4}-\d{2}-\d{2}$/.test(value) && !Number.isNaN(Date.parse(value)) && new Date(value).toISOString().slice(0, 10) === value;
                for (const [index, row] of Object.entries(this.certificates)) {
                    if (!Object.values(row).some(value => String(value ?? '').trim())) continue;
                    for (const [key, [label, type]] of Object.entries(config.certificateFields)) {
                        const value = row[key], path = `certificates.${index}.${key}`;
                        if (['name', 'issuer'].includes(key) && !String(value ?? '').trim()) add(path, `${label} wajib diisi.`);
                        else if (!blank(value)) {
                            if (type === 'date' && !validDate(value)) add(path, 'Gunakan tanggal yang valid dengan format YYYY-MM-DD.');
                            else if (type === 'select' && !config.categories.includes(value)) add(path, 'Pilih salah satu opsi yang tersedia.');
                            else if (String(value).length > (type === 'textarea' ? 2000 : 255)) add(path, `${label} terlalu panjang (maksimal ${type === 'textarea' ? 2000 : 255} karakter).`);
                        }
                    }
                    if (row.issued_at && row.expires_at && validDate(row.issued_at) && validDate(row.expires_at) && row.expires_at < row.issued_at) {
                        add(`certificates.${index}.expires_at`, 'Tanggal kedaluwarsa tidak boleh lebih awal daripada tanggal diterbitkan.');
                    }
                }
            }
            // Live energy checks only replace errors belonging to that section.
            const prefix = step === 4 ? 'certificates.' : null;
            for (const key of Object.keys(this.errors)) {
                if ((prefix && key.startsWith(prefix)) || Object.keys(fields).some(field => key === `form.${field}`)
                    || (step === 1 && key.startsWith('form.') && ![...Object.keys(config.capacityFields), ...Object.keys(config.energyFields)].includes(key.slice(5)))) delete this.errors[key];
            }
            Object.assign(this.errors, errors);
            return Object.keys(errors).length === 0;
        },

        async submit() {
            if (this.busy || this.disposed) return;
            if (this.step !== 5) { this.next(); return; }
            this.errors = {};
            for (let step = 1; step <= 4; step++) {
                if (!this.validateStep(step)) {
                    this.step = step;
                    if (!this.visitedSteps.includes(step)) this.visitedSteps.push(step);
                    this.focusError();
                    return;
                }
            }
            this.busy = true;
            this.message = '';
            try {
                const result = await this.$wire.submit(clone({ form: this.form, certificates: this.certificates, newCompanies: this.newCompanies }));
                if (this.disposed) return;
                if (result?.success !== true) {
                    this.errors = result?.errors ?? {};
                    this.step = Number(result?.step) || this.step;
                    if (!this.visitedSteps.includes(this.step)) this.visitedSteps.push(this.step);
                    this.message = result?.message ?? (Object.keys(this.errors).length ? '' : 'Konfirmasi belum berhasil. Silakan coba lagi.');
                    this.focusError();
                }
            } catch {
                if (!this.disposed) this.message = 'Proses belum berhasil. Periksa koneksi lalu coba lagi.';
            } finally {
                if (!this.disposed) this.busy = false;
            }
        },
        destroy() { this.disposed = true; },
    };
}

document.addEventListener('alpine:init', () => {
    window.Alpine.data('dataCenterCreate', dataCenterCreate);
});
