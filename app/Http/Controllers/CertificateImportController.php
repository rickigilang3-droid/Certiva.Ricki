<?php

namespace App\Http\Controllers;

use App\Services\CertificateBulkImportService;
use App\Services\CryptoService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateImportController extends Controller
{
    public function __construct(
        protected CertificateBulkImportService $importService,
        protected CryptoService $cryptoService
    ) {}

    /**
     * Show form for bulk importing certificates via CSV.
     */
    public function showForm()
    {
        $activeKey = $this->cryptoService->getActiveKey();

        return view('certificates.import', [
            'activeKey' => $activeKey,
        ]);
    }

    /**
     * Download sample CSV template.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_sertifikat_certiva.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'recipient_name',
                'recipient_identifier',
                'recipient_email',
                'title',
                'category',
                'department',
                'description',
                'institution_name',
                'signatory_name',
                'signatory_title',
                'issued_date',
                'expiry_date',
            ]);

            // Sample row 1
            fputcsv($handle, [
                'Ahmad Fauzi',
                '12220199',
                'ahmad.fauzi@student.bsi.ac.id',
                'Sarjana Komputer - Teknik Informatika',
                'Ijazah Kelulusan',
                'Fakultas Teknologi Informasi',
                'Lulus dengan predikat Pujian (Cum Laude).',
                'Universitas Bina Sarana Informatika',
                'Prof. Dr. Ir. H. Budi Rahardjo, M.Sc.',
                'Rektor Universitas Bina Sarana Informatika',
                date('Y-m-d'),
                '',
            ]);

            // Sample row 2
            fputcsv($handle, [
                'Amelia Dwi Oktaviani',
                '12220200',
                'amelia.oktaviani@student.bsi.ac.id',
                'Sarjana Manajemen - Sistem Informasi Bisnis',
                'Ijazah Kelulusan',
                'Fakultas Ekonomi dan Komunikasi',
                'Lulus dengan predikat Sangat Memuaskan.',
                'Universitas Bina Sarana Informatika',
                'Prof. Dr. Ir. H. Budi Rahardjo, M.Sc.',
                'Rektor Universitas Bina Sarana Informatika',
                date('Y-m-d'),
                '',
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Process uploaded CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ], [
            'csv_file.required' => 'Silakan pilih berkas CSV untuk diunggah.',
            'csv_file.mimes' => 'Format berkas harus berupa .csv atau .txt dengan pemisah koma/titik koma.',
            'csv_file.max' => 'Ukuran berkas CSV maksimal 10MB.',
        ]);

        $result = $this->importService->importFromCsv($request->file('csv_file'));

        if ($result['imported'] === 0 && ! empty($result['errors'])) {
            return redirect()->back()
                ->withErrors(['csv_file' => 'Import gagal: '.implode(' ', array_slice($result['errors'], 0, 3))])
                ->withInput();
        }

        $message = "Berhasil mengimpor dan menandatangani secara kriptografis {$result['imported']} sertifikat.";
        if (! empty($result['errors'])) {
            $message .= ' Namun terdapat '.count($result['errors']).' catatan periksa: '.implode('; ', array_slice($result['errors'], 0, 3));
        }

        return redirect()->route('certificates.index')->with('status', $message);
    }
}
