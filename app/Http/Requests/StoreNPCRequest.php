<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNPCRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:alive,dead,missing,unknown'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'attributes' => ['nullable', 'array'],
            'attributes.*.key' => ['nullable', 'string', 'max:100'],
            'attributes.*.value' => ['nullable', 'string', 'max:5000'],
            'relationships' => ['nullable', 'array'],
            'relationships.*.related_npc_id' => ['required', 'integer', 'exists:npcs,id'],
            'relationships.*.type' => ['required', 'string', 'max:50'],
            'relationships.*.description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
