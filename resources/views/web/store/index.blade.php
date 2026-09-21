@extends('web.layouts.app')
@section('title', __('web.pages.store_title').' — '.config('clinic.name'))
@section('meta_description', 'Shop clinic-selected skincare and beauty essentials from De Luxe Aesthetic Clinic, with delivery and pickup options in Accra.')
@section('content')
<section class="store-v2-hero">
    <div class="container-site store-v2-hero__grid">
        <div class="store-v2-hero__copy reveal">
            <p class="text-label">{{ $cmsPage?->hero_eyebrow ?: 'The De Luxe edit' }}</p>
            <h1>@if($cmsPage?->hero_title){!! nl2br(e($cmsPage->hero_title)) !!}@else Professional care,<br><em>beyond the clinic.</em>@endif</h1>
            <p>{{ $cmsPage?->hero_body ?: 'Shop carefully selected skincare and beauty essentials chosen to support your routine, treatment results and everyday confidence.' }}</p>
            <div class="store-v2-hero__actions">
                <a href="#shop" class="btn btn-primary">Shop the collection</a>
                <a href="{{ route('web.cart.index') }}" class="btn btn-secondary">
                    View cart
                    @if(count($cartProductIds)) <span>({{ count($cartProductIds) }})</span> @endif
                </a>
            </div>
            <div class="store-v2-assurance">
                <span>Clinic selected</span>
                <span>{{ \App\Support\WhatsAppOrder::enabled() ? 'Order directly on WhatsApp' : 'Secure online checkout' }}</span>
                <span>Delivery or pickup</span>
            </div>
        </div>
        <div class="store-v2-hero__visual reveal reveal-delay-2">
            <img src="{{ $cmsPage?->hero_image_url ?: asset('assets/web/images/store/store-hero.webp') }}" alt="A curated collection of skincare and beauty products" width="1600" height="1067" decoding="async" fetchpriority="high" data-editorial-parallax>
            <span class="store-v2-hero__edition" aria-hidden="true">The edit · 2026</span>
            <div class="store-v2-hero__note">
                <span>Curated essentials</span>
                <strong>For skin that feels as good as it looks.</strong>
            </div>
        </div>
    </div>
</section>

<section class="store-v2-categories">
    <div class="container-site">
        <div class="store-v2-categories__intro">
            <p class="text-label">Browse by category</p>
            <a href="{{ route('web.store.index') }}">View all products <span aria-hidden="true">→</span></a>
        </div>
        <nav class="store-v2-category-list" aria-label="Product categories">
            <a href="{{ route('web.store.index') }}" @class(['is-active' => ! $filters['category']])>
                <span>All</span>
                <small>{{ $products->total() }}</small>
            </a>
            @foreach($categories as $category)
                <a href="{{ route('web.store.index', ['category' => $category->id]) }}" @class(['is-active' => (string) $filters['category'] === (string) $category->id])>
                    <span>{{ $category->name }}</span>
                    <small>{{ $category->products_count }}</small>
                </a>
            @endforeach
        </nav>
    </div>
</section>

<section class="store-v2-shop" id="shop">
    <div class="container-site">
        <header class="store-v2-shop__header">
            <div>
                <p class="text-label">Shop De Luxe</p>
                <h2 class="text-section">{{ $filters['category'] ? ($categories->firstWhere('id', $filters['category'])?->name ?? 'Products') : 'The complete collection' }}</h2>
                <p>
                    {{ $products->total() }} {{ \Illuminate\Support\Str::plural('product', $products->total()) }}
                    @if($filters['min_price'] !== null || $filters['max_price'] !== null)
                        <span class="store-v2-price-summary">
                            ·
                            @if($filters['min_price'] !== null && $filters['max_price'] !== null)
                                GHS {{ number_format($filters['min_price'], 0) }}–{{ number_format($filters['max_price'], 0) }}
                            @elseif($filters['min_price'] !== null)
                                From GHS {{ number_format($filters['min_price'], 0) }}
                            @else
                                Up to GHS {{ number_format($filters['max_price'], 0) }}
                            @endif
                        </span>
                    @endif
                </p>
            </div>
            <form method="GET" action="{{ route('web.store.index') }}" class="store-v2-filters">
                @if($filters['category'])<input type="hidden" name="category" value="{{ $filters['category'] }}">@endif
                <div class="store-v2-filters__primary">
                    <label class="store-v2-search">
                        <span class="sr-only">Search products</span>
                        <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search the collection">
                        <button type="submit" aria-label="Search">→</button>
                    </label>
                    <label>
                        <span class="sr-only">Sort products</span>
                        <select name="sort" onchange="this.form.submit()">
                            <option value="featured" @selected($filters['sort'] === 'featured')>Featured first</option>
                            <option value="newest" @selected($filters['sort'] === 'newest')>Newest</option>
                            <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Price: low to high</option>
                            <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Price: high to low</option>
                            <option value="name" @selected($filters['sort'] === 'name')>Name: A–Z</option>
                        </select>
                    </label>
                    <label class="store-v2-stock-filter">
                        <input type="checkbox" name="in_stock" value="1" @checked($filters['in_stock']) onchange="this.form.submit()">
                        <span>In stock only</span>
                    </label>
                </div>

                <fieldset class="store-v2-price-filter">
                    <legend>Filter by price</legend>
                    <div class="store-v2-price-filter__fields">
                        <label>
                            <span>Minimum</span>
                            <span class="store-v2-price-filter__input"><b>GHS</b><input type="number" name="min_price" min="0" max="1000000" step="1" inputmode="decimal" value="{{ $filters['min_price'] }}" placeholder="{{ $availablePriceRange['minimum'] !== null ? number_format($availablePriceRange['minimum'], 0, '.', '') : '0' }}"></span>
                        </label>
                        <span aria-hidden="true">—</span>
                        <label>
                            <span>Maximum</span>
                            <span class="store-v2-price-filter__input"><b>GHS</b><input type="number" name="max_price" min="0" max="1000000" step="1" inputmode="decimal" value="{{ $filters['max_price'] }}" placeholder="{{ $availablePriceRange['maximum'] !== null ? number_format($availablePriceRange['maximum'], 0, '.', '') : 'Any' }}"></span>
                        </label>
                        <button type="submit" class="store-v2-price-filter__apply">Apply price</button>
                    </div>
                    <div class="store-v2-price-presets" aria-label="Quick price ranges">
                        @foreach($pricePresets as $preset)
                            @php
                                $presetQuery = array_filter([
                                    'q' => $filters['q'],
                                    'category' => $filters['category'],
                                    'sort' => $filters['sort'] !== 'featured' ? $filters['sort'] : null,
                                    'in_stock' => $filters['in_stock'] ? 1 : null,
                                    'min_price' => $preset['minimum'],
                                    'max_price' => $preset['maximum'],
                                ], fn ($value) => $value !== null && $value !== '');
                                $presetActive = (string) ($filters['min_price'] ?? '') === (string) ($preset['minimum'] ?? '')
                                    && (string) ($filters['max_price'] ?? '') === (string) ($preset['maximum'] ?? '');
                            @endphp
                            <a href="{{ route('web.store.index', $presetQuery) }}#shop" @class(['is-active' => $presetActive])>{{ $preset['label'] }}</a>
                        @endforeach
                    </div>
                </fieldset>

                @if($filters['q'] || $filters['in_stock'] || $filters['min_price'] !== null || $filters['max_price'] !== null)
                    <a class="store-v2-filters__clear" href="{{ route('web.store.index', array_filter(['category' => $filters['category'], 'sort' => $filters['sort'] !== 'featured' ? $filters['sort'] : null])) }}#shop">Clear filters</a>
                @endif
            </form>
        </header>

        @if ($products->isEmpty())
            <div class="store-v2-empty">
                <span>Nothing here—yet.</span>
                <h3>No products match your selection</h3>
                <p>Try another category or clear the current search filters.</p>
                <a href="{{ route('web.store.index') }}" class="btn btn-primary">Browse all products</a>
            </div>
        @else
            <div class="store-v2-grid">
                @foreach ($products as $product)
                    <article class="store-v2-card reveal" style="--reveal-delay: {{ ($loop->index % 3) * 90 }}ms">
                        <a href="{{ route('web.store.show', $product->slug) }}" class="store-v2-card__media">
                            @if ($product->imageUrl())
                                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div class="store-v2-card__placeholder">
                                    <span>{{ $product->category?->name ?? 'De Luxe' }}</span>
                                    <strong>{{ $product->name }}</strong>
                                </div>
                            @endif
                            <div class="store-v2-card__badges">
                                @if($product->sale_price)<span class="is-sale">Sale</span>@endif
                                @if($product->is_featured)<span>De Luxe pick</span>@endif
                                @if(in_array($product->id, $cartProductIds))<span class="is-cart">In cart</span>@endif
                            </div>
                            <span class="store-v2-card__view">View details</span>
                        </a>
                        <div class="store-v2-card__body">
                            <p class="store-v2-card__category">{{ $product->category?->name }}</p>
                            <div class="store-v2-card__title-row">
                                <h3><a href="{{ route('web.store.show', $product->slug) }}">{{ $product->name }}</a></h3>
                                <p class="store-v2-card__price">
                                    @if($product->sale_price)<del>GHS {{ number_format((float) $product->price, 2) }}</del>@endif
                                    <strong>GHS {{ number_format((float) $product->effectivePrice(), 2) }}</strong>
                                </p>
                            </div>
                            @if($product->description)
                                <p class="store-v2-card__description">{{ \Illuminate\Support\Str::limit($product->description, 92) }}</p>
                            @endif
                            <div class="store-v2-card__footer">
                                <span @class(['is-out' => ! $product->isPurchasable()])>
                                    {{ $product->isPurchasable() ? 'Ready to order' : 'Currently unavailable' }}
                                </span>
                                @if($product->isPurchasable())
                                    @if(in_array($product->id, $cartProductIds))
                                        <a href="{{ route('web.cart.index') }}" class="store-v2-card__action">Go to cart →</a>
                                    @else
                                        <form method="POST" action="{{ route('web.cart.store') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button type="submit" aria-label="Add {{ $product->name }} to cart">Add to cart <span>+</span></button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                            @if($product->isPurchasable())
                                @if(\App\Support\WhatsAppOrder::enabled())
                                    <a class="store-v2-card__buy" href="{{ $whatsAppOrderUrls[$product->id] }}" target="_blank" rel="noopener noreferrer">
                                        <span>Order on WhatsApp</span>
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('web.cart.store') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button type="submit" name="buy_now" value="1" class="btn btn-primary w-full mt-3">Buy now</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="store-v2-pagination">{{ $products->links() }}</div>
        @endif
    </div>
</section>

<section class="store-v2-service">
    <div class="container-site store-v2-service__grid">
        <div class="reveal">
            <p class="text-label">Not sure where to begin?</p>
            <h2>Let your routine start with expert advice.</h2>
        </div>
        <div class="reveal reveal-delay-2">
            <p>Book a consultation for personalised guidance on treatments, skincare and the products best suited to your goals.</p>
            <a href="{{ route('web.booking.create') }}" class="btn btn-primary">Book a consultation</a>
        </div>
    </div>
</section>
@endsection
