@extends('dashboard.layouts.admin-layout')

@section('title','General Settings')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">General Settings</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- BUSINESS INFO --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">Business Identity</div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Shop Name *</label>
                    <input name="shop_name" class="form-control" value="{{ old('shop_name',$setting->shop_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Legal Name</label>
                    <input name="legal_name" class="form-control" value="{{ old('legal_name',$setting->legal_name) }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Tagline / Slogan</label>
                    <input name="tagline" class="form-control" value="{{ old('tagline',$setting->tagline) }}">
                </div>
            </div>
        </div>

        {{-- CONTACT --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">Contact &amp; Address</div>
            <div class="card-body row g-3">
                <div class="col-md-4"><label class="form-label">Phone 1</label>
                    <input name="phone_primary" class="form-control" value="{{ old('phone_primary',$setting->phone_primary) }}">
                </div>
                <div class="col-md-4"><label class="form-label">Phone 2</label>
                    <input name="phone_secondary" class="form-control" value="{{ old('phone_secondary',$setting->phone_secondary) }}">
                </div>
                <div class="col-md-4"><label class="form-label">WhatsApp</label>
                    <input name="whatsapp" class="form-control" value="{{ old('whatsapp',$setting->whatsapp) }}">
                </div>

                <div class="col-md-6"><label class="form-label">E-mail</label>
                    <input name="email" type="email" class="form-control" value="{{ old('email',$setting->email) }}">
                </div>
                <div class="col-md-6"><label class="form-label">Website</label>
                    <input name="website" type="url" class="form-control" value="{{ old('website',$setting->website) }}">
                </div>

                <div class="col-md-8"><label class="form-label">Address Line 1</label>
                    <input name="address_line1" class="form-control" value="{{ old('address_line1',$setting->address_line1) }}">
                </div>
                <div class="col-md-4"><label class="form-label">Address Line 2</label>
                    <input name="address_line2" class="form-control" value="{{ old('address_line2',$setting->address_line2) }}">
                </div>

                <div class="col-md-3"><label class="form-label">City</label>
                    <input name="city" class="form-control" value="{{ old('city',$setting->city) }}">
                </div>
                <div class="col-md-3"><label class="form-label">State</label>
                    <input name="state" class="form-control" value="{{ old('state',$setting->state) }}">
                </div>
                <div class="col-md-3"><label class="form-label">Post Code</label>
                    <input name="postcode" class="form-control" value="{{ old('postcode',$setting->postcode) }}">
                </div>
                <div class="col-md-3"><label class="form-label">Country</label>
                    <input name="country" class="form-control" value="{{ old('country',$setting->country) }}">
                </div>
            </div>
        </div>

        {{-- REGULATORY / FINANCE --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">Regulatory & Finance</div>
            <div class="card-body row g-3">
                <div class="col-md-4"><label class="form-label">Tax / BIN / TIN</label>
                    <input name="tax_id" class="form-control" value="{{ old('tax_id',$setting->tax_id) }}">
                </div>
                <div class="col-md-4"><label class="form-label">VAT Reg. No.</label>
                    <input name="vat_registration_no" class="form-control" value="{{ old('vat_registration_no',$setting->vat_registration_no) }}">
                </div>
                <div class="col-md-2"><label class="form-label">Currency</label>
                    <input name="currency_code" class="form-control text-uppercase" value="{{ old('currency_code',$setting->currency_code) }}" maxlength="3" required>
                </div>
                <div class="col-md-2"><label class="form-label">Timezone</label>
                    <input name="timezone" class="form-control" value="{{ old('timezone',$setting->timezone) }}" required>
                </div>
            </div>
        </div>

        {{-- BRANDING --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">Branding</div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Logo (PNG/JPG)</label>
                    <input type="file" name="logo" class="form-control">
                    @if($setting->logo_path)
                        <img src="{{ asset('storage/'.$setting->logo_path) }}" style="height:60px" class="mt-2">
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Favicon (PNG/ICO)</label>
                    <input type="file" name="favicon" class="form-control">
                    @if($setting->favicon_path)
                        <img src="{{ asset('storage/'.$setting->favicon_path) }}" style="height:32px" class="mt-2">
                    @endif
                </div>
            </div>
        </div>

        {{-- DOCUMENT FOOTERS --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">Document Footers / Notes</div>
            <div class="card-body">
                <label class="form-label">Invoice / Receipt Footer</label>
                <textarea name="invoice_footer" rows="3" class="form-control">{{ old('invoice_footer',$setting->invoice_footer) }}</textarea>

                <label class="form-label mt-3">Email Signature / Footer</label>
                <textarea name="email_signature" rows="3" class="form-control">{{ old('email_signature',$setting->email_signature) }}</textarea>
            </div>
        </div>

        {{-- DOCUMENT FOOTERS --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">Document Footers / Notes</div>
            <div class="card-body">
                <label class="form-label">Invoice / Receipt Footer</label>
                <textarea name="invoice_footer" rows="3" class="form-control">{{ old('invoice_footer',$setting->invoice_footer) }}</textarea>

                <label class="form-label mt-3">Email Signature / Footer</label>
                <textarea name="email_signature" rows="3" class="form-control">{{ old('email_signature',$setting->email_signature) }}</textarea>

                <label class="form-label mt-3">Website Footer Text</label>
                <textarea name="site_footer" rows="3" class="form-control">{{ old('site_footer',$setting->site_footer) }}</textarea>
            </div>
        </div>

        {{-- SOCIAL MEDIA --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">Social Media Links</div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Facebook</label>
                    <input name="facebook" type="url" class="form-control" value="{{ old('facebook',$setting->facebook) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Instagram</label>
                    <input name="instagram" type="url" class="form-control" value="{{ old('instagram',$setting->instagram) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">X (Twitter)</label>
                    <input name="x" type="url" class="form-control" value="{{ old('x',$setting->x) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">LinkedIn</label>
                    <input name="linkedin" type="url" class="form-control" value="{{ old('linkedin',$setting->linkedin) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">YouTube</label>
                    <input name="youtube" type="url" class="form-control" value="{{ old('youtube',$setting->youtube) }}">
                </div>
            </div>
        </div>

        {{-- MISC --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">Preferences</div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Default Language</label>
                    <input name="default_language" class="form-control" value="{{ old('default_language',$setting->default_language) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Receipt Paper Size</label>
                    <select name="receipt_paper_size" class="form-control">
                        @foreach(['A4','A5','80mm','58mm'] as $size)
                            <option value="{{ $size }}" @selected(old('receipt_paper_size',$setting->receipt_paper_size)==$size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="invoice_auto_print" value="1"
                               @checked(old('invoice_auto_print',$setting->invoice_auto_print))>
                        <label class="form-check-label">Auto-print invoice after sale</label>
                    </div>
                </div>
            </div>
        </div>

        <button class="btn btn-primary">Save Settings</button>
    </form>
</div>
@endsection
