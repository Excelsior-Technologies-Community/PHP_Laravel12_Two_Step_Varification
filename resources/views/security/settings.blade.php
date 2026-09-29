<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ⚙️ Two-Factor Security Settings
        </h2>
    </x-slot>

    <div class="py-8">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 rounded-lg bg-green-100 border border-green-200
                            text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <!-- 2FA Status -->

            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">

                <div class="flex items-center justify-between">

                    <div>

                        <h3 class="text-lg font-semibold text-gray-800">
                            Two-Factor Authentication
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Protect your account using email-based OTP verification.
                        </p>

                    </div>

                    @if($user->two_factor_enabled)

                        <span class="px-3 py-1 rounded-full
                                     bg-green-100 text-green-700 text-sm font-medium">
                            Enabled
                        </span>

                    @else

                        <span class="px-3 py-1 rounded-full
                                     bg-red-100 text-red-700 text-sm font-medium">
                            Disabled
                        </span>

                    @endif

                </div>

                <div class="border-t mt-6 pt-6">

                    @if($user->two_factor_enabled)

                        <div class="mb-6">

                            <h4 class="font-medium text-gray-800">
                                Current OTP Status
                            </h4>

                            @if($user->hasActiveTwoFactorCode())

                                <p class="text-green-600 mt-2">
                                    ● An active OTP is currently available.
                                </p>

                                <p class="text-sm text-gray-500 mt-1">
                                    Expires:
                                    {{ $user->two_factor_expires_at->format('d M Y, h:i A') }}
                                </p>

                            @else

                                <p class="text-gray-500 mt-2">
                                    No active OTP.
                                </p>

                            @endif

                        </div>

                        <form method="POST"
                              action="{{ route('security.disable') }}">

                            @csrf

                            <div class="mb-4">

                                <label class="block text-sm font-medium text-gray-700">
                                    Confirm Password
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    placeholder="Enter your current password"
                                >

                                @error('password')
                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            <button
                                type="submit"
                                class="px-5 py-2.5 bg-red-600 text-white rounded-md
                                       hover:bg-red-700"
                            >
                                Disable Two-Factor Authentication
                            </button>

                        </form>

                    @else

                        <form method="POST"
                              action="{{ route('security.enable') }}">

                            @csrf

                            <p class="text-gray-600 mb-4">
                                Two-factor authentication is currently disabled.
                                Enable it to require an OTP after login.
                            </p>

                            <button
                                type="submit"
                                class="px-5 py-2.5 bg-green-600 text-white rounded-md
                                       hover:bg-green-700"
                            >
                                Enable Two-Factor Authentication
                            </button>

                        </form>

                    @endif

                </div>

            </div>

            <!-- Security Information -->

            <div class="bg-white shadow-sm rounded-lg p-6">

                <h3 class="text-lg font-semibold text-gray-800 mb-5">
                    Security Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="border rounded-lg p-4">

                        <p class="text-sm text-gray-500">
                            Last OTP Generated
                        </p>

                        <p class="font-medium text-gray-800 mt-2">

                            @if($lastOtp)
                                {{ $lastOtp->created_at->format('d M Y, h:i A') }}
                            @else
                                No record
                            @endif

                        </p>

                    </div>

                    <div class="border rounded-lg p-4">

                        <p class="text-sm text-gray-500">
                            Last Successful Verification
                        </p>

                        <p class="font-medium text-gray-800 mt-2">

                            @if($lastVerification)
                                {{ $lastVerification->created_at->format('d M Y, h:i A') }}
                            @else
                                No successful verification yet
                            @endif

                        </p>

                    </div>

                </div>

                <div class="mt-6">

                    <a
                        href="{{ route('security.dashboard') }}"
                        class="inline-block px-5 py-2.5 bg-indigo-600 text-white rounded-md
                               hover:bg-indigo-700"
                    >
                        View Security Activity
                    </a>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>