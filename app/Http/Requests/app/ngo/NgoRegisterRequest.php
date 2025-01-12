<?php

namespace App\Http\Requests\app\ngo;

use Illuminate\Foundation\Http\FormRequest;

class NgoRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:emails,value',
            'contact' => 'required|unique:contacts,value',
            'district_id' => 'required|integer|exists:districts,id',
            "password" => "required",
            'area' => 'required|string|max:255',
            'abbr' => 'required|string|max:50|unique:ngos,abbr',
            'ngo_type_id' => 'required|integer|exists:ngo_types,id',
            'district_id' => 'required|integer|exists:districts,id',
            'name_en' => 'required|string|unique:ngo_trans,name',
            'name_ps' => 'required|string|unique:ngo_trans,name',
            'name_fa' => 'required|string|unique:ngo_trans,name',
        ];
    }
}
