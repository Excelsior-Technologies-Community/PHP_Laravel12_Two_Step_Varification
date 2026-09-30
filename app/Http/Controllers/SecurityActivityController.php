<?php

namespace App\Http\Controllers;

use App\Models\SecurityActivity;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecurityActivityController extends Controller
{
    /**
     * Security analytics dashboard.
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $query = SecurityActivity::where(
            'user_id',
            $user->id
        );

        $this->applyFilters($query, $request);

        $totalActivities = SecurityActivity::where(
            'user_id',
            $user->id
        )->count();

        $successfulVerifications = SecurityActivity::where(
            'user_id',
            $user->id
        )
            ->where('event', 'OTP Verified')
            ->count();

        $failedAttempts = SecurityActivity::where(
            'user_id',
            $user->id
        )
            ->where('event', 'OTP Failed')
            ->count();

        $expiredOtps = SecurityActivity::where(
            'user_id',
            $user->id
        )
            ->where('event', 'OTP Expired')
            ->count();

        $resentOtps = SecurityActivity::where(
            'user_id',
            $user->id
        )
            ->where('event', 'OTP Resent')
            ->count();

        $lockedAttempts = SecurityActivity::where(
            'user_id',
            $user->id
        )
            ->where('event', 'OTP Locked')
            ->count();

        $todayActivities = SecurityActivity::where(
            'user_id',
            $user->id
        )
            ->whereDate('created_at', today())
            ->count();

        $activities = $query
            ->oldest()
            ->paginate(5)
            ->withQueryString();

        $events = SecurityActivity::where(
            'user_id',
            $user->id
        )
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        $ipAddresses = SecurityActivity::where(
            'user_id',
            $user->id
        )
            ->whereNotNull('ip_address')
            ->select('ip_address')
            ->distinct()
            ->orderBy('ip_address')
            ->pluck('ip_address');

        return view('security.dashboard', compact(
            'totalActivities',
            'successfulVerifications',
            'failedAttempts',
            'expiredOtps',
            'resentOtps',
            'lockedAttempts',
            'todayActivities',
            'activities',
            'events',
            'ipAddresses'
        ));
    }

    /**
     * Export filtered security activity as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $user = auth()->user();

        $query = SecurityActivity::where(
            'user_id',
            $user->id
        );

        $this->applyFilters($query, $request);

        $activities = $query
            ->latest()
            ->get();

        $filename = 'security-activity-' .
            now()->format('Y-m-d-H-i-s') .
            '.csv';

        return response()->streamDownload(
            function () use ($activities) {
                $handle = fopen('php://output', 'w');

                fputcsv($handle, [
                    'Event',
                    'Description',
                    'IP Address',
                    'User Agent',
                    'Date',
                ]);

                foreach ($activities as $activity) {
                    fputcsv($handle, [
                        $activity->event,
                        $activity->description,
                        $activity->ip_address,
                        $activity->user_agent,
                        $activity->created_at?->format(
                            'Y-m-d H:i:s'
                        ),
                    ]);
                }

                fclose($handle);
            },
            $filename,
            [
                'Content-Type' => 'text/csv',
            ]
        );
    }

    /**
     * Apply dashboard filters.
     */
    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where(
                    'event',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'description',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'ip_address',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'user_agent',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($request->filled('event')) {
            $query->where(
                'event',
                $request->input('event')
            );
        }

        if ($request->filled('ip_address')) {
            $query->where(
                'ip_address',
                $request->input('ip_address')
            );
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->input('date_from')
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->input('date_to')
            );
        }

        $allowedSorts = [
            'created_at',
            'event',
            'ip_address',
        ];

        $sort = $request->input(
            'sort',
            'created_at'
        );

        $direction = $request->input(
            'direction',
            'desc'
        );

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query->orderBy(
            $sort,
            $direction
        );
    }
}