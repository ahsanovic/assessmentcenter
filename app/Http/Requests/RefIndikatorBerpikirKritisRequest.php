<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefIndikatorBerpikirKritisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'indikator_nama' => 'required',
            'indikator_nomor' => 'required|numeric',
            'kualifikasi_deskripsi.*.deskripsi' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'indikator_nama.required' => 'harus diisi',
            'indikator_nomor.required' => 'harus diisi',
            'indikator_nomor.numeric' => 'harus angka',
            'kualifikasi_deskripsi.*.deskripsi.required' => 'deskripsi harus diisi',
        ];
    }
}
