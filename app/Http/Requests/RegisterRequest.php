<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:client,driver',
        ];

        if ($this->input('role') === 'driver') {
            $rules = array_merge($rules, [
                'description'     => 'nullable|string',
                'car_model'       => 'required|string|max:255',
                'city'            => 'required|string|max:255',
                'work_days'       => 'required|array',
                'work_start'      => 'required|date_format:H:i',
                'work_end'        => 'required|date_format:H:i',
                'profile_picture' => 'nullable|image|max:2048',
            ]);
        }

        return $rules;
    }
}

