<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Practitioner — {{ config('clinic.name') }}</title>
    @vite(['resources/css/admin/admin.css'])
</head>
<body class="admin-body p-8">
    <h1 class="font-display text-4xl mb-4">Practitioner workspace</h1>
    <p class="mb-6">Hello, {{ $user->name }}. Schedule and appointment tools continue in Phase 3.</p>
    <a href="{{ route('admin.dashboard') }}">Admin</a>
</body>
</html>
