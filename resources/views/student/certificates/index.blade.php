@extends('student.layouts.app')

@section('title', __('student.certificates.title'))
@section('heading', __('student.certificates.title'))
@section('eyebrow', 'Student portal')

@section('content')
<div class="student-panel mb-6">
    <p class="font-display text-2xl mb-2">{{ __('student.certificates.title') }}</p>
    <p class="text-[var(--color-soft-grey)]">{{ __('student.certificates.intro') }}</p>
</div>

@if ($certificates->isEmpty())
    <div class="student-panel">
        <p class="text-[var(--color-soft-grey)]">{{ __('student.certificates.empty') }}</p>
    </div>
@else
    <div class="student-panel student-table-wrap">
        <table class="student-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Certificate no.</th>
                    <th>Issued</th>
                    <th>{{ __('student.certificates.verification') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($certificates as $certificate)
                    <tr>
                        <td>{{ $certificate->course_name ?: $certificate->course?->name }}</td>
                        <td>{{ $certificate->number }}</td>
                        <td>{{ $certificate->issued_at?->format('d M Y') ?? $certificate->completion_date?->format('d M Y') }}</td>
                        <td><code>{{ $certificate->verification_code }}</code></td>
                        <td class="text-right whitespace-nowrap">
                            @if ($certificate->isDownloadable() || $certificate->isIssued())
                                <a href="{{ route('student.certificates.download', $certificate) }}" class="student-action">{{ __('student.certificates.download') }}</a>
                            @else
                                <span class="text-[var(--color-soft-grey)] text-sm">{{ __('student.certificates.unavailable') }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
