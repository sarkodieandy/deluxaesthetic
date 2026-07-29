@extends('student.layouts.app')
@section('title', __('student.nav.materials'))
@section('heading', __('student.nav.materials'))
@section('content')
<div class="student-panel space-y-4">
    @forelse($materials as $material)
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[var(--color-border)] pb-4 last:border-0 last:pb-0">
            <div class="min-w-0">
                <p class="font-medium">{{ $material->title }}</p>
                @if($material->description)
                    <p class="text-sm text-[var(--color-soft-grey)]">{{ $material->description }}</p>
                @endif
            </div>
            @if($material->file_path)
                <a class="student-action shrink-0" href="{{ route('student.materials.download', $material) }}">Download</a>
            @endif
        </div>
    @empty
        <p class="text-[var(--color-soft-grey)]">No published materials yet.</p>
    @endforelse
</div>
@endsection
