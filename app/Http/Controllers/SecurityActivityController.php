<?php

namespace App\Http\Controllers;

use App\Models\SecurityActivity;
use Illuminate\Http\Request;

class SecurityActivityController extends Controller
{
    /**
     * Security analytics dashboard.
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $query = SecurityActivity::where('user_id', $user->id);

        /*
         * Search.
         */
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('event', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        /*
         * Event filter.
         */
        if ($request->filled('event')) {
            $query->where('event', $request->input('event'));
        }

        /*
         * Statistics.
         */
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

        $todayActivities = SecurityActivity::where(
            'user_id',
            $user->id
        )
            ->whereDate('created_at', today())
            ->count();

        /*
         * Recent activity.
         */
        $activities = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /*
         * Available event filters.
         */
        $events = SecurityActivity::where(
            'user_id',
            $user->id
        )
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        return view('security.dashboard', compact(
            'totalActivities',
            'successfulVerifications',
            'failedAttempts',
            'expiredOtps',
            'resentOtps',
            'todayActivities',
            'activities',
            'events'
        ));
    }
}