@php
    $phone = config('clinic.phone');
    $email = config('clinic.email');
    $whatsapp = config('clinic.whatsapp');
    $whatsappDigits = $whatsapp ? preg_replace('/\D+/', '', $whatsapp) : '';
@endphp

<dl class="contact-band__details">
    <div>
        <dt>{{ __('web.home.contact_address') }}</dt>
        <dd>{{ config('clinic.address') ?: __('web.home.contact_address_value') }}</dd>
    </div>
    <div>
        <dt>{{ __('web.home.contact_hours') }}</dt>
        <dd>{{ config('clinic.hours') ?: __('web.home.contact_hours_value') }}</dd>
    </div>
    <div>
        <dt>{{ __('web.home.contact_phone') }}</dt>
        <dd>
            @if($phone)
                <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a>
            @else
                {{ __('web.home.contact_phone') }}
            @endif
        </dd>
    </div>
    <div>
        <dt>{{ __('web.home.contact_email') }}</dt>
        <dd>
            @if($email)
                <a href="mailto:{{ $email }}">{{ $email }}</a>
            @endif
        </dd>
    </div>
    @if($whatsappDigits)
        <div>
            <dt>{{ __('web.home.contact_whatsapp') }}</dt>
            <dd>
                <a class="contact-whatsapp-link" href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer">
                    @include('web.components.whatsapp-icon', ['class' => 'contact-whatsapp-link__icon'])
                    {{ $whatsapp }}
                </a>
            </dd>
        </div>
    @endif
</dl>
