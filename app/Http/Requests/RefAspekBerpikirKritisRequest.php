<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefAspekBerpikirKritisRequest extends FormRequest
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
            'aspek' => 'required',
            'aspek_nomor' => 'required|numeric',
            'indikator_nomor' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'aspek.required' => 'harus diisi',
            'aspek_nomor.required' => 'harus diisi',
            'aspek_nomor.numeric' => 'harus angka',
            'indikator_nomor.required' => 'harus dipilih',
        ];
    }
}
