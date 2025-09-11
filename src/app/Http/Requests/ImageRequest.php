<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\HasIptc;
use App\Models\Image;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ImageRequest extends FormRequest
{
    use HasIptc;

    public function authorize(): bool
    {
        $image = $this->route('image');

        if ($image) {
            return Gate::allows('update', $image);
        }

        return Gate::allows('create', Image::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            ...$this::iptcRules()
        ];
    }


    public function attributes(): array
    {
        return [
            trans('image'),
            ...$this::iptcAttributes(),
        ];
    }
}
