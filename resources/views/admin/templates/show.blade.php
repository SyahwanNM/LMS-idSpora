@extends('layouts.admin')

@section('title', 'Detail Template: ' . $template->name)

@section('content')
    <div class="container-fluid px-4 py-4">
        <div style="max-width: 1000px; margin: 0 auto;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 30px;">
                <a href="{{ route('admin.templates.index') }}"
                    style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #e2e8f0; color: #2d3748; border-radius: 6px; text-decoration: none; font-size: 16px;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 style="margin: 0; font-size: 28px; color: #1a202c; font-weight: 600;">
                        {{ $template->name }}
                    </h1>
                    <p style="margin: 5px 0 0 0; color: #718096; font-size: 14px;">
                        Versi {{ $template->version }} • Dibuat oleh {{ $template->creator->name ?? 'System' }}
                    </p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div style="background: white; padding: 16px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <p
                        style="margin: 0 0 8px 0; color: #718096; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                        Level
                    </p>
                    <p style="margin: 0; color: #2d3748; font-weight: 600; font-size: 18px;">
                        {{ ucfirst($template->level) }}
                    </p>
                </div>

                <div style="background: white; padding: 16px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <p
                        style="margin: 0 0 8px 0; color: #718096; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                        Jumlah Modul
                    </p>
                    <p style="margin: 0; color: #2d3748; font-weight: 600; font-size: 18px;">
                        {{ $template->modules_count }}
                    </p>
                </div>

                <div style="background: white; padding: 16px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <p
                        style="margin: 0 0 8px 0; color: #718096; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                        Course Aktif
                    </p>
                    <p style="margin: 0; color: #2d3748; font-weight: 600; font-size: 18px;">
                        {{ $template->courses_count }}
                    </p>
                </div>
            </div>

            @if($template->description)
                <div
                    style="background: white; padding: 16px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
                    <h3 style="margin: 0 0 8px 0; color: #2d3748; font-weight: 600;">Deskripsi</h3>
                    <p style="margin: 0; color: #4a5568; line-height: 1.6;">
                        {{ $template->description }}
                    </p>
                </div>
            @endif

            <div
                style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
                <div style="border-bottom: 1px solid #e2e8f0; padding: 16px; background: #f7fafc;">
                    <h3 style="margin: 0; color: #2d3748; font-weight: 600;">
                        <i class="bi bi-collection"></i> Struktur Modul
                    </h3>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f7fafc;">
                        <tr>
                            <th
                                style="padding: 12px 16px; text-align: left; font-weight: 600; color: #2d3748; font-size: 13px;">
                                No</th>
                            <th
                                style="padding: 12px 16px; text-align: left; font-weight: 600; color: #2d3748; font-size: 13px;">
                                Judul Module</th>
                            <th
                                style="padding: 12px 16px; text-align: left; font-weight: 600; color: #2d3748; font-size: 13px;">
                                Tipe</th>
                            <th
                                style="padding: 12px 16px; text-align: left; font-weight: 600; color: #2d3748; font-size: 13px;">
                                Durasi</th>
                            <th
                                style="padding: 12px 16px; text-align: left; font-weight: 600; color: #2d3748; font-size: 13px;">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($template->modules as $module)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 12px 16px; color: #2d3748; font-weight: 600;">
                                    {{ $module->order_no }}
                                </td>
                                <td style="padding: 12px 16px; color: #2d3748;">
                                    <strong>{{ $module->title }}</strong>
                                    @if($module->description)
                                        <br><small style="color: #718096;">{{ Str::limit($module->description, 60) }}</small>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px;">
                                    <span
                                        style="display: inline-block; background: #ebf8ff; color: #2c5282; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                        {{ strtoupper($module->type) }}
                                    </span>
                                </td>
                                <td style="padding: 12px 16px; color: #2d3748;">
                                    {{ $module->duration }} menit
                                </td>
                                <td style="padding: 12px 16px;">
                                    @if($module->is_required)
                                        <span style="color: #38a169; font-size: 12px; font-weight: 600;">
                                            <i class="bi bi-check-circle-fill"></i> Wajib
                                        </span>
                                    @else
                                        <span style="color: #805ad5; font-size: 12px; font-weight: 600;">
                                            <i class="bi bi-dash-circle-fill"></i> Opsional
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 40px; text-align: center; color: #718096;">
                                    Belum ada modul
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="display: flex; gap: 12px;">
                <a href="{{ route('admin.templates.edit', $template) }}"
                    style="padding: 10px 20px; background: #2d3748; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-pencil"></i> Edit Template
                </a>
                <form action="{{ route('admin.templates.destroy', $template) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        style="padding: 10px 20px; background: #c53030; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;"
                        onclick="return confirm('Yakin ingin mengarsipkan template ini?')">
                        <i class="bi bi-trash"></i> Arsipkan
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection