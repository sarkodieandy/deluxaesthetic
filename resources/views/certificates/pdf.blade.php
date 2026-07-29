<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Certificate {{ $certificate->number }}</title>
    <style>
        @page { margin: 0; }
        body {
            margin: 0;
            font-family: "DejaVu Serif", serif;
            color: #1a1a1a;
            background: #faf8f5;
        }
        .frame {
            border: 3px solid #b8956b;
            margin: 28px;
            padding: 42px 48px;
            min-height: 480px;
            position: relative;
        }
        .inner {
            border: 1px solid #d8c4a8;
            padding: 36px 40px;
            text-align: center;
        }
        .brand {
            font-size: 13px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: #8a7355;
            margin-bottom: 18px;
        }
        h1 {
            font-size: 34px;
            font-weight: normal;
            margin: 0 0 8px;
            letter-spacing: 0.08em;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 28px;
        }
        .student {
            font-size: 28px;
            margin: 18px 0 8px;
            border-bottom: 1px solid #d8c4a8;
            display: inline-block;
            padding-bottom: 6px;
            min-width: 60%;
        }
        .course {
            font-size: 20px;
            margin: 16px 0 24px;
        }
        .meta {
            font-size: 12px;
            color: #555;
            line-height: 1.7;
        }
        .signatory {
            margin-top: 36px;
            font-size: 16px;
        }
        .signatory small {
            display: block;
            font-size: 11px;
            color: #666;
            margin-top: 4px;
        }
        .footer {
            position: absolute;
            left: 48px;
            right: 48px;
            bottom: 36px;
            font-size: 10px;
            color: #777;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="frame">
        <div class="inner">
            <p class="brand">{{ $academyName }}</p>
            <h1>Certificate of Completion</h1>
            <p class="subtitle">This is to certify that</p>
            <p class="student">{{ $certificate->student_name }}</p>
            <p class="subtitle">has successfully completed the course</p>
            <p class="course">{{ $certificate->course_name }}</p>
            <p class="meta">
                Completion date: {{ $certificate->completion_date->format('d F Y') }}<br>
                Certificate no.: {{ $certificate->number }}<br>
                Verification code: {{ $certificate->verification_code }}
            </p>
            <div class="signatory">
                {{ $certificate->signatory }}
                <small>{{ $ceoTitle }}</small>
            </div>
        </div>
        <div class="footer">
            <span>{{ $clinicName }}</span>
            <span>Issued {{ optional($certificate->issued_at)->timezone(config('clinic.timezone'))->format('d M Y') ?? now()->timezone(config('clinic.timezone'))->format('d M Y') }}</span>
        </div>
    </div>
</body>
</html>
