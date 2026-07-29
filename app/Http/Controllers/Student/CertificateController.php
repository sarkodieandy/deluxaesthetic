<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\Academy\CertificateIssuanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    public function __construct(
        private readonly CertificateIssuanceService $issuer,
    ) {}

    public function index(Request $request): View
    {
        $profileId = $request->user()->studentProfile?->id;

        $certificates = Certificate::query()
            ->with('course')
            ->when($profileId, fn ($query) => $query->where('student_profile_id', $profileId))
            ->where('status', 'issued')
            ->latest('issued_at')
            ->get();

        return view('student.certificates.index', [
            'certificates' => $certificates,
        ]);
    }

    public function download(Request $request, Certificate $certificate): StreamedResponse
    {
        $profileId = $request->user()->studentProfile?->id;

        abort_unless($profileId && (int) $certificate->student_profile_id === (int) $profileId, 403);
        abort_unless($certificate->isIssued(), 404);

        if (! $certificate->isDownloadable()) {
            $this->issuer->writePdf($certificate->fresh());
            $certificate->refresh();
        }

        abort_unless($certificate->isDownloadable(), 404);

        return Storage::disk('public')->download(
            $certificate->pdf_path,
            $certificate->downloadFilename(),
            ['Content-Type' => 'application/pdf']
        );
    }
}
