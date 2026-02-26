<?php

namespace App\Http\Requests\Auth;

use App\Models\Customer;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
        return [
            'account_number' => ['required', 'digits:11'],
            'email' => ['nullable', 'string', 'email'],
            'phone_number' => ['nullable', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $email = trim((string) $this->string('email'));
            $phone = trim((string) $this->string('phone_number'));

            if ($email === '' && $phone === '') {
                $validator->errors()->add('email', 'Email or phone number is required.');
                $validator->errors()->add('phone_number', 'Email or phone number is required.');
            }
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $accountNumber = (int) $this->integer('account_number');
        $emailInput = trim((string) $this->string('email'));
        $phoneInput = trim((string) $this->string('phone_number'));
        $password = (string) $this->string('password');
        $remember = $this->boolean('remember');

        $customer = Customer::query()
            ->whereHas('accounts', function ($query) use ($accountNumber) {
                $query->where('A_Number', $accountNumber);
            })
            ->first();

        if (! $customer?->C_Email) {
            $this->failAuthentication();
        }

        if ($emailInput !== '' && strcasecmp($emailInput, (string) $customer->C_Email) !== 0) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if ($phoneInput !== '' && ! $this->phonesMatch($phoneInput, (string) $customer->C_PhoneNumber)) {
            throw ValidationException::withMessages([
                'phone_number' => trans('auth.failed'),
            ]);
        }

        if (! Auth::attempt([
            'email' => $customer->C_Email,
            'password' => $password,
        ], $remember)) {
            $this->failAuthentication();
        }

        RateLimiter::clear($this->throttleKey());
    }

    private function failAuthentication(): void
    {
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'account_number' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'account_number' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $accountNumber = (string) $this->string('account_number');
        $email = (string) $this->string('email');
        $phone = (string) $this->string('phone_number');

        return Str::transliterate($accountNumber.'|'.$email.'|'.$phone.'|'.$this->ip());
    }

    private function phonesMatch(string $input, string $stored): bool
    {
        $inputDigits = preg_replace('/\D/', '', $input);
        $storedDigits = preg_replace('/\D/', '', $stored);

        if ($inputDigits !== '' && $storedDigits !== '') {
            return $inputDigits === $storedDigits;
        }

        return trim($input) !== '' && trim($stored) !== '' && $input === $stored;
    }
}
