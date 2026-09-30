<x-guest-layout>

    <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-indigo-50 to-blue-100">

        <div class="w-full max-w-md p-8 bg-white shadow-2xl rounded-2xl border border-gray-100">

            <div class="flex justify-center mb-6">

                <div class="p-3 bg-indigo-100 rounded-full">

                    <svg class="w-8 h-8 text-indigo-600"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />

                    </svg>

                </div>

            </div>

            <h2 class="text-3xl font-extrabold text-center text-gray-900 mb-2">
                Verify OTP
            </h2>

            <p class="mb-8 text-sm text-gray-500 text-center px-4">
                Enter the 6-digit verification code sent to your email.
            </p>

            @if(session('message'))

                <div class="mb-6 p-4 text-sm text-green-800 bg-green-50
                            rounded-xl border border-green-200 text-center">

                    {{ session('message') }}

                </div>

            @endif

            @if ($errors->has('otp'))

                <div class="mb-6 p-4 text-sm text-red-800 bg-red-50
                            rounded-xl border border-red-200 text-center">

                    {{ $errors->first('otp') }}

                </div>

            @endif

            @if($user->isTwoFactorLocked())

                <div class="mb-6 p-4 text-sm text-red-800 bg-red-50
                            rounded-xl border border-red-200 text-center">

                    <strong>Verification Locked</strong>

                    <p class="mt-1">
                        Too many failed attempts.
                    </p>

                    <p class="mt-1">
                        Please try again later.
                    </p>

                </div>

            @else

                <form method="POST"
                      action="{{ route('verify.store') }}">

                    @csrf

                    <div class="space-y-2">

                        <x-input-label
                            for="two_factor_code"
                            :value="__('Security Code')"
                            class="ml-1 text-gray-700" />

                        <x-text-input
                            id="two_factor_code"
                            class="block w-full text-center text-2xl font-mono
                                   tracking-[0.5em] py-3 border-gray-300
                                   focus:border-indigo-500 focus:ring-4
                                   focus:ring-indigo-200 rounded-xl
                                   transition-all duration-200"
                            type="text"
                            name="two_factor_code"
                            maxlength="6"
                            minlength="6"
                            pattern="[0-9]{6}"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            placeholder="000000"
                            required
                            autofocus
                            oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,6)" />

                        <x-input-error
                            :messages="$errors->get('two_factor_code')"
                            class="mt-2 text-center" />

                    </div>

                    @if($user->two_factor_expires_at)

                        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200
                                    rounded-lg text-center">

                            <p class="text-xs text-yellow-700">
                                OTP expires at
                            </p>

                            <p class="font-semibold text-yellow-800 mt-1">
                                {{ $user->two_factor_expires_at->format('d M Y, h:i A') }}
                            </p>

                            <p class="text-sm font-bold text-yellow-700 mt-2">
                                Time remaining:
                                <span id="otp-countdown">
                                    Loading...
                                </span>
                            </p>

                        </div>

                    @endif

                    <div class="mt-8 flex flex-col space-y-4">

                        <x-primary-button
                            class="w-full justify-center py-3 text-base
                                   shadow-lg">

                            {{ __('Verify Account') }}

                        </x-primary-button>

                        <div class="text-center">

                            <span class="text-sm text-gray-500">
                                Didn't receive the code?
                            </span>

                            <a
                                id="resend-link"
                                class="text-sm text-indigo-600
                                       hover:text-indigo-800 font-bold ml-1"
                                href="{{ route('verify.resend') }}">

                                Resend OTP

                            </a>

                            <p id="resend-countdown"
                               class="text-xs text-gray-500 mt-2">
                            </p>

                        </div>

                    </div>

                </form>

            @endif

            <div class="mt-6 text-center border-t border-gray-100 pt-5">

                <form method="POST"
                      action="{{ route('logout') }}">

                    @csrf

                    <button
                        type="submit"
                        class="text-sm text-gray-500 hover:text-gray-700">

                        Logout

                    </button>

                </form>

            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            let otpExpiry = @json(
                $user->two_factor_expires_at
                    ? $user->two_factor_expires_at->timestamp * 1000
                    : null
            );

            let resendSeconds = @json($resendCooldown);

            const otpCountdown =
                document.getElementById('otp-countdown');

            const resendLink =
                document.getElementById('resend-link');

            const resendCountdown =
                document.getElementById('resend-countdown');


            function updateOtpCountdown() {

                if (!otpExpiry || !otpCountdown) {
                    return;
                }

                const remaining =
                    Math.max(
                        0,
                        Math.floor(
                            (otpExpiry - Date.now()) / 1000
                        )
                    );

                const minutes =
                    Math.floor(remaining / 60);

                const seconds =
                    remaining % 60;

                otpCountdown.textContent =
                    `${minutes}:${String(seconds).padStart(2, '0')}`;

                if (remaining <= 0) {

                    otpCountdown.textContent =
                        'Expired';

                    otpCountdown.classList.add(
                        'text-red-600'
                    );
                }
            }


            function updateResendCountdown() {

                if (!resendLink || !resendCountdown) {
                    return;
                }

                if (resendSeconds <= 0) {

                    resendLink.classList.remove(
                        'pointer-events-none',
                        'opacity-50'
                    );

                    resendCountdown.textContent = '';

                    return;
                }

                resendLink.classList.add(
                    'pointer-events-none',
                    'opacity-50'
                );

                resendCountdown.textContent =
                    `You can resend OTP in ${resendSeconds} seconds.`;

                resendSeconds--;
            }


            updateOtpCountdown();
            updateResendCountdown();

            setInterval(updateOtpCountdown, 1000);

            setInterval(updateResendCountdown, 1000);

        });
    </script>

</x-guest-layout>