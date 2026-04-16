@extends('layouts.app')

@section('title', 'Upload Module Event')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between gap-3 mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Upload Module Event</h1>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Kembali ke Dashboard</a>
        </div>

        <div class="rounded border bg-white">
            <div class="border-b px-4 py-3 text-sm text-gray-700">
                Daftar event yang memiliki Anda sebagai pembicara. Upload module/materi untuk event yang masih "Belum".
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Event</th>
                            <th class="px-4 py-3 text-left font-medium">Tanggal</th>
                            <th class="px-4 py-3 text-left font-medium">Status Module</th>
                            <th class="px-4 py-3 text-left font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="rows" class="divide-y"></tbody>
                </table>
            </div>
            <div id="empty" class="hidden px-4 py-6 text-sm text-gray-600">Belum ada event untuk Anda.</div>
            <div id="loading" class="px-4 py-6 text-sm text-gray-600">Memuat data...</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const rowsEl = document.getElementById('rows');
            const emptyEl = document.getElementById('empty');
            const loadingEl = document.getElementById('loading');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const uploadUrlTemplate = @json(route('trainer.events.module.upload', ['event' => '__EVENT_ID__']));

            const escapeHtml = (s) => String(s ?? '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#039;');

            try {
                const res = await fetch('{{ route('trainer.api.event-modules') }}', { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                const items = Array.isArray(json?.data) ? json.data : [];

                loadingEl.classList.add('hidden');

                if (!items.length) {
                    emptyEl.classList.remove('hidden');
                    return;
                }

                rowsEl.innerHTML = items.map((it) => {
                    const title = escapeHtml(it.title);
                    const date = escapeHtml(it.event_date || '—');
                    const status = it.module_uploaded ? 'Sudah' : 'Belum';
                    const statusCls = it.module_uploaded ? 'text-green-700' : 'text-red-700';
                    
                    const modules = Array.isArray(it.modules) ? it.modules : [];
                    const modulesHtml = modules.length > 0 
                        ? `<div class="mt-2 space-y-1">` + 
                            modules.map((m, idx) => `
                                <div class="flex items-center justify-between bg-gray-50 p-1.5 rounded border text-xs">
                                    <span class="truncate max-w-[150px]" title="${escapeHtml(m.name)}">${escapeHtml(m.name)}</span>
                                    <a href="${escapeHtml(m.url)}" target="_blank" class="text-blue-600 font-bold hover:underline">Unduh</a>
                                </div>
                            `).join('') + 
                          `</div>`
                        : '';

                    return `
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">${title}</div>
                                <div class="text-xs text-gray-500">${escapeHtml(it.jenis || '')}</div>
                            </td>
                            <td class="px-4 py-3">${date}</td>
                            <td class="px-4 py-3"><span class="font-medium ${statusCls}">${status}</span></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-2">
                                    ${download}
                                    <form method="POST" action="${uploadUrlTemplate.replace('__EVENT_ID__', encodeURIComponent(it.id))}" enctype="multipart/form-data" class="flex items-center gap-2">
                                        <input type="hidden" name="_token" value="${escapeHtml(csrf)}" />
                                        <input type="file" name="module" required class="block w-full text-sm" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar,.7z" />
                                        <button type="submit" class="rounded bg-gray-900 px-3 py-2 text-xs font-medium text-white">Upload</button>
                                    </form>
                                    <div class="text-xs text-gray-500">Format: PDF/DOC/PPT/ZIP. Maks 20MB.</div>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

            } catch (e) {
                loadingEl.classList.add('hidden');
                emptyEl.classList.remove('hidden');
                emptyEl.textContent = 'Gagal memuat data.';
            }
        });
    </script>
@endsection