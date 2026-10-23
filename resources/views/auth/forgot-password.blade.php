<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-texto">Recuperar contraseña</h1>
        <p class="mt-2 text-sm text-tenue">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="tucorreo@empresa.com" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <x-primary-button class="w-full justify-center">
            Enviar enlace de recuperación
        </x-primary-button>

        <a href="{{ route('login') }}" class="block text-center text-sm font-medium text-brand hover:text-brand">
            Volver a iniciar sesión
        </a>
    </form>
</x-guest-layout>
