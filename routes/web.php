<?php

use App\Livewire\Pages\ActivityLogs\Index as ActivityLogs;
use App\Livewire\Pages\Areas\Index as AreasIndex;
use App\Livewire\Pages\Auth\Login;
use App\Livewire\Pages\Companies\Index as Companies;
use App\Livewire\Pages\Dashboard;
use App\Livewire\Pages\DataCenters\DaftarDataCenter\Index as DataCentersIndex;
use App\Livewire\Pages\DataCenters\TambahDataCenter\Create as DataCentersCreate;
use App\Livewire\Pages\Services\Index as ServicesIndex;
use App\Livewire\Pages\Trash\Index as Trash;
use App\Livewire\Pages\Users\Index as Users;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => to_route('dashboard'));

Route::get('/login', Login::class)->name('login');
Route::get('/dashboard', Dashboard::class)->name('dashboard');
Route::get('/perusahaan', Companies::class)->name('companies.index');
Route::get('/data-center/tambah', DataCentersCreate::class)->name('data-centers.create');
Route::get('/data-center', DataCentersIndex::class)->name('data-centers.index');
Route::get('/jenis-kawasan', AreasIndex::class)->name('area-types.index');
Route::get('/jenis-layanan', ServicesIndex::class)->name('service-types.index');
Route::get('/pengguna', Users::class)->name('users.index');
Route::get('/log-aktivitas', ActivityLogs::class)->name('activity-logs.index');
Route::get('/trash', Trash::class)->name('trash.index');
