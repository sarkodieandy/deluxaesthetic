@foreach(collect($sections)->where('is_enabled', true) as $section)
    <section class="cms-content-section">
        <div class="container-site cms-content-section__grid{{ empty($section['image_url']) ? ' cms-content-section__grid--copy' : '' }}">
            <div class="cms-content-section__copy reveal">
                @if(!empty($section['eyebrow']))<p class="text-label">{{ $section['eyebrow'] }}</p>@endif
                @if(!empty($section['heading']))<h2>{{ $section['heading'] }}</h2>@endif
                @if(!empty($section['body']))<div class="cms-content-section__body">{!! nl2br(e($section['body'])) !!}</div>@endif
                @if(!empty($section['cta_label']) && !empty($section['cta_url']))<a href="{{ $section['cta_url'] }}" class="btn btn-primary">{{ $section['cta_label'] }}</a>@endif
            </div>
            @if(!empty($section['image_url']))
                <div class="cms-content-section__media reveal reveal-delay-2"><img src="{{ $section['image_url'] }}" alt="{{ $section['heading'] ?? $cmsPage->name }}" loading="lazy"></div>
            @endif
        </div>
    </section>
@endforeach
