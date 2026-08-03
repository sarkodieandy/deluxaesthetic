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
<section class="section">
    <div class="container-site">
        <p class="text-sm text-[var(--color-soft-grey)] mb-6">
            <a href="{{ route('web.store.index') }}">Store</a> /
            {{ $product->category?->name }} /
            {{ $product->name }}
        </p>
        <div class="grid gap-10 lg:grid-cols-2">
            <div class="border border-[var(--color-border)] bg-[var(--color-stone)] overflow-hidden">
                @if ($product->imageUrl())
                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="w-full object-cover min-h-[22rem]">
                @else
                    <div class="flex min-h-[22rem] items-center justify-center text-[var(--color-soft-grey)]">Photo coming soon</div>
                @endif
            </div>
            <div>
                <p class="text-label mb-2">{{ $product->category?->name }}</p>
                <h1 class="text-page-title mb-4">{{ $product->name }}</h1>
                @if(! empty($inCart))
                    <p class="mb-4 inline-flex rounded-full bg-red-600 px-3 py-1 text-sm font-semibold text-white">Already in cart</p>
                @endif
                <p class="mb-4 text-xl">
                    @if($product->sale_price)
                        <span class="line-through text-[var(--color-soft-grey)] mr-2">GHS {{ number_format((float) $product->price, 2) }}</span>
                    @endif
                    GHS {{ number_format((float) $product->effectivePrice(), 2) }}
                </p>
                <p class="mb-6 text-[var(--color-soft-grey)]">{{ $product->stock_quantity > 0 ? 'In stock ('.$product->stock_quantity.')' : 'Out of stock' }}</p>
                <p class="mb-8">{{ $product->description }}</p>

                @if ($errors->any())
                    <div class="mb-4 border border-[var(--color-error)] p-4 text-[var(--color-error)]">{{ $errors->first() }}</div>
                @endif

                @if($product->isPurchasable())
                    <form method="POST" action="{{ route('web.cart.store') }}" class="flex flex-wrap items-end gap-3">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div>
                            <label class="text-label mb-2 block" for="quantity">Quantity</label>
                            <input class="field w-24" id="quantity" type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Add to cart</button>
                        <button type="submit" name="buy_now" value="1" class="btn btn-whatsapp">
                            @include('web.components.whatsapp-icon', ['class' => 'btn-whatsapp__icon'])
                            Order on WhatsApp
                        </button>
                        <a href="{{ route('web.cart.index') }}" class="btn btn-secondary">View cart</a>
                    </form>
                @else
                    <p class="text-[var(--color-error)]">Currently unavailable.</p>
                @endif

                <p class="mt-4 text-sm text-[var(--color-soft-grey)]">You’ll chat directly with {{ config('clinic.ceo.name') }} to confirm availability, delivery and payment.</p>

                @if($product->usage_instructions)
                    <div class="mt-10">
                        <h2 class="font-display text-2xl mb-3">Usage</h2>
                        <p class="whitespace-pre-line text-[var(--color-soft-grey)]">{{ $product->usage_instructions }}</p>
                    </div>
                @endif
                @if($product->ingredients)
                    <div class="mt-8">
                        <h2 class="font-display text-2xl mb-3">Ingredients</h2>
                        <p class="whitespace-pre-line text-[var(--color-soft-grey)]">{{ $product->ingredients }}</p>
                    </div>
                @endif
            </div>
        </div>

        @if($related->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-section mb-6">Related products</h2>
                <div class="grid gap-6 md:grid-cols-4">
                    @foreach($related as $item)
                        <a href="{{ route('web.store.show', $item->slug) }}" class="border border-[var(--color-border)] p-4">
                            @if($item->imageUrl())
                                <img src="{{ $item->imageUrl() }}" alt="" class="mb-3 h-40 w-full object-cover">
                            @endif
                            <p class="font-display text-xl">{{ $item->name }}</p>
                            <p class="text-sm">GHS {{ number_format((float) $item->effectivePrice(), 2) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
