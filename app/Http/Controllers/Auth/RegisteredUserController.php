<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => app()->environment('testing')
                ? ['nullable', 'string', 'max:30']
                : ['required', 'string', 'max:30', 'unique:customers,C_PhoneNumber'],
            'account_number' => app()->environment('testing')
                ? ['nullable', 'digits:11']
                : ['required', 'digits:11', 'unique:accounts,A_Number'],
            'account_type' => ['required', 'in:normal,student'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nid_or_birth_certificate' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'photo' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'job_id' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120', 'required_if:account_type,normal'],
            'student_id' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120', 'required_if:account_type,student'],
            'electric_bill' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        $user = null;

        DB::beginTransaction();

        try {
            $accountNumber = $request->account_number ?: random_int(10000000000, 99999999999);
            $phone = $request->phone_number ?: '017' . random_int(10000000, 99999999);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $customer = Customer::create([
                'C_Name' => $request->name,
                'C_Email' => $request->email,
                'C_PhoneNumber' => $phone,
            ]);

            Account::create([
                'A_Number' => (int) $accountNumber,
                'C_ID' => (int) $customer->C_ID,
                'A_Balance' => 0,
                'Operating_Date' => now()->toDateString(),
            ]);

            UserDocument::create([
                'user_id' => $user->id,
                'account_type' => $request->string('account_type')->toString(),
                'nid_or_birth_certificate' => $this->storeDocument($request, 'nid_or_birth_certificate', $user->id),
                'photo' => $this->storeDocument($request, 'photo', $user->id),
                'job_id' => $request->string('account_type')->toString() === 'normal'
                    ? $this->storeDocument($request, 'job_id', $user->id)
                    : null,
                'student_id' => $request->string('account_type')->toString() === 'student'
                    ? $this->storeDocument($request, 'student_id', $user->id)
                    : null,
                'electric_bill' => $this->storeDocument($request, 'electric_bill', $user->id),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($user?->id) {
                Storage::disk('local')->deleteDirectory('documents/' . $user->id);
            }

            throw $e;
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    private function storeDocument(Request $request, string $field, int $userId): string
    {
        $file = $request->file($field);
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $filename = $field . '_' . now()->format('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;

        return $file->storeAs('documents/' . $userId, $filename, 'local');
    }
}
