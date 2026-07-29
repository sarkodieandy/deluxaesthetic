<?php

namespace App\Services\Academy;

use App\Models\Certificate;
use App\Models\Enrolment;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateIssuanceService
{
    /**
     * @param  array{completion_date: string, signatory?: string|null, issue?: bool}  $data
     */
    public function createForEnrolment(Enrolment $enrolment, array $data, ?User $issuedBy = null): Certificate
    {
        $enrolment->loadMissing(['studentProfile.user', 'course']);

        if ($enrolment->hasIssuedCertificate()) {
            throw new \InvalidArgumentException('This enrolment already has an issued certificate.');
        }

        $studentName = $enrolment->studentProfile?->user?->name ?? 'Student';
        $courseName = $enrolment->course?->name ?? 'Course';
        $signatory = ($data['signatory'] ?? null) ?: config('clinic.ceo.name');
        $issue = $data['issue'] ?? true;

        return DB::transaction(function () use ($enrolment, $data, $issuedBy, $studentName, $courseName, $signatory, $issue) {
            $certificate = Certificate::create([
                'number' => $this->nextCertificateNumber(),
                'enrolment_id' => $enrolment->id,
                'student_profile_id' => $enrolment->student_profile_id,
                'course_id' => $enrolment->course_id,
                'trainer_profile_id' => $enrolment->course?->trainer_profile_id,
                'student_name' => $studentName,
                'course_name' => $courseName,
                'completion_date' => $data['completion_date'],
                'signatory' => $signatory,
                'verification_code' => Str::upper(Str::random(10)),
                'status' => $issue ? 'issued' : 'draft',
                'issued_at' => $issue ? now() : null,
            ]);

            if ($issue) {
                $this->writePdf($certificate);
                $this->markEnrolmentCertificateIssued($enrolment, $issuedBy);
                $this->recordStatusChange($certificate, null, 'issued', $issuedBy);
                $this->notifyCertificateIssued($certificate->fresh(), $enrolment);
            }

            return $certificate->fresh();
        });
    }

    public function issueExisting(Certificate $certificate, ?User $issuedBy = null): Certificate
    {
        if ($certificate->status === 'issued') {
            return $certificate;
        }

        if ($certificate->status === 'revoked') {
            throw new \InvalidArgumentException('Revoked certificates cannot be re-issued from this screen.');
        }

        return DB::transaction(function () use ($certificate, $issuedBy) {
            $from = $certificate->status;
            $certificate->loadMissing(['enrolment.studentProfile.user', 'enrolment.course']);

            $certificate->update([
                'status' => 'issued',
                'issued_at' => now(),
                'revoked_at' => null,
            ]);

            $this->writePdf($certificate->fresh());
            $this->markEnrolmentCertificateIssued($certificate->enrolment, $issuedBy);
            $this->recordStatusChange($certificate, $from, 'issued', $issuedBy);
            $this->notifyCertificateIssued($certificate->fresh(), $certificate->enrolment);

            return $certificate->fresh();
        });
    }

    public function writePdf(Certificate $certificate): string
    {
        $html = view('certificates.pdf', [
            'certificate' => $certificate,
            'academyName' => config('academy.name'),
            'clinicName' => config('clinic.name'),
            'ceoTitle' => config('clinic.ceo.title'),
        ])->render();

        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $path = 'certificates/'.$certificate->number.'.pdf';
        Storage::disk('public')->put($path, $dompdf->output());

        $certificate->update(['pdf_path' => $path]);

        return $path;
    }

    private function nextCertificateNumber(): string
    {
        $prefix = config('academy.certificate_prefix', 'DLX');
        $year = now()->format('Y');

        $latest = Certificate::withTrashed()
            ->where('number', 'like', $prefix.'-'.$year.'-%')
            ->orderByDesc('id')
            ->value('number');

        $sequence = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    private function markEnrolmentCertificateIssued(Enrolment $enrolment, ?User $issuedBy): void
    {
        if ($enrolment->status === 'certificate_issued') {
            return;
        }

        $from = $enrolment->status;

        $enrolment->update(['status' => 'certificate_issued']);

        DB::table('enrolment_status_histories')->insert([
            'enrolment_id' => $enrolment->id,
            'from_status' => $from,
            'to_status' => 'certificate_issued',
            'changed_by' => $issuedBy?->id,
            'notes' => 'Certificate issued',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function recordStatusChange(Certificate $certificate, ?string $from, string $to, ?User $user): void
    {
        DB::table('certificate_status_histories')->insert([
            'certificate_id' => $certificate->id,
            'from_status' => $from,
            'to_status' => $to,
            'changed_by' => $user?->id,
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function notifyCertificateIssued(Certificate $certificate, ?Enrolment $enrolment): void
    {
        $enrolment?->loadMissing(['studentProfile.user', 'course']);
        $student = $enrolment?->studentProfile?->user;

        if (! $student) {
            return;
        }

        app(\App\Services\Notifications\InAppNotificationService::class)->notifyUser($student, [
            'title' => 'Certificate issued',
            'message' => 'Your certificate for '.($certificate->course_name ?: $enrolment?->course?->name ?? 'your course').' is ready to download.',
            'action_url' => route('student.certificates.index', absolute: false),
            'category' => 'certificate',
        ]);
    }
}
