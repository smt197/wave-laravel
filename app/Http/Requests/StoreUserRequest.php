<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Enums\StatusResponseEnum;
use App\Enums\UserRole;
use App\Rules\CustomPasswordRule;
use App\Rules\CustumPasswordRule;
use App\Rules\PasswordRules;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
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
    public function Rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'statut' => 'required|string|max:255',
            'telephone' => 'required|string|max:255',
            'password' =>['confirmed'],
            'role_id' =>'required|string|max:255|exists:roles,id'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'Le email est obligatoire.',
            'email.unique' => "Cet login est déjà utilisé.",
            'statut.required' =>'le statut est obligatoire.',
            'role_id.required' => 'Le role est obligatoire.',
            'role_id.exists' => 'Le rôle sélectionné n\'existe pas.',

        ];
    }

    function validation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StatusResponseEnum::ECHEC,404));
    }
}