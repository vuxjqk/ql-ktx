<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="avatar" value="Ảnh đại diện mới (nếu muốn đổi)" icon="fas fa-image" />
            <label class="inline-block cursor-pointer">
                @if (Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                        class="w-16 h-16 rounded-full object-cover">
                @else
                    <div
                        class="w-16 h-16 rounded-full bg-blue-500 flex items-center justify-center font-bold text-2xl text-white">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                @endif
                <input id="avatar" class="hidden" type="file" name="avatar">
            </label>
            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="name" value="Tên" icon="fas fa-user" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)"
                required autofocus autocomplete="name" placeholder="Nhập tên" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" icon="fas fa-envelope" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)"
                required autocomplete="email" placeholder="Nhập email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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

        <div>
            <x-input-label for="date_of_birth" value="Ngày sinh" icon="fas fa-calendar-alt" />
            <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth"
                :value="old('date_of_birth', $user->date_of_birth?->format('Y-m-d'))" placeholder="Nhập ngày sinh" />
            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="gender" value="Giới tính" icon="fas fa-venus-mars" />
            <x-select id="gender" class="block mt-1 w-full" :options="[
                'male' => 'Nam',
                'female' => 'Nữ',
            ]" name="gender" :selected="old('gender', $user->gender)"
                placeholder="Chọn giới tính" />
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="phone" value="Số điện thoại" icon="fas fa-phone" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone', $user->phone)"
                autocomplete="phone" placeholder="Nhập số điện thoại" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="address" value="Địa chỉ" icon="fas fa-map-marker-alt" />
            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $user->address)"
                autocomplete="address" placeholder="Nhập địa chỉ" />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
