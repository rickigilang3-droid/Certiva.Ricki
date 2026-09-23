<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
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

        return Storage::disk('public')->download($certificate->pdf_path, "{$certificate->certificate_number}.pdf");
    }

    /**
     * Stream PDF directly for embedded preview.
     */
    public function previewPdf(Request $request, Certificate $certificate)
    {
        $this->authorizeAccess($request, $certificate);

        return $this->pdfService->streamPdf($certificate);
    }
}
