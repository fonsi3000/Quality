<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user),
            ],

            'password' => $this->isMethod('POST')
                ? ['required', 'confirmed', Password::defaults()]
                : ['nullable', 'confirmed', Password::defaults()],

            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048'
            ],

            'unit_id' => [
                'nullable',
                'exists:units,id',
            ],

            'process_id' => [
                'nullable',
                'exists:processes,id',
            ],

            'second_process_id' => [
                'nullable',
                'different:process_id',
                'exists:processes,id',
            ],

            'position_id' => [
                'nullable',
                'exists:positions,id',
            ],

            'active' => [
                'boolean',
                'nullable'
            ],

            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'profile_photo.image' => 'El archivo debe ser una imagen.',
            'profile_photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'profile_photo.max' => 'La imagen no debe pesar más de 2MB.',
            'unit_id.exists' => 'La unidad seleccionada no es válida.',
            'process_id.exists' => 'El proceso seleccionado no es válido.',
            'second_process_id.exists' => 'El segundo proceso seleccionado no es válido.',
            'second_process_id.different' => 'El segundo proceso no puede ser igual al primero.',
            'position_id.exists' => 'El cargo seleccionado no es válido.',
            'active.boolean' => 'El estado debe ser activo o inactivo.',
            'role.required' => 'El rol es obligatorio.',
            'role.exists' => 'El rol seleccionado no es válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'profile_photo' => 'foto de perfil',
            'unit_id' => 'unidad',
            'process_id' => 'proceso',
            'second_process_id' => 'segundo proceso',
            'position_id' => 'cargo',
            'active' => 'estado',
            'role' => 'rol',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('active')) {
            $this->merge([
                'active' => filter_var($this->active, FILTER_VALIDATE_BOOLEAN)
            ]);
        }

        if ($this->has('role')) {
            $this->merge([
                'role' => strtolower($this->role)
            ]);
        }
    }
}
