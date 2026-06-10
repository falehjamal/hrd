<?php

namespace App\Http\Requests;

use App\Models\OrganizationalUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateOrganizationalUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isHrUser();
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('organizational_units', 'code')->ignore($this->route('organizational_unit'))],
            'name' => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer', Rule::exists('organizational_units', 'id')],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $parentId = $this->input('parent_id');
            $unit = $this->route('organizational_unit');

            if (! $parentId || ! $unit instanceof OrganizationalUnit) {
                return;
            }

            if ((int) $parentId === $unit->id) {
                $validator->errors()->add('parent_id', 'Unit induk tidak boleh sama dengan unit ini.');

                return;
            }

            $parent = OrganizationalUnit::query()->find($parentId);

            if ($parent && $parent->isDescendantOf($unit->id)) {
                $validator->errors()->add('parent_id', 'Unit induk tidak boleh berada di bawah unit ini.');
            }
        });
    }
}
