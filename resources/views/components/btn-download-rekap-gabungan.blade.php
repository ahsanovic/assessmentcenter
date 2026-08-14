@props([
    'eventId',
    'tanggalTes' => '',
    'disabled' => false,
])

<x-btn-download
    :route="'admin.event.download-rekap-gabungan'"
    :params="[$eventId]"
    :query="['tanggalTes' => $tanggalTes]"
    text="Rekap Gabungan (Excel)"
    icon="download"
    color="success"
    :disabled="$disabled"
/>
