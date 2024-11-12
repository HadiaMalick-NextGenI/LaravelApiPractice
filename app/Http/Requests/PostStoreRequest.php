<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostStoreRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg,gif',
            'is_published' => 'required|boolean'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Title is required',
            'title.max' => 'Title should be less than 255 characters',
            'description.required' => 'Description is required',
            'image.required' => 'Image is required',
            'image.mimes' => 'Image must be in png,jpg,jpeg,gif format',
            'is_published.required' => 'Is_published is required',
            'is_published.boolean' => 'is_published should be boolean'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error('Validation error', details: $validator->errors()->all())
        );
    }
}
