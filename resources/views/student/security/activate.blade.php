<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow"><title>Activate portal</title>@vite(['resources/css/portals/student.css'])</head>
<body class="portal-student p-8"><div class="max-w-md border border-[var(--color-border)] bg-white p-6">
<h1 class="font-display text-2xl mb-4">Activate student portal</h1>
<form method="POST" action="{{ route('student.activate.store', $token) }}">@csrf
<label class="block mb-2">Password</label><input type="password" name="password" class="field w-full mb-3" required>
<label class="block mb-2">Confirm password</label><input type="password" name="password_confirmation" class="field w-full mb-4" required>
<button type="submit" class="student-action">Create password</button>
</form></div></body></html>
