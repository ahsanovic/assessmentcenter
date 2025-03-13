<?php

use App\Http\Controllers\LogoutAssessorController;
use App\Http\Controllers\LogoutPesertaController;
use App\Http\Middleware\CheckExamPin;
use Illuminate\Support\Facades\Route;

// admin
Route::prefix('/bkdac')->group(function () {
    Route::get('dashboard', \App\Livewire\Admin\Dashboard\Index::class)->name('admin.dashboard');

    // alat tes
    Route::get('alat-tes', \App\Livewire\Admin\AlatTes\Index::class)->name('admin.alat-tes');
    Route::get('alat-tes/create', \App\Livewire\Admin\AlatTes\Form::class)->name('admin.alat-tes.create');
    Route::get('alat-tes/{id}/edit', \App\Livewire\Admin\AlatTes\Form::class)->name('admin.alat-tes.edit');

    // urutan tes
    Route::get('settings/urutan-tes', \App\Livewire\Admin\Settings\Urutan\Index::class)->name('admin.settings.urutan');
    Route::get('settings/urutan-tes/create', \App\Livewire\Admin\Settings\Urutan\Form::class)->name('admin.settings.urutan.create');
    Route::get('settings/urutan-tes/{id}/edit', \App\Livewire\Admin\Settings\Urutan\Form::class)->name('admin.settings.urutan.edit');
    
    // waktu tes
    Route::get('settings/waktu-tes', \App\Livewire\Admin\Settings\Waktu\Index::class)->name('admin.settings.waktu');
    Route::get('settings/waktu-tes/create', \App\Livewire\Admin\Settings\Waktu\Form::class)->name('admin.settings.waktu.create');
    Route::get('settings/waktu-tes/{id}/edit', \App\Livewire\Admin\Settings\Waktu\Form::class)->name('admin.settings.waktu.edit');

    // event
    Route::get('event', \App\Livewire\Admin\Event\Index::class)->name('admin.event');
    Route::get('event/create', \App\Livewire\Admin\Event\Form::class)->name('admin.event.create');
    Route::get('event/{id}/edit', \App\Livewire\Admin\Event\Form::class)->name('admin.event.edit');
    Route::get('event/{idEvent}/show-peserta', \App\Livewire\Admin\Event\ShowPeserta::class)->name('admin.event.show-peserta');
    Route::get('event/{idEvent}/show-assessor', \App\Livewire\Admin\Event\ShowAssessor::class)->name('admin.event.show-assessor');

    // data tes berlangsung
    Route::get('tes-berlangsung', \App\Livewire\Admin\DataTes\TesBerlangsung\Index::class)->name('admin.tes-berlangsung');
    Route::get('tes-berlangsung/{idEvent}/show-peserta-interpersonal', \App\Livewire\Admin\DataTes\TesBerlangsung\ShowPesertaInterpersonal::class)->name('admin.tes-berlangsung.show-peserta-interpersonal');
    Route::get('tes-berlangsung/{idEvent}/show-peserta-kesadaran-diri', \App\Livewire\Admin\DataTes\TesBerlangsung\ShowPesertaKesadaranDiri::class)->name('admin.tes-berlangsung.show-peserta-kesadaran-diri');
    Route::get('tes-berlangsung/{idEvent}/show-peserta-berpikir-kritis', \App\Livewire\Admin\DataTes\TesBerlangsung\ShowPesertaBerpikirKritis::class)->name('admin.tes-berlangsung.show-peserta-berpikir-kritis');
    Route::get('tes-berlangsung/{idEvent}/show-peserta-problem-solving', \App\Livewire\Admin\DataTes\TesBerlangsung\ShowPesertaProblemSolving::class)->name('admin.tes-berlangsung.show-peserta-problem-solving');
    Route::get('tes-berlangsung/{idEvent}/show-peserta-kecerdasan-emosi', \App\Livewire\Admin\DataTes\TesBerlangsung\ShowPesertaKecerdasanEmosi::class)->name('admin.tes-berlangsung.show-peserta-kecerdasan-emosi');
    Route::get('tes-berlangsung/{idEvent}/show-peserta-pengembangan-diri', \App\Livewire\Admin\DataTes\TesBerlangsung\ShowPesertaPengembanganDiri::class)->name('admin.tes-berlangsung.show-peserta-pengembangan-diri');
    Route::get('tes-berlangsung/{idEvent}/show-peserta-motivasi-komitmen', \App\Livewire\Admin\DataTes\TesBerlangsung\ShowPesertaMotivasiKomitmen::class)->name('admin.tes-berlangsung.show-peserta-motivasi-komitmen');

    // data tes selesai
    Route::get('tes-selesai', \App\Livewire\Admin\DataTes\TesSelesai\Index::class)->name('admin.tes-selesai');
    Route::get('tes-selesai/{idEvent}/show-peserta', \App\Livewire\Admin\DataTes\TesSelesai\ShowPeserta::class)->name('admin.tes-selesai.show-peserta');
    Route::get('tes-selesai/{idEvent}/show-peserta-interpersonal', \App\Livewire\Admin\DataTes\TesSelesai\ShowPesertaInterpersonal::class)->name('admin.tes-selesai.show-peserta-interpersonal');
    Route::get('tes-selesai/{idEvent}/show-peserta-kesadaran-diri', \App\Livewire\Admin\DataTes\TesSelesai\ShowPesertaKesadaranDiri::class)->name('admin.tes-selesai.show-peserta-kesadaran-diri');
    Route::get('tes-selesai/{idEvent}/show-peserta-berpikir-kritis', \App\Livewire\Admin\DataTes\TesSelesai\ShowPesertaBerpikirKritis::class)->name('admin.tes-selesai.show-peserta-berpikir-kritis');
    Route::get('tes-selesai/{idEvent}/show-peserta-problem-solving', \App\Livewire\Admin\DataTes\TesSelesai\ShowPesertaProblemSolving::class)->name('admin.tes-selesai.show-peserta-problem-solving');
    Route::get('tes-selesai/{idEvent}/show-peserta-kecerdasan-emosi', \App\Livewire\Admin\DataTes\TesSelesai\ShowPesertaKecerdasanEmosi::class)->name('admin.tes-selesai.show-peserta-kecerdasan-emosi');
    Route::get('tes-selesai/{idEvent}/show-peserta-pengembangan-diri', \App\Livewire\Admin\DataTes\TesSelesai\ShowPesertaPengembanganDiri::class)->name('admin.tes-selesai.show-peserta-pengembangan-diri');
    Route::get('tes-selesai/{idEvent}/show-peserta-motivasi-komitmen', \App\Livewire\Admin\DataTes\TesSelesai\ShowPesertaMotivasiKomitmen::class)->name('admin.tes-selesai.show-peserta-motivasi-komitmen');
    Route::get('tes-selesai/{idEvent}/{nip}/show-report', \App\Livewire\Admin\DataTes\TesSelesai\ShowReport::class)->name('admin.tes-selesai.show-report');

    // peserta
    Route::get('peserta', \App\Livewire\Admin\Peserta\Index::class)->name('admin.peserta');
    Route::get('peserta/create', \App\Livewire\Admin\Peserta\Form::class)->name('admin.peserta.create');
    Route::get('peserta/{id}/edit', \App\Livewire\Admin\Peserta\Form::class)->name('admin.peserta.edit');

    // assessor
    Route::get('assessor', \App\Livewire\Admin\Assessor\Index::class)->name('admin.assessor');
    Route::get('assessor/create', \App\Livewire\Admin\Assessor\Form::class)->name('admin.assessor.create');
    Route::get('assessor/{id}/edit', \App\Livewire\Admin\Assessor\Form::class)->name('admin.assessor.edit');

    // distribusi peserta
    Route::get('distribusi', \App\Livewire\Admin\DistribusiPeserta\Index::class)->name('admin.distribusi-peserta');
    Route::get('distribusi/{idEvent}/show-assessor', \App\Livewire\Admin\DistribusiPeserta\ShowAssessor::class)->name('admin.distribusi-peserta.show-assessor');
    Route::get('distribusi/{idEvent}/{idAssessor}/list-asessee', \App\Livewire\Admin\DistribusiPeserta\ListAsessee::class)->name('admin.distribusi-peserta.list-asessee');

    // pertanyaan pengalaman spesifik
    Route::get('pertanyaan', \App\Livewire\Admin\RefPertanyaanPengalaman\Index::class)->name('admin.pertanyaan-pengalaman');
    Route::get('pertanyaan/create', \App\Livewire\Admin\RefPertanyaanPengalaman\Form::class)->name('admin.pertanyaan-pengalaman.create');
    Route::get('pertanyaan/{id}/edit', \App\Livewire\Admin\RefPertanyaanPengalaman\Form::class)->name('admin.pertanyaan-pengalaman.edit');

    // pertanyaan penilaian pribadi
    Route::get('penilaian', \App\Livewire\Admin\RefPertanyaanPenilaian\Index::class)->name('admin.pertanyaan-penilaian');
    Route::get('penilaian/create', \App\Livewire\Admin\RefPertanyaanPenilaian\Form::class)->name('admin.pertanyaan-penilaian.create');
    Route::get('penilaian/{id}/edit', \App\Livewire\Admin\RefPertanyaanPenilaian\Form::class)->name('admin.pertanyaan-penilaian.edit');

    // soal pengembangan diri
    Route::get('soal-pengembangan-diri', \App\Livewire\Admin\PengembanganDiri\SoalPengembanganDiri\Index::class)->name('admin.soal-pengembangan-diri');
    Route::get('soal-pengembangan-diri/create', \App\Livewire\Admin\PengembanganDiri\SoalPengembanganDiri\Create::class)->name('admin.soal-pengembangan-diri.create');
    Route::get('soal-pengembangan-diri/{id}/edit', \App\Livewire\Admin\PengembanganDiri\SoalPengembanganDiri\Edit::class)->name('admin.soal-pengembangan-diri.edit');

    // referensi pengembangan diri
    Route::get('ref-pengembangan-diri', \App\Livewire\Admin\PengembanganDiri\RefPengembanganDiri\Index::class)->name('admin.ref-pengembangan-diri');
    Route::get('ref-pengembangan-diri/create', \App\Livewire\Admin\PengembanganDiri\RefPengembanganDiri\Create::class)->name('admin.ref-pengembangan-diri.create');
    Route::get('ref-pengembangan-diri/{id}/edit', \App\Livewire\Admin\PengembanganDiri\RefPengembanganDiri\Edit::class)->name('admin.ref-pengembangan-diri.edit');

    // soal interpersonal
    Route::get('soal-interpersonal', \App\Livewire\Admin\Interpersonal\SoalInterpersonal\Index::class)->name('admin.soal-interpersonal');
    Route::get('soal-interpersonal/create', \App\Livewire\Admin\Interpersonal\SoalInterpersonal\Create::class)->name('admin.soal-interpersonal.create');
    Route::get('soal-interpersonal/{id}/edit', \App\Livewire\Admin\Interpersonal\SoalInterpersonal\Edit::class)->name('admin.soal-interpersonal.edit');

    // referensi interpersonal
    Route::get('ref-interpersonal', \App\Livewire\Admin\Interpersonal\RefInterpersonal\Index::class)->name('admin.ref-interpersonal');
    Route::get('ref-interpersonal/create', \App\Livewire\Admin\Interpersonal\RefInterpersonal\Create::class)->name('admin.ref-interpersonal.create');
    Route::get('ref-interpersonal/{id}/edit', \App\Livewire\Admin\Interpersonal\RefInterpersonal\Edit::class)->name('admin.ref-interpersonal.edit');

    // soal kecerdasan emosi
    Route::get('soal-kecerdasan-emosi', \App\Livewire\Admin\KecerdasanEmosi\SoalKecerdasanEmosi\Index::class)->name('admin.soal-kecerdasan-emosi');
    Route::get('soal-kecerdasan-emosi/create', \App\Livewire\Admin\KecerdasanEmosi\SoalKecerdasanEmosi\Create::class)->name('admin.soal-kecerdasan-emosi.create');
    Route::get('soal-kecerdasan-emosi/{id}/edit', \App\Livewire\Admin\KecerdasanEmosi\SoalKecerdasanEmosi\Edit::class)->name('admin.soal-kecerdasan-emosi.edit');

    // referensi kecerdasan emosi
    Route::get('ref-kecerdasan-emosi', \App\Livewire\Admin\KecerdasanEmosi\RefKecerdasanEmosi\Index::class)->name('admin.ref-kecerdasan-emosi');
    Route::get('ref-kecerdasan-emosi/create', \App\Livewire\Admin\KecerdasanEmosi\RefKecerdasanEmosi\Create::class)->name('admin.ref-kecerdasan-emosi.create');
    Route::get('ref-kecerdasan-emosi/{id}/edit', \App\Livewire\Admin\KecerdasanEmosi\RefKecerdasanEmosi\Edit::class)->name('admin.ref-kecerdasan-emosi.edit');

    // soal motivasi komitmen
    Route::get('soal-motivasi-komitmen', \App\Livewire\Admin\MotivasiKomitmen\SoalMotivasiKomitmen\Index::class)->name('admin.soal-motivasi-komitmen');
    Route::get('soal-motivasi-komitmen/create', \App\Livewire\Admin\MotivasiKomitmen\SoalMotivasiKomitmen\Create::class)->name('admin.soal-motivasi-komitmen.create');
    Route::get('soal-motivasi-komitmen/{id}/edit', \App\Livewire\Admin\MotivasiKomitmen\SoalMotivasiKomitmen\Edit::class)->name('admin.soal-motivasi-komitmen.edit');

    // referensi motivasi dan komitmen
    Route::get('ref-motivasi-komitmen', \App\Livewire\Admin\MotivasiKomitmen\RefMotivasiKomitmen\Index::class)->name('admin.ref-motivasi-komitmen');
    Route::get('ref-motivasi-komitmen/create', \App\Livewire\Admin\MotivasiKomitmen\RefMotivasiKomitmen\Create::class)->name('admin.ref-motivasi-komitmen.create');
    Route::get('ref-motivasi-komitmen/{id}/edit', \App\Livewire\Admin\MotivasiKomitmen\RefMotivasiKomitmen\Edit::class)->name('admin.ref-motivasi-komitmen.edit');

    // soal berpikir kritis dan strategis
    Route::get('soal-berpikir-kritis', \App\Livewire\Admin\BerpikirKritis\SoalBerpikirKritis\Index::class)->name('admin.soal-berpikir-kritis');
    Route::get('soal-berpikir-kritis/create', \App\Livewire\Admin\BerpikirKritis\SoalBerpikirKritis\Create::class)->name('admin.soal-berpikir-kritis.create');
    Route::get('soal-berpikir-kritis/{id}/edit', \App\Livewire\Admin\BerpikirKritis\SoalBerpikirKritis\Edit::class)->name('admin.soal-berpikir-kritis.edit');

    // referensi aspek berpikir kritis dan strategis
    Route::get('ref-aspek-berpikir-kritis', \App\Livewire\Admin\BerpikirKritis\RefBerpikirKritis\RefAspekBerpikirKritis\Index::class)->name('admin.ref-aspek-berpikir-kritis');
    Route::get('ref-aspek-berpikir-kritis/create', \App\Livewire\Admin\BerpikirKritis\RefBerpikirKritis\RefAspekBerpikirKritis\Create::class)->name('admin.ref-aspek-berpikir-kritis.create');
    Route::get('ref-aspek-berpikir-kritis/{id}/edit', \App\Livewire\Admin\BerpikirKritis\RefBerpikirKritis\RefAspekBerpikirKritis\Edit::class)->name('admin.ref-aspek-berpikir-kritis.edit');

    // referensi indikator berpikir kritis dan strategis
    Route::get('ref-indikator-berpikir-kritis', \App\Livewire\Admin\BerpikirKritis\RefBerpikirKritis\RefIndikatorBerpikirKritis\Index::class)->name('admin.ref-indikator-berpikir-kritis');
    Route::get('ref-indikator-berpikir-kritis/create', \App\Livewire\Admin\BerpikirKritis\RefBerpikirKritis\RefIndikatorBerpikirKritis\Create::class)->name('admin.ref-indikator-berpikir-kritis.create');
    Route::get('ref-indikator-berpikir-kritis/{id}/edit', \App\Livewire\Admin\BerpikirKritis\RefBerpikirKritis\RefIndikatorBerpikirKritis\Edit::class)->name('admin.ref-indikator-berpikir-kritis.edit');
    
    // soal problem solving
    Route::get('soal-problem-solving', \App\Livewire\Admin\ProblemSolving\SoalProblemSolving\Index::class)->name('admin.soal-problem-solving');
    Route::get('soal-problem-solving/create', \App\Livewire\Admin\ProblemSolving\SoalProblemSolving\Create::class)->name('admin.soal-problem-solving.create');
    Route::get('soal-problem-solving/{id}/edit', \App\Livewire\Admin\ProblemSolving\SoalProblemSolving\Edit::class)->name('admin.soal-problem-solving.edit');

    // referensi aspek problem solving
    Route::get('ref-aspek-problem-solving', \App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefAspekProblemSolving\Index::class)->name('admin.ref-aspek-problem-solving');
    Route::get('ref-aspek-problem-solving/create', \App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefAspekProblemSolving\Create::class)->name('admin.ref-aspek-problem-solving.create');
    Route::get('ref-aspek-problem-solving/{id}/edit', \App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefAspekProblemSolving\Edit::class)->name('admin.ref-aspek-problem-solving.edit');

    // referensi indikator problem solving
    Route::get('ref-indikator-problem-solving', \App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefIndikatorProblemSolving\Index::class)->name('admin.ref-indikator-problem-solving');
    Route::get('ref-indikator-problem-solving/create', \App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefIndikatorProblemSolving\Create::class)->name('admin.ref-indikator-problem-solving.create');
    Route::get('ref-indikator-problem-solving/{id}/edit', \App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefIndikatorProblemSolving\Edit::class)->name('admin.ref-indikator-problem-solving.edit');
    
    // soal kesadaran diri
    Route::get('soal-kesadaran-diri', \App\Livewire\Admin\KesadaranDiri\SoalKesadaranDiri\Index::class)->name('admin.soal-kesadaran-diri');
    Route::get('soal-kesadaran-diri/create', \App\Livewire\Admin\KesadaranDiri\SoalKesadaranDiri\Create::class)->name('admin.soal-kesadaran-diri.create');
    Route::get('soal-kesadaran-diri/{id}/edit', \App\Livewire\Admin\KesadaranDiri\SoalKesadaranDiri\Edit::class)->name('admin.soal-kesadaran-diri.edit');

    // referensi kesadaran diri
    Route::get('ref-kesadaran-diri', \App\Livewire\Admin\KesadaranDiri\RefKesadaranDiri\Index::class)->name('admin.ref-kesadaran-diri');
    Route::get('ref-kesadaran-diri/create', \App\Livewire\Admin\KesadaranDiri\RefKesadaranDiri\Create::class)->name('admin.ref-kesadaran-diri.create');
    Route::get('ref-kesadaran-diri/{id}/edit', \App\Livewire\Admin\KesadaranDiri\RefKesadaranDiri\Edit::class)->name('admin.ref-kesadaran-diri.edit');
});

// assessor
Route::prefix('assessor')->group(function () {
    Route::get('/', \App\Livewire\Assessor\Auth\Login::class)->name('assessor.login')->middleware('guest:assessor');
    Route::middleware(['auth:assessor'])->group(function () {
        // dashboard
        Route::get('/dashboard', \App\Livewire\Assessor\Dashboard\Index::class)->name('assessor.dashboard');

        // event
        Route::get('event', \App\Livewire\Assessor\Event\Index::class)->name('assessor.event');
        Route::get('event/{idEvent}/show-peserta', \App\Livewire\Assessor\Event\ShowPeserta::class)->name('assessor.event.show-peserta');
        Route::get('event/{idEvent}/{idPeserta}/portofolio', \App\Livewire\Assessor\Event\ShowPortofolio::class)->name('assessor.peserta.portofolio');

        Route::post('logout', LogoutAssessorController::class)->name('assessor.logout');
    });
});

// peserta
Route::get('/', \App\Livewire\Peserta\Auth\Login::class)->name('peserta.login')->middleware('guest:peserta');
Route::middleware(['auth:peserta'])->group(function () {
    // dashboard
    Route::get('/dashboard', \App\Livewire\Peserta\Dashboard\Index::class)->name('peserta.dashboard');

    // portofolio
    Route::get('portofolio', \App\Livewire\Peserta\Portofolio\Portofolio::class)->name('peserta.portofolio');
    Route::get('biodata', \App\Livewire\Peserta\Portofolio\Biodata::class)->name('peserta.biodata');
    Route::get('pendidikan', \App\Livewire\Peserta\Portofolio\Pendidikan::class)->name('peserta.pendidikan');
    Route::get('pendidikan/create', \App\Livewire\Peserta\Portofolio\PendidikanForm::class)->name('peserta.pendidikan.create');
    Route::get('pendidikan/{id}/edit', \App\Livewire\Peserta\Portofolio\PendidikanForm::class)->name('peserta.pendidikan.edit');
    Route::get('pelatihan', \App\Livewire\Peserta\Portofolio\Pelatihan::class)->name('peserta.pelatihan');
    Route::get('pelatihan/create', \App\Livewire\Peserta\Portofolio\PelatihanForm::class)->name('peserta.pelatihan.create');
    Route::get('pelatihan/{id}/edit', \App\Livewire\Peserta\Portofolio\PelatihanForm::class)->name('peserta.pelatihan.edit');
    Route::get('karir', \App\Livewire\Peserta\Portofolio\Karir::class)->name('peserta.karir');
    Route::get('karir/create', \App\Livewire\Peserta\Portofolio\KarirForm::class)->name('peserta.karir.create');
    Route::get('karir/{id}/edit', \App\Livewire\Peserta\Portofolio\KarirForm::class)->name('peserta.karir.edit');
    Route::get('pengalaman', \App\Livewire\Peserta\Portofolio\Pengalaman::class)->name('peserta.pengalaman');
    Route::get('penilaian', \App\Livewire\Peserta\Portofolio\Penilaian::class)->name('peserta.penilaian');

    // tes potensi
    Route::prefix('tes-potensi')->group(function () {
        Route::get('/', \App\Livewire\Peserta\TesPotensi\Index::class)->name('peserta.tes-potensi')->middleware(CheckExamPin::class);
        Route::get('home', \App\Livewire\Peserta\TesPotensi\Dashboard::class)->name('peserta.tes-potensi.home')->middleware(CheckExamPin::class);
        Route::get('interpersonal/{id}', \App\Livewire\Peserta\TesPotensi\Interpersonal::class)->name('peserta.tes-potensi.interpersonal')->middleware(CheckExamPin::class);
        Route::get('pengembangan-diri/{id}', \App\Livewire\Peserta\TesPotensi\PengembanganDiri::class)->name('peserta.tes-potensi.pengembangan-diri')->middleware(CheckExamPin::class);
        Route::get('kecerdasan-emosi/{id}', \App\Livewire\Peserta\TesPotensi\KecerdasanEmosi::class)->name('peserta.tes-potensi.kecerdasan-emosi')->middleware(CheckExamPin::class);
        Route::get('motivasi-komitmen/{id}', \App\Livewire\Peserta\TesPotensi\MotivasiKomitmen::class)->name('peserta.tes-potensi.motivasi-komitmen')->middleware(CheckExamPin::class);
        Route::get('berpikir-kritis/{id}', \App\Livewire\Peserta\TesPotensi\BerpikirKritis::class)->name('peserta.tes-potensi.berpikir-kritis')->middleware(CheckExamPin::class);
        Route::get('problem-solving/{id}', \App\Livewire\Peserta\TesPotensi\ProblemSolving::class)->name('peserta.tes-potensi.problem-solving')->middleware(CheckExamPin::class);
        Route::get('kesadaran-diri/{id}', \App\Livewire\Peserta\TesPotensi\KesadaranDiri::class)->name('peserta.tes-potensi.kesadaran-diri')->middleware(CheckExamPin::class);
    });

    Route::post('logout', LogoutPesertaController::class)->name('peserta.logout');
});
