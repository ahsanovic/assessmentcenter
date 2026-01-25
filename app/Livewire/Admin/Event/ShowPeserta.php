<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefJenisPeserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;

#[Layout('components.layouts.admin.app', ['title' => 'Event'])]
class ShowPeserta extends Component
{
    use WithPagination, WithFileUploads;

    public $id_event;
    public $event;
    public $selected_id;

    // Form fields
    public $nama;
    public $nip;
    public $nik;
    public $jabatan;
    public $instansi;
    public $unit_kerja;
    public $jenis_peserta_id;
    public $password;
    public $is_active;
    public $isUpdate = false;

    // Filter fields
    public $filter_jenis_peserta;
    public $is_portofolio_completed;

    // Import
    public $file_import;

    #[Url(as: 'q')]
    public ?string $search = '';

    protected function rules()
    {
        $rules = [
            'nama' => ['required'],
            'instansi' => ['required'],
            'unit_kerja' => ['required'],
            'jenis_peserta_id' => ['required'],
            'password' => $this->isUpdate ? ['nullable', 'min:8'] : ['required', 'min:8'],
        ];

        if ($this->jenis_peserta_id == 1) {
            // Validasi NIP untuk ASN
            $nipRule = ['required', 'numeric', 'digits:18'];
            
            // Cek duplikat NIP dalam event yang sama
            if ($this->isUpdate) {
                $nipRule[] = 'unique:peserta,nip,' . $this->selected_id . ',id,event_id,' . $this->id_event;
            } else {
                $nipRule[] = 'unique:peserta,nip,NULL,id,event_id,' . $this->id_event;
            }
            
            $rules['nip'] = $nipRule;
            $rules['jabatan'] = ['required'];
        } else if ($this->jenis_peserta_id == 2) {
            // Validasi NIK untuk Non ASN
            $nikRule = ['required', 'numeric', 'digits:16'];
            
            // Cek duplikat NIK dalam event yang sama
            if ($this->isUpdate) {
                $nikRule[] = 'unique:peserta,nik,' . $this->selected_id . ',id,event_id,' . $this->id_event;
            } else {
                $nikRule[] = 'unique:peserta,nik,NULL,id,event_id,' . $this->id_event;
            }
            
            $rules['nik'] = $nikRule;
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'nama.required' => 'harus diisi',
            'nip.required' => 'harus diisi',
            'nip.numeric' => 'harus angka',
            'nip.digits' => 'nip harus 18 digit',
            'nip.unique' => 'nip sudah terdaftar di event ini',
            'nik.required' => 'harus diisi',
            'nik.numeric' => 'harus angka',
            'nik.digits' => 'nik harus 16 digit',
            'nik.unique' => 'nik sudah terdaftar di event ini',
            'instansi.required' => 'harus diisi',
            'jabatan.required' => 'harus diisi',
            'password.required' => 'harus diisi',
            'password.min' => 'minimal 8 karakter',
            'jenis_peserta_id.required' => 'harus dipilih',
            'unit_kerja.required' => 'harus diisi'
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterJenisPeserta()
    {
        $this->resetPage();
    }

    public function updatedIsPortofolioCompleted()
    {
        $this->resetPage();
    }

    public function updatedIsActive()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filter_jenis_peserta', 'is_portofolio_completed', 'is_active']);
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset(['nama', 'nip', 'nik', 'jabatan', 'instansi', 'unit_kerja', 'jenis_peserta_id', 'password', 'is_active', 'isUpdate', 'selected_id']);
        $this->resetValidation();
    }

    public function mount($idEvent)
    {
        $this->id_event = $idEvent;
        $this->event = Event::findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::with(['jenisPeserta', 'golPangkat'])
            ->where('event_id', $this->id_event)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('jabatan', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter_jenis_peserta, function ($query) {
                $query->where('jenis_peserta_id', $this->filter_jenis_peserta);
            })
            ->when(!is_null($this->is_portofolio_completed) && $this->is_portofolio_completed !== '', function ($query) {
                if ($this->is_portofolio_completed === 'true') {
                    $query->whereNotNull('tempat_lahir')
                        ->whereNotNull('tgl_lahir')
                        ->whereNotNull('jk')
                        ->whereNotNull('agama_id')
                        ->whereNotNull('alamat')
                        ->whereNotNull('no_hp')
                        ->whereNotNull('foto')
                        ->where(function ($q) {
                            $q->where(function ($q1) {
                                $q1->where('jenis_peserta_id', 1)
                                    ->whereNotNull('nip')
                                    ->whereNotNull('gol_pangkat_id');
                            })->orWhere(function ($q2) {
                                $q2->where('jenis_peserta_id', 2)
                                    ->whereNotNull('nik');
                            });
                        });
                } elseif ($this->is_portofolio_completed === 'false') {
                    $query->where(function ($q) {
                        $q->whereNull('tempat_lahir')
                            ->orWhereNull('tgl_lahir')
                            ->orWhereNull('jk')
                            ->orWhereNull('agama_id')
                            ->orWhereNull('alamat')
                            ->orWhereNull('no_hp')
                            ->orWhereNull('foto')
                            ->orWhere(function ($q2) {
                                $q2->where(function ($q3) {
                                    $q3->where('jenis_peserta_id', 1)
                                        ->where(function ($qq) {
                                            $qq->whereNull('nip')
                                                ->orWhereNull('gol_pangkat_id');
                                        });
                                })->orWhere(function ($q4) {
                                    $q4->where('jenis_peserta_id', 2)
                                        ->whereNull('nik');
                                });
                            });
                    });
                }
            })
            ->when($this->is_active, function ($query) {
                $query->where('is_active', $this->is_active);
            })
            ->orderByDesc('id')
            ->paginate(10);

        $option_jenis_peserta = RefJenisPeserta::pluck('jenis_peserta', 'id');
        $option_status = ['true' => 'aktif', 'false' => 'tidak aktif'];

        return view('livewire.admin.event.show-peserta', [
            'data' => $data,
            'option_jenis_peserta' => $option_jenis_peserta,
            'option_status' => $option_status,
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->dispatch('open-modal-form');
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isUpdate = true;
        $this->selected_id = $id;

        $peserta = Peserta::findOrFail($id);
        $this->nama = $peserta->getOriginal('nama');
        $this->nip = $peserta->nip;
        $this->nik = $peserta->nik;
        $this->jabatan = $peserta->getOriginal('jabatan');
        $this->instansi = $peserta->getOriginal('instansi');
        $this->unit_kerja = $peserta->getOriginal('unit_kerja');
        $this->jenis_peserta_id = $peserta->jenis_peserta_id;
        $this->is_active = $peserta->is_active;

        $this->dispatch('open-modal-form');
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isUpdate) {
                $data = Peserta::findOrFail($this->selected_id);
                $old_data = $data->getOriginal();

                $data->nama = $this->nama;

                if ($this->jenis_peserta_id == 2 && ($data->jenis_peserta_id == 1 && $data->nip != null)) {
                    $data->nip = null;
                    $data->nik = $this->nik;
                    $data->jabatan = null;
                } else if ($this->jenis_peserta_id == 1 && ($data->jenis_peserta_id == 2 && $data->nik != null)) {
                    $data->nik = null;
                    $data->nip = $this->nip;
                    $data->jabatan = $this->jabatan;
                } else if ($this->jenis_peserta_id == 1) {
                    $data->nik = null;
                    $data->nip = $this->nip;
                    $data->jabatan = $this->jabatan;
                } else if ($this->jenis_peserta_id == 2) {
                    $data->nip = null;
                    $data->nik = $this->nik;
                    $data->jabatan = null;
                }

                $data->jenis_peserta_id = $this->jenis_peserta_id;
                $data->instansi = $this->instansi;
                $data->unit_kerja = $this->unit_kerja;
                $data->is_active = $this->is_active;

                if ($this->password) {
                    $data->password = bcrypt($this->password);
                }

                $data->save();

                activity_log($data, 'update', 'peserta', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data peserta']);
            } else {
                $data = Peserta::create([
                    'nama' => $this->nama,
                    'event_id' => $this->id_event,
                    'jenis_peserta_id' => $this->jenis_peserta_id,
                    'nip' => $this->nip,
                    'nik' => $this->nik,
                    'jabatan' => $this->jenis_peserta_id == 1 ? $this->jabatan : null,
                    'instansi' => $this->instansi,
                    'unit_kerja' => $this->unit_kerja,
                    'password' => bcrypt($this->password),
                    'is_active' => 'true',
                ]);

                activity_log($data, 'create', 'peserta');

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data peserta']);
            }

            $this->resetForm();
            $this->dispatch('close-modal-form');
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan: ' . $th->getMessage()]);
        }
    }

    public function deleteConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            $data = Peserta::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'peserta', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data peserta']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data peserta']);
        }
    }

    public function changeStatusPesertaConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('change-status-peserta-confirmation');
    }

    #[On('changeStatusPeserta')]
    public function changeStatusPeserta()
    {
        try {
            $data = Peserta::find($this->selected_id);

            if ($data->is_active == 'true') {
                $data->update(['is_active' => 'false']);
            } else {
                $data->update(['is_active' => 'true']);
            }

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil merubah status peserta']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal merubah status peserta']);
        }
    }

    public function openImportModal()
    {
        $this->reset('file_import');
        $this->resetValidation();
        $this->dispatch('open-modal-import');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['No', 'Nama (tanpa gelar)', 'Jenis Peserta (ASN/Non ASN)', 'NIP (18 digit, kosongkan jika Non ASN)', 'NIK (16 digit, kosongkan jika ASN)', 'Jabatan (kosongkan jika Non ASN)', 'Unit Kerja', 'Instansi', 'Password (min 8 karakter)'];
        $sheet->fromArray($headers, null, 'A1');

        // Contoh data
        $exampleData = [
            [1, 'BUDI SANTOSO', 'ASN', '199001012020011001', '', 'Kepala Bagian', 'BKD Jatim', 'Pemerintah Provinsi Jawa Timur', 'password123'],
            [2, 'SITI RAHAYU', 'Non ASN', '', '3201012345678901', '', 'SDM', 'PT ABC', 'password456'],
        ];
        $sheet->fromArray($exampleData, null, 'A2');

        // Auto width
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'template_import_peserta.xlsx';
        $path = storage_path('app/' . $filename);

        $writer->save($path);

        return response()->streamDownload(function () use ($path) {
            echo file_get_contents($path);
            unlink($path);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importPeserta()
    {
        $this->validate([
            'file_import' => 'required|mimes:xlsx,xls|max:2048',
        ], [
            'file_import.required' => 'file harus dipilih',
            'file_import.mimes' => 'file harus berformat xlsx atau xls',
            'file_import.max' => 'ukuran file maksimal 2MB',
        ]);

        try {
            $path = $this->file_import->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Skip header
            array_shift($rows);

            $imported = 0;
            $errors = [];
            $importedNips = []; // Track NIP dalam batch import
            $importedNiks = []; // Track NIK dalam batch import

            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                // Skip empty row
                if (empty($row[1])) {
                    continue;
                }

                $nama = trim($row[1] ?? '');
                $jenisPesertaStr = strtolower(trim($row[2] ?? ''));
                $nip = trim($row[3] ?? '');
                $nik = trim($row[4] ?? '');
                $jabatan = trim($row[5] ?? '');
                $unitKerja = trim($row[6] ?? '');
                $instansi = trim($row[7] ?? '');
                $password = trim($row[8] ?? '');

                // Determine jenis peserta
                $jenisPesertaId = null;
                if ($jenisPesertaStr === 'asn') {
                    $jenisPesertaId = 1;
                } elseif ($jenisPesertaStr === 'non asn') {
                    $jenisPesertaId = 2;
                }

                // Validation
                if (empty($nama)) {
                    $errors[] = "Baris $rowNum: Nama harus diisi";
                    continue;
                }

                if (!$jenisPesertaId) {
                    $errors[] = "Baris $rowNum: Jenis peserta harus ASN atau Non ASN";
                    continue;
                }

                if ($jenisPesertaId == 1 && (empty($nip) || strlen($nip) != 18)) {
                    $errors[] = "Baris $rowNum: NIP harus 18 digit untuk ASN";
                    continue;
                }

                // Cek duplikat NIP di event ini (database)
                if ($jenisPesertaId == 1) {
                    $existingNip = Peserta::where('event_id', $this->id_event)
                        ->where('nip', $nip)
                        ->exists();
                    
                    if ($existingNip) {
                        $errors[] = "Baris $rowNum: NIP $nip sudah terdaftar di event ini";
                        continue;
                    }

                    // Cek duplikat NIP dalam file import yang sama
                    if (in_array($nip, $importedNips)) {
                        $errors[] = "Baris $rowNum: NIP $nip duplikat dalam file import";
                        continue;
                    }

                    $importedNips[] = $nip;
                }

                if ($jenisPesertaId == 2 && (empty($nik) || strlen($nik) != 16)) {
                    $errors[] = "Baris $rowNum: NIK harus 16 digit untuk Non ASN";
                    continue;
                }

                // Cek duplikat NIK di event ini (database)
                if ($jenisPesertaId == 2) {
                    $existingNik = Peserta::where('event_id', $this->id_event)
                        ->where('nik', $nik)
                        ->exists();
                    
                    if ($existingNik) {
                        $errors[] = "Baris $rowNum: NIK $nik sudah terdaftar di event ini";
                        continue;
                    }

                    // Cek duplikat NIK dalam file import yang sama
                    if (in_array($nik, $importedNiks)) {
                        $errors[] = "Baris $rowNum: NIK $nik duplikat dalam file import";
                        continue;
                    }

                    $importedNiks[] = $nik;
                }

                if ($jenisPesertaId == 1 && empty($jabatan)) {
                    $errors[] = "Baris $rowNum: Jabatan harus diisi untuk ASN";
                    continue;
                }

                if (empty($unitKerja)) {
                    $errors[] = "Baris $rowNum: Unit kerja harus diisi";
                    continue;
                }

                if (empty($instansi)) {
                    $errors[] = "Baris $rowNum: Instansi harus diisi";
                    continue;
                }

                if (empty($password) || strlen($password) < 8) {
                    $errors[] = "Baris $rowNum: Password harus minimal 8 karakter";
                    continue;
                }

                // Create peserta
                $data = Peserta::create([
                    'nama' => $nama,
                    'event_id' => $this->id_event,
                    'jenis_peserta_id' => $jenisPesertaId,
                    'nip' => $jenisPesertaId == 1 ? $nip : null,
                    'nik' => $jenisPesertaId == 2 ? $nik : null,
                    'jabatan' => $jenisPesertaId == 1 ? $jabatan : null,
                    'unit_kerja' => $unitKerja,
                    'instansi' => $instansi,
                    'password' => bcrypt($password),
                    'is_active' => 'true',
                ]);

                activity_log($data, 'create', 'peserta (import)');
                $imported++;
            }

            $this->reset('file_import');
            $this->dispatch('close-modal-import');

            if (count($errors) > 0) {
                // Kategorikan error
                $errorSummary = $this->categorizeImportErrors($errors);
                
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => "Import selesai: $imported berhasil, " . count($errors) . " gagal.",
                ]);
                
                // Dispatch event dengan data error
                $this->dispatch('show-import-errors', 
                    errors: $errors,
                    summary: $errorSummary,
                    imported: $imported,
                    failed: count($errors)
                );
            } else {
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => "Berhasil import $imported peserta",
                ]);
            }
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Gagal import: ' . $th->getMessage()]);
        }
    }

    private function categorizeImportErrors($errors)
    {
        $categories = [
            'duplikat_database' => 0,
            'duplikat_file' => 0,
            'format_salah' => 0,
            'data_kosong' => 0,
            'lainnya' => 0,
        ];

        foreach ($errors as $error) {
            if (strpos($error, 'sudah terdaftar di event') !== false) {
                $categories['duplikat_database']++;
            } elseif (strpos($error, 'duplikat dalam file') !== false) {
                $categories['duplikat_file']++;
            } elseif (strpos($error, 'harus') !== false && (strpos($error, 'digit') !== false || strpos($error, 'karakter') !== false)) {
                $categories['format_salah']++;
            } elseif (strpos($error, 'harus diisi') !== false) {
                $categories['data_kosong']++;
            } else {
                $categories['lainnya']++;
            }
        }

        return $categories;
    }
}
