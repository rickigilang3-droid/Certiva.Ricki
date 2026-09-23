<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CertificateImportController;
use App\Http\Controllers\CryptoKeyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicVerificationController;
use App\Http\Controllers\StudentPortalController;
use Illuminate\Support\Facades\Route;

// Public Verification Portal Routes
Route::get('/', [PublicVerificationController::class, 'index'])->name('home');
Route::get('/verify', [PublicVerificationController::class, 'index'])->name('verify.index');
Route::post('/verify/pdf-upload', [PublicVerificationController::class, 'verifyPdfUpload'])->name('verify.pdf.upload');
Route::get('/verify/{certificate_number}', [PublicVerificationController::class, 'show'])->name('verify.show');
Route::get('/verify/{certificate_number}/download', [PublicVerificationController::class, 'downloadPdf'])->name('verify.download');
Route::get('/verify/{certificate_number}/proof', [PublicVerificationController::class, 'rawProof'])->name('verify.proof');

// Authenticated Routes (Mahasiswa & Admin)
Route::middleware(['auth', 'verified'])->group(function () {
    // Student Portal (Self-Service Certificates)
    Route::get('/my-certificates', [StudentPortalController::class, 'index'])->name('student.certificates');
    Route::get('/my-certificates/{certificate}/pdf', [StudentPortalController::class, 'downloadPdf'])->name('student.certificates.pdf');
    Route::get('/my-certificates/{certificate}/preview', [StudentPortalController::class, 'previewPdf'])->name('student.certificates.preview');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Authority Only Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        // Certificate Management
        Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/create', [CertificateController::class, 'create'])->name('certificates.create');
        Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');

        // CSV Bulk Import
        Route::get('/certificates/import', [CertificateImportController::class, 'showForm'])->name('certificates.import.form');
        Route::post('/certificates/import', [CertificateImportController::class, 'import'])->name('certificates.import');
        Route::get('/certificates/import/template', [CertificateImportController::class, 'downloadTemplate'])->name('certificates.import.template');

        Route::get('/certificates/{certificate}', [CertificateController::class, 'show'])->name('certificates.show');
        Route::post('/certificates/{certificate}/revoke', [CertificateController::class, 'revoke'])->name('certificates.revoke');
        Route::get('/certificates/{certificate}/pdf', [CertificateController::class, 'downloadPdf'])->name('certificates.pdf');
        Route::get('/certificates/{certificate}/preview', [CertificateController::class, 'previewPdf'])->name('certificates.preview');

        // Cryptographic Key Management
        Route::get('/crypto-keys', [CryptoKeyController::class, 'index'])->name('crypto-keys.index');
        Route::post('/crypto-keys/rotate', [CryptoKeyController::class, 'rotate'])->name('crypto-keys.rotate');
        Route::get('/crypto-keys/{cryptoKey}/download-public', [CryptoKeyController::class, 'downloadPublic'])->name('crypto-keys.download-public');
    });
});

require __DIR__.'/auth.php';
