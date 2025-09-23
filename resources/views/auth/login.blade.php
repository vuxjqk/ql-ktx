<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <hr class="my-6 mx-auto w-48 border-t border-gray-300">
    <p class="text-center text-sm text-gray-600">Hoặc đăng nhập bằng</p>

    <div class="flex justify-center gap-6 mt-6">
        <a href="{{ route('auth.redirect', 'google') }}"
            class="inline-flex items-center px-4 py-2 gap-2 border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-100 focus:bg-gray-100 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google"
                class="w-6 h-6">
            <span class="text-gray-600">Google</span>
        </a>
        <a href="{{ route('auth.redirect', 'facebook') }}"
            class="inline-flex items-center px-4 py-2 gap-2 border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-100 focus:bg-gray-100 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook"
                class="w-6 h-6">
            <span class="text-gray-600">Facebook</span>
        </a>
        <a href="{{ route('auth.redirect', 'github') }}"
            class="inline-flex items-center px-4 py-2 gap-2 border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-100 focus:bg-gray-100 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png" alt="GitHub"
                class="w-6 h-6">
            <span class="text-gray-600">GitHub</span>
        </a>
    </div>
</x-guest-layout>
