<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-indigo-50 to-blue-100">
        <div class="w-full max-w-md p-8 bg-white shadow-2xl rounded-2xl border border-gray-100">

            <div class="flex justify-center mb-6">
                <div class="p-3 bg-indigo-100 rounded-full">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <h2 class="text-3xl font-extrabold text-center text-gray-900 mb-2">
                Verify OTP
            </h2>

            <p class="mb-8 text-sm text-gray-500 text-center px-4">
                Tamara account ne verify karva mate tamara email par mokel <span class="font-semibold text-gray-700">6-digit OTP</span> niche enter karo.
            </p>

            @if (session('message'))
                <div class="mb-6 p-4 text-sm text-green-800 bg-green-50 rounded-xl border border-green-200 text-center animate-pulse">
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verify.store') }}">
                @csrf

                <div class="space-y-2">
                    <x-input-label for="two_factor_code" :value="__('Security Code')" class="ml-1 text-gray-700" />

                    <x-text-input 
                        id="two_factor_code"
                        class="block w-full text-center text-2xl font-mono tracking-[0.5em] py-3 border-gray-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 rounded-xl transition-all duration-200 ease-in-out"
                        type="text"
                        name="two_factor_code"
                        maxlength="6"
                        placeholder="000000"
                        required
                        autofocus
                    />

                    <x-input-error :messages="$errors->get('two_factor_code')" class="mt-2 text-center" />
                </div>

                <div class="mt-8 flex flex-col space-y-4">
                    <x-primary-button class="w-full justify-center py-3 text-base shadow-lg shadow-indigo-200 transform transition-transform active:scale-95">
                        {{ __('Verify Account') }}
                    </x-primary-button>

                    <div class="text-center">
                        <span class="text-sm text-gray-500">Code nathi malyo?</span>
                        <a class="text-sm text-indigo-600 hover:text-indigo-800 font-bold ml-1 transition-colors duration-200"
                           href="{{ route('verify.resend') }}">
                            Resend OTP
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-guest-layout>