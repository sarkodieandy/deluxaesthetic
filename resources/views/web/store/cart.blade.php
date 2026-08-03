@extends('web.layouts.app')
@section('title', 'Cart — '.config('clinic.name'))
@section('content')
<section class="section">
    <div class="container-site">
        <h1 class="text-page-title mb-8">Your cart</h1>
        @if (session('status'))
            <p class="mb-4 text-[var(--color-success)]">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <div class="mb-4 border border-[var(--color-error)] p-4 text-[var(--color-error)]">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif
        @foreach($notices ?? [] as $notice)
            <p class="mb-2 text-sm text-[var(--color-bronze)]">{{ $notice }}</p>
        @endforeach

        @if($count < 1)
            <p class="mb-6 text-[var(--color-soft-grey)]">Your cart is empty.</p>
            <a href="{{ route('web.store.index') }}" class="btn btn-primary">Continue shopping</a>
        @else
            <div class="grid gap-10 lg:grid-cols-[1.4fr,0.8fr]">
                <div class="space-y-4">
                    @foreach($items as $item)
                        <article class="border border-[var(--color-border)] bg-white p-4 grid gap-4 md:grid-cols-[7rem,1fr,auto]">
                            <div class="border border-[var(--color-border)] bg-[var(--color-stone)] overflow-hidden">
                                @if($item->product?->imageUrl())
                                    <img src="{{ $item->product->imageUrl() }}" alt="" class="h-28 w-full object-cover">
                                @endif
                            </div>
                            <div>
                                <h2 class="font-display text-2xl mb-1">{{ $item->product?->name }}</h2>
                                <p class="text-sm text-[var(--color-soft-grey)] mb-3">SKU {{ $item->product?->sku }}</p>
                                <p class="mb-3">GHS {{ number_format((float) $item->unit_price, 2) }}</p>
                                <form method="POST" action="{{ route('web.cart.items.update', $item) }}" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label class="sr-only" for="qty-{{ $item->id }}">Quantity</label>
                                    <input id="qty-{{ $item->id }}" class="field w-20" type="number" name="quantity" value="{{ $item->quantity }}" min="0" max="99">
                                    <button class="btn btn-secondary" type="submit">Update</button>
                                </form>
                            </div>
                            <div class="text-right">
                                <p class="mb-4 font-medium">GHS {{ number_format($item->quantity * (float) $item->unit_price, 2) }}</p>
                                <form method="POST" action="{{ route('web.cart.items.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-secondary" type="submit">Remove</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
                <aside class="border border-[var(--color-border)] bg-white p-6 h-fit lg:sticky lg:top-6">
                    <h2 class="font-display text-2xl mb-4">Order summary</h2>
                    <div class="space-y-2 text-sm mb-6">
                        <div class="flex justify-between"><span>Subtotal</span><span>GHS {{ number_format($subtotal, 2) }}</span></div>
                        @if($discount > 0)
                            <div class="flex justify-between"><span>Discount</span><span>- GHS {{ number_format($discount, 2) }}</span></div>
                        @endif
                        <div class="flex justify-between font-medium text-base pt-2 border-t border-[var(--color-border)]"><span>Estimated total</span><span>GHS {{ number_format(max($subtotal - $discount, 0), 2) }}</span></div>
                    </div>
                    <form method="POST" action="{{ route('web.cart.coupon.apply') }}" class="mb-4 flex gap-2">
                        @csrf
                        <input class="field" type="text" name="code" placeholder="Coupon code" value="{{ $coupon_code }}">
                        <button class="btn btn-secondary" type="submit">Apply</button>
                    </form>
                    @if($coupon_code)
                        <form method="POST" action="{{ route('web.cart.coupon.remove') }}" class="mb-4">@csrf @method('DELETE')
                            <button class="btn btn-secondary" type="submit">Remove coupon</button>
                        </form>
                    @endif
                    <a href="{{ $whatsAppCheckoutUrl }}" class="btn btn-whatsapp w-full mb-3" target="_blank" rel="noopener noreferrer">
                        @include('web.components.whatsapp-icon', ['class' => 'btn-whatsapp__icon'])
                        Order on WhatsApp
                    </a>
                    <p class="mb-4 text-xs text-center text-[var(--color-soft-grey)]">Your order summary will be sent to {{ config('clinic.ceo.name') }}. Delivery and payment will be confirmed in the chat.</p>
                    <a href="{{ route('web.store.index') }}" class="btn btn-secondary w-full">Continue shopping</a>
                </aside>
            </div>
        @endif
    </div>
</section>
@endsection
