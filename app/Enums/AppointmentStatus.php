<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case AwaitingPayment = 'awaiting_payment';
    case Confirmed = 'confirmed';
    case Rescheduled = 'rescheduled';
    case CheckedIn = 'checked_in';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';
    case RefundRequested = 'refund_requested';
    case Refunded = 'refunded';
}
