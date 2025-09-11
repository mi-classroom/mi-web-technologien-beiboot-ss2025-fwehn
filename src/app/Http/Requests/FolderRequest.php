<?php

namespace App\Http\Requests;

use App\FolderOperation;
use App\Http\Requests\Concerns\HasIptc;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FolderRequest extends FormRequest
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
            "parent_folder_id" => "nullable|integer|exists:folders,id",

            "operation" => ["required", Rule::enum(FolderOperation::class)],

            ...$this::iptcRules()
        ];
    }

//    TODO attributes
}
