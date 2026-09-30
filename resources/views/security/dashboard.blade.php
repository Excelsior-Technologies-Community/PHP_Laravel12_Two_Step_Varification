<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col md:flex-row
                    md:justify-between md:items-center gap-3">

            <h2 class="font-semibold text-xl text-gray-800">
                🔐 Security Activity Dashboard
            </h2>

            <div class="flex gap-2">

                <a
                    href="{{ route('security.settings') }}"
                    class="px-4 py-2 bg-gray-800 text-white
                           rounded-md text-sm hover:bg-gray-700">

                    Security Settings

                </a>

                <a
                    href="{{ route('security.export', request()->query()) }}"
                    class="px-4 py-2 bg-green-600 text-white
                           rounded-md text-sm hover:bg-green-700">

                    Export CSV

                </a>

            </div>

        </div>

    </x-slot>


    <div class="py-8">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))

                <div class="mb-6 rounded-lg bg-green-100
                            border border-green-200 text-green-800
                            px-4 py-3">

                    {{ session('success') }}

                </div>

            @endif


            <!-- Statistics -->

            <div class="grid grid-cols-1 md:grid-cols-2
                        lg:grid-cols-4 gap-6 mb-8">

                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Total Activities
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $totalActivities }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Successful Verifications
                    </p>

                    <p class="text-3xl font-bold text-green-600 mt-2">
                        {{ $successfulVerifications }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Failed Attempts
                    </p>

                    <p class="text-3xl font-bold text-red-600 mt-2">
                        {{ $failedAttempts }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Locked Attempts
                    </p>

                    <p class="text-3xl font-bold text-orange-600 mt-2">
                        {{ $lockedAttempts }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Expired OTPs
                    </p>

                    <p class="text-3xl font-bold text-orange-500 mt-2">
                        {{ $expiredOtps }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Resent OTPs
                    </p>

                    <p class="text-3xl font-bold text-blue-600 mt-2">
                        {{ $resentOtps }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Today's Activities
                    </p>

                    <p class="text-3xl font-bold text-purple-600 mt-2">
                        {{ $todayActivities }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Current Failed Counter
                    </p>

                    <p class="text-3xl font-bold text-indigo-600 mt-2">
                        {{ auth()->user()->two_factor_failed_attempts }}/5
                    </p>

                </div>

            </div>


            <!-- Filters -->

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">

                <form
                    method="GET"
                    action="{{ route('security.dashboard') }}"
                    class="grid grid-cols-1 md:grid-cols-2
                           lg:grid-cols-4 gap-4">

                    <div>

                        <label class="block text-sm font-medium
                                      text-gray-700 mb-1">

                            Search

                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Event, description, IP..."
                            class="w-full rounded-md border-gray-300
                                   shadow-sm">

                    </div>


                    <div>

                        <label class="block text-sm font-medium
                                      text-gray-700 mb-1">

                            Event

                        </label>

                        <select
                            name="event"
                            class="w-full rounded-md border-gray-300
                                   shadow-sm">

                            <option value="">
                                All Events
                            </option>

                            @foreach($events as $event)

                                <option
                                    value="{{ $event }}"
                                    @selected(request('event') === $event)>

                                    {{ $event }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label class="block text-sm font-medium
                                      text-gray-700 mb-1">

                            IP Address

                        </label>

                        <select
                            name="ip_address"
                            class="w-full rounded-md border-gray-300
                                   shadow-sm">

                            <option value="">
                                All IP Addresses
                            </option>

                            @foreach($ipAddresses as $ip)

                                <option
                                    value="{{ $ip }}"
                                    @selected(request('ip_address') === $ip)>

                                    {{ $ip }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label class="block text-sm font-medium
                                      text-gray-700 mb-1">

                            Sort

                        </label>

                        <select
                            name="sort"
                            class="w-full rounded-md border-gray-300
                                   shadow-sm">

                            <option
                                value="created_at"
                                @selected(request('sort', 'created_at') === 'created_at')>

                                Date

                            </option>

                            <option
                                value="event"
                                @selected(request('sort') === 'event')>

                                Event

                            </option>

                            <option
                                value="ip_address"
                                @selected(request('sort') === 'ip_address')>

                                IP Address

                            </option>

                        </select>

                    </div>


                    <div>

                        <label class="block text-sm font-medium
                                      text-gray-700 mb-1">

                            Direction

                        </label>

                        <select
                            name="direction"
                            class="w-full rounded-md border-gray-300
                                   shadow-sm">

                            <option
                                value="desc"
                                @selected(request('direction', 'desc') === 'desc')>

                                Descending

                            </option>

                            <option
                                value="asc"
                                @selected(request('direction') === 'asc')>

                                Ascending

                            </option>

                        </select>

                    </div>


                    <div>

                        <label class="block text-sm font-medium
                                      text-gray-700 mb-1">

                            Date From

                        </label>

                        <input
                            type="date"
                            name="date_from"
                            value="{{ request('date_from') }}"
                            class="w-full rounded-md border-gray-300
                                   shadow-sm">

                    </div>


                    <div>

                        <label class="block text-sm font-medium
                                      text-gray-700 mb-1">

                            Date To

                        </label>

                        <input
                            type="date"
                            name="date_to"
                            value="{{ request('date_to') }}"
                            class="w-full rounded-md border-gray-300
                                   shadow-sm">

                    </div>


                    <div class="flex items-end gap-2">

                        <button
                            type="submit"
                            class="px-5 py-2.5 bg-indigo-600
                                   text-white rounded-md
                                   hover:bg-indigo-700">

                            Filter

                        </button>

                        <a
                            href="{{ route('security.dashboard') }}"
                            class="px-5 py-2.5 bg-gray-200
                                   text-gray-700 rounded-md
                                   hover:bg-gray-300">

                            Reset

                        </a>

                    </div>

                </form>

            </div>


            <!-- Activity Table -->

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                <div class="p-6 border-b">

                    <h3 class="text-lg font-semibold text-gray-800">
                        Security Activity
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        {{ $activities->total() }} matching activities
                    </p>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs
                                           font-medium text-gray-500 uppercase">

                                    Event

                                </th>

                                <th class="px-6 py-3 text-left text-xs
                                           font-medium text-gray-500 uppercase">

                                    Description

                                </th>

                                <th class="px-6 py-3 text-left text-xs
                                           font-medium text-gray-500 uppercase">

                                    IP Address

                                </th>

                                <th class="px-6 py-3 text-left text-xs
                                           font-medium text-gray-500 uppercase">

                                    Date & Time

                                </th>

                            </tr>

                        </thead>


                        <tbody class="bg-white divide-y divide-gray-200">

                            @forelse($activities as $activity)

                                <tr class="hover:bg-gray-50">

                                    <td class="px-6 py-4 whitespace-nowrap">

                                        @if($activity->event === 'OTP Verified')

                                            <span class="px-2 py-1 text-xs
                                                         rounded-full
                                                         bg-green-100
                                                         text-green-700">

                                                ✓ {{ $activity->event }}

                                            </span>

                                        @elseif($activity->event === 'OTP Failed')

                                            <span class="px-2 py-1 text-xs
                                                         rounded-full
                                                         bg-red-100
                                                         text-red-700">

                                                ✕ {{ $activity->event }}

                                            </span>

                                        @elseif($activity->event === 'OTP Locked')

                                            <span class="px-2 py-1 text-xs
                                                         rounded-full
                                                         bg-red-200
                                                         text-red-800">

                                                🔒 {{ $activity->event }}

                                            </span>

                                        @elseif($activity->event === 'OTP Expired')

                                            <span class="px-2 py-1 text-xs
                                                         rounded-full
                                                         bg-orange-100
                                                         text-orange-700">

                                                ⏱ {{ $activity->event }}

                                            </span>

                                        @else

                                            <span class="px-2 py-1 text-xs
                                                         rounded-full
                                                         bg-blue-100
                                                         text-blue-700">

                                                {{ $activity->event }}

                                            </span>

                                        @endif

                                    </td>


                                    <td class="px-6 py-4 text-sm text-gray-600">

                                        {{ $activity->description }}

                                    </td>


                                    <td class="px-6 py-4 text-sm text-gray-600">

                                        {{ $activity->ip_address ?? 'N/A' }}

                                    </td>


                                    <td class="px-6 py-4 text-sm text-gray-600
                                               whitespace-nowrap">

                                        {{ $activity->created_at->format('d M Y, h:i A') }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4"
                                        class="px-6 py-8 text-center text-gray-500">

                                        No security activity found.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                <div class="p-6">

                    {{ $activities->links() }}

                </div>

            </div>

        </div>

    </div>

</x-app-layout>