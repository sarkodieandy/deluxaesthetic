<?php

namespace App\Enums;

enum EnrolmentStatus: string
{
    case Enquiry = 'enquiry';
    case ApplicationPending = 'application_pending';
    case AwaitingPhysicalVerification = 'awaiting_physical_verification';
    case AwaitingPayment = 'awaiting_payment';
    case PartiallyPaid = 'partially_paid';
    case Active = 'active';
    case Suspended = 'suspended';
    case OnHold = 'on_hold';
    case Completed = 'completed';
    case Withdrawn = 'withdrawn';
    case Cancelled = 'cancelled';
    case Graduated = 'graduated';
    case CertificateIssued = 'certificate_issued';

    /**
     * @return list<string>
     */
    public static function portalAccessStatuses(): array
    {
        return [
            self::Active->value,
            self::PartiallyPaid->value,
            self::Completed->value,
            self::Graduated->value,
            self::CertificateIssued->value,
            self::Suspended->value,
            self::OnHold->value,
        ];
    }

    /**
     * @return list<string>
     */
    public static function fullMaterialAccessStatuses(): array
    {
        return [
            self::Active->value,
            self::PartiallyPaid->value,
            self::Completed->value,
            self::Graduated->value,
            self::CertificateIssued->value,
        ];
    }
}
