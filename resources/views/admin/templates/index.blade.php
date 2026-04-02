@extends('layouts.admin')

@section('title', 'Course Templates Management')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
            <div>
                <h1 style="margin: 0; font-size: 28px; color: #1a202c; font-weight: 600;">
                    <i class="bi bi-collection"></i> Course Templates
                </h1>
                <p style="margin: 5px 0 0 0; color: #718096; font-size: 14px;">Kelola template struktur course untuk
                    memudahkan admin membuat course baru</p>
            </div>
            <a href="{{ route('admin.templates.create') }}" class="btn"
                style="background: #2d3748; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="bi bi-plus-lg"></i> Buat Template Baru
            </a>
        </div>

        @if(session('success'))
            <div
                style="background: #c6f6d5; border-left: 4px solid #38a169; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; color: #22543d;">
                {{ session('success') }}
            </div>
        @endif

        <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f7fafc; border-bottom: 1px solid #e2e8f0;">
                    <tr>
                        <th
                            style="padding: 12px 16px; text-align: left; font-weight: 600; color: #2d3748; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">
                            Nama Template
                        </th>
                        <th
                            style="padding: 12px 16px; text-align: left; font-weight: 600; color: #2d3748; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">
                            Level
                        </th>
                        <th
                            style="padding: 12px 16px; text-align: left; font-weight: 600; color: #2d3748; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">
                            Modul
                        </th>
                        <th
                            style="padding: 12px 16px; text-align: left; font-weight: 600; color: #2d3748; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">
                            Course Aktif
                        </th>
                        <th
                            style="padding: 12px 16px; text-align: left; font-weight: 600; color: #2d3748; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">
                            Versi
                        </th>
                        <th
                            style="padding: 12px 16px; text-align: center; font-weight: 600; color: #2d3748; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr style="border-bottom: 1px solid #e2e8f0; transition: background 0.2s;">
                            <td style="padding: 12px 16px;">
                                <div>
                                    <strong style="color: #2d3748; display: block;">{{ $template->name }}</strong>
                                    <small style="color: #718096; display: block; margin-top: 4px;">
                                        @if($template->category)
                                            📁 {{ $template->category->name }}
                                        @endif
                                    </small>
                                </div>
                            </td>
                            <td style="padding: 12px 16px;">
                                <span
                                    style="display: inline-block; background: #ebf8ff; color: #2c5282; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    {{ ucfirst($template->level) }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; color: #2d3748;">
                                <strong>{{ $template->modules_count }}</strong>
                            </td>
                            <td style="padding: 12px 16px; color: #2d3748;">
                                <strong>{{ $template->courses_count }}</strong>
                            </td>
                            <td style="padding: 12px 16px; color: #2d3748;">
                                <span style="background: #edf2f7; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                    v{{ $template->version }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <a href="{{ route('admin.templates.show', $template) }}" class="btn-icon"
                                        style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #e2e8f0; color: #2d3748; border-radius: 4px; text-decoration: none; font-size: 14px; transition: all 0.2s;"
                                        title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.templates.edit', $template) }}" class="btn-icon"
                                        style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #bee3f8; color: #2c5282; border-radius: 4px; text-decoration: none; font-size: 14px; transition: all 0.2s;"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.templates.destroy', $template) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon"
                                            style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #fed7d7; color: #c53030; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; transition: all 0.2s;"
                                            title="Arsipkan" onclick="return confirm('Yakin ingin mengarsipkan template ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center; color: #718096;">
                                <i class="bi bi-inbox"
                                    style="font-size: 32px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                Belum ada template. <a href="{{ route('admin.templates.create') }}"
                                    style="color: #2d3748; font-weight: 600;">Buat yang pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
            <div style="margin-top: 20px;">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
@endsection