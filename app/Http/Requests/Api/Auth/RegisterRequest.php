<?php

namespace App\Http\Requests\Api\Auth;

use App\Helpers\Helper;
use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
{
    use ApiResponse;
    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
        parent::__construct();
    }

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'  => 'string|max:255',
            'email'      => 'required|string|email|max:255|unique:users,email',
            // 'password'   => [
            //     'required',
            //     'string',
            //     'min:8',
            //     'confirmed',
            //     'regex:/[a-z]/',
            //     'regex:/[A-Z]/',
            //     'regex:/[0-9]/',
            //     'regex:/[@$!%*?&#]/'
            // ],
            'password'   => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     */
    // protected function failedValidation(Validator $validator): void {
    //     $response = $this->helper->jsonResponse(false, 'Validation failed', 422, ['errors' => $validator->errors()], 422);
    //     throw new ValidationException($validator, $response);
    // }

    /**
     * 
     */
    protected function failedValidation(Validator $validator): never
    {
        $fieldsToCheck = ['name', 'email', 'password'];
        $message = 'Validation error';

        foreach ($fieldsToCheck as $field) {
            $errors = $validator->errors()->get($field);
            if (!empty($errors)) {
                $message = $errors[0];
                break;
            }
        }

        $response = $this->error(
            422,
            $message,
            $validator->errors(),
        );

        throw new ValidationException($validator, $response);
    }
}
