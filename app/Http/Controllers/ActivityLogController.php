<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($activityQuery) use ($search) {
                $activityQuery->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->string('action')->toString());
        }

        return view('admin.activity-logs.index', [
            'logs' => $query->paginate(20)->withQueryString(),
            'actions' => ActivityLog::query()
                ->select('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action'),
        ]);
    }
}
