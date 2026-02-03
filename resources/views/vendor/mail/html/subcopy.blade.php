@php
    $rtlLocales = ['ar', 'fa', 'he', 'ur'];
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, $rtlLocales, true);
    $align = $isRtl ? 'right' : 'left';
@endphp
<table class="subcopy" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td style="text-align: {{ $align }};">
<p>
{{ Illuminate\Mail\Markdown::parse($slot) }}
</p>
</td>
</tr>
</table>
