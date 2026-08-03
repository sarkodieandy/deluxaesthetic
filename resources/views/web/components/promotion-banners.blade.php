<section class="promotion-stack" aria-label="Current promotions">
    @foreach($promotions as $promotion)
        <article class="promotion-banner{{ $promotion->imageUrl() ? ' promotion-banner--image' : '' }}" style="--promo-bg:{{ $promotion->background_color }};--promo-text:{{ $promotion->text_color }}">
            @if($promotion->imageUrl())
                <picture>
                    @if($promotion->mobileImageUrl())<source media="(max-width: 639px)" srcset="{{ $promotion->mobileImageUrl() }}">@endif
                    <img src="{{ $promotion->imageUrl() }}" alt="" loading="eager">
                </picture>
                <span class="promotion-banner__veil"></span>
            @endif
            <div class="container-site promotion-banner__content">
                <div><p>{{ $promotion->title }}</p>@if($promotion->subtitle)<span>{{ $promotion->subtitle }}</span>@endif</div>
                @if($promotion->coupon_code)<strong>Use code {{ $promotion->coupon_code }}</strong>@endif
                @if($promotion->cta_label && $promotion->cta_url)<a href="{{ $promotion->cta_url }}">{{ $promotion->cta_label }} <span aria-hidden="true">→</span></a>@endif
            </div>
        </article>
    @endforeach
</section>
