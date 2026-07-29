<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academy\StoreCertificateRequest;
use App\Http\Requests\Admin\Academy\UpdateCertificateRequest;
use App\Models\Certificate;
use App\Models\Enrolment;
use App\Services\Academy\CertificateIssuanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    public function __construct(
        private readonly CertificateIssuanceService $certificates,
    ) {}

    public function index(): View
    {
        $certificates = Certificate::query()
            ->latest('created_at')
            ->paginate(20);

        return view('admin.certificates.index', compact('certificates'));
    }

    public function create(): View
    {
        $enrolments = Enrolment::query()
            ->with(['studentProfile.user', 'course'])
            ->whereIn('status', ['completed', 'in_progress', 'confirmed'])
            ->whereDoesntHave('certificates', fn ($query) => $query->where('status', 'issued'))
            ->orderByDesc('updated_at')
            ->get();

        return view('admin.certificates.create', [
            'enrolments' => $enrolments,
            'defaultSignatory' => config('clinic.ceo.name'),
        ]);
    }

    public function store(StoreCertificateRequest $request): RedirectResponse
    {
        $enrolment = Enrolment::query()->findOrFail($request->integer('enrolment_id'));

        try {
            $certificate = $this->certificates->createForEnrolment($enrolment, [
                'completion_date' => $request->date('completion_date')->toDateString(),
                'signatory' => $request->input('signatory'),
                'issue' => $request->boolean('issue_now', true),
            ], $request->user());
        } catch (\InvalidArgumentException $exception) {
            return back()->withInput()->withErrors(['enrolment_id' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.certificates.edit', $certificate)
            ->with('status', $certificate->isIssued()
                ? 'Certificate created and ready for download.'
                : 'Certificate saved as draft.');
    }

    public function edit(Certificate $certificate): View
    {
        return view('admin.certificates.edit', compact('certificate'));
    }

    public function update(UpdateCertificateRequest $request, Certificate $certificate): RedirectResponse
    {
        $data = $request->validated();
        $from = $certificate->status;

        if ($data['status'] === 'issued' && ! $certificate->isIssued()) {
            try {
                $certificate = $this->certificates->issueExisting($certificate, $request->user());
            } catch (\InvalidArgumentException $exception) {
                return back()->withErrors(['status' => $exception->getMessage()]);
            }

            if (($data['signatory'] ?? '') !== '') {
                $certificate->update(['signatory' => $data['signatory']]);
                $this->certificates->writePdf($certificate->fresh());
            }

            return redirect()->route('admin.certificates.edit', $certificate)->with('status', 'Certificate issued successfully.');
        }

        DB::transaction(function () use ($data, $certificate, $from, $request) {
            $certificate->update([
                'status' => $data['status'],
                'signatory' => $data['signatory'] ?: null,
                'issued_at' => $data['status'] === 'issued' ? ($certificate->issued_at ?? now()) : null,
                'revoked_at' => $data['status'] === 'revoked' ? now() : null,
            ]);

            if ($from !== $data['status']) {
                DB::table('certificate_status_histories')->insert([
                    'certificate_id' => $certificate->id,
                    'from_status' => $from,
                    'to_status' => $data['status'],
                    'changed_by' => $request->user()?->id,
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        if ($certificate->fresh()->isIssued() && ($data['signatory'] ?? '') !== ($certificate->signatory ?? '')) {
            $this->certificates->writePdf($certificate->fresh());
        }

        return redirect()->route('admin.certificates.edit', $certificate)->with('status', 'Certificate updated successfully.');
    }

    public function download(Certificate $certificate): StreamedResponse
    {
        abort_unless(auth()->user()?->can('certificates.view'), 403);
        abort_unless($certificate->isDownloadable(), 404);

        return Storage::disk('public')->download(
            $certificate->pdf_path,
            $certificate->downloadFilename(),
            ['Content-Type' => 'application/pdf']
        );
    }
}
