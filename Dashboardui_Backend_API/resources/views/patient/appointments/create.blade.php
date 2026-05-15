@extends('layouts.clinic-portal')

@section('title', get_translation('book_appointment'))

@section('header_links')
    <a href="{{ route_with_lang('patient.dashboard') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">{{ get_translation('dashboard') }}</a>
    <form method="POST" action="{{ route_with_lang('patient.logout') }}">
        @csrf
        <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-sm">{{ get_translation('logout') }}</button>
    </form>
@endsection

@section('content')
    <h1 class="text-2xl font-semibold mb-4">{{ get_translation('book_appointment') }}</h1>

    @if ($errors->any())
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-red-700 text-sm">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route_with_lang('patient.appointments.store') }}" class="bg-white rounded-lg border p-4 space-y-4">
        @csrf

        @if(Auth::user()->isCompanion())
            <div>
                <label class="block text-sm mb-1">{{ get_translation('patient') }}</label>
                <select name="patient_user_id" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
                    <option value="">{{ get_translation('select_patient') }}</option>
                    @foreach($linkedPatients as $patient)
                        <option value="{{ $patient->id }}" @selected(old('patient_user_id') == $patient->id)>{{ $patient->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div>
            <label class="block text-sm mb-1">{{ get_translation('doctor') }}</label>
            <select name="provider_id" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
                <option value="">{{ get_translation('select_provider') }}</option>
                @foreach($providers as $provider)
                    <option value="{{ $provider->id }}" @selected(old('provider_id') == $provider->id)>
                        {{ optional($provider->user)->name }}{{ $provider->title ? ' - '.$provider->title : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm mb-1">{{ get_translation('location') }}</label>
            <select name="location_id" class="w-full rounded-md border border-gray-300 px-3 py-2">
                <option value="">{{ get_translation('select_location_optional') }}</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">{{ get_translation('starts_at') }}</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm mb-1">{{ get_translation('ends_at') }}</label>
                <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">{{ get_translation('patient_notes') }}</label>
            <textarea name="patient_notes" rows="4" class="w-full rounded-md border border-gray-300 px-3 py-2">{{ old('patient_notes') }}</textarea>
        </div>

        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-white">{{ get_translation('submit') }}</button>
    </form>
@endsection
