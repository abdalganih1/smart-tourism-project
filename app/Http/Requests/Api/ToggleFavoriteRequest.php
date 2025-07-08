<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResolvesPolymorphicTargets; // Import the Trait

class ToggleFavoriteRequest extends FormRequest
{
    use ResolvesPolymorphicTargets; // Use the Trait

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $targetType = $this->input('target_type');
        // Define allowed target types as per your schema 'Enum'
        $allowedTargetTypes = ['TouristSite', 'Product', 'Article', 'Hotel', 'SiteExperience'];

        // Get the actual table name based on the target_type
        $targetTable = $this->mapTargetTypeToTable($targetType);
//   dd($targetType, $targetTable);
        // Fallback table name for Rule::exists if $targetTable is null (invalid target_type will be caught by Rule::in first)
        $existsRuleTable = $targetTable ?? 'invalid_dummy_table'; // Use a dummy table to prevent DB errors if map returns null

        return [
            'target_type' => [
                'required',
                'string',
                Rule::in($allowedTargetTypes),
            ],
            'target_id' => [
                'required',
                'integer',
                // Use Rule::exists with the dynamically determined table name
                Rule::exists($existsRuleTable, 'id'),
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'target_type' => __('Target Type'),
            'target_id' => __('Target ID'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'target_type.required' => 'يجب تحديد نوع العنصر.',
            'target_type.in' => 'نوع العنصر المحدد غير صالح.',
            'target_id.required' => 'يجب تحديد العنصر.',
            'target_id.integer' => 'معرف العنصر يجب أن يكون رقماً صحيحاً.',
            'target_id.exists' => 'العنصر المحدد غير موجود أو نوع العنصر غير صحيح.', // More specific message
        ];
    }
}