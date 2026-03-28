<?php

namespace App\Http\Requests;

use App\Models\Character;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCharacterRequest extends FormRequest
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
        $isPlayer = $this->input('type') === Character::TYPE_PLAYER;

        return [
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in([Character::TYPE_NPC, Character::TYPE_PLAYER])],

            // Kept for NPC compatibility (existing UI/filters).
            'status' => ['nullable', 'string', 'in:alive,dead,missing,unknown'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],

            'attributes' => ['nullable', 'array'],
            'attributes.*.key' => ['nullable', 'string', 'max:100'],
            'attributes.*.value' => ['nullable', 'string', 'max:5000'],

            'relationships' => ['nullable', 'array'],
            'relationships.*.related_character_id' => [
                'required',
                'integer',
                Rule::exists('characters', 'id')->where(fn ($query) => $query->where('user_id', $this->user()?->id)),
            ],
            'relationships.*.type' => ['required', 'string', 'max:50'],
            'relationships.*.description' => ['nullable', 'string', 'max:5000'],

            'player_user_id' => [Rule::requiredIf($isPlayer), 'integer', 'exists:users,id'],
            'campaign_id' => [Rule::requiredIf($isPlayer), 'integer', 'exists:campaigns,id'],
        ];
    }
}
