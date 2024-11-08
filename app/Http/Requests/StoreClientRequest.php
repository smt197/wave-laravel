<?php

namespace App\Http\Requests;
use App\Rules\TelephoneRule;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\StatusResponseEnum;
use App\Rules\CustomPasswordRule;


class StoreClientRequest extends FormRequest
{
    use RestResponseTrait;
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
        $rules = [
            'surname' => ['required', 'string', 'max:255','unique:clients,surname'],
            'adresse' => ['string', 'max:255'],
            'telephone' => ['required'],
            'solde' => 'nullable|integer',
            'soldeMax' => 'nullable|numeric',
            'cumulTransaction' => 'nullable|integer',

            'user' => ['sometimes','array'],
            'user.name' => ['required_with:user','string'],
            'user.email' => ['required_with:user','string'],
            'user.password' => ['required_with:user','confirmed'],
            // 'user.photo' => ['required_with:user','image', 'mimes:jpeg,png,jpg'],
            'user.statut' => ['nullable','string'],
            'user.role_id' => ['required_with:user.role_id','integer'],
        ];

        return $rules;
    }

    function messages()
    {
        return [
            // 'surname.required' => "Le surnom est obligatoire.",
            // 'surname.unique' => "Ce surnom existe déjà.",
            // 'surname.string' => "Le surnom doit être une chaîne de caractères.",
            // 'surname.max' => "Le surnom ne doit pas dépasser 255 caractères.",
            // 'adresse.string' => "L'adresse doit être une chaîne de caractères.",
            'adresse.max' => "L'adresse ne doit pas dépasser 255 caractères.",
            // 'telephone.required' => "Le numéro de téléphone est obligatoire.",
            'solde.integer' => "Le solde doit être un entier.",
            'soldeMax.numeric' => "Le solde max doit être un nombre.",
            'cumulTransaction.integer' => "Le cumul de transactions doit être un entier.",
            'user.name.required_with' => "Le nom est obligatoire lorsque l'utilisateur est fourni.",
            'user.name.string' => "Le nom doit être une chaîne de caractères.",
            'user.email.required_with' => "L'email est obligatoire lorsque l'utilisateur est fourni.",
            'user.email.string' => "L'email doit être une chaîne de caractères.",
            'user.password.required_with' => "Le mot de passe est obligatoire lorsque l'utilisateur est fourni.",
            'user.password.confirmed' => "Les mots de passe ne sont pas identiques.",
            // 'user.photo.required_with' => "La photo est obligatoire lorsque l'utilisateur est fourni.",
            // 'user.photo.image' => "La photo doit être une image.",
            // 'user.photo.mimes' => "La photo doit être au format jpeg, png ou jpg.",
            'user.statut.string' => "Le statut doit être une chaîne de caractères.",
            'user.role_id.required_with' => "Le rôle est obligatoire lorsque l'utilisateur est fourni.",
            'user.role_id.integer' => "Le rôle doit être un entier."

        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StatusResponseEnum::ECHEC,404));
    }
}
