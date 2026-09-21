@extends('web.layouts.app')
@section('title', $product->name.' — '.config('clinic.name'))
@section('meta_description', $product->seo_description ?: \Illuminate\Support\Str::limit(strip_tags($product->description), 155, ''))
@section('meta_image', $product->imageUrl() ?: asset(config('seo.default_image')))
@section('meta_image_alt', $product->name)
@section('og_type', 'product')
@push('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product->name,
    'description' => $product->seo_description ?: strip_tags($product->description),
    'image' => array_values(array_filter([$product->imageUrl()])),
    'sku' => $product->sku,
    'category' => $product->category?->name,
    'offers' => [
        '@type' => 'Offer',
        'url' => route('web.store.show', $product->slug),
        'priceCurrency' => config('clinic.currency'),
        'price' => $product->effectivePrice(),
        'availability' => $product->isPurchasable()
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock',
        'itemCondition' => 'https://schema.org/NewCondition',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
@section('content')
@php
    $productMedia = $product->images
        ->filter(fn ($image) => $image->imageUrl())
        ->values();
@endphp

<section class="product-v3">
    <div class="container-site">
        <nav class="product-v3__breadcrumbs reveal" aria-label="Breadcrumb">
            <a href="{{ route('web.store.index') }}">Store</a>
            <span aria-hidden="true">/</span>
            @if($product->category)
                <a href="{{ route('web.store.index', ['category' => $product->category->id]) }}">{{ $product->category->name }}</a>
                <span aria-hidden="true">/</span>
            @endif
            <span>{{ $product->name }}</span>
        </nav>

        <div class="product-v3__layout">
            <div class="product-v3__gallery reveal" x-data="{ active: 0 }">
                <div class="product-v3__media-stage">
                    @if($productMedia->isNotEmpty())
                        @foreach($productMedia as $image)
                            <figure
                                class="product-v3__media"
                                x-show="active === {{ $loop->index }}"
                                @if(! $loop->first) x-cloak @endif
                                x-transition.opacity.duration.500ms
                            >
                                <img
                                    src="{{ $image->imageUrl() }}"
                                    alt="{{ $image->alt_text ?: $product->name }}"
                                    width="1200"
                                    height="1500"
                                    @if($loop->first) fetchpriority="high" @else loading="lazy" @endif
                                >
                            </figure>
                        @endforeach
                    @else
                        <div class="product-v3__placeholder">
                            <span>De Luxe essentials</span>
                            <strong>{{ $product->name }}</strong>
                            <small>Product photography coming soon</small>
                        </div>
                    @endif

                    <div class="product-v3__media-badges">
                        @if($product->sale_price)<span>Sale edit</span>@endif
                        @if($product->is_featured)<span>Clinic selected</span>@endif
                    </div>
                    <span class="product-v3__media-number">{{ str_pad((string) max(1, $productMedia->count()), 2, '0', STR_PAD_LEFT) }}</span>
                </div>

                @if($productMedia->count() > 1)
                    <div class="product-v3__thumbnails" aria-label="Product photographs">
                        @foreach($productMedia as $image)
                            <button type="button" @click="active = {{ $loop->index }}" :class="{ 'is-active': active === {{ $loop->index }} }" aria-label="View product image {{ $loop->iteration }}">
                                <img src="{{ $image->imageUrl() }}" alt="" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <article class="product-v3__details reveal reveal-delay-2">
                <div class="product-v3__heading">
                    <p class="text-label">{{ $product->category?->name ?? 'De Luxe collection' }}</p>
                    <h1>{{ $product->name }}</h1>
                    <div class="product-v3__price">
                        <strong>GHS {{ number_format((float) $product->effectivePrice(), 2) }}</strong>
                        @if($product->sale_price)
                            <del>GHS {{ number_format((float) $product->price, 2) }}</del>
                        @endif
                    </div>
                </div>

                <div class="product-v3__availability">
                    <span @class(['is-unavailable' => ! $product->isPurchasable()])></span>
                    <p>{{ $product->isPurchasable() ? 'In stock and ready to order' : 'Currently unavailable' }}</p>
                    @if(! empty($inCart))<strong>Already in cart</strong>@endif
                </div>

                @if($product->description)
                    <p class="product-v3__description">{{ $product->description }}</p>
                @endif

                @if ($errors->any())
                    <div class="product-v3__error" role="alert">{{ $errors->first() }}</div>
                @endif

                @if($product->isPurchasable())
                    <form method="POST" action="{{ route('web.cart.store') }}" class="product-v3__purchase" x-data="{ quantity: 1, maximum: {{ (int) $product->stock_quantity }} }">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="product-v3__quantity">
                            <label for="quantity">Quantity</label>
                            <div>
                                <button type="button" @click="quantity = Math.max(1, quantity - 1)" aria-label="Decrease quantity">−</button>
                                <input id="quantity" type="number" name="quantity" x-model.number="quantity" min="1" max="{{ $product->stock_quantity }}" inputmode="numeric">
                                <button type="button" @click="quantity = Math.min(maximum, quantity + 1)" aria-label="Increase quantity">+</button>
                            </div>
                        </div>
                        <button type="submit" class="product-v3__add">Add to cart <span aria-hidden="true">+</span></button>
                        @if(\App\Support\WhatsAppOrder::enabled())
                            <button type="submit" name="buy_now" value="1" class="product-v3__buy product-v3__buy--whatsapp">
                                @include('web.components.whatsapp-icon', ['class' => 'product-v3__whatsapp-icon'])
                                <span>Order on WhatsApp</span>
                                <b aria-hidden="true">→</b>
                            </button>
                        @else
                            <button type="submit" name="buy_now" value="1" class="product-v3__buy">
                                <span>Buy now</span>
                                <b aria-hidden="true">→</b>
                            </button>
                        @endif
                        <a href="{{ route('web.cart.index') }}" class="product-v3__cart-link">View cart <span aria-hidden="true">→</span></a>
                    </form>
                @endif

                <div class="product-v3__assurance">
                    @if($product->delivery_eligible)
                        <div><span aria-hidden="true">01</span><p><strong>Delivery available</strong><small>Confirm your location during checkout.</small></p></div>
                    @endif
                    @if($product->pickup_eligible)
                        <div><span aria-hidden="true">02</span><p><strong>Clinic pickup</strong><small>Collect directly from our Accra clinic.</small></p></div>
                    @endif
                    <div><span aria-hidden="true">03</span><p><strong>Expert selected</strong><small>Chosen to complement professional care.</small></p></div>
                </div>

                @if(\App\Support\WhatsAppOrder::enabled())
                    <p class="product-v3__checkout-note">You’ll chat directly with {{ config('clinic.ceo.name') }} to confirm availability, delivery and payment.</p>
                @else
                    <p class="product-v3__checkout-note">Choose delivery or clinic pickup and pay securely at checkout.</p>
                @endif

                @if($product->usage_instructions || $product->ingredients)
                    <div class="product-v3__information" x-data="{ open: 'usage' }">
                        @if($product->usage_instructions)
                            <section>
                                <button type="button" @click="open = open === 'usage' ? '' : 'usage'" :aria-expanded="open === 'usage'">
                                    <span>How to use</span><i x-text="open === 'usage' ? '−' : '+'">−</i>
                                </button>
                                <div x-show="open === 'usage'" x-transition.opacity.duration.250ms>
                                    <p>{{ $product->usage_instructions }}</p>
                                </div>
                            </section>
                        @endif
                        @if($product->ingredients)
                            <section>
                                <button type="button" @click="open = open === 'ingredients' ? '' : 'ingredients'" :aria-expanded="open === 'ingredients'">
                                    <span>Ingredients</span><i x-text="open === 'ingredients' ? '−' : '+'">+</i>
                                </button>
                                <div x-show="open === 'ingredients'" x-transition.opacity.duration.250ms x-cloak>
                                    <p>{{ $product->ingredients }}</p>
                                </div>
                            </section>
                        @endif
                    </div>
                @endif
            </article>
        </div>
    </div>
</section>

@if($related->isNotEmpty())
    <section class="product-v3-related">
        <div class="container-site">
            <header class="product-v3-related__header reveal">
                <div><p class="text-label">Continue your ritual</p><h2>Related products</h2></div>
                <a href="{{ route('web.store.index') }}">View all products <span aria-hidden="true">→</span></a>
            </header>
            <div class="product-v3-related__grid">
                @foreach($related as $item)
                    <a href="{{ route('web.store.show', $item->slug) }}" class="product-v3-related__card reveal" style="--reveal-delay: {{ $loop->index * 80 }}ms">
                        <div>
                            @if($item->imageUrl())
                                <img src="{{ $item->imageUrl() }}" alt="{{ $item->name }}" loading="lazy">
                            @else
                                <span>{{ $item->category?->name ?? 'De Luxe' }}</span>
                            @endif
                            <i aria-hidden="true">↗</i>
                        </div>
                        <p>{{ $item->category?->name }}</p>
                        <h3>{{ $item->name }}</h3>
                        <strong>GHS {{ number_format((float) $item->effectivePrice(), 2) }}</strong>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
