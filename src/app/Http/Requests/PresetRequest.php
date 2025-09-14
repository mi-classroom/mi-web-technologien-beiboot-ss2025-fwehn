<?php

namespace App\Http\Requests;

use App\FolderOperation;
use App\Http\Requests\Concerns\HasIptc;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PresetRequest extends FormRequest
{
    use HasIptc;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => "required|string",

            ...$this::iptcRules()
        ];
    }

//    TODO attributes
}
