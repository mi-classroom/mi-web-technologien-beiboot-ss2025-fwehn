<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\HasIptc;
use Illuminate\Foundation\Http\FormRequest;

class ImageSelectionRequest extends FormRequest
{
    use HasIptc;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images' => 'required|array',
            'images.*' => 'integer|exists:images,id',

            'name_prefix' => 'nullable|string',

            ...$this::iptcRules()
        ];
    }

//    TODO attributes
}
