<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ActivityLogPresenter;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request, ActivityLogPresenter $presenter)
    {
        $filters = [
            'section' => (string) $request->input('section', ''),
            'event' => (string) $request->input('event', ''),
            'causer' => $request->integer('causer') ?: null,
            'from' => (string) $request->input('from', ''),
            'to' => (string) $request->input('to', ''),
        ];

        $activities = Activity::with('causer')
            ->when(
                $filters['section'] !== '' && isset(ActivityLogPresenter::SECTIONS[$filters['section']]),
                fn ($q) => $q->where('subject_type', 'App\\Models\\' . $filters['section'])
            )
            ->when(
                $filters['event'] !== '' && isset(ActivityLogPresenter::EVENTS[$filters['event']]),
                fn ($q) => $q->where(function ($q) use ($filters) {
                    $q->where('event', $filters['event'])->orWhere('description', $filters['event']);
                })
            )
            ->when($filters['causer'], fn ($q) => $q->where('causer_id', $filters['causer'])->where('causer_type', User::class))
            ->when($filters['from'] !== '', fn ($q) => $q->whereDate('created_at', '>=', $filters['from']))
            ->when($filters['to'] !== '', fn ($q) => $q->whereDate('created_at', '<=', $filters['to']))
            ->latest()
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $rows = $presenter->present($activities->items());

        // Only people who actually did something appear in the "user" filter.
        $causers = User::whereIn('id', Activity::where('causer_type', User::class)->distinct()->pluck('causer_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $sections = collect(ActivityLogPresenter::SECTIONS)->map(fn ($s) => $s[0]);
        $events = collect(ActivityLogPresenter::EVENTS)->map(fn ($e) => $e[1]);

        return view('activity-logs.index', compact('activities', 'rows', 'filters', 'causers', 'sections', 'events'));
    }
}
