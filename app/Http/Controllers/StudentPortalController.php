<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\User;
use App\Services\CertificatePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentPortalController extends Controller
{
    public function __construct(
        protected CertificatePdfService $pdfService
    ) {}

    /**
     * Display a listing of certificates owned by the authenticated student.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Certificate::with('cryptoKey')
            ->where(function ($q) use ($user) {
                $q->where('recipient_email', $user->email);
                if (! empty($user->identifier)) {
                    $q->orWhere('recipient_identifier', $user->identifier);
                }
            })
            ->latest('issued_date');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $certificates = $query->paginate(9)->withQueryString();

        $allMyCertificates = Certificate::where(function ($q) use ($user) {
            $q->where('recipient_email', $user->email);
            if (! empty($user->identifier)) {
                $q->orWhere('recipient_identifier', $user->identifier);
            }
        })->get();

        $stats = [
            'total' => $allMyCertificates->count(),
            'active' => $allMyCertificates->where('status', 'active')->count(),
            'revoked' => $allMyCertificates->where('status', 'revoked')->count(),
        ];

        return view('student.certificates', [
            'user' => $user,
            'certificates' => $certificates,
            'stats' => $stats,
        ]);
    }

    /**
     * Authorize that the current user owns the certificate or is an admin.
     */
    protected function authorizeAccess(Request $request, Certificate $certificate): void
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return;
        }

        $isOwner = ($certificate->recipient_email === $user->email)
            || (! empty($user->identifier) && $certificate->recipient_identifier === $user->identifier);

        if (! $isOwner) {
            abort(403, 'Anda tidak memiliki hak akses untuk sertifikat ini.');
        }
    }

    /**
     * Download the certificate PDF.
     */
    public function downloadPdf(Request $request, Certificate $certificate)
    {
        $this->authorizeAccess($request, $certificate);

        if ($request->boolean('preview') || $request->boolean('stream')) {
            return $this->pdfService->streamPdf($certificate);
        }

        if (! $certificate->pdf_path || ! Storage::disk('public')->exists($certificate->pdf_path)) {
            $path = $this->pdfService->generateAndSavePdf($certificate);
            $certificate->pdf_path = $path;
        }

        if ($certificate->pdf_path && Storage::disk('public')->exists($certificate->pdf_path)) {
            return Storage::disk('public')->download($certificate->pdf_path, "{$certificate->certificate_number}.pdf");
        }

        return $this->pdfService->streamPdf($certificate);
    }

    /**
     * Stream PDF directly for embedded preview.
     */
    public function previewPdf(Request $request, Certificate $certificate)
    {
        $this->authorizeAccess($request, $certificate);

        return $this->pdfService->streamPdf($certificate);
    }

    /**
     * Display public verified student portfolio / SKPI.
     */
    public function publicPortfolio(Request $request, string $identifier)
    {
        $identifier = trim($identifier);

        $student = User::where('identifier', $identifier)
            ->orWhere('email', $identifier)
            ->orWhere(function ($q) use ($identifier) {
                if (is_numeric($identifier)) {
                    $q->where('id', (int) $identifier);
                }
            })
            ->first();

        $certificates = Certificate::with('cryptoKey')
            ->where(function ($q) use ($identifier, $student) {
                $q->where('recipient_identifier', $identifier)
                    ->orWhere('recipient_email', $identifier);
                if ($student) {
                    $q->orWhere('recipient_email', $student->email);
                    if ($student->identifier) {
                        $q->orWhere('recipient_identifier', $student->identifier);
                    }
                }
            })
            ->where('status', 'active')
            ->latest('issued_date')
            ->get();

        if (! $student && $certificates->isEmpty()) {
            abort(404, 'Portofolio kredensial mahasiswa tidak ditemukan.');
        }

        $studentName = $student?->name ?? ($certificates->first()?->recipient_name ?? 'Mahasiswa');
        $studentNim = $student?->identifier ?? ($certificates->first()?->recipient_identifier ?? $identifier);
        $studentEmail = $student?->email ?? ($certificates->first()?->recipient_email ?? null);

        return view('public.portfolio', [
            'student' => $student,
            'studentName' => $studentName,
            'studentNim' => $studentNim,
            'studentEmail' => $studentEmail,
            'certificates' => $certificates,
        ]);
    }
}
