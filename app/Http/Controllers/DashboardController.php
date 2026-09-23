<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\VerificationLog;
use App\Services\CryptoService;
use Illuminate\Http\Request;

class DashboardController
{
    protected CryptoService $cryptoService;

    public function __construct(CryptoService $cryptoService)
    {
        $this->cryptoService = $cryptoService;
    }

    public function __invoke(Request $request)
    {
        if ($request->user() && $request->user()->isMahasiswa()) {
            return redirect()->route('student.certificates');
        }

        $activeKey = $this->cryptoService->getActiveKey();

        $stats = [
            'total_certificates' => Certificate::count(),
            'active_certificates' => Certificate::where('status', 'active')->count(),
            'revoked_certificates' => Certificate::where('status', 'revoked')->count(),
            'total_verifications' => VerificationLog::count(),
            'verified_today' => VerificationLog::whereDate('verified_at', today())->count(),
        ];

        $recentCertificates = Certificate::with('cryptoKey')->latest()->limit(5)->get();
        $recentLogs = VerificationLog::with('certificate')->latest()->limit(7)->get();

        return view('dashboard', [
            'stats' => $stats,
            'activeKey' => $activeKey,
            'recentCertificates' => $recentCertificates,
            'recentLogs' => $recentLogs,
        ]);
    }
}
