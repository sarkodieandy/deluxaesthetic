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
        $enrolment = $this->portal->primaryEnrolment($user);

        if (! $enrolment) {
            return $this->portal->viewOrNoEnrolment($user, 'student.payments.index', [
                'title' => __('student.nav.payments'),
                'heading' => __('student.nav.payments'),
            ]);
        }

        return view('student.payments.index', [
            'enrolment' => $enrolment,
            'instalments' => $this->portal->instalmentPlans($enrolment),
            'payments' => $this->portal->paymentsForEnrolment($enrolment),
            'onlinePaymentEnabled' => config('academy.online_balance_payment_enabled'),
        ]);
    }

    public function receipt(Request $request, int $payment): Response
    {
        $enrolment = $this->portal->primaryEnrolment($request->user());
        $record = DB::table('payments')->where('id', $payment)->first();

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
