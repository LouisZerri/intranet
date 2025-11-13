@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
{{-- Logo GEST'IMMO au lieu de Laravel --}}
<img src="{{ asset('images/logo3d.png') }}" class="logo" alt="GEST'IMMO">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>