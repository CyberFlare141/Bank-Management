<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            'phone_number' => ['required', 'string', 'max:30', 'unique:customers,C_PhoneNumber'],
            'account_number' => ['required', 'integer', 'unique:accounts,A_Number'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $customer = Customer::create([
                'C_Name' => $request->name,
                'C_Email' => $request->email,
                'C_PhoneNumber' => $request->phone_number,
            ]);

            Account::create([
                'A_Number' => (int) $request->account_number,
                'C_ID' => (int) $customer->C_ID,
                'A_Balance' => 0,
                'Operating_Date' => now()->toDateString(),
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('home', absolute: false));
    }
}
