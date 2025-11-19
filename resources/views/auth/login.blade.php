<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
<<<<<<< HEAD
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
=======
            <x-input-label for="email" :value="__('Email')" icon="fas fa-envelope" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" :placeholder="__('Nhập địa chỉ email')" />
>>>>>>> upstream-main
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
<<<<<<< HEAD
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
=======
            <x-input-label for="password" :value="__('Password')" icon="fas fa-lock" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" :placeholder="__('Nhập mật khẩu')" />
>>>>>>> upstream-main

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
<<<<<<< HEAD
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
=======
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
>>>>>>> upstream-main
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
<<<<<<< HEAD
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
=======
            @if (Route::has('register'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('register') }}">
                    {{ __('Chưa có tài khoản?') }}
                </a>
            @endif

            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-3"
                    href="{{ route('password.request') }}">
>>>>>>> upstream-main
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
<<<<<<< HEAD
=======

    <hr class="w-48 mx-auto my-6 border border-gray-300">
    <p class="text-sm text-gray-600 text-center mb-6">{{ __('Hoặc đăng nhập bằng') }}</p>

    <div class="flex justify-center gap-3">
        <x-secondary-button id="google-login">
            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google"
                class="w-6 h-6">
            <span class="text-gray-600">Google</span>
        </x-secondary-button>
        <x-secondary-button id="facebook-login">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook"
                class="w-6 h-6">
            <span class="text-gray-600">Facebook</span>
        </x-secondary-button>
        <x-secondary-button id="github-login">
            <img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png" alt="GitHub"
                class="w-6 h-6">
            <span class="text-gray-600">GitHub</span>
        </x-secondary-button>
    </div>

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const login = (provider) => {
                    const baseUrl = '{{ url('/auth') }}';

                    fetch(`${baseUrl}/${provider}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.socialite_url) {
                                window.location.href = data.socialite_url;
                            } else {
                                alert('Không lấy được URL login');
                            }
                        })
                        .catch(err => console.error('Lỗi:', err));
                }

                ['google-login', 'facebook-login', 'github-login'].forEach(id => {
                    const btn = document.getElementById(id);
                    if (btn) {
                        btn.addEventListener('click', () => login(id.split('-')[0]));
                    }
                });
            });
        </script>
    @endPushOnce
>>>>>>> upstream-main
</x-guest-layout>
