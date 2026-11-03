<?php

namespace App\Traits;

use Livewire\Attributes\On;

trait KembalikanSubtesPotensi
{
    public function kembalikanConfirmation($id): void
    {
        $this->selected_id = $id;
        $this->dispatch('show-kembalikan-subtes-confirmation');
    }

    #[On('kembalikan-subtes')]
    public function kembalikan(): void
    {
        try {
            $kelas = $this->kelasUjian();
            $ujian = $kelas::query()
                ->whereKey($this->selected_id)
                ->where('event_id', $this->id_event)
                ->first();

            if (! $ujian || ! in_array($ujian->is_finished, ['true', true, 1, '1'], true)) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Subtes ini tidak dalam status selesai.',
                ]);

                return;
            }

            $ujian->is_finished = 'false';
            $ujian->save();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Peserta dapat melanjutkan subtes ini. Jawaban yang sudah tersimpan tetap ada.',
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Gagal mengembalikan peserta ke subtes.',
            ]);
        }
    }

    abstract protected function kelasUjian(): string;
}
