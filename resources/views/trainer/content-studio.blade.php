@extends('layouts.trainer')

@section('title', 'Content Studio - Trainer')

@php
    $pageTitle = 'Content Studio';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Content Studio']
    ];
@endphp

@push('styles')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.2/font/bootstrap-icons.min.css" />
    <style>
        main.content-studio-main {
            max-width: 1000px;
            margin: 0 auto;
            padding: var(--spacing-xl);
            flex: 1;
            width: 100%;
        }

        .studio-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--spacing-lg);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--line-clr);
            margin-bottom: var(--spacing-lg);
        }

        .studio-title-wrap {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }

        .back-btn {
            width: 46px;
            height: 46px;
            border-radius: var(--radius-xl);
            border: 1px solid #d8dee9;
            color: var(--gray-second-clr);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            background: var(--white-clr);
        }

        .kicker {
            margin: 0 0 var(--spacing-xs) 0;
            font-size: var(--font-size-xs);
            color: #d4a62f;
            letter-spacing: 0.12em;
            font-weight: 600;
        }

        .studio-title-wrap h1 {
            margin: 0;
            font-size: var(--font-size-2xl);
            color: var(--main-navy-clr);
            font-weight: 600;
        }

        .studio-tabs {
            background: #eef2f7;
            border: 1px solid #dce3ee;
            border-radius: var(--radius-2xl);
            padding: var(--spacing-xs);
            display: flex;
            gap: var(--spacing-xs);
        }

        .studio-tab {
            border: none;
            border-radius: var(--radius-xl);
            background: transparent;
            color: var(--gray-second-clr);
            font-size: var(--font-size-xs);
            font-weight: 600;
            padding: var(--spacing-sm) var(--spacing-lg);
            cursor: pointer;
        }

        .studio-tab.active {
            background: var(--white-clr);
            color: var(--main-navy-clr);
            box-shadow: var(--shadow-md);
        }

        .studio-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: var(--spacing-lg);
        }

        .panel {
            background: var(--white-clr);
            border: 1px solid #e3e9f2;
            border-radius: 24px;
            padding: var(--spacing-lg);
            display: none;
        }

        .panel.active {
            display: block;
        }

        .dropzone {
            min-height: 280px;
            border-radius: 20px;
            border: 2px dashed #dfe6f2;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: var(--spacing-sm);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dropzone:hover {
            border-color: var(--main-navy-clr);
            background: #f0f5fc;
        }

        .dropzone i {
            font-size: 40px;
            color: #c8d0de;
        }

        .dropzone h2 {
            margin: 0;
            font-size: var(--font-size-xl);
            color: var(--main-navy-clr);
            font-weight: 600;
        }

        .dropzone p {
            margin: 0;
            font-size: var(--font-size-xs);
            letter-spacing: 0.12em;
            color: var(--gray-second-clr);
            font-weight: 600;
        }

        .panel-footer {
            margin-top: var(--spacing-lg);
            padding-top: var(--spacing-md);
            border-top: 1px solid var(--line-clr);
            display: flex;
            justify-content: flex-end;
        }

        .primary-btn {
            border: none;
            background: #231d79;
            color: var(--white-clr);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: 12px;
            font-size: var(--font-size-sm);
            font-weight: 600;
            letter-spacing: 0.06em;
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .primary-btn i {
            color: var(--yellow-clr);
        }

        .validation-card {
            background: #231d79;
            color: var(--white-clr);
            border-radius: 32px;
            padding: var(--spacing-lg);
        }

        .validation-card h3 {
            margin: 0 0 var(--spacing-md) 0;
            color: var(--yellow-clr);
            font-size: var(--font-size-md);
            font-weight: 600;
        }

        .validation-card ol {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }

        .validation-card li {
            display: grid;
            grid-template-columns: 32px 1fr;
            gap: var(--spacing-sm);
        }

        .validation-card li span {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-lg);
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--yellow-clr);
            font-weight: 600;
            font-size: var(--font-size-sm);
        }

        .validation-card h4 {
            margin: 0 0 var(--spacing-xs) 0;
            font-size: var(--font-size-sm);
            font-weight: 600;
        }

        .validation-card p {
            margin: 0;
            color: #b8bce2;
            font-size: var(--font-size-xs);
        }

        .quiz-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .meta-box {
            border: 1px solid #dde5f1;
            border-radius: var(--radius-xl);
            padding: var(--spacing-sm);
            background: #f8fafc;
        }

        .meta-box {
            cursor: default;
            transition: all 0.2s ease;
        }

        #passingGradeBox {
            cursor: pointer;
        }

        #passingGradeBox:hover {
            opacity: 0.9;
        }

        .meta-box p {
            margin: 0 0 var(--spacing-xs) 0;
            font-size: var(--font-size-xs);
            color: var(--gray-second-clr);
            font-weight: 600;
            letter-spacing: 0.08em;
        }

        .meta-value {
            background: #eff3f8;
            border: 1px solid #dbe3ef;
            border-radius: var(--radius-lg);
            padding: var(--spacing-sm);
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 4px;
        }

        .meta-value strong {
            color: var(--main-navy-clr);
            font-size: var(--font-size-lg);
            font-weight: 600;
            min-width: 50px;
            text-align: center;
            display: inline-block;
        }

        .meta-value span {
            color: #b0bdd2;
            font-size: var(--font-size-xl);
            font-weight: 600;
        }

        .meta-value em {
            font-style: normal;
            color: var(--white-clr);
            background: #2c237f;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: var(--font-size-xs);
            font-weight: 600;
            margin-left: auto;
        }

        .meta-input {
            border: none !important;
            background: transparent !important;
            font-weight: 600;
            color: var(--main-navy-clr);
            width: 50px;
            text-align: center;
            display: none;
            outline: none;
            padding: 0;
            margin: 0;
            font-size: var(--font-size-lg);
            box-shadow: none !important;
        }

        .meta-input:focus {
            outline: none;
            box-shadow: none;
            border: none;
        }

        .quiz-editor {
            border: 1px solid #e4eaf4;
            border-radius: 24px;
            padding: var(--spacing-lg);
            background: var(--white-clr);
        }

        .q-head {
            display: grid;
            grid-template-columns: max-content minmax(0, 1fr) 100px auto;
            gap: var(--spacing-md);
            align-items: stretch;
            margin-bottom: var(--spacing-md);
        }

        .q-number {
            width: auto;
            height: 100%;
            aspect-ratio: 1 / 1;
            min-width: 40px;
            border-radius: 12px;
            background: #231d79;
            color: var(--yellow-clr);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-size-xl);
            font-weight: 600;
            align-self: stretch;
        }

        .q-inputs label,
        .q-score label {
            display: block;
            margin-bottom: var(--spacing-xs);
            font-size: var(--font-size-xs);
            color: var(--gray-second-clr);
            font-weight: 600;
            letter-spacing: 0.08em;
        }

        .q-inputs input,
        .q-score input {
            width: 100%;
            border: 1px solid #d9e1ee;
            border-radius: var(--radius-xl);
            background: #f3f6fb;
            padding: var(--spacing-sm);
            font-size: var(--font-size-base);
            color: var(--main-navy-clr);
            outline: none;
        }

        .q-inputs input:focus,
        .q-score input:focus {
            border-color: var(--main-navy-clr);
            background: #fff;
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: var(--spacing-md);
        }

        .option-btn {
            border: 1px solid #e1e8f3;
            background: #f8fafc;
            border-radius: var(--radius-xl);
            padding: var(--spacing-sm);
            min-height: 48px;
            height: 48px;
            text-align: left;
            font-size: var(--font-size-sm);
            color: #b8c2d3;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            justify-content: flex-start;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .option-btn:hover {
            background: #eff3f8;
            border-color: var(--main-navy-clr);
        }

        .option-btn.is-correct {
            border: 2px solid #14b87a;
            background: #e8faf2;
            color: #1da775;
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
            font-weight: 600;
        }

        .delete-question {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            height: fit-content;
            margin-top: 22px;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }

        .delete-question:hover {
            background: #e25555;
        }

        .option-container {
            display: flex;
            gap: var(--spacing-sm);
            align-items: stretch;
        }

        .option-input {
            border: 1px solid #d9e1ee;
            border-radius: 8px;
            padding: var(--spacing-sm);
            min-height: 48px;
            height: 48px;
            font-size: 13px;
            background: #f8fafc;
            flex: 1;
            min-width: 0;
            outline: none;
            transition: all 0.2s ease;
        }

        .option-input:focus {
            border-color: var(--main-navy-clr);
            background: #fff;
        }

        .options-section {
            margin-top: var(--spacing-md);
        }

        .options-label {
            margin: 0 0 var(--spacing-sm) 0;
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-second-clr);
            letter-spacing: 0.08em;
        }

        @media (max-width: 1024px) {
            main.content-studio-main {
                padding: var(--spacing-lg);
            }

            .options-grid {
                grid-template-columns: 1fr;
            }

            .q-head {
                grid-template-columns: 40px minmax(0, 1fr);
            }

            .delete-question {
                grid-column: 1 / -1;
                margin-top: var(--spacing-sm);
                justify-self: start;
            }
        }

        @media (max-width: 720px) {
            main.content-studio-main {
                padding: var(--spacing-md);
            }

            .studio-tabs {
                width: 100%;
            }

            .studio-tab {
                flex: 1;
                padding: var(--spacing-sm) var(--spacing-sm);
            }

            .quiz-meta,
            .options-grid {
                grid-template-columns: 1fr;
            }

            .q-head {
                grid-template-columns: 1fr;
                gap: var(--spacing-sm);
            }

            .q-number {
                width: 32px;
                height: 32px;
                font-size: var(--font-size-lg);
            }

            .q-score {
                grid-row: 2;
                grid-column: 1;
            }

            .delete-question {
                margin-top: 0;
            }
        }

        .module-form,
        .quiz-form {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .file-list h3 {
            font-size: var(--font-size-md);
            font-weight: 600;
            color: var(--main-navy-clr);
            margin: 0 0 var(--spacing-md) 0;
        }

        .quiz-actions {
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-md);
            justify-content: flex-start;
        }

        .quiz-actions .primary-btn {
            border-radius: 999px;
            border: none;
            padding: var(--spacing-md) var(--spacing-2xl);
            font-weight: 700;
            font-size: var(--font-size-xs);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
            transition: all 0.2s ease;
        }

        .quiz-add-btn {
            background: transparent;
            color: #94a3b8;
            border: 1px solid #dbe3ef;
        }

        .quiz-add-btn i {
            color: #94a3b8;
        }

        .quiz-add-btn:hover {
            background: #f1f5f9;
            color: var(--main-navy-clr);
            border-color: #cbd5e1;
        }

        .quiz-add-btn:hover i {
            color: var(--main-navy-clr);
        }

        .quiz-save-btn {
            background: #1f1b5a;
            color: var(--white-clr);
            box-shadow: 0 10px 20px rgba(31, 27, 90, 0.2);
        }

        .quiz-save-btn i {
            color: var(--yellow-clr);
        }

        .quiz-save-btn:hover {
            background: #19164a;
        }

        @media (max-width: 720px) {
            .quiz-actions {
                flex-direction: column-reverse;
            }

            .quiz-actions .primary-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
    <main class="content-studio-main">
        <header class="studio-header">
            <div class="studio-title-wrap">
                <a class="back-btn" href="{{ route('trainer.courses') }}">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <p class="kicker">STUDIO NARASUMBER • PENYUSUNAN MATERI</p>
                    <h1>{{ $course->name }}</h1>
                </div>
            </div>

            <div class="studio-tabs" role="tablist">
                <button class="studio-tab active" data-tab="module" type="button">
                    MODUL &amp; VIDEO
                </button>
                <button class="studio-tab" data-tab="quiz" type="button">
                    PENYUSUNAN QUIZ
                </button>
            </div>
        </header>

        <section class="studio-layout">
            <div class="studio-left">
                <section class="panel panel-module active" data-panel="module">
                    <form id="moduleForm" class="module-form"
                        action="{{ route('trainer.courses.studio.upload', $course->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="courseId" value="{{ $course->id }}">
                        <input type="hidden" name="target_modules"
                            value="{{ $activeUnitModules->pluck('id')->implode(',') }}">
                        <input type="hidden" name="replace_module_id" value="">

                        <div class="dropzone" id="dropzone">
                            <input type="file" id="fileInput" multiple
                                accept=".pdf,.mp4,.pptx,.ppt,.docx,.doc,.jpg,.png,.jpeg" name="files[]"
                                style="display: none" />
                            <i class="bi bi-file-earmark-arrow-up"></i>
                            <h2>Drop Pedagogical Assets</h2>
                            <p>SUPPORT: PDF, MP4, PPTX</p>
                            <p style="font-size: 12px; color: #999; margin-top: 8px">atau klik untuk memilih file</p>
                        </div>

                        <div id="fileList" class="file-list" style="margin-top: 20px; display: none">
                            <h3>Materi yang Diunggah</h3>
                            <ul id="uploadedFiles" style="list-style: none; padding: 0; margin: 0"></ul>
                        </div>

                        @php
                            $existingMaterials = $activeUnitModules->filter(function ($module) {
                                return in_array($module->type, ['pdf', 'video']) && !empty($module->content_url);
                            });

                            $existingMaterialsPayload = $existingMaterials->map(function ($material) use ($course) {
                                return [
                                    'module_id' => (int) $material->id,
                                    'order_no' => (int) $material->order_no,
                                    'type' => (string) $material->type,
                                    'title' => (string) ($material->title ?? ''),
                                    'file_name' => (string) ($material->file_name ?: basename($material->content_url)),
                                    'view_url' => route('trainer.courses.studio.material.view', [$course->id, $material->id]),
                                    'updated_at' => optional($material->updated_at)->toDateTimeString(),
                                ];
                            })->values();
                        @endphp
                            <div id="existingMaterialsBlock" class="file-list" style="margin-top: 16px; display: {{ $existingMaterials->isNotEmpty() ? 'block' : 'none' }};">
                                <h3>Materi Tersimpan Sebelumnya</h3>
                                <ul id="existingMaterialsList" style="list-style: none; padding: 0; margin: 0;">
                                    @foreach($existingMaterials as $material)
                                        <li
                                            style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--main-navy-clr);">
                                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                                <i class="bi {{ $material->type === 'video' ? 'bi-camera-video' : 'bi-file-earmark-pdf' }}"
                                                    style="font-size: 20px; color: var(--main-navy-clr);"></i>
                                                <div>
                                                    <p
                                                        style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);">
                                                        {{ $material->file_name ?: basename($material->content_url) }}</p>
                                                    <p style="margin: 0; font-size: 12px; color: #999;">
                                                        {{ strtoupper($material->type) }} • Slot {{ $material->order_no }}</p>
                                                </div>
                                            </div>
                                            <div style="display: flex; gap: 6px;">
                                                <button type="button" class="preview-material-btn"
                                                    data-view-url="{{ route('trainer.courses.studio.material.view', [$course->id, $material->id]) }}"
                                                    data-material-type="{{ $material->type }}"
                                                    data-file-name="{{ $material->file_name ?: basename($material->content_url) }}"
                                                    title="Preview File"
                                                    style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border-radius: 4px; text-decoration: none; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                    onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>
                                                <button type="button" class="select-replace-btn"
                                                    data-module-id="{{ $material->id }}" data-module-type="{{ $material->type }}"
                                                    data-file-name="{{ $material->file_name ?: basename($material->content_url) }}"
                                                    title="Ganti File"
                                                    style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border: none; border-radius: 4px; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                    onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            @if(($uploadedMaterials ?? collect())->isNotEmpty())
                                <div class="file-list" style="margin-top: 16px; display: block;">
                                    <h3>Semua Materi Yang Sudah Diupload</h3>
                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                        @foreach($uploadedMaterials as $material)
                                            <li style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid #dce3ee;">
                                                <div style="display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0;">
                                                    <i class="bi {{ $material['type'] === 'video' ? 'bi-camera-video' : 'bi-file-earmark-pdf' }}" style="font-size: 18px; color: var(--main-navy-clr);"></i>
                                                    <div style="min-width: 0;">
                                                        <p style="margin: 0; font-size: 13px; font-weight: 600; color: var(--main-navy-clr); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $material['file_name'] }}
                                                        </p>
                                                        <p style="margin: 0; font-size: 11px; color: #999;">
                                                            {{ strtoupper($material['type']) }} • Bab {{ $material['unit_no'] }} • Slot {{ $material['order_no'] }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div style="display: inline-flex; gap: 6px;">
                                                    <button type="button" class="preview-material-btn"
                                                        data-view-url="{{ $material['view_url'] }}"
                                                        data-material-type="{{ $material['type'] }}"
                                                        data-file-name="{{ $material['file_name'] }}"
                                                        title="Preview File"
                                                        style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border: none; border-radius: 4px; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                        onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                    <button type="button" class="select-replace-btn"
                                                        data-module-id="{{ $material['module_id'] }}"
                                                        data-module-type="{{ $material['type'] }}"
                                                        data-file-name="{{ $material['file_name'] }}"
                                                        title="Ganti File"
                                                        style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border: none; border-radius: 4px; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                        onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        <div class="panel-footer">
                            <button type="submit" class="primary-btn" id="uploadSubmitBtn">
                                <i class="bi bi-send"></i> SUBMIT FOR REVIEW
                            </button>
                        </div>
                    </form>
                </section>

                <section class="panel panel-quiz" data-panel="quiz">
                    @php
                        $existingQuizModules = $activeUnitModules->filter(function ($module) {
                            return $module->type === 'quiz' && (
                                !empty($module->content_url) ||
                                (($module->quiz_questions_count ?? 0) > 0)
                            );
                        });

                        $existingQuizPayloads = $existingQuizModules->mapWithKeys(function ($module) {
                            $questions = $module->quizQuestions
                                ->sortBy('order_no')
                                ->values()
                                ->map(function ($question) {
                                    $answers = $question->answers->sortBy('order_no')->values();
                                    $correctIdx = $answers->search(function ($answer) {
                                        return (bool) $answer->is_correct;
                                    });
                                    if ($correctIdx === false) {
                                        $correctIdx = 0;
                                    }

                                    return [
                                        'text' => (string) $question->question,
                                        'weight' => (int) ($question->points ?? 10),
                                        'options' => $answers->pluck('answer_text')->values()->toArray(),
                                        'correctAnswer' => (int) $correctIdx,
                                    ];
                                })
                                ->toArray();

                            return [
                                (int) $module->id => $questions,
                            ];
                        });
                    @endphp

                    @if($existingQuizModules->isNotEmpty())
                        <div class="file-list" style="margin-bottom: 16px; display: block;">
                            <h3>Quiz Tersimpan Sebelumnya</h3>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                @foreach($existingQuizModules as $quizModule)
                                    <li style="padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--main-navy-clr);">
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                                <i class="bi bi-patch-check" style="font-size: 20px; color: var(--main-navy-clr);"></i>
                                                <div>
                                                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);">
                                                        {{ $quizModule->title ?: ('Quiz Unit ' . ($unitIndex + 1)) }}
                                                    </p>
                                                    <p style="margin: 0; font-size: 12px; color: #999;">
                                                        {{ $quizModule->quiz_questions_count ?? 0 }} Soal • Slot {{ $quizModule->order_no }}
                                                        @if($quizModule->updated_at)
                                                            • Update terakhir {{ $quizModule->updated_at->format('d M Y H:i') }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div style="display: inline-flex; align-items: center; gap: 6px;">
                                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border-radius: 999px; background: rgba(27, 23, 99, 0.1); color: var(--main-navy-clr); font-size: 12px; font-weight: 600;">
                                                    <i class="bi bi-clock-history"></i>
                                                    Riwayat
                                                </span>
                                                <button type="button"
                                                    class="quiz-edit-btn"
                                                    data-module-id="{{ $quizModule->id }}"
                                                    title="Edit Quiz"
                                                    style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border: none; border-radius: 6px; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                    onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                @if($quizModule->quizQuestions->isNotEmpty())
                                                    <button type="button"
                                                        class="quiz-history-toggle"
                                                        data-target="quiz-history-{{ $quizModule->id }}"
                                                        title="Lihat Riwayat Soal"
                                                        style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border: none; border-radius: 6px; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                        onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        @if($quizModule->quizQuestions->isNotEmpty())
                                            <div id="quiz-history-{{ $quizModule->id }}" style="margin-top: 10px; display: none; flex-direction: column; gap: 8px;">
                                                    @foreach($quizModule->quizQuestions as $questionIndex => $question)
                                                        <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px;">
                                                            <p style="margin: 0 0 6px 0; font-size: 13px; font-weight: 600; color: var(--main-navy-clr);">
                                                                {{ $questionIndex + 1 }}. {{ $question->question }}
                                                            </p>
                                                            <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: #64748b;">
                                                                @foreach($question->answers as $answer)
                                                                    <li style="margin-bottom: 4px; color: {{ $answer->is_correct ? '#0f766e' : '#64748b' }}; font-weight: {{ $answer->is_correct ? '600' : '400' }};">
                                                                        {{ $answer->answer_text }}
                                                                        @if($answer->is_correct)
                                                                            <span style="margin-left: 6px; font-size: 11px;">(Jawaban benar)</span>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endforeach
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="quizForm" class="quiz-form" action="{{ route('trainer.courses.studio.quiz', $course->id) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="courseId" value="{{ $course->id }}">
                        <input type="hidden" id="quizModuleId" name="quiz_module_id" value="">

                        <div class="quiz-meta">
                            <div class="meta-box" id="passingGradeBox">
                                <p>BATAS KELULUSAN (PASSING GRADE)</p>
                                <div class="meta-value">
                                    <input type="text" id="passingGradeInput" class="meta-input" value="70"
                                        inputmode="numeric" pattern="[0-9]*" />
                                    <strong id="passingGrade">70</strong>
                                    <span>%</span>
                                </div>
                            </div>
                            <div class="meta-box">
                                <p>BOBOT TOTAL TERDETEKSI</p>
                                <div class="meta-value">
                                    <strong id="totalWeight">0</strong><span> Points</span><em
                                        id="verifyStatus">PENDING</em>
                                </div>
                            </div>
                        </div>

                        <div id="questionsContainer" style="display: flex; flex-direction: column; gap: var(--spacing-lg);">
                        </div>

                        <div class="quiz-actions" style="margin-top: 24px;">
                            <button type="button" id="addQuestionBtn" class="primary-btn quiz-add-btn">
                                <i class="bi bi-plus-lg"></i> TAMBAH SOAL
                            </button>
                            <button type="submit" class="primary-btn quiz-save-btn">
                                <i class="bi bi-check-lg"></i> SIMPAN QUIZ
                            </button>
                        </div>
                    </form>
                </section>
            </div>

            <aside class="studio-right">
                <div class="validation-card">
                    <h3>ALUR VALIDASI MATERI</h3>
                    <ol>
                        <li>
                            <span>1</span>
                            <div>
                                <h4>DRAFTING & UPLOAD</h4>
                                <p>Pastikan nama file dan konten sesuai dengan standar platform.</p>
                            </div>
                        </li>
                        <li>
                            <span>2</span>
                            <div>
                                <h4>AUDIT ADMIN</h4>
                                <p>Tim Admin akan mereview kualitas video/PDF dan logika soal kuis Anda.</p>
                            </div>
                        </li>
                        <li>
                            <span>3</span>
                            <div>
                                <h4>STATUS: APPROVED</h4>
                                <p>Materi yang lolos akan langsung bisa diakses oleh seluruh siswa terdaftar.</p>
                            </div>
                        </li>
                    </ol>
                </div>
            </aside>
        </section>
    </main>

    <!-- REPLACEMENT CONFIRMATION MODAL -->
    <div id="replacementModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
        <div
            style="background: white; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 500px; width: 100%; animation: slideUp 0.3s ease; overflow-y: auto; max-height: 90vh;">
            <!-- Header -->
            <div
                style="padding: 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 12px; background: #f9fafb;">
                <i class="bi bi-arrow-repeat" style="font-size: 24px; color: var(--main-navy-clr);"></i>
                <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: var(--main-navy-clr);">Ganti File Materi
                </h3>
            </div>

            <!-- Body -->
            <div style="padding: 24px; display: flex; flex-direction: column; gap: 20px;">
                <!-- File Lama -->
                <div>
                    <p
                        style="margin: 0 0 8px 0; font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase;">
                        File Saat Ini</p>
                    <div
                        style="padding: 12px; background: #f3f4f6; border-radius: 8px; border-left: 3px solid var(--main-navy-clr);">
                        <p id="modalOldFileName"
                            style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);"></p>
                        <p id="modalOldFileInfo" style="margin: 4px 0 0 0; font-size: 12px; color: #64748b;"></p>
                    </div>
                </div>

                <!-- File Baru -->
                <div>
                    <p
                        style="margin: 0 0 8px 0; font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase;">
                        Pilih File Pengganti</p>
                    <div style="position: relative; border: 2px dashed #dfe6f2; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.2s; background: #f8fafc;"
                        id="replacementDropzone"
                        onmouseover="this.style.borderColor='var(--main-navy-clr)'; this.style.background='#f0f5fc';"
                        onmouseout="this.style.borderColor='#dfe6f2'; this.style.background='#f8fafc';">
                        <i class="bi bi-cloud-arrow-up"
                            style="font-size: 28px; color: var(--main-navy-clr); display: block; margin-bottom: 8px;"></i>
                        <p style="margin: 0; font-size: 13px; font-weight: 600; color: var(--main-navy-clr);">Pilih File
                            Baru</p>
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #64748b;">atau drag & drop</p>
                        <input type="file" id="replacementFileInput" style="display: none;"
                            accept=".pdf,.mp4,.pptx,.ppt,.docx,.doc,.jpg,.png,.jpeg" />
                    </div>
                </div>

                <!-- Preview File Baru -->
                <div id="replacementPreview" style="display: none;">
                    <p
                        style="margin: 0 0 8px 0; font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase;">
                        Preview Pengganti</p>
                    <div
                        style="padding: 12px; background: #f0fdf4; border-radius: 8px; border-left: 3px solid #10b981; display: flex; align-items: center; gap: 12px;">
                        <i class="bi bi-check-circle-fill" style="font-size: 20px; color: #10b981;"></i>
                        <div style="flex: 1;">
                            <p id="replacementFileName"
                                style="margin: 0; font-size: 14px; font-weight: 600; color: #059669;"></p>
                            <p id="replacementFileSize" style="margin: 4px 0 0 0; font-size: 12px; color: #64748b;"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 8px; justify-content: flex-end; background: #f9fafb;">
                <button type="button" id="replacementCancelBtn"
                    style="padding: 10px 16px; background: #e5e7eb; color: var(--main-navy-clr); border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; transition: background 0.2s;"
                    onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">
                    BATAL
                </button>
                <button type="button" id="replacementConfirmBtn" disabled
                    style="padding: 10px 16px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: not-allowed; font-weight: 600; font-size: 13px; transition: all 0.2s; opacity: 0.5;">
                    GANTI FILE
                </button>
            </div>
        </div>
    </div>

    <!-- NOTIFICATION MODAL -->
    <div id="notificationModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div
            style="background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); max-width: 400px; width: 90%; animation: slideUp 0.3s ease;">
            <div style="padding: 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 12px;">
                <i id="modalIcon" class="bi" style="font-size: 24px;"></i>
                <h3 id="modalTitle" style="margin: 0; font-size: 18px; font-weight: 600; color: var(--main-navy-clr);"></h3>
            </div>
            <div style="padding: 16px 24px; color: #64748b; font-size: 14px; line-height: 1.6;">
                <p id="modalMessage" style="margin: 0;"></p>
            </div>
            <div
                style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 8px; justify-content: flex-end;">
                <button id="modalCloseBtn" type="button"
                    style="padding: 8px 16px; background: #f3f4f6; color: var(--main-navy-clr); border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: background 0.2s;"
                    onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                    OK
                </button>
            </div>
        </div>
    </div>

    <div id="materialPreviewModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.65); z-index: 10001; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #fff; border-radius: 12px; width: min(980px, 100%); max-height: 92vh; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.25); display: flex; flex-direction: column;">
            <div style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                <div style="min-width: 0;">
                    <h3 style="margin: 0; font-size: 16px; color: var(--main-navy-clr);">Preview Materi</h3>
                    <p id="materialPreviewName" style="margin: 2px 0 0 0; font-size: 12px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></p>
                </div>
                <button type="button" id="materialPreviewCloseBtn" style="border: none; background: #f3f4f6; color: #334155; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div id="materialPreviewBody" style="padding: 0; height: min(74vh, 760px); background: #f8fafc;"></div>
        </div>
    </div>

    <style>
        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>

    <script>
        let replacementState = {
            moduleId: null,
            fileName: null,
            fileType: null,
            selectedFile: null
        };

        function showNotificationModal(title, message, type = 'info') {
            const modal = document.getElementById('notificationModal');
            const icon = document.getElementById('modalIcon');
            const titleEl = document.getElementById('modalTitle');
            const messageEl = document.getElementById('modalMessage');

            titleEl.textContent = title;
            messageEl.textContent = message;

            if (type === 'success') {
                icon.className = 'bi bi-check-circle-fill';
                icon.style.color = '#10b981';
                titleEl.style.color = '#10b981';
            } else if (type === 'error') {
                icon.className = 'bi bi-exclamation-circle-fill';
                icon.style.color = '#ef4444';
                titleEl.style.color = '#ef4444';
            } else {
                icon.className = 'bi bi-info-circle-fill';
                icon.style.color = 'var(--main-navy-clr)';
                titleEl.style.color = 'var(--main-navy-clr)';
            }

            modal.style.display = 'flex';
        }

        function closeNotificationModal() {
            document.getElementById('notificationModal').style.display = 'none';
        }

        function openMaterialPreview(viewUrl, fileName, materialType) {
            const modal = document.getElementById('materialPreviewModal');
            const nameEl = document.getElementById('materialPreviewName');
            const body = document.getElementById('materialPreviewBody');

            if (!modal || !nameEl || !body) {
                return;
            }

            nameEl.textContent = fileName || 'Materi';

            if (String(materialType || '').toLowerCase() === 'video') {
                body.innerHTML = `<video controls style="width:100%; height:100%; background:#000;"><source src="${viewUrl}"></video>`;
            } else {
                body.innerHTML = `<iframe src="${viewUrl}" style="width:100%; height:100%; border:none;" title="Preview Materi"></iframe>`;
            }

            modal.style.display = 'flex';
        }

        function closeMaterialPreview() {
            const modal = document.getElementById('materialPreviewModal');
            const body = document.getElementById('materialPreviewBody');

            if (body) {
                body.innerHTML = '';
            }

            if (modal) {
                modal.style.display = 'none';
            }
        }

        function openReplacementModal(moduleId, fileName, fileType) {
            console.log('🔵 openReplacementModal CALLED with:', { moduleId, fileName, fileType });

            replacementState = { moduleId, fileName, fileType, selectedFile: null };

            const modal = document.getElementById('replacementModal');
            const oldFileName = document.getElementById('modalOldFileName');
            const oldFileInfo = document.getElementById('modalOldFileInfo');
            const preview = document.getElementById('replacementPreview');
            const confirmBtn = document.getElementById('replacementConfirmBtn');
            const fileInput = document.getElementById('replacementFileInput');

            console.log('🔍 Elements check:', { 
                modal: !!modal, 
                oldFileName: !!oldFileName, 
                oldFileInfo: !!oldFileInfo,
                preview: !!preview,
                confirmBtn: !!confirmBtn,
                fileInput: !!fileInput
            });

            if (!modal || !oldFileName || !oldFileInfo || !preview || !confirmBtn || !fileInput) {
                console.error('❌ ERROR: One or more modal elements not found!', { modal, oldFileName, oldFileInfo, preview, confirmBtn, fileInput });
                return;
            }

            oldFileName.textContent = fileName;
            oldFileInfo.textContent = `Tipe: ${String(fileType).toUpperCase()}`;
            preview.style.display = 'none';
            confirmBtn.disabled = true;
            confirmBtn.style.opacity = '0.5';
            fileInput.value = '';

            modal.style.display = 'flex';
            console.log('✅ Modal displayed successfully!');
        }

        function closeReplacementModal() {
            document.getElementById('replacementModal').style.display = 'none';
            replacementState = { moduleId: null, fileName: null, fileType: null, selectedFile: null };
        }

        function validateReplacementFile(file) {
            const ext = (file.name.split('.').pop() || '').toLowerCase();
            const uploadType = ext === 'mp4' ? 'video' : 'pdf';

            if (uploadType !== replacementState.fileType) {
                showNotificationModal('Tipe File Tidak Sesuai', `File harus bertipe ${String(replacementState.fileType).toUpperCase()}.`, 'error');
                return false;
            }

            if (file.size > 512 * 1024 * 1024) {
                showNotificationModal('File Terlalu Besar', 'Ukuran file maksimal 512MB.', 'error');
                return false;
            }

            return true;
        }

        document.getElementById('modalCloseBtn').addEventListener('click', closeNotificationModal);
        document.getElementById('notificationModal').addEventListener('click', (e) => {
            if (e.target.id === 'notificationModal') closeNotificationModal();
        });
        document.getElementById('replacementModal').addEventListener('click', (e) => {
            if (e.target.id === 'replacementModal') closeReplacementModal();
        });
        document.getElementById('materialPreviewCloseBtn').addEventListener('click', closeMaterialPreview);
        document.getElementById('materialPreviewModal').addEventListener('click', (e) => {
            if (e.target.id === 'materialPreviewModal') closeMaterialPreview();
        });

        document.addEventListener("DOMContentLoaded", function () {
            // --- TAB SWITCHING ---
            const tabs = document.querySelectorAll(".studio-tab");
            const panels = document.querySelectorAll("[data-panel]");

            const setTab = (targetTab, updateUrl = true) => {
                tabs.forEach((tab) => tab.classList.toggle("active", tab.dataset.tab === targetTab));
                panels.forEach((panel) => panel.classList.toggle("active", panel.dataset.panel === targetTab));

                if (updateUrl) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', targetTab);
                    window.history.replaceState({}, '', url.toString());
                }
            };

            tabs.forEach((tab) => {
                tab.addEventListener("click", () => setTab(tab.dataset.tab));
            });

            const requestedTab = new URLSearchParams(window.location.search).get('tab');
            if (requestedTab === 'module' || requestedTab === 'quiz') {
                setTab(requestedTab, false);
            }

            // --- UPLOAD LOGIC ---
            let uploadedFiles = [];
            let persistedMaterials = @json($existingMaterialsPayload);
            const activeUnitModules = @json($activeUnitModules->map(function ($module) {
                return ['id' => $module->id, 'type' => $module->type];
            })->values());
            const existingQuizPayloads = @json($existingQuizPayloads);
            const dropzone = document.getElementById("dropzone");
            const fileInput = document.getElementById("fileInput");
            const fileList = document.getElementById("fileList");
            const uploadedFilesList = document.getElementById("uploadedFiles");
            const existingMaterialsBlock = document.getElementById('existingMaterialsBlock');
            const existingMaterialsList = document.getElementById('existingMaterialsList');
            const moduleForm = document.getElementById("moduleForm");
            const uploadBtn = document.getElementById("uploadSubmitBtn");
            const targetModulesInput = moduleForm.querySelector('input[name="target_modules"]');

            function escapeHtml(raw) {
                return String(raw ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function renderExistingMaterials() {
                const items = Array.isArray(persistedMaterials) ? [...persistedMaterials] : [];
                items.sort((a, b) => (a.order_no || 0) - (b.order_no || 0));

                if (!existingMaterialsBlock || !existingMaterialsList) {
                    return;
                }

                if (items.length === 0) {
                    existingMaterialsBlock.style.display = 'none';
                    existingMaterialsList.innerHTML = '';
                    return;
                }

                existingMaterialsBlock.style.display = 'block';
                existingMaterialsList.innerHTML = items.map((material) => {
                    const iconClass = material.type === 'video' ? 'bi-camera-video' : 'bi-file-earmark-pdf';
                    const slot = material.order_no ?? '-';
                    const fileName = escapeHtml(material.file_name || 'file');
                    const type = escapeHtml(String(material.type || '').toUpperCase());
                    const moduleType = escapeHtml(material.type || 'pdf');
                    const viewUrl = escapeHtml(material.view_url || '#');
                    const moduleId = Number(material.module_id || 0);

                    return `
                        <li style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--main-navy-clr);">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <i class="bi ${iconClass}" style="font-size: 20px; color: var(--main-navy-clr);"></i>
                                <div>
                                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);">${fileName}</p>
                                    <p style="margin: 0; font-size: 12px; color: #999;">${type} • Slot ${slot}</p>
                                </div>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="preview-material-btn"
                                    data-view-url="${viewUrl}" data-material-type="${moduleType}" data-file-name="${fileName}"
                                    title="Preview File"
                                    style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border-radius: 4px; border: none; text-decoration: none; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                    onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                <button type="button" class="select-replace-btn"
                                    data-module-id="${moduleId}" data-module-type="${moduleType}" data-file-name="${fileName}"
                                    title="Ganti File"
                                    style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border: none; border-radius: 4px; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                    onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                            </div>
                        </li>
                    `;
                }).join('');
            }

            renderExistingMaterials();

            document.addEventListener('click', function (event) {
                const replaceBtn = event.target.closest('.select-replace-btn');
                if (replaceBtn) {
                    openReplacementModal(
                        parseInt(replaceBtn.dataset.moduleId, 10),
                        replaceBtn.dataset.fileName || 'file',
                        replaceBtn.dataset.moduleType
                    );
                    return;
                }

                const previewBtn = event.target.closest('.preview-material-btn');
                if (previewBtn) {
                    openMaterialPreview(
                        previewBtn.dataset.viewUrl || '#',
                        previewBtn.dataset.fileName || 'Materi',
                        previewBtn.dataset.materialType || 'pdf'
                    );
                    return;
                }

                const editQuizBtn = event.target.closest('.quiz-edit-btn');
                if (editQuizBtn) {
                    const moduleId = Number(editQuizBtn.dataset.moduleId || 0);
                    const key = String(moduleId);
                    const quizPayload = (existingQuizPayloads && Object.prototype.hasOwnProperty.call(existingQuizPayloads, key))
                        ? existingQuizPayloads[key]
                        : [];

                    if (!Array.isArray(quizPayload) || quizPayload.length === 0) {
                        showNotificationModal('Info', 'Data quiz belum tersedia untuk diedit.', 'error');
                        return;
                    }

                    quizQuestions = quizPayload.map((q) => ({
                        id: questionCounter++,
                        text: q.text || '',
                        weight: Number(q.weight || 10),
                        options: Array.isArray(q.options) && q.options.length ? q.options : ['', '', '', ''],
                        correctAnswer: Number(q.correctAnswer || 0),
                    }));

                    document.getElementById('quizModuleId').value = String(moduleId);
                    renderAllQuestions();
                    saveQuizDraft();
                    setTab('quiz');
                    showNotificationModal('Siap Diedit', 'Quiz tersimpan dimuat ke editor. Kamu bisa ubah lalu simpan ulang.', 'success');
                }
            });

            const quizHistoryToggleButtons = document.querySelectorAll('.quiz-history-toggle');
            quizHistoryToggleButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    const targetId = this.dataset.target;
                    const target = document.getElementById(targetId);
                    if (!target) return;

                    const isHidden = target.style.display === 'none' || target.style.display === '';
                    target.style.display = isHidden ? 'flex' : 'none';
                });
            });

            // Setup replacement modal
            const replacementDropzone = document.getElementById('replacementDropzone');
            const replacementFileInput = document.getElementById('replacementFileInput');
            const replacementCancelBtn = document.getElementById('replacementCancelBtn');
            const replacementConfirmBtn = document.getElementById('replacementConfirmBtn');

            replacementDropzone.addEventListener('click', () => replacementFileInput.click());
            replacementDropzone.addEventListener('dragover', (e) => { e.preventDefault(); });
            replacementDropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                if (e.dataTransfer.files.length > 0) {
                    const file = e.dataTransfer.files[0];
                    if (validateReplacementFile(file)) {
                        replacementState.selectedFile = file;
                        showReplacementPreview(file);
                    }
                }
            });

            replacementFileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    const file = e.target.files[0];
                    if (validateReplacementFile(file)) {
                        replacementState.selectedFile = file;
                        showReplacementPreview(file);
                    }
                }
            });

            replacementCancelBtn.addEventListener('click', closeReplacementModal);

            replacementConfirmBtn.addEventListener('click', () => {
                if (!replacementState.selectedFile) {
                    showNotificationModal('Perhatian', 'Silakan pilih file terlebih dahulu.', 'error');
                    return;
                }

                replacementConfirmBtn.disabled = true;
                replacementConfirmBtn.textContent = 'MEMPROSES...';

                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('target_modules', String(replacementState.moduleId));
                formData.append('replace_module_id', String(replacementState.moduleId));
                formData.append('files[]', replacementState.selectedFile);

                fetch(moduleForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(async (res) => {
                        let data = {};
                        try {
                            data = await res.json();
                        } catch (_) {
                            data = {};
                        }

                        if (!res.ok) {
                            const firstValidationError = data.errors ? Object.values(data.errors).flat()[0] : null;
                            throw new Error(data.error || data.message || firstValidationError || "Unknown error");
                        }

                        return data;
                    })
                    .then(data => {
                        closeReplacementModal();
                        if (data.success) {
                            const updates = Array.isArray(data.updated_modules) ? data.updated_modules : [];
                            updates.forEach((row) => {
                                const idx = persistedMaterials.findIndex((m) => Number(m.module_id) === Number(row.module_id));
                                if (idx >= 0) {
                                    persistedMaterials[idx] = row;
                                } else {
                                    persistedMaterials.push(row);
                                }
                            });
                            renderExistingMaterials();
                            showNotificationModal('Berhasil', data.message || 'File berhasil diganti!', 'success');
                        } else {
                            let errorMsg = data.error || data.message || 'Unknown error';
                            
                            // If there are available types info, add it to the error message
                            if (data.available_types && typeof data.available_types === 'object' && Object.keys(data.available_types).length > 0) {
                                const typeInfos = Object.entries(data.available_types)
                                    .map(([type, info]) => `${type.toUpperCase()}: ${info.filled}/${info.count} terisi`)
                                    .join(' | ');
                                errorMsg += '\n\n📊 Slot Tersedia: ' + typeInfos;
                            }
                            
                            showNotificationModal('Gagal', errorMsg, 'error');
                        }
                    })
                    .catch(err => {
                        closeReplacementModal();
                        showNotificationModal('Gagal', err.message || 'Terjadi kesalahan koneksi.', 'error');
                    })
                    .finally(() => {
                        replacementConfirmBtn.disabled = false;
                        replacementConfirmBtn.textContent = 'GANTI FILE';
                    });
            });

            // Define showReplacementPreview inside DOMContentLoaded
            window.showReplacementPreview = function(file) {
                document.getElementById('replacementFileName').textContent = file.name;
                document.getElementById('replacementFileSize').textContent = `${(file.size / 1024 / 1024).toFixed(2)} MB`;
                document.getElementById('replacementPreview').style.display = 'block';
                replacementConfirmBtn.disabled = false;
                replacementConfirmBtn.style.opacity = '1';
                replacementConfirmBtn.style.cursor = 'pointer';
            };

            dropzone.addEventListener("click", () => fileInput.click());
            dropzone.addEventListener("dragover", (e) => { e.preventDefault(); dropzone.style.borderColor = "#1a237e"; dropzone.style.background = "#f0f5fc"; });
            dropzone.addEventListener("dragleave", () => { dropzone.style.borderColor = "#dfe6f2"; dropzone.style.background = "#f8fafc"; });
            dropzone.addEventListener("drop", (e) => { e.preventDefault(); dropzone.style.borderColor = "#dfe6f2"; dropzone.style.background = "#f8fafc"; handleFiles(e.dataTransfer.files); });
            fileInput.addEventListener("change", (e) => handleFiles(e.target.files));

            function handleFiles(files) {
                Array.from(files).forEach(file => uploadedFiles.push(file)); // Simpan file objek asli
                updateFileList();
                fileInput.value = "";
            }

            function getUploadType(file) {
                const ext = (file.name.split('.').pop() || '').toLowerCase();
                return ext === 'mp4' ? 'video' : 'pdf';
            }

            function updateFileList() {
                if (uploadedFiles.length > 0) {
                    fileList.style.display = "block";
                    uploadedFilesList.innerHTML = uploadedFiles.map((file, index) => `
                            <li style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--main-navy-clr);">
                                <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                    <i class="bi bi-file-earmark" style="font-size: 20px; color: var(--main-navy-clr);"></i>
                                    <div>
                                        <p style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);">${file.name}</p>
                                        <p style="margin: 0; font-size: 12px; color: #999;">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                                    </div>
                                </div>
                                <button type="button" class="delete-file" data-index="${index}" style="background: #ff6b6b; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">HAPUS</button>
                            </li>
                        `).join("");

                    document.querySelectorAll(".delete-file").forEach(btn => {
                        btn.addEventListener("click", (e) => {
                            uploadedFiles.splice(parseInt(e.target.dataset.index), 1);
                            updateFileList();
                        });
                    });
                } else {
                    fileList.style.display = "none";
                }
            }

            // AJAX SUBMIT UPLOAD
            moduleForm.addEventListener("submit", (e) => {
                e.preventDefault();
                if (uploadedFiles.length === 0) return showNotificationModal('Perhatian', 'Silakan pilih minimal 1 file untuk diupload.', 'error');

                const selectedTypes = [...new Set(uploadedFiles.map(getUploadType))];
                const availableTypesInUnit = [...new Set(activeUnitModules.map(module => module.type))];
                const unsupportedTypes = selectedTypes.filter((type) => !availableTypesInUnit.includes(type));

                if (unsupportedTypes.length > 0) {
                    const selectedText = selectedTypes.map((type) => String(type).toUpperCase()).join(', ');
                    const availableText = availableTypesInUnit.length > 0
                        ? availableTypesInUnit.map((type) => String(type).toUpperCase()).join(', ')
                        : '-';
                    showNotificationModal(
                        'Tipe Materi Tidak Sesuai Bab',
                        `File yang kamu pilih: ${selectedText}. Slot di bab ini: ${availableText}. Untuk upload ulang file lama, gunakan tombol Ganti File di daftar Semuanya Materi Yang Sudah Diupload.`,
                        'error'
                    );
                    return;
                }

                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<i class="spinner-border spinner-border-sm"></i> UPLOADING...';

                const formData = new FormData(moduleForm);
                formData.delete('files[]');
                formData.set('replace_module_id', '');

                const filteredModuleIds = activeUnitModules
                    .filter(module => selectedTypes.includes(module.type))
                    .map(module => module.id);

                if (filteredModuleIds.length > 0) {
                    const dynamicTargetModules = filteredModuleIds.join(',');
                    formData.set('target_modules', dynamicTargetModules);
                    if (targetModulesInput) {
                        targetModulesInput.value = dynamicTargetModules;
                    }
                }

                uploadedFiles.forEach(file => formData.append('files[]', file));

                fetch(moduleForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(async (res) => {
                        let data = {};
                        try {
                            data = await res.json();
                        } catch (_) {
                            data = {};
                        }

                        if (!res.ok) {
                            const firstValidationError = data.errors
                                ? Object.values(data.errors).flat()[0]
                                : null;
                            throw new Error(data.error || data.message || firstValidationError || "Unknown error");
                        }

                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            const updates = Array.isArray(data.updated_modules) ? data.updated_modules : [];
                            updates.forEach((row) => {
                                const idx = persistedMaterials.findIndex((m) => Number(m.module_id) === Number(row.module_id));
                                if (idx >= 0) {
                                    persistedMaterials[idx] = row;
                                } else {
                                    persistedMaterials.push(row);
                                }
                            });
                            renderExistingMaterials();
                            uploadedFiles = [];
                            updateFileList();
                            showNotificationModal('Berhasil', data.message || 'Materi berhasil disubmit ke Admin!', 'success');
                            setTimeout(() => window.location.reload(), 1200);
                            return;
                        } else {
                            const firstValidationError = data.errors
                                ? Object.values(data.errors).flat()[0]
                                : null;
                            let errorMsg = data.error || data.message || firstValidationError || 'Unknown error';
                            
                            // If there are available types info, add it to the error message
                            if (data.available_types && typeof data.available_types === 'object' && Object.keys(data.available_types).length > 0) {
                                const typeInfos = Object.entries(data.available_types)
                                    .map(([type, info]) => `${type.toUpperCase()}: ${info.filled}/${info.count} terisi`)
                                    .join(' | ');
                                errorMsg += '\n\n📊 Slot Tersedia: ' + typeInfos;
                            }
                            
                            showNotificationModal('Gagal', errorMsg, 'error');
                        }
                    })
                    .catch(err => showNotificationModal('Gagal', err.message || 'Terjadi kesalahan koneksi.', 'error'))
                    .finally(() => {
                        uploadBtn.disabled = false;
                        uploadBtn.innerHTML = '<i class="bi bi-send"></i> SUBMIT FOR REVIEW';
                    });
            });


            // --- QUIZ LOGIC (FIXED FOR CONTROLLER) ---
            let quizQuestions = [];
            let questionCounter = 1;
            const qContainer = document.getElementById("questionsContainer");
            const addQuestionBtn = document.getElementById("addQuestionBtn");
            const passingGradeInput = document.getElementById("passingGradeInput");
            const passingGradeDisplay = document.getElementById("passingGrade");
            const totalWeightDisplay = document.getElementById("totalWeight");
            const verifyStatusDisplay = document.getElementById("verifyStatus");
            const currentUnit = new URLSearchParams(window.location.search).get('unit') || '0';
            const quizDraftStorageKey = `trainer_quiz_draft_{{ $course->id }}_${currentUnit}`;

            function saveQuizDraft() {
                const payload = {
                    passingGrade: parseInt(passingGradeInput.value) || 70,
                    questions: quizQuestions,
                    questionCounter,
                };
                localStorage.setItem(quizDraftStorageKey, JSON.stringify(payload));
            }

            function loadQuizDraft() {
                const raw = localStorage.getItem(quizDraftStorageKey);
                if (!raw) {
                    return false;
                }

                try {
                    const parsed = JSON.parse(raw);
                    if (!parsed || !Array.isArray(parsed.questions) || parsed.questions.length === 0) {
                        return false;
                    }

                    quizQuestions = parsed.questions;
                    questionCounter = Math.max(parsed.questionCounter || 1, quizQuestions.length + 1);

                    const restoredPassingGrade = parseInt(parsed.passingGrade);
                    const finalPassingGrade = Number.isNaN(restoredPassingGrade) ? 70 : Math.max(0, Math.min(100, restoredPassingGrade));
                    passingGradeInput.value = finalPassingGrade;
                    passingGradeDisplay.textContent = finalPassingGrade;

                    renderAllQuestions();
                    return true;
                } catch (_) {
                    return false;
                }
            }

            addQuestionBtn.addEventListener("click", addQuestion);

            function addQuestion() {
                quizQuestions.push({ id: questionCounter++, text: "", weight: 10, options: ["", "", "", ""], correctAnswer: 0 });
                renderAllQuestions();
                saveQuizDraft();
            }

            function renderAllQuestions() {
                qContainer.innerHTML = "";
                quizQuestions.forEach((q, index) => {
                    const qEl = document.createElement("article");
                    qEl.className = "quiz-editor";
                    qEl.innerHTML = `
                            <div class="q-head">
                                <div class="q-number">${index + 1}</div>
                                <div class="q-inputs">
                                    <label>PERTANYAAN</label>
                                    <input type="text" class="q-text" placeholder="Masukkan pertanyaan..." value="${q.text}" />
                                </div>
                                <div class="q-score">
                                    <label>BOBOT</label>
                                    <input type="number" class="q-weight" value="${q.weight}" min="1" />
                                </div>
                                <button type="button" class="delete-question"><i class="bi bi-trash"></i> HAPUS</button>
                            </div>
                            <div class="options-section">
                                <p class="options-label">PILIHAN JAWABAN</p>
                                <div class="options-grid">
                                    ${q.options.map((opt, oIdx) => `
                                        <div class="option-container">
                                            <button type="button" class="option-btn ${q.correctAnswer === oIdx ? 'is-correct' : ''}" data-opt="${oIdx}">
                                                <i class="bi ${q.correctAnswer === oIdx ? 'bi-check-circle-fill' : 'bi-circle'}"></i>
                                                <span>Opsi ${oIdx + 1}</span>
                                            </button>
                                            <input type="text" class="option-input" value="${opt}" placeholder="Jawaban opsi ${oIdx + 1}" />
                                        </div>
                                    `).join("")}
                                </div>
                            </div>
                        `;

                    // Event Listeners for this question
                    qEl.querySelector(".q-text").addEventListener("input", (e) => {
                        q.text = e.target.value;
                        saveQuizDraft();
                    });
                    qEl.querySelector(".q-weight").addEventListener("input", (e) => {
                        q.weight = parseInt(e.target.value) || 0;
                        updateTotalWeight();
                        saveQuizDraft();
                    });
                    qEl.querySelector(".delete-question").addEventListener("click", () => {
                        quizQuestions.splice(index, 1);
                        renderAllQuestions();
                        saveQuizDraft();
                    });

                    qEl.querySelectorAll(".option-btn").forEach(btn => {
                        btn.addEventListener("click", () => {
                            q.correctAnswer = parseInt(btn.dataset.opt);
                            renderAllQuestions(); // Re-render to update UI
                            saveQuizDraft();
                        });
                    });

                    qEl.querySelectorAll(".option-input").forEach((inp, oIdx) => {
                        inp.addEventListener("input", (e) => {
                            q.options[oIdx] = e.target.value;
                            saveQuizDraft();
                        });
                    });

                    qContainer.appendChild(qEl);
                });
                updateTotalWeight();
            }

            function updateTotalWeight() {
                const total = quizQuestions.reduce((sum, q) => sum + q.weight, 0);
                totalWeightDisplay.textContent = total;
                verifyStatusDisplay.textContent = total > 0 ? "VERIFIED" : "PENDING";
                verifyStatusDisplay.style.background = total > 0 ? "#2c237f" : "#ff6b6b";
            }

            // Passing Grade UI Edit
            document.getElementById("passingGradeBox").addEventListener("click", () => {
                passingGradeDisplay.style.display = "none";
                passingGradeInput.style.display = "inline-block";
                passingGradeInput.focus();
                passingGradeInput.select();
            });

            passingGradeInput.addEventListener("blur", () => {
                let val = parseInt(passingGradeInput.value) || 70;
                val = Math.max(0, Math.min(100, val));
                passingGradeInput.value = val;
                passingGradeDisplay.textContent = val;
                passingGradeDisplay.style.display = "inline";
                passingGradeInput.style.display = "none";
                saveQuizDraft();
            });

            // Initialize from draft, fallback to first empty question
            if (!loadQuizDraft()) {
                addQuestion();
            }

            // AJAX SUBMIT QUIZ
            document.getElementById("quizForm").addEventListener("submit", function (e) {
                e.preventDefault();

                // Validasi Kosong
                if (quizQuestions.length === 0) return showNotificationModal('Perhatian', 'Tambahkan minimal 1 soal!', 'error');
                const isInvalid = quizQuestions.some(q => q.text.trim() === "" || q.options.some(o => o.trim() === ""));
                if (isInvalid) return showNotificationModal('Perhatian', 'Semua pertanyaan dan opsi jawaban wajib diisi!', 'error');

                // Dapatkan quiz_module_id dari activeUnitModules
                const explicitQuizModuleId = Number(document.getElementById('quizModuleId').value || 0);
                let quizModuleId = explicitQuizModuleId > 0 ? explicitQuizModuleId : null;

                if (!quizModuleId) {
                    for (const module of activeUnitModules) {
                        if (module.type === 'quiz') {
                            quizModuleId = module.id;
                            break;
                        }
                    }
                }
                
                if (!quizModuleId) {
                    return showNotificationModal('Perhatian', 'Modul quiz tidak ditemukan untuk bab ini.', 'error');
                }

                // Format data untuk dikirim ke Controller via fetch JSON
                const quizData = {
                    quiz_module_id: quizModuleId,
                    passingGrade: parseInt(passingGradeInput.value),
                    questions: quizQuestions.map(q => ({
                        text: q.text,
                        options: q.options,
                        correctAnswer: q.correctAnswer,
                        weight: q.weight
                    }))
                };

                const btnSubmit = this.querySelector('.quiz-save-btn');
                const origText = btnSubmit.innerHTML;
                btnSubmit.innerHTML = 'MENYIMPAN...';
                btnSubmit.disabled = true;

                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(quizData)
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            localStorage.removeItem(quizDraftStorageKey);
                            showNotificationModal('Berhasil', 'Kuis berhasil disimpan! ' + data.message, 'success');
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            showNotificationModal('Gagal', data.message || 'Pastikan data terisi dengan benar.', 'error');
                        }
                    })
                    .catch(err => showNotificationModal('Gagal', 'Terjadi kesalahan jaringan.', 'error'))
                    .finally(() => {
                        btnSubmit.innerHTML = origText;
                        btnSubmit.disabled = false;
                    });
            });
        });
    </script>
@endsection