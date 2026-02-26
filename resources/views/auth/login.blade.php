<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="text-center mb-6">
                <a href="{{ url('/') }}">
                    <x-application-logo class="w-20 h-20 mx-auto text-gray-700" />
                </a>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">{{ __('Sign in to your account') }}</h2>
                <p class="mt-2 text-sm text-gray-600">{{ __('Welcome back — please enter your details to continue.') }}</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="bg-white shadow rounded-lg p-6 space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                    <x-text-input id="email" class="mt-1 block w-full px-3 py-2" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                    <x-text-input id="password" class="mt-1 block w-full px-3 py-2" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm">
                        <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                        <span class="ml-2 text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-500">{{ __('Forgot your password?') }}</a>
                    @endif
                </div>

                <div>
                    <x-primary-button class="w-full py-2">{{ __('Log in') }}</x-primary-button>
                </div>

                <p class="text-center text-sm text-gray-600">{{ "Don't have an account?" }} <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-500 font-medium">{{ __('Create one') }}</a></p>
            </form>
        </div>
    </div>
</x-guest-layout>
