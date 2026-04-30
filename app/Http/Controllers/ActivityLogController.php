<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = Activity::query()
            ->with(['causer', 'subject'])
            ->when($request->filled('user_id'), fn($q) => $q->where('causer_id', $request->user_id))
            ->when($request->filled('log_name'), fn($q) => $q->where('log_name', $request->log_name))
            ->when($request->filled('from'), fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->q.'%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('description', 'like', $term)
                        ->orWhere('properties', 'like', $term)
                        ->orWhere('subject_type', 'like', $term);
                });
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $users = User::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $logNames = Activity::query()->select('log_name')->distinct()->orderBy('log_name')->pluck('log_name');

        return view('activity-log.index', compact('logs', 'users', 'logNames'));
    }
}
