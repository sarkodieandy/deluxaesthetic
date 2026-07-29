@props([
    'beforeUrl',
    'afterUrl',
    'beforeAlt' => '',
    'afterAlt' => '',
    'title' => null,
])

<div
    class="ba-compare reveal"
    x-data="beforeAfterCompare(50)"
    x-ref="track"
    @pointerdown.prevent="startDrag($event)"
    @pointermove="onDrag($event)"
    @pointerup="endDrag($event)"
    @pointercancel="endDrag($event)"
    @keydown="onKeydown($event)"
    role="group"
    aria-roledescription="{{ __('Before and after comparison') }}"
    @if($title) aria-label="{{ $title }}" @endif
>
    <img
        class="ba-compare__image ba-compare__image--after"
        src="{{ $afterUrl }}"
        alt="{{ $afterAlt }}"
        loading="lazy"
        decoding="async"
        draggable="false"
        @load="syncBeforeImageWidth()"
    >
    <div class="ba-compare__before-layer" :style="`width: ${position}%`">
        <img
            class="ba-compare__image ba-compare__image--before"
            x-ref="beforeImg"
            src="{{ $beforeUrl }}"
            alt="{{ $beforeAlt }}"
            loading="lazy"
            decoding="async"
            draggable="false"
            @load="syncBeforeImageWidth()"
        >
    </div>
    <div class="ba-compare__divider" :style="`left: ${position}%`" aria-hidden="true"></div>
    <button
        type="button"
        class="ba-compare__handle"
        :style="`left: ${position}%`"
        tabindex="0"
        aria-label="{{ __('Drag to compare before and after') }}"
        :aria-valuenow="Math.round(position)"
        aria-valuemin="0"
        aria-valuemax="100"
        @pointerdown.stop="startDrag($event)"
    >
        <span class="ba-compare__handle-icon" aria-hidden="true">&lsaquo;&rsaquo;</span>
    </button>
    <span class="ba-compare__badge ba-compare__badge--before">{{ __('web.home.ba_before') }}</span>
    <span class="ba-compare__badge ba-compare__badge--after">{{ __('web.home.ba_after') }}</span>
</div>
