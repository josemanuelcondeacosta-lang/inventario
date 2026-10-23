<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-texto">Verifica tu correo</h1>
        <p class="mt-2 text-sm text-tenue">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <x-alert variant="success" class="mb-4">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </x-alert>
    @endif

    <div class="flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                Reenviar correo de verificación
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-tenue hover:text-suave">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
