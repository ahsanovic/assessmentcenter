@props([
    'terdaftar' => 0,
    'mulai' => 0,
])

<span
    {{ $attributes->merge(['class' => 'text-nowrap']) }}
    title="{{ (int) $terdaftar }} peserta aktif, {{ (int) $mulai }} sudah mulai ujian"
>{{ (int) $terdaftar }}/{{ (int) $mulai }}</span>
