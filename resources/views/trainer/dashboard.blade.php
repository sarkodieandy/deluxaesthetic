<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trainer — {{ config('clinic.name') }}</title>
    @vite(['resources/css/admin/admin.css'])
</head>
<body class="admin-body p-8">
    <h1 class="font-display text-4xl mb-4">Trainer workspace</h1>
    <p class="mb-6">Hello, {{ $user->name }}. Attendance and assessment tools continue in Phase 4.</p>
    <a href="{{ route('web.academy.index') }}">Academy</a>
</body>
</html>
