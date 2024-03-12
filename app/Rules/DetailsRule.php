<?php
declare(strict_types=1);

namespace App\Rules;

use App\Models\Enums\ItemEnum;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Translation\PotentiallyTranslatedString;

class DetailsRule implements ValidationRule, DataAwareRule
{
    /** @var array<string, mixed> */
    protected array $data = [];

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rules = match ($this->data['type']) {
            ItemEnum::BOOK->value => ['genre' => 'required|string'],
            ItemEnum::COMIC->value => ['series' => 'required|string'],
            ItemEnum::SHORT_STORY_COLLECTION->value => ['theme' => 'required|string'],
            default => [],
        };

        if (empty($rules)) {
            $fail('The :attribute is not correct for given type.');
            return;
        }

        $validator = Validator::make($this->data['details'], $rules);
        if (!$validator->passes()) {
            $fail($validator->messages()->all()[0]);
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
