<svg class="ht-icon" width="{{ $size ?? 20 }}" height="{{ $size ?? 20 }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($name)
@case('menu') <path d="M4 6h16M4 12h16M4 18h16"/> @break
@case('search') <circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4.5 4.5"/> @break
@case('bag') <path d="M5 7h14l1 14H4L5 7Z"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/> @break
@case('user') <circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/> @break
@case('arrow') <path d="M4 12h16m-6-6 6 6-6 6"/> @break
@case('chevron') <path d="m7 10 5 5 5-5"/> @break
@case('flower') <path d="M12 12C4 10 3 3 7 3c3 0 5 5 5 9Zm0 0c8-2 9-9 5-9-3 0-5 5-5 9Zm0 0c-8-2-12 3-8 6 3 2 7-3 8-6Zm0 0c8-2 12 3 8 6-3 2-7-3-8-6Zm0 0c-5 6-3 10 0 10s5-4 0-10Z"/> @break
@case('shield') <path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z"/><path d="m8 12 3 3 5-6"/> @break
@case('truck') <path d="M3 5h11v12H3V5Zm11 5h4l3 4v3h-7"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/> @break
@case('gift') <path d="M3 8h18v4H3zM5 12v9h14v-9M12 8v13"/><path d="M12 8H8a3 3 0 1 1 3-3l1 3Zm0 0h4a3 3 0 1 0-3-3l-1 3Z"/> @break
@case('heart') <path d="M20.5 5.5a5 5 0 0 0-7 0L12 7l-1.5-1.5a5 5 0 0 0-7 7L12 21l8.5-8.5a5 5 0 0 0 0-7Z"/> @break
@case('mail') <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 6 9 7 9-7"/> @break
@case('lock') <rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V6a4 4 0 0 1 8 0v4m-4 4v3"/> @break
@case('chat') <path d="M21 11a9 9 0 0 1-9 9H3l2-4a9 9 0 1 1 16-5Z"/><path d="M8 10h8m-8 4h5"/> @break
@default <path d="m12 3 2.5 6.5L21 12l-6.5 2.5L12 21l-2.5-6.5L3 12l6.5-2.5L12 3Z"/>
@endswitch
</svg>