<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /** Show the settings form */
    public function edit()
    {
        $setting = GeneralSetting::current(); // helper method that returns the first row
        return view('settings.edit', compact('setting'));
    }

    /** Persist the changes */
    public function update(Request $request)
    {
        $setting = GeneralSetting::current();

        $validated = $request->validate([
            'shop_name'         => 'required|string|max:191',
            'legal_name'        => 'nullable|string|max:191',
            'tagline'           => 'nullable|string|max:191',

            'phone_primary'     => 'nullable|string|max:30',
            'phone_secondary'   => 'nullable|string|max:30',
            'whatsapp'          => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:191',
            'website'           => 'nullable|url|max:191',

            'address_line1'     => 'nullable|string|max:191',
            'address_line2'     => 'nullable|string|max:191',
            'city'              => 'nullable|string|max:100',
            'state'             => 'nullable|string|max:100',
            'postcode'          => 'nullable|string|max:20',
            'country'           => 'nullable|string|max:100',

            'tax_id'            => 'nullable|string|max:50',
            'vat_registration_no' => 'nullable|string|max:50',
            'currency_code'     => 'required|string|size:3',
            'timezone'          => 'required|string|max:64',

            'invoice_footer'    => 'nullable|string',
            'email_signature'   => 'nullable|string',

            'logo'              => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'favicon'           => 'nullable|image|mimes:png,ico|max:512',

            'default_language'  => 'required|string|max:10',
            'invoice_auto_print'=> 'nullable|boolean',
            'receipt_paper_size'=> 'required|in:A4,A5,80mm,58mm',
        ]);

        // Remove file fields before update
        unset($validated['logo'], $validated['favicon']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('branding', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            if ($setting->favicon_path) {
                Storage::disk('public')->delete($setting->favicon_path);
            }
            $validated['favicon_path'] = $request->file('favicon')->store('branding', 'public');
        }

        // Handle checkbox (default is false if not sent)
        $validated['invoice_auto_print'] = $request->has('invoice_auto_print');

        // Update the settings
        $setting->update($validated);

        // Clear cache if applicable
        cache()->forget('general_settings');

        return back()->with('success', 'Settings updated successfully!');
    }
}
