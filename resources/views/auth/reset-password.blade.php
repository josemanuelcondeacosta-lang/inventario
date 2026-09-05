<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-slate-900">Restablecer contraseña</h1>
        <p class="mt-1 text-sm text-slate-500">Crea una nueva contraseña para tu cuenta.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Nueva contraseña')" />
            <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />

            <x-text-input id="password_confirmation" class="block w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button class="w-full justify-center">
            Restablecer contraseña
        </x-primary-button>
    </form>
</x-guest-layout>
