<?php

namespace App\Http\Controllers;

use App\Models\VerificationLog;
use Illuminate\Http\Request;

class VerificationLogController extends Controller
{
    /**
     * Display a listing of verification audit logs for the admin.
     */
    public function index(Request $request)
    {
        $query = VerificationLog::with('certificate')->latest('verified_at');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number_queried', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('user_agent', 'like', "%{$search}%")
                    ->orWhereHas('certificate', function ($cq) use ($search) {
                        $cq->where('recipient_name', 'like', "%{$search}%")
                            ->orWhere('title', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total_scans' => VerificationLog::count(),
            'valid_scans' => VerificationLog::where('status', 'valid')->count(),
            'revoked_scans' => VerificationLog::where('status', 'revoked')->count(),
            'not_found_scans' => VerificationLog::where('status', 'not_found')->count(),
            'scans_today' => VerificationLog::whereDate('verified_at', today())->count(),
        ];

        return view('admin.logs.index', [
            'logs' => $logs,
            'stats' => $stats,
        ]);
    }
}

