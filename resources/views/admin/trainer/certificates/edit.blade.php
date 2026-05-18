@extends('layouts.admin')

@section('title', 'Konfigurasi Sertifikat Trainer')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Konfigurasi Sertifikat</h3>
            <p class="text-muted mb-0">
                Trainer:
                <strong>{{ $trainer->name }}</strong>
            </p>
        </div>

        <a href="{{ route('admin.trainer.certificates.show', $trainer) }}"
           class="btn btn-secondary">
            Kembali
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <span class="badge bg-primary mb-2">
                {{ strtoupper($context) }}
            </span>

            <h5 class="mb-0">
                {{ $context === 'event' ? $model->title : $model->name }}
            </h5>
        </div>
    </div>

    <form method="POST"
          action="{{ route('admin.trainer.certificates.update', [
              'trainer' => $trainer->id,
              'context' => $context,
              'id' => $model->id,
          ]) }}"
          enctype="multipart/form-data">

        @csrf

        <div class="row g-4">

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header fw-bold">
                        Template
                    </div>

                    <div class="card-body">
                        <select name="template" class="form-select" required>
                            <option value="template_1" {{ old('template') === 'template_1' ? 'selected' : '' }}>
                                Template 1
                            </option>

                            <option value="template_2" {{ old('template') === 'template_2' ? 'selected' : '' }}>
                                Template 2
                            </option>

                            <option value="template_3" {{ old('template') === 'template_3' ? 'selected' : '' }}>
                                Template 3
                            </option>
                        </select>

                        <small class="text-muted d-block mt-2">
                            Template mengikuti desain sertifikat CRM.
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header fw-bold">
                        Logo
                    </div>

                    <div class="card-body">
                        <input type="file"
                               name="logos[]"
                               multiple
                               accept=".jpg,.jpeg,.png,.webp"
                               class="form-control">

                        <small class="text-muted d-block mt-2">
                            Upload maksimal 3 logo.
                        </small>

                        @php
                            $logos = $assets->where('type', 'logo')->values();
                        @endphp

                        @if($logos->count())
                            <div class="mt-3">
                                <div class="small fw-bold text-muted mb-2">
                                    Logo tersimpan:
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($logos as $asset)
                                        <div class="border rounded p-2 bg-light">
                                            <img src="{{ asset('storage/' . $asset->image_path) }}"
                                                 style="height:60px; max-width:120px; object-fit:contain;">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header fw-bold">
                        Signature
                    </div>

                    <div class="card-body">
                        @php
                            $oldSignatures = $assets->where('type', 'signature')->values();
                        @endphp

                        @for($i = 0; $i < 3; $i++)
                            @php
                                $oldSig = $oldSignatures->get($i);
                            @endphp

                            <div class="border rounded p-3 mb-3">
                                <label class="form-label fw-bold">
                                    TTD {{ $i + 1 }}
                                </label>

                                <input type="file"
                                       name="signatures[{{ $i }}][file]"
                                       accept=".jpg,.jpeg,.png,.webp"
                                       class="form-control mb-2">

                                <input type="text"
                                       name="signatures[{{ $i }}][name]"
                                       class="form-control mb-2"
                                       placeholder="Nama penandatangan"
                                       value="{{ old('signatures.' . $i . '.name', $oldSig?->name) }}"

                                <input type="text"
                                       name="signatures[{{ $i }}][position]"
                                       class="form-control"
                                       placeholder="Jabatan"
                                       value="{{ old('signatures.' . $i . '.position', $oldSig?->position) }}"

                                @if($oldSig)
                                    <div class="mt-3 p-2 bg-light rounded">
                                        <img src="{{ asset('storage/' . $oldSig->image_path) }}"
                                             style="height:70px; max-width:160px; object-fit:contain;">

                                        <div class="small text-muted mt-2">
                                            <strong>{{ $oldSig->name }}</strong>
                                            <br>
                                            {{ $oldSig->position }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                Simpan Konfigurasi
            </button>

            <a href="{{ route('admin.trainer.certificates.show', $trainer) }}"
               class="btn btn-secondary">
                Kembali
            </a>
        </div>
    </form>

</div>
@endsection