<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class GeneratePromptRequest extends FormRequest
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
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:10240', // 10MB max
                'min:1', // Minimum 1KB
                'dimensions:min_width=64,min_height=64,max_width=4096,max_height=4096',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.required' => 'An image file is required.',
            'image.file' => 'The uploaded file must be a valid file.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif, webp.',
            'image.max' => 'The image may not be greater than 10MB.',
            'image.min' => 'The image file is too small (minimum 1KB).',
            'image.dimensions' => 'The image dimensions must be between 64x64 and 4096x4096 pixels.',
        ];
    }
}
