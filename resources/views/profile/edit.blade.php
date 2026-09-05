<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-800">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <div class="card card-body">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card card-body">
            @include('profile.partials.update-password-form')
        </div>

        <div class="card card-body">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
