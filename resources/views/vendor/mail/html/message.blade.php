@php
    $rtlLocales = ['ar', 'fa', 'he', 'ur'];
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, $rtlLocales, true);
    $dir = $isRtl ? 'rtl' : 'ltr';
    $align = $isRtl ? 'right' : 'left';
@endphp

@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    <div dir="{{ $dir }}" style="text-align: {{ $align }};">
        {{ $slot }}
    </div>

    @isset($subcopy)
        @slot('subcopy')
            <div dir="{{ $dir }}" style="text-align: {{ $align }};">
                @component('mail::subcopy')
                    {{ $subcopy }}
                @endcomponent
            </div>
        @endslot
    @endisset

    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
