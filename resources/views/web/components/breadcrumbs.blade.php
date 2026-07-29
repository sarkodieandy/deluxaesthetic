@props([
    'items' => [],
])

@if(count($items))
<nav class="breadcrumbs" aria-label="Breadcrumb">
    @foreach($items as $i => $item)
        @if($i > 0)
            <span class="breadcrumbs__sep" aria-hidden="true">/</span>
        @endif
        @if(!empty($item['href']) && !$loop->last)
            <a href="{{ $item['href'] }}">{{ $item['label'] }}</a>
        @else
            <span aria-current="{{ $loop->last ? 'page' : 'false' }}">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
@endif
