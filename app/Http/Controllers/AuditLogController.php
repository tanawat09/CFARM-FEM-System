<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model')) {
            $query->where('auditable_type', 'App\\Models\\' . $request->model);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(30)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name']);

        $modelTypes = AuditLog::select('auditable_type')
            ->distinct()
            ->pluck('auditable_type')
            ->map(fn($type) => class_basename($type))
            ->sort()
            ->values();

        return view('audit-logs.index', compact('logs', 'users', 'modelTypes'));
    }
}
