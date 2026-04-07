<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.profile.edit', compact('customer'));
    }

    public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $rules = [
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|unique:customers,email,' . $customer->id,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ];

        if ($request->filled('password')) {
            $rules['current_password'] = 'required';
            $rules['password']         = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $customer->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
            }
            $customer->password = Hash::make($request->password);
        }

        $customer->name    = $validated['name'];
        $customer->email   = $validated['email'];
        $customer->phone   = $validated['phone'] ?? $customer->phone;
        $customer->address = $validated['address'] ?? $customer->address;
        $customer->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
