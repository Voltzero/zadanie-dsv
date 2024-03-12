<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Enums\ItemEnum;
use App\Rules\DetailsRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateItemRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'price' => 'required|decimal:2',
            'type' => ['required', Rule::enum(ItemEnum::class)],
            'author_id' => 'required|exists:authors,id',
            'details' => ['required', 'array', 'size:1' , new DetailsRule()],
        ];
    }
}
