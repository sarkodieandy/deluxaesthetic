@extends('web.layouts.app')

@section('title', ($selectedCategory?->name ?: 'Clinical Procedures').' — '.config('clinic.name'))
@section('meta_description', $selectedCategory?->description ?: 'Explore professional spa, facial, skin, body and injectable procedures at our clinic in East Legon, Accra, with published prices and consultation-led booking.')
@section('meta_image', $selectedCategory?->imageUrl() ?: ($cmsPage?->hero_image_url ?: asset('assets/web/images/hero/hero-botox.webp')))
@section('meta_image_alt', $selectedCategory?->name ?: 'Clinical procedures at '.config('clinic.name'))

@section('content')
<section class="relative isolate min-h-[620px] overflow-hidden bg-[#151512] text-white">
    <img src="{{ $cmsPage?->hero_image_url ?: asset('assets/web/images/hero/hero-botox.webp') }}" alt="Professional injectable aesthetic treatment" class="absolute inset-0 -z-20 h-full w-full object-cover object-center" width="1600" height="1067" decoding="async" fetchpriority="high">
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-black/95 via-black/75 to-black/25"></div>
    <div class="container-site flex min-h-[620px] items-end py-16 lg:items-center lg:py-24">
        <div class="max-w-3xl">
            <p class="text-label text-white/65">{{ $cmsPage?->hero_eyebrow ?: 'De Luxe · Clinical Procedures' }}</p>
            @if ($cmsPage?->hero_title)
                <h1 class="mt-5 max-w-4xl font-display text-5xl leading-[.95] sm:text-6xl lg:text-8xl">{{ $cmsPage->hero_title }}</h1>
            @else
                <h1 class="mt-5 font-display text-5xl leading-[.95] sm:text-6xl lg:text-8xl">Professional care.<br><em class="text-[#d5af72]">Considered results.</em></h1>
            @endif
            <p class="mt-7 max-w-2xl text-lg leading-relaxed text-white/75">{{ $cmsPage?->hero_body ?: 'Discover our clinic pathways from restorative spa therapy and advanced skin care to body and injectable procedures. Every appointment starts with assessment, honest guidance and a plan suited to you.' }}</p>
            <div class="mt-9 flex flex-wrap gap-3">
                <a href="#procedure-pathways" class="btn btn-primary">Explore procedures &amp; prices</a>
                <a href="{{ route('web.booking.create') }}" class="btn btn-outline-light">Book a consultation</a>
            </div>
            <dl class="mt-12 grid max-w-2xl grid-cols-3 border-y border-white/20 py-5 text-sm text-white/65">
                <div><dt class="text-label text-white/45">Approach</dt><dd class="mt-2 text-white">Consultation-led</dd></div>
                <div class="border-l border-white/20 pl-5"><dt class="text-label text-white/45">Location</dt><dd class="mt-2 text-white">East Legon, Accra</dd></div>
                <div class="border-l border-white/20 pl-5"><dt class="text-label text-white/45">Booking</dt><dd class="mt-2 text-white">Open to guests</dd></div>
            </dl>
        </div>
    </div>
</section>

<section id="procedure-pathways" class="section bg-[#f3efe8]">
    <div class="container-site">
        <header class="mb-10 grid gap-6 lg:grid-cols-2 lg:items-end">
            <div><p class="text-label mb-3">Begin with your goal</p><h2 class="text-page-title">Clinical pathways. One standard of care.</h2></div>
            <p class="max-w-xl text-[var(--color-soft-grey)] lg:justify-self-end">Choose a category to see its published procedures and prices. If you are unsure where to begin, book a consultation and our team will guide you.</p>
        </header>

        @if ($categories->isEmpty())
            <div class="border border-[var(--color-border)] bg-white p-10 text-center"><h3 class="font-display text-3xl">Our clinical menu is being prepared.</h3><p class="mx-auto mt-3 max-w-xl text-[var(--color-soft-grey)]">Please contact the clinic for current procedures, suitability guidance and prices.</p><a href="{{ route('web.contact') }}" class="btn btn-primary mt-6">Contact the clinic</a></div>
        @else
            <div class="grid gap-px border border-[var(--color-border)] bg-[var(--color-border)] md:grid-cols-2 xl:grid-cols-4">
                @foreach ($categories as $index => $category)
                    <a href="{{ route('web.clinical.index', ['category' => $category->slug]) }}#procedures" class="group relative min-h-[390px] overflow-hidden bg-[#1b1a17] text-white no-underline">
                        @if ($category->imageUrl())
                            <img src="{{ $category->imageUrl() }}" alt="{{ $category->name }}" class="absolute inset-0 h-full w-full object-cover opacity-65 transition duration-700 group-hover:scale-105 group-hover:opacity-50" loading="lazy" decoding="async">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/45 to-black/5"></div>
                        <div class="absolute inset-x-0 bottom-0 p-7">
                            <p class="text-label text-white/55">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }} · {{ $category->published_treatments_count }} {{ Str::plural('procedure', $category->published_treatments_count) }}</p>
                            <h3 class="mt-3 font-display text-3xl leading-tight">{{ $category->name }}</h3>
                            @if ($category->description)<p class="mt-3 line-clamp-3 text-sm leading-relaxed text-white/70">{{ $category->description }}</p>@endif
                            <span class="mt-5 inline-block text-sm uppercase tracking-[.16em] text-[#e6c995]">View pathway →</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="section" id="procedures">
    <div class="container-site">
        <header class="mb-9 flex flex-wrap items-end justify-between gap-6">
            <div>
                <p class="text-label mb-3">{{ $selectedCategory ? 'Selected pathway' : 'Published clinical menu' }}</p>
                <h2 class="text-page-title">{{ $selectedCategory?->name ?: 'Procedures & prices' }}</h2>
                @if ($selectedCategory?->description)<p class="mt-4 max-w-2xl text-[var(--color-soft-grey)]">{{ $selectedCategory->description }}</p>@endif
            </div>
            @if ($selectedCategory)<a href="{{ route('web.clinical.index') }}#procedures" class="btn btn-secondary">View all procedures</a>@endif
        </header>

        <form method="GET" action="{{ route('web.clinical.index') }}" class="mb-10 grid gap-3 border border-[var(--color-border)] bg-white p-4 shadow-sm md:grid-cols-4">
            <label class="sr-only" for="procedure-search">Search procedures</label>
            <input id="procedure-search" class="field md:col-span-2" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search clinical procedures">
            <label class="sr-only" for="procedure-category">Category</label>
            <select id="procedure-category" class="field" name="category">
                <option value="">All categories</option>
                @foreach ($categories as $category)<option value="{{ $category->slug }}" @selected($filters['category'] === $category->slug)>{{ $category->name }}</option>@endforeach
            </select>
            <label class="sr-only" for="procedure-sort">Sort</label>
            <select id="procedure-sort" class="field" name="sort">
                <option value="">Recommended order</option>
                <option value="name" @selected($filters['sort'] === 'name')>Name</option>
                <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Price: low to high</option>
                <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Price: high to low</option>
            </select>
            <button class="btn btn-primary md:col-span-4 md:w-fit" type="submit">Update results</button>
        </form>

        <div class="grid gap-px border border-[var(--color-border)] bg-[var(--color-border)] md:grid-cols-2 xl:grid-cols-3">
            @forelse ($treatments as $treatment)
                @php($imageUrl = $treatment->imageUrl())
                <article class="group flex min-h-full flex-col bg-white">
                    <a href="{{ route('web.treatments.show', $treatment->slug) }}" class="relative block h-72 overflow-hidden bg-[#ebe6dd]">
                        @if ($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $treatment->name }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy" decoding="async">
                        @else
                            <div class="flex h-full items-end bg-gradient-to-br from-[#292722] to-[#8a765c] p-7 text-white"><span class="text-label text-white/65">{{ $treatment->category?->name }}</span></div>
                        @endif
                        @if ($treatment->is_featured)<span class="absolute left-4 top-4 bg-black/80 px-3 py-2 text-xs uppercase tracking-[.15em] text-white">Featured</span>@endif
                    </a>
                    <div class="flex flex-1 flex-col p-7">
                        <p class="text-label mb-3">{{ $treatment->category?->name }}</p>
                        <h3 class="font-display text-3xl leading-tight"><a class="text-inherit no-underline" href="{{ route('web.treatments.show', $treatment->slug) }}">{{ $treatment->name }}</a></h3>
                        <p class="mt-4 flex-1 text-[var(--color-soft-grey)]">{{ $treatment->short_description }}</p>
                        <div class="mt-6 flex items-end justify-between gap-4 border-t border-[var(--color-border)] pt-5">
                            <div>
                                <p class="text-xs uppercase tracking-[.14em] text-[var(--color-soft-grey)]">From</p>
                                <p class="mt-1 font-display text-2xl">GHS {{ number_format((float) $treatment->effectivePrice(), 2) }}</p>
                                @if ($treatment->promotional_price !== null)<p class="text-sm text-[var(--color-soft-grey)] line-through">GHS {{ number_format((float) $treatment->price, 2) }}</p>@endif
                            </div>
                            <p class="text-sm text-[var(--color-soft-grey)]">{{ $treatment->duration_minutes }} min</p>
                        </div>
                        <div class="mt-6 grid grid-cols-2 gap-3"><a href="{{ route('web.treatments.show', $treatment->slug) }}" class="btn btn-secondary">Details</a><a href="{{ route('web.booking.create', ['treatment_id' => $treatment->id]) }}" class="btn btn-primary">Book</a></div>
                    </div>
                </article>
            @empty
                <div class="bg-white p-12 text-center md:col-span-2 xl:col-span-3"><h3 class="font-display text-3xl">No published procedures match this search.</h3><p class="mx-auto mt-3 max-w-xl text-[var(--color-soft-grey)]">Try another category or contact our team for the latest clinical menu and personalised guidance.</p><div class="mt-6 flex flex-wrap justify-center gap-3"><a href="{{ route('web.clinical.index') }}#procedures" class="btn btn-secondary">Clear filters</a><a href="{{ route('web.contact') }}" class="btn btn-primary">Contact the clinic</a></div></div>
            @endforelse
        </div>
        <div class="mt-8">{{ $treatments->links() }}</div>
    </div>
</section>

@if ($beforeAfter->isNotEmpty())
<section class="section bg-[#171714] text-white">
    <div class="container-site">
        <header class="mb-10 grid gap-5 lg:grid-cols-2 lg:items-end"><div><p class="text-label text-white/55">Real clinic work</p><h2 class="mt-3 font-display text-5xl">Before &amp; after</h2></div><p class="max-w-xl text-white/65 lg:justify-self-end">Results vary by person. Images are shared for educational reference; every procedure begins with an individual assessment and realistic treatment plan.</p></header>
        <div class="grid gap-8 lg:grid-cols-2">
            @foreach ($beforeAfter as $item)
                <article>
                    @include('web.components.before-after-compare', [
                        'beforeUrl' => $item->beforeImageUrl(),
                        'afterUrl' => $item->afterImageUrl(),
                        'beforeAlt' => $item->alt_text ?: $item->title.' before',
                        'afterAlt' => $item->alt_text ?: $item->title.' after',
                        'title' => $item->title,
                    ])
                    <div class="mt-4 flex items-start justify-between gap-4"><div><h3 class="font-display text-2xl">{{ $item->title }}</h3>@if($item->description)<p class="mt-1 text-sm text-white/60">{{ $item->description }}</p>@endif</div>@if($item->treatment)<a class="text-sm text-[#e6c995]" href="{{ route('web.treatments.show', $item->treatment->slug) }}">View procedure →</a>@endif</div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="section bg-[#d5af72]">
    <div class="container-site grid gap-8 lg:grid-cols-[1fr_auto] lg:items-center"><div><p class="text-label text-black/55">Not sure what is suitable?</p><h2 class="mt-3 max-w-3xl font-display text-5xl leading-tight text-[#171714]">Start with a professional conversation.</h2><p class="mt-4 max-w-2xl text-black/65">Tell us your goals and concerns. Our team will help you choose the safest, most appropriate next step.</p></div><a href="{{ route('web.booking.create') }}" class="btn bg-[#171714] text-white hover:bg-black">Book consultation</a></div>
</section>
@endsection
