<x-guest-layout>
    <div class="mb-4 text-sm text-gray-500">
        {{ __('Mot de passe oublié ? Pas de problème. Indiquez-nous simplement votre adresse e-mail et nous vous enverrons un lien de réinitialisation qui vous permettra d\'en choisir un nouveau.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('Adresse e-mail') }}</label>
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <x-primary-button>
                {{ __('Envoyer le lien de réinitialisation') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
