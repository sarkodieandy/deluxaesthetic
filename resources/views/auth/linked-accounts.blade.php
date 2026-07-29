<x-app-layout>
    <div class="max-w-xl mx-auto py-10 px-4">
        <h1 class="text-xl font-semibold mb-4">{{ __('Linked accounts') }}</h1>
        @if (session('status'))
            <p class="mb-4 text-green-700">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <ul class="mb-4 text-red-600 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        <p class="mb-4">Google: {{ $googleLinked ? __('Linked') : __('Not linked') }}</p>
        @if (! $googleLinked)
            <form method="POST" action="{{ route('account.google.link') }}">@csrf
                <button type="submit" class="btn btn-primary">{{ __('auth.google.continue') }}</button>
            </form>
        @else
            <form method="POST" action="{{ route('account.google.unlink') }}">@csrf @method('DELETE')
                @if (auth()->user()->hasUsablePassword())
                    <input class="field mb-3" type="password" name="password" placeholder="{{ __('Password') }}" required>
                @endif
                <button type="submit" class="btn btn-secondary">{{ __('Unlink Google') }}</button>
            </form>
        @endif
    </div>
</x-app-layout>
