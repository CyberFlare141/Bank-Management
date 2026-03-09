<x-guest-layout>
    <div class="mx-auto mt-10 max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow">
        <h1 class="text-2xl font-semibold text-slate-900">Admin Login</h1>
        <p class="mt-2 text-sm text-slate-500">Sign in to review and accept requests.</p>

        <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>Remember me</span>
            </label>

            <x-primary-button class="w-full justify-center">
                {{ __('Admin Login') }}
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>
