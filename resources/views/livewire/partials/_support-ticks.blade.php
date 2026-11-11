{{-- An agent message's ticks (admin support inbox): ✓ sent, ✓✓ delivered,
     blue ✓✓ read. Drawn, not "✓✓" text: the font put a wide gap between the
     two marks. The second check overlaps the first, WhatsApp style. --}}
<span class="si-ticks">
    @if(in_array($status, ['read', 'delivered'], true))
        <svg class="t {{ $status === 'read' ? 'read' : '' }}" viewBox="0 0 16 11" width="16" height="11" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-label="{{ ucfirst($status) }}"><path d="M1.2 5.9 4.3 9l6.3-7.4M6.9 7.7 8.2 9l6.4-7.4"/></svg>
    @else
        <svg class="t" viewBox="0 0 12 11" width="12" height="11" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-label="Sent"><path d="M1.2 5.9 4.3 9l6.3-7.4"/></svg>
    @endif
</span>
