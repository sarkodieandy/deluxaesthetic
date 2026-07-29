<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><title>Receipt {{ $payment->reference }}</title></head>
<body style="font-family: sans-serif; padding: 2rem;">
<h1>Payment receipt</h1>
<p>Reference: {{ $payment->reference }}</p>
<p>Amount: GHS {{ number_format((float) $payment->amount, 2) }}</p>
<p>Status: {{ ucfirst($payment->status) }}</p>
<p>Course: {{ $enrolment->course?->name }}</p>
</body></html>
