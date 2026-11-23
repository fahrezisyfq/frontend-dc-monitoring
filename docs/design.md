# Design Guidelines — DC Monitoring

> Dokumen ini khusus mengatur **desain antarmuka (UI)**, konsistensi visual, responsivitas, aksesibilitas, dan interaksi antarmuka. Dokumen ini **bukan** tempat mendefinisikan fitur, proses bisnis, hak akses, route, database, atau logika aplikasi. Rincian tersebut mengikuti `prd.md` dan `task.md`.

## 1. Arah desain

- **Gaya:** modern, profesional, minimalis, bersih, dan mengutamakan keterbacaan.
- **Tema utama:** light mode; permukaan putih dengan latar netral terang.
- **Prioritas:** informasi mudah dipindai, hierarki visual jelas, dan elemen konsisten.
- **Pendekatan:** *component-based design* agar setiap halaman menggunakan pola tampilan yang seragam.
- Hindari dekorasi berlebihan, bayangan tebal, gradien mencolok, dan animasi yang tidak membantu pengguna.

## 2. Sistem warna

Warna utama mengikuti referensi palet DJID yang dilampirkan. Kode HEX di bawah mengacu pada **teks kode warna dalam gambar**, bukan hasil perkiraan dari piksel gambar.

### 2.1 Warna primer (referensi)

| Token | HEX | Penggunaan visual |
| --- | --- | --- |
| `primary` | `#006FB0` | Aksi utama, elemen aktif, tautan, fokus visual |
| `cyan` | `#00ACE0` | Aksen pendukung, penanda informasi |
| `yellow` | `#ECBB1D` | Aksen peringatan atau perhatian |
| `red` | `#DE262A` | Aksen kesalahan dan tindakan berisiko |

### 2.2 Warna netral

| Token | HEX | Penggunaan visual |
| --- | --- | --- |
| `neutral-900` | `#181818` | Heading dan teks utama |
| `neutral-700` | `#4E4C4C` | Teks sekunder |
| `neutral-500` | `#979495` | Ikon pendukung dan teks non-esensial |
| `neutral-300` | `#D0CECE` | Border dan pemisah |
| `neutral-100` | `#F1F1F1` | Latar halaman dan hover netral |
| `white` | `#FFFFFF` | Surface, kartu, formulir, dan dialog |

> `#181818` berasal dari teks referensi. Empat nilai abu-abu lainnya merupakan **pendekatan visual dari swatch gambar**, bukan spesifikasi HEX resmi. Warna putih digunakan sebagai warna antarmuka tambahan.

### 2.3 Aturan penggunaan

- Jadikan `primary` satu-satunya warna dominan untuk aksi utama dan navigasi aktif.
- Gunakan `cyan`, `yellow`, dan `red` secara terbatas sesuai konteks, bukan sebagai dekorasi acak.
- Teks kecil di atas `cyan` atau `yellow` menggunakan warna gelap (`neutral-900`), bukan putih.
- Konten utama menggunakan permukaan `white` atau `neutral-100` dengan kontras jelas.
- Jangan membedakan informasi hanya berdasarkan warna; selalu kombinasikan dengan teks atau ikon.
- Kelola warna sebagai **design tokens** terpusat, bukan mengulang nilai HEX di setiap komponen.

## 3. Tipografi

- **Font utama:** `Inter`; fallback: `ui-sans-serif`, `system-ui`, `sans-serif`.
- **Judul halaman:** 24–28 px, semibold (600) atau bold (700).
- **Judul bagian:** 18–20 px, semibold (600).
- **Judul kartu/dialog:** 16–18 px, semibold (600).
- **Teks isi, navigasi, input, dan tabel:** 14–16 px, regular (400) atau medium (500).
- **Keterangan sekunder:** 12–13 px; jangan gunakan untuk informasi utama.
- **Line height:** 1.4–1.6 untuk teks biasa; sesuaikan pada judul.
- Gunakan kapitalisasi kalimat yang konsisten, bukan semua huruf kapital pada label panjang.
- Untuk kolom angka, gunakan *tabular numerals* agar digit sejajar.

## 4. Grid, layout, dan jarak

- Gunakan **grid 12 kolom** bila diperlukan untuk layout kompleks, dengan susunan fleksibel.
- Skala jarak dasar **4 px**, menggunakan nilai konsisten: `4 / 8 / 12 / 16 / 24 / 32 / 48 px`.
- **Padding konten:** desktop 24–32 px, tablet 20–24 px, mobile 16 px.
- **Header/topbar:** tinggi acuan 64 px.
- **Sidebar desktop:** lebar acuan 248–264 px; ruang konten menyesuaikan secara fleksibel.
- **Kartu:** padding 16–24 px dan jarak antarkartu 16–24 px.
- **Border:** tipis (1 px) dengan warna netral.
- **Radius:** tombol/input 8 px; kartu/dialog 10–12 px; badge boleh berbentuk pill.
- **Shadow:** ringan dan hanya jika diperlukan untuk pemisahan lapisan.
- Utamakan whitespace daripada menambahkan garis atau kotak di setiap area.

## 5. Navigasi dan kerangka antarmuka

### Sidebar

- Letakkan identitas aplikasi di bagian atas.
- Gunakan kombinasi **ikon Lucide + label teks** untuk item navigasi.
- Hierarki grup dan item harus jelas melalui jarak, ukuran, serta bobot teks.
- Item aktif memiliki penanda tegas: warna `primary`, latar aktif, atau indikator sisi.
- Item tidak aktif menggunakan warna netral; hover memakai latar netral ringan.
- Jaga tinggi item konsisten, sekitar 40–44 px.
- Jika sidebar dapat diciutkan, sediakan tooltip saat label tidak terlihat.
- Pada layar kecil, tampilkan sebagai **drawer** dengan backdrop.

### Topbar dan area konten

- Tinggi, alignment, dan jarak elemen topbar harus konsisten.
- Setiap tampilan memiliki **satu judul utama** dengan penempatan seragam.
- Jaga lebar konten agar nyaman dibaca; data yang lebar boleh memakai seluruh area tersedia.
- Gunakan breadcrumb hanya untuk membantu orientasi pada hierarki tampilan yang dalam.

## 6. Komponen visual

### Tombol

- **Primary:** latar `#006FB0`, teks putih.
- **Secondary:** latar putih, border netral, teks gelap.
- **Ghost:** latar transparan, hover netral.
- **Danger:** latar `#DE262A`, teks putih.
- Tinggi umum 40–44 px; ikon dan teks memiliki jarak 8 px.
- Semua varian memiliki tampilan konsisten untuk `default`, `hover`, `focus`, `disabled`, dan `loading`.

### Input, select, dan textarea

- Selalu tampilkan **label di luar field**; placeholder hanya sebagai contoh.
- Tinggi input satu baris sekitar 40–44 px.
- Gunakan border netral, background putih, dan focus ring biru yang terlihat jelas.
- Pesan bantuan, kesalahan, dan batasan input ditempatkan dekat field.
- Gunakan jarak vertikal antarfield 16–20 px.
- Kelompokkan field yang berhubungan dengan heading pendek bila formulir panjang.

### Tabel

- Header tabel jelas, latar netral lembut, dan pemisah baris tipis.
- Teks rata kiri; nilai numerik rata kanan.
- Tinggi baris nyaman dibaca, dengan padding horizontal 12–16 px.
- Elemen aksi baris, jika ada, harus memakai pola ikon/label yang konsisten.
- Gunakan *truncation* hanya bila informasi lengkap tetap dapat diakses.
- Pada layar kecil, prioritaskan scroll horizontal daripada mengecilkan teks berlebihan.
- Desain state **kosong, memuat, dan gagal** dengan struktur visual seragam.

### Kartu dan panel

- Gunakan permukaan putih, border tipis, radius seragam, dan padding 16–24 px.
- Pisahkan judul, nilai utama, dan keterangan secara visual.
- Jangan memberikan warna latar kuat pada semua kartu sekaligus.

### Dialog, popover, dropdown, tooltip, dan tab

- Gunakan ukuran dan posisi konsisten; dialog memiliki overlay yang tidak terlalu gelap.
- Sediakan ruang yang jelas antara judul, isi, serta area tombol dialog.
- Dropdown dan popover memiliki elevasi ringan dan alignment terhadap pemicu.
- Tooltip hanya berisi keterangan singkat dan tidak menjadi satu-satunya sumber informasi penting.
- Tab aktif memiliki penanda yang mudah dikenali selain perubahan warna teks.

### Badge, feedback, dan skeleton

- Badge berukuran ringkas, memakai teks yang mudah dibaca dan warna semantik yang tepat.
- Notifikasi menggunakan ikon, judul atau pesan ringkas, dan jarak antarelemen konsisten.
- Skeleton mengikuti ukuran konten yang akan muncul agar layout tidak bergeser.
- Empty state memuat ikon/ilustrasi sederhana, teks singkat, dan whitespace cukup.

## 7. Ikon dan aset visual

- Gunakan **Lucide Icons** sebagai pustaka ikon utama.
- Ukuran acuan: 16 px untuk ikon kecil, 18–20 px untuk kontrol dan navigasi, 24 px untuk ikon yang lebih menonjol.
- Pertahankan gaya outline dan ketebalan stroke yang konsisten.
- Hindari emoji sebagai pengganti ikon UI.
- Ikon yang berdiri sendiri harus memiliki nama aksesibel atau tooltip bila maknanya tidak jelas.
- Gambar, logo, atau ilustrasi harus proporsional, resolusinya memadai, dan tidak memenuhi ruang secara berlebihan.

## 8. Responsivitas

- **Mobile (< 640 px):** konten satu kolom, padding 16 px, sidebar berupa drawer, kontrol nyaman disentuh.
- **Tablet (640–1023 px):** grid dan jarak disesuaikan, navigasi tidak boleh menutupi konten.
- **Desktop (≥ 1024 px):** sidebar tetap terlihat, ruang kerja lebih luas, konten dapat menggunakan beberapa kolom.
- Hindari overflow horizontal pada halaman secara keseluruhan; jika dibutuhkan, batasi scroll ke kontainer data.
- Jangan memperkecil teks di bawah ukuran nyaman hanya untuk memaksakan seluruh komponen masuk layar.
- Target sentuh untuk kontrol penting idealnya minimal 44 × 44 px.

## 9. Animasi dan interaksi antarmuka

- Gunakan interaksi seperlunya untuk memperjelas keadaan UI, bukan sebagai efek dekoratif.
- Durasi transisi umum: **150–250 ms**, dengan easing halus.
- Contoh interaksi: buka/tutup sidebar, dropdown, modal, accordion, tab visual, tooltip, dan pergantian state kontrol.
- **Alpine.js** direkomendasikan untuk interaksi lokal ringan seperti toggle, open/close, dan state tampilan sementara.
- **Livewire** dapat digunakan bersama Alpine.js ketika komponen UI perlu sinkron dengan state aplikasi; hindari pengelolaan state yang saling bertentangan.
- **Tailwind CSS** digunakan untuk styling dan responsive utilities; gunakan komponen yang dapat dipakai ulang.
- Tambahkan pustaka UI/animasi lain hanya jika komponen yang diperlukan tidak efektif dibuat dengan Tailwind + Alpine.js.
- Hormati preferensi `prefers-reduced-motion` dan jangan membuat animasi yang menghalangi akses konten.

> Bagian ini hanya menetapkan **cara interaksi terlihat dan terasa**. Tidak mendefinisikan event bisnis, penyimpanan data, API, otorisasi, atau perilaku fitur.

## 10. Aksesibilitas

- Target kontras minimum **WCAG AA**: 4.5:1 untuk teks biasa dan 3:1 untuk teks besar.
- Semua kontrol interaktif harus dapat diakses menggunakan keyboard.
- Gunakan indikator fokus yang jelas dan tidak bergantung pada warna hover.
- Dialog mengelola fokus dengan benar; tombol `Esc` dapat menutup dialog non-kritis.
- Status dan validasi tidak disampaikan hanya melalui warna.
- Gunakan label yang terlihat, atribut aksesibilitas yang sesuai, dan hierarki heading yang tertib.
- Pastikan tampilan tetap terbaca pada zoom browser yang lebih besar.

## 11. Konsistensi implementasi tampilan

- **Tailwind CSS**: sistem styling utama; sesuaikan konvensi dengan versi yang terpasang.
- **Alpine.js**: interaksi UI ringan yang bersifat lokal.
- **Livewire**: integrasi komponen UI dengan state aplikasi hanya ketika diperlukan.
- **Lucide Icons**: ikon utama.
- Simpan token warna, ukuran, dan pola komponen dalam sumber terpusat yang dapat dipakai ulang.
- Hindari inline style dan nilai hardcoded berulang jika tersedia utility atau token yang sesuai.
- Jangan menambahkan library hanya untuk satu efek sederhana yang sudah dapat ditangani stack utama.
- Pembuatan fitur, struktur route, skema database, serta logika bisnis **tidak dibahas dalam dokumen ini**.

## 12. Checklist review UI

- [ ] Warna sesuai palet dan dipakai konsisten.
- [ ] Hierarki tipografi dan whitespace jelas.
- [ ] Ukuran, radius, border, serta state komponen seragam.
- [ ] Sidebar, topbar, dan area konten memiliki alignment konsisten.
- [ ] Tampilan mobile, tablet, dan desktop tidak rusak.
- [ ] Loading, empty, error, focus, dan disabled state memiliki desain yang jelas.
- [ ] Ikon berasal dari Lucide dan tampil konsisten.
- [ ] Interaksi terasa ringan dan tidak berlebihan.
- [ ] Navigasi keyboard dan kontras teks memenuhi aksesibilitas dasar.
- [ ] Tidak ada spesifikasi fitur, aturan bisnis, atau struktur database dalam dokumen ini.
