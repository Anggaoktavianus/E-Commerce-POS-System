<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

abstract class SecureFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    abstract public function rules(): array;

    /**
     * Sanitize input data
     */
    protected function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Remove potential XSS content
                $data[$key] = strip_tags($value);
                $data[$key] = htmlspecialchars($data[$key], ENT_QUOTES, 'UTF-8');
                $data[$key] = trim($data[$key]);
            }
        }
        
        return $data;
    }

    /**
     * Get validated and sanitized data
     */
    public function getValidatedSanitized(): array
    {
        return $this->sanitize($this->validated());
    }

    /**
     * Handle failed validation
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->isJson() && !$this->header('X-Requested-With')) {
                $validator->errors()->add('headers', 'X-Requested-With header required for AJAX requests');
            }
        });
    }
}
