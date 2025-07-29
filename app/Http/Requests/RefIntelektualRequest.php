<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefIntelektualRequest extends FormRequest
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
            'indikator' => 'required',
            'sub_tes' => 'required|numeric|min:1',
            'kualifikasi.*.uraian_potensi' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'indikator.required' => 'harus diisi',
            'sub_tes.required' => 'harus diisi',
            'sub_tes.numeric' => 'harus angka',
            'sub_tes.min' => 'harus lebih besar dari 0',
            'kualifikasi.*.uraian_potensi.required' => 'uraian potensi harus diisi',
        ];
    }
}
