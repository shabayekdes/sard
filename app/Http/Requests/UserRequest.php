<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

class UserRequest extends FormRequest
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
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;
        $tenantId = function_exists('tenant') && tenant() ? tenant()->getTenantKey() : null;

        $emailRule = Rule::unique('users', 'email')
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId))
            ->when($tenantId === null, fn ($q) => $q->whereNull('tenant_id'));
        if ($userId) {
            $emailRule->ignore($userId);
        }

        $phoneRule = Rule::unique('users', 'phone')
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId))
            ->when($tenantId === null, fn ($q) => $q->whereNull('tenant_id'));
        if ($userId) {
            $phoneRule->ignore($userId);
        }

        return [
            'name'             => 'required|string',
            'email'            => ['required', 'email', $emailRule],
            'phone'            => ['nullable', 'string', $phoneRule],
            'password'         => $this->isMethod('POST') ? 'required|string|min:6' : 'nullable|string|min:6',
            'password_confirmation' => $this->isMethod('POST') ? 'required|same:password' : 'nullable|same:password',
            'roles'            => 'required'
        ];
    }
}
