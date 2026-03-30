@extends('layouts.app')

@section('title', 'Profil')

@section('content')

    <div class="space-y-6">
        <div class="bg-surface rounded-2xl border border-theme-subtle p-5 sm:p-6">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="bg-surface rounded-2xl border border-theme-subtle p-5 sm:p-6">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="bg-surface rounded-2xl border border-theme-subtle p-5 sm:p-6">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

@endsection
