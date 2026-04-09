@extends('layouts.trainer')

@section('title', 'Edit Profile Trainer')

@php
    $pageTitle = 'Edit Profile';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Profile', 'url' => route('trainer.profile')],
        ['label' => 'Edit']
    ];
@endphp

@section('content')
    <div style="max-width:900px;margin:0 auto;display:grid;gap:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
            <h1 style="margin:0;color:#0f172a;font-size:24px;">Edit Profile Trainer</h1>
            <a href="{{ route('trainer.profile') }}"
                style="text-decoration:none;color:#1b1763;font-weight:600;font-size:13px;">← Kembali ke Profile</a>
        </div>

        @if(session('success'))
            <div
                style="background:#ecfdf5;border:1px solid #86efac;color:#166534;padding:10px 12px;border-radius:10px;font-size:13px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div
                style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:10px 12px;border-radius:10px;font-size:13px;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('trainer.profile.update') }}" method="POST" enctype="multipart/form-data"
            style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:16px;display:grid;gap:14px;">
            @csrf
            @method('PUT')

            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                <img src="{{ $trainer->avatar_url }}" alt="{{ $trainer->name }}"
                    style="width:72px;height:72px;border-radius:12px;object-fit:cover;border:1px solid #e2e8f0;" />
                <div style="display:grid;gap:6px;">
                    <label for="avatar" style="font-size:12px;color:#334155;font-weight:600;">Avatar</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" style="font-size:12px;color:#334155;" />
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;">
                <div style="display:grid;gap:6px;">
                    <label for="name" style="font-size:12px;font-weight:600;color:#334155;">Nama</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $trainer->name) }}" required
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="academic_title" style="font-size:12px;font-weight:600;color:#334155;">Gelar Akademik</label>
                    <input id="academic_title" name="academic_title" type="text"
                        value="{{ old('academic_title', $trainer->academic_title) }}" placeholder="Contoh: S.Kom., M.Kom."
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="phone" style="font-size:12px;font-weight:600;color:#334155;">WhatsApp Aktif</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone', $trainer->phone) }}"
                        placeholder="Contoh: +6281234567890"
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="profession" style="font-size:12px;font-weight:600;color:#334155;">Jabatan / Profesi</label>
                    <input id="profession" name="profession" type="text"
                        value="{{ old('profession', $trainer->profession) }}"
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="institution" style="font-size:12px;font-weight:600;color:#334155;">Institusi</label>
                    <input id="institution" name="institution" type="text"
                        value="{{ old('institution', $trainer->institution) }}"
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                </div>
            </div>

            <div style="display:grid;gap:6px;">
                <label for="website" style="font-size:12px;font-weight:600;color:#334155;">Website</label>
                <input id="website" name="website" type="text" value="{{ old('website', $trainer->website) }}"
                    placeholder="contoh: https://example.com"
                    style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
            </div>

            <div style="display:grid;gap:6px;">
                <label for="linkedin_url" style="font-size:12px;font-weight:600;color:#334155;">LinkedIn</label>
                <input id="linkedin_url" name="linkedin_url" type="url"
                    value="{{ old('linkedin_url', $trainer->linkedin_url) }}" placeholder="https://www.linkedin.com/in/..."
                    style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;">
                <div style="display:grid;gap:6px;">
                    <label for="bank_name" style="font-size:12px;font-weight:600;color:#334155;">Nama Bank</label>
                    <input id="bank_name" name="bank_name" type="text" value="{{ old('bank_name', $trainer->bank_name) }}"
                        placeholder="BCA / Mandiri / BNI / dll"
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="bank_account_number" style="font-size:12px;font-weight:600;color:#334155;">Nomor
                        Rekening</label>
                    <input id="bank_account_number" name="bank_account_number" type="text"
                        value="{{ old('bank_account_number', $trainer->bank_account_number) }}"
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="bank_account_holder" style="font-size:12px;font-weight:600;color:#334155;">Nama Pemilik
                        Rekening</label>
                    <input id="bank_account_holder" name="bank_account_holder" type="text"
                        value="{{ old('bank_account_holder', $trainer->bank_account_holder) }}"
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                </div>
            </div>

            <div style="display:grid;gap:6px;">
                <label for="bio" style="font-size:12px;font-weight:600;color:#334155;">Bio</label>
                <textarea id="bio" name="bio" rows="5"
                    style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;resize:vertical;">{{ old('bio', $trainer->bio) }}</textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;">
                <a href="{{ route('trainer.profile') }}"
                    style="text-decoration:none;border:1px solid #cbd5e1;border-radius:10px;padding:10px 14px;color:#334155;font-size:12px;font-weight:600;">Batal</a>
                <button type="submit"
                    style="border:none;background:#1b1763;color:#fff;border-radius:10px;padding:10px 14px;font-size:12px;font-weight:700;">Simpan
                    Perubahan</button>
            </div>
        </form>
    </div>
@endsection