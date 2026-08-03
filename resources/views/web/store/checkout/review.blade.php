@extends('web.layouts.app')
@section('title', 'Checkout — '.config('clinic.name'))
@section('content')
<section class="section">
    <div class="container-site max-w-5xl">
        <h1 class="text-page-title mb-8">Checkout</h1>
        @if ($errors->any())
            <div class="mb-6 border border-[var(--color-error)] p-4 text-[var(--color-error)]">
                <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <form method="POST" action="{{ route('web.checkout.pay') }}" class="grid gap-10 lg:grid-cols-[1.2fr,0.8fr]" x-data="{ fulfillment: @js($fulfillmentType) }">
            @csrf
            <div class="space-y-6">
                <div class="border border-[var(--color-border)] bg-white p-6 space-y-4">
                    <h2 class="font-display text-2xl">Contact</h2>
                    <div><label class="text-label mb-2 block" for="name">Full name</label><input class="field" id="name" name="name" value="{{ $contact['name'] ?? '' }}" required></div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div><label class="text-label mb-2 block" for="email">Email</label><input class="field" id="email" type="email" name="email" value="{{ $contact['email'] ?? '' }}" required></div>
                        <div><label class="text-label mb-2 block" for="phone">Phone</label><input class="field" id="phone" name="phone" value="{{ $contact['phone'] ?? '' }}" required></div>
                    </div>
                    <div><label class="text-label mb-2 block" for="notes">Order notes</label><textarea class="field" id="notes" name="notes" rows="3">{{ $contact['notes'] ?? '' }}</textarea></div>
                </div>

                <div class="border border-[var(--color-border)] bg-white p-6 space-y-4">
                    <h2 class="font-display text-2xl">Fulfilment</h2>
                    <div class="flex flex-wrap gap-4">
                        @if(config('ecommerce.delivery_enabled'))
                            <label class="inline-flex items-center gap-2"><input type="radio" name="fulfillment_type" value="delivery" x-model="fulfillment" @checked($fulfillmentType === 'delivery')> Delivery</label>
                        @endif
                        @if(config('ecommerce.pickup_enabled'))
                            <label class="inline-flex items-center gap-2"><input type="radio" name="fulfillment_type" value="pickup" x-model="fulfillment" @checked($fulfillmentType === 'pickup')> Clinic pickup</label>
                        @endif
                    </div>
                    <div x-show="fulfillment === 'pickup'" x-cloak>
                        <label class="text-label mb-2 block" for="branch_id">Pickup branch</label>
                        <select class="field" id="branch_id" name="branch_id">
                            <option value="">Select branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected((string) $branchId === (string) $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-4" x-show="fulfillment === 'delivery'" x-cloak>
                        <div><label class="text-label mb-2 block" for="line_1">Address line 1</label><input class="field" id="line_1" name="line_1" value="{{ old('line_1', $address['line_1'] ?? '') }}"></div>
                        <div><label class="text-label mb-2 block" for="line_2">Address line 2</label><input class="field" id="line_2" name="line_2" value="{{ old('line_2', $address['line_2'] ?? '') }}"></div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div><label class="text-label mb-2 block" for="city">City</label><input class="field" id="city" name="city" value="{{ old('city', $address['city'] ?? '') }}"></div>
                            <div><label class="text-label mb-2 block" for="region">Region</label><input class="field" id="region" name="region" value="{{ old('region', $address['region'] ?? '') }}"></div>
                        </div>
                    </div>
                </div>

                <label class="inline-flex items-start gap-3 text-sm">
                    <input type="checkbox" name="terms" value="1" required>
                    <span>I confirm these details and accept the clinic purchase terms.</span>
                </label>
            </div>

            <aside class="border border-[var(--color-border)] bg-white p-6 h-fit lg:sticky lg:top-6">
                <h2 class="font-display text-2xl mb-4">Review</h2>
                <ul class="space-y-3 mb-6 text-sm">
                    @foreach($items as $item)
                        <li class="flex justify-between gap-3">
                            <span>{{ $item->product?->name }} × {{ $item->quantity }}</span>
                            <span>GHS {{ number_format($item->quantity * (float) $item->unit_price, 2) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="space-y-2 text-sm border-t border-[var(--color-border)] pt-4 mb-6">
                    <div class="flex justify-between"><span>Subtotal</span><span>GHS {{ number_format($quote['subtotal'], 2) }}</span></div>
                    @if($quote['discount'] > 0)<div class="flex justify-between"><span>Discount</span><span>- GHS {{ number_format($quote['discount'], 2) }}</span></div>@endif
                    <div class="flex justify-between"><span>Delivery</span><span>GHS {{ number_format($quote['delivery_fee'], 2) }}</span></div>
                    @if($quote['tax'] > 0)<div class="flex justify-between"><span>Tax</span><span>GHS {{ number_format($quote['tax'], 2) }}</span></div>@endif
                    <div class="flex justify-between font-medium text-base"><span>Total</span><span>GHS {{ number_format($quote['grand_total'], 2) }}</span></div>
                </div>
                <button type="submit" class="btn btn-whatsapp w-full">
                    @include('web.components.whatsapp-icon', ['class' => 'btn-whatsapp__icon'])
                    Continue on WhatsApp
                </button>
                <p class="mt-3 text-center text-xs text-[var(--color-soft-grey)]">You’ll chat directly with our team to confirm availability, delivery and payment.</p>
            </aside>
        </form>
    </div>
</section>
@endsection
