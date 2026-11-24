<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $q = ActivityLog::with('user:id,name,email')
            ->when($request->filled('q'), function ($w) use ($request) {
                $s = $request->input('q');
                $w->where(function ($x) use ($s) {
                    $x->where('action', 'like', "%{$s}%")
                        ->orWhere('module', 'like', "%{$s}%")
                        ->orWhere('description', 'like', "%{$s}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
                });
            })
            ->when($request->filled('action'), fn ($w) => $w->where('action', $request->input('action')))
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        return view('logs.index', [
            'logs' => $q,
            'search' => (string) $request->input('q', ''),
            'action' => (string) $request->input('action', ''),
            'actions' => ActivityLog::distinct()->orderBy('action')->pluck('action'),
        ]);
    }
}
