@extends('web.layouts.app')
@section('title', __('web.pages.store_title').' — '.config('clinic.name'))
@section('content')
<section class="store-v2-hero">
    <div class="container-site store-v2-hero__grid">
        <div class="store-v2-hero__copy reveal">
            <p class="text-label">The De Luxe edit</p>
            <h1>Professional care,<br><em>beyond the clinic.</em></h1>
            <p>Shop carefully selected skincare and beauty essentials chosen to support your routine, treatment results and everyday confidence.</p>
            <div class="store-v2-hero__actions">
                <a href="#shop" class="btn btn-primary">Shop the collection</a>
                <a href="{{ route('web.cart.index') }}" class="btn btn-secondary">
                    View cart
                    @if(count($cartProductIds)) <span>({{ count($cartProductIds) }})</span> @endif
                </a>
            </div>
            <div class="store-v2-assurance">
                <span>Clinic selected</span>
                <span>Secure Paystack checkout</span>
                <span>Delivery or pickup</span>
            </div>
        </div>
        <div class="store-v2-hero__visual reveal reveal-delay-2">
            <img src="https://unsplash.com/photos/7mVXMtnI2MA/download?force=true&w=1600" alt="A curated collection of skincare and beauty products" fetchpriority="high">
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
                <p>{{ $products->total() }} {{ \Illuminate\Support\Str::plural('product', $products->total()) }}</p>
            </div>
            <form method="GET" action="{{ route('web.store.index') }}" class="store-v2-filters">
                @if($filters['category'])<input type="hidden" name="category" value="{{ $filters['category'] }}">@endif
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
                @if($filters['q'] || $filters['in_stock'])
                    <a href="{{ route('web.store.index', array_filter(['category' => $filters['category'], 'sort' => $filters['sort']])) }}">Clear</a>
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
                    <article class="store-v2-card reveal">
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
                            @if($product->isPurchasable() && !in_array($product->id, $cartProductIds))
                                <form method="POST" action="{{ route('web.cart.store') }}" class="store-v2-card__buy">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" name="buy_now" value="1">Buy now with Paystack</button>
                                </form>
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
        <div>
            <p class="text-label">Not sure where to begin?</p>
            <h2>Let your routine start with expert advice.</h2>
        </div>
        <div>
            <p>Book a consultation for personalised guidance on treatments, skincare and the products best suited to your goals.</p>
            <a href="{{ route('web.booking.create') }}" class="btn btn-primary">Book a consultation</a>
        </div>
    </div>
</section>
@endsection
