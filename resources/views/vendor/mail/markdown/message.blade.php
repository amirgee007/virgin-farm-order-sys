@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => null])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    {{-- Body --}}
    <div style="color:black">{{ $slot }}</div>
    
    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
