<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrolment;
use App\Services\Student\StudentPortalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function __construct(
        private readonly StudentPortalService $portal,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $enrolments = $this->portal->portalEnrolments($user);
        $enrolment = $this->portal->portalEnrolment($user, $request->input('enrolment'));
        abort_if($request->filled('enrolment') && ! $enrolment, 403);

        if (! $enrolment) {
            return $this->portal->viewOrNoEnrolment($user, 'student.payments.index', [
                'title' => __('student.nav.payments'),
                'heading' => __('student.nav.payments'),
            ]);
        }

        return view('student.payments.index', [
            'enrolment' => $enrolment,
            'enrolments' => $enrolments,
            'instalments' => $this->portal->instalmentPlans($enrolment),
            'payments' => $this->portal->paymentsForEnrolment($enrolment),
            'onlinePaymentEnabled' => config('academy.online_balance_payment_enabled'),
        ]);
    }

    public function receipt(Request $request, int $payment): Response
    {
        $record = DB::table('payments')->where('id', $payment)->first();
        $enrolment = $record && $record->payable_type === Enrolment::class
            ? $this->portal->portalEnrolment($request->user(), (int) $record->payable_id)
            : null;

        abort_unless(
            $enrolment
            && $record
            && $record->payable_type === Enrolment::class
            && (int) $record->payable_id === (int) $enrolment->id,
            403
        );

        return response()->view('student.payments.receipt', [
            'payment' => $record,
            'enrolment' => $enrolment,
        ]);
    }
}
