<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
<<<<<<< HEAD
=======
            <i class="fas fa-info-circle text-blue-600 me-1"></i>
>>>>>>> upstream-main
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

<<<<<<< HEAD
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
=======
    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
>>>>>>> upstream-main
        @csrf
        @method('patch')

        <div>
<<<<<<< HEAD
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
=======
            <x-input-label for="name" :value="__('Họ tên')" icon="fas fa-user" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)"
                required autofocus autocomplete="name" :placeholder="__('Nhập họ tên')" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" icon="fas fa-envelope" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)"
                required autocomplete="email" :placeholder="__('Nhập địa chỉ email')" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
>>>>>>> upstream-main
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

<<<<<<< HEAD
                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
=======
                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
>>>>>>> upstream-main
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

<<<<<<< HEAD
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
=======
        <div>
            <x-input-label for="avatar" :value="__('Ảnh đại diện mới (nếu muốn đổi)')" icon="fas fa-image" />
            <x-file-input id="avatar" class="block mt-1 w-full" name="avatar" />
            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                <i class="fas fa-save"></i>
                {{ __('Cập nhật') }}
            </x-primary-button>
>>>>>>> upstream-main
        </div>
    </form>
</section>
