@extends('layouts.trainer')

@section('title', 'Content Studio - Trainer')

@php
    $pageTitle = 'Content Studio';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Content Studio']
    ];

    $courseStatus = strtolower(trim((string) ($course->status ?? '')));
    $courseRejectionReason = trim((string) ($course->rejection_reason ?? ''));
    $moduleRejectionNotes = collect($activeUnitModules ?? [])
        ->filter(function ($module) {
            return strtolower(trim((string) ($module->review_status ?? ''))) === 'rejected'
                && trim((string) ($module->review_rejection_reason ?? '')) !== '';
        })
        ->map(function ($module) {
            $title = trim((string) ($module->title ?? 'Modul'));
            $reason = trim((string) ($module->review_rejection_reason ?? ''));
            return $title . ': ' . $reason;
        })
        ->values();

    $showCourseRejectionNotice = $courseStatus === 'rejected'
        || $courseRejectionReason !== ''
        || $moduleRejectionNotes->isNotEmpty();

    $activeSchemeType = (int) ($activeSchemeType ?? 1);
    $schemePermissions = $schemePermissions ?? [
        'can_module' => true,
        'can_video' => true,
        'can_quiz' => true,
    ];
    $activeTab = (string) ($activeTab ?? 'module');
    $moduleTargetModules = collect($activeUnitModules ?? [])->filter(function ($module) {
        return (string) ($module->type ?? '') === 'pdf';
    });
    $videoTargetModules = collect($activeUnitModules ?? [])->filter(function ($module) {
        return (string) ($module->type ?? '') === 'video';
    });
    $moduleTargetIds = $moduleTargetModules->isNotEmpty()
        ? $moduleTargetModules->pluck('id')->implode(',')
        : collect($activeUnitModules ?? [])->pluck('id')->implode(',');
    $videoTargetIds = $videoTargetModules->isNotEmpty()
        ? $videoTargetModules->pluck('id')->implode(',')
        : collect($activeUnitModules ?? [])->pluck('id')->implode(',');
@endphp

@push('styles')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.2/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css" />
    <style>
        main.content-studio-main {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0;
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

        .studio-tab.is-locked {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .revision-alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: var(--spacing-lg);
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid #fecaca;
            background: #fef2f2;
        }

        .revision-alert .icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: #fee2e2;
            color: #b91c1c;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .revision-alert .label {
            margin: 0 0 4px 0;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #b91c1c;
        }

        .revision-alert .reason {
            margin: 0;
            font-size: 13px;
            line-height: 1.55;
            color: #7f1d1d;
            white-space: pre-line;
        }

        .revision-alert ul {
            margin: 6px 0 0 16px;
            padding: 0;
        }

        .revision-alert li {
            margin: 0 0 4px 0;
            color: #7f1d1d;
            font-size: 13px;
            line-height: 1.45;
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

        .text-upload-shell {
            border: 1px solid #d9e3f1;
            border-radius: 18px;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.05);
        }

        .text-upload-header {
            padding: 18px 18px 14px;
            border-bottom: 1px solid #e7edf6;
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        }

        .text-upload-header h3 {
            margin: 0 0 6px;
            font-size: 18px;
            color: var(--main-navy-clr);
            font-weight: 700;
        }

        .text-upload-header p {
            margin: 0;
            font-size: 13px;
            line-height: 1.5;
            color: #64748b;
        }

        .material-outline {
            margin: 12px 0 0;
            padding-left: 18px;
            display: grid;
            gap: 6px;
        }

        .material-outline li {
            font-size: 12px;
            color: #475569;
            line-height: 1.45;
        }

        .text-editor-block {
            padding: 16px 18px;
            border-bottom: 1px solid #e7edf6;
        }

        .text-editor-label {
            margin: 0 0 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #64748b;
        }

        .text-editor-input {
            width: 100%;
            min-height: 180px;
            border: 1px solid #cdd8e8;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            line-height: 1.65;
            color: #1e293b;
            background: #fcfdff;
            resize: vertical;
        }

        .text-editor-input:focus {
            outline: none;
            border-color: var(--main-navy-clr);
            box-shadow: 0 0 0 3px rgba(35, 29, 121, 0.12);
            background: #fff;
        }

        .wysiwyg-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #d5e0ef;
            border-radius: 10px;
            background: #f8fafd;
        }

        .wysiwyg-btn {
            border: 1px solid #ccd9ea;
            background: #fff;
            color: #334155;
            border-radius: 8px;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .wysiwyg-btn:hover {
            border-color: var(--main-navy-clr);
            color: var(--main-navy-clr);
        }

        .wysiwyg-editor {
            min-height: 260px;
            border: 1px solid #cdd8e8;
            border-radius: 12px;
            padding: 14px;
            font-size: 15px;
            line-height: 1.7;
            color: #1e293b;
            background: #fff;
        }

        .wysiwyg-editor:focus {
            outline: none;
            border-color: var(--main-navy-clr);
            box-shadow: 0 0 0 3px rgba(35, 29, 121, 0.12);
        }

        .wysiwyg-editor h1,
        .wysiwyg-editor h2,
        .wysiwyg-editor h3 {
            color: var(--main-navy-clr);
            margin: 12px 0 6px;
            line-height: 1.35;
        }

        .wysiwyg-editor h1 {
            font-size: 24px;
        }

        .wysiwyg-editor h2 {
            font-size: 20px;
        }

        .wysiwyg-editor h3 {
            font-size: 18px;
        }

        .wysiwyg-editor ul {
            margin: 0 0 10px 20px;
        }

        .wysiwyg-editor img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 12px 0;
            border-radius: 12px;
        }

        .wysiwyg-editor .module-inline-image {
            margin: 12px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .wysiwyg-editor .module-inline-image img {
            max-width: 560px;
            width: 100%;
            max-height: 360px;
            object-fit: contain;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .wysiwyg-editor .module-inline-image[data-image-align="left"] {
            justify-content: flex-start;
        }

        .wysiwyg-editor .module-inline-image[data-image-align="center"] {
            justify-content: center;
        }

        .wysiwyg-editor .module-inline-image[data-image-align="right"] {
            justify-content: flex-end;
        }

        .wysiwyg-editor .module-inline-image.is-selected img {
            outline: 3px solid rgba(35, 29, 121, 0.22);
            outline-offset: 3px;
        }

        .module-code-block {
            border: 1px solid #d7deea;
            border-radius: 10px;
            background: #f8fafc;
            margin: 10px 0;
            overflow: hidden;
        }

        .module-code-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-bottom: 1px solid #d7deea;
            background: #eef2f7;
        }

        .module-code-lang {
            border: 1px solid #c7d1e2;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 12px;
            color: #334155;
            background: #fff;
        }

        .module-code-copy {
            border: 1px solid #c7d1e2;
            border-radius: 6px;
            background: #fff;
            color: #334155;
            font-size: 12px;
            padding: 4px 8px;
            cursor: pointer;
        }

        .module-code-copy:hover {
            border-color: var(--main-navy-clr);
            color: var(--main-navy-clr);
        }

        .module-code-block pre {
            margin: 0;
            padding: 12px 14px;
            background: #0f172a;
            color: #e2e8f0;
            overflow-x: auto;
            font-family: Consolas, Monaco, 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
        }

        .module-code-block code[contenteditable="true"] {
            display: block;
            min-height: 36px;
            white-space: pre;
            outline: none;
        }

        .text-editor-note {
            margin: 8px 0 0;
            font-size: 12px;
            color: #64748b;
        }

        .dropzone {
            margin: 16px 18px 18px;
            min-height: 124px;
            border-radius: 14px;
            border: 2px dashed #cfd9e8;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.25s ease;
            padding: 18px;
        }

        .dropzone:hover {
            border-color: var(--main-navy-clr);
            background: #f3f7fd;
        }

        .dropzone i {
            font-size: 28px;
            color: var(--main-navy-clr);
        }

        .dropzone h2 {
            margin: 0;
            font-size: 16px;
            color: var(--main-navy-clr);
            font-weight: 700;
        }

        .dropzone p {
            margin: 0;
            font-size: 12px;
            letter-spacing: 0.02em;
            color: #64748b;
            font-weight: 500;
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

        .secondary-btn {
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #334155;
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: 12px;
            font-size: var(--font-size-sm);
            font-weight: 600;
            letter-spacing: 0.06em;
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .secondary-btn:hover {
            border-color: var(--main-navy-clr);
            color: var(--main-navy-clr);
            background: #f8fafc;
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
                <button
                    class="studio-tab {{ $activeTab === 'module' ? 'active' : '' }} {{ !$schemePermissions['can_module'] ? 'is-locked' : '' }}"
                    data-tab="module" type="button" {{ !$schemePermissions['can_module'] ? 'data-locked="1"' : '' }}>
                    MODUL
                </button>
                <button
                    class="studio-tab {{ $activeTab === 'video' ? 'active' : '' }} {{ !$schemePermissions['can_video'] ? 'is-locked' : '' }}"
                    data-tab="video" type="button" {{ !$schemePermissions['can_video'] ? 'data-locked="1"' : '' }}>
                    VIDEO
                </button>
                <button
                    class="studio-tab {{ $activeTab === 'quiz' ? 'active' : '' }} {{ !$schemePermissions['can_quiz'] ? 'is-locked' : '' }}"
                    data-tab="quiz" type="button" {{ !$schemePermissions['can_quiz'] ? 'data-locked="1"' : '' }}>
                    PENYUSUNAN QUIZ
                </button>
            </div>
        </header>

        @if(!$schemePermissions['can_module'] || !$schemePermissions['can_video'] || !$schemePermissions['can_quiz'])
            <section
                style="margin-bottom:16px; padding: 12px 14px; border:1px dashed #cbd5e1; border-radius: 12px; background:#f8fafc; color:#475569; font-size:13px;">
                @if(!$schemePermissions['can_module'])
                    Tab modul dikunci oleh skema aktif.
                @endif
                @if(!$schemePermissions['can_video'])
                    Tab video dikunci oleh skema aktif.
                @endif
                @if(!$schemePermissions['can_quiz'])
                    Tab quiz dikunci oleh skema aktif.
                @endif
            </section>
        @endif

        @if($showCourseRejectionNotice)
            <section class="revision-alert" aria-label="Alasan revisi materi course">
                <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <p class="label">Alasan Revisi dari Admin</p>
                    @if($courseRejectionReason !== '')
                        <p class="reason">{{ $courseRejectionReason }}</p>
                    @endif
                    @if($moduleRejectionNotes->isNotEmpty())
                        <ul>
                            @foreach($moduleRejectionNotes as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>
        @endif

        <section class="studio-layout">
            <div class="studio-left">
                <section class="panel panel-module {{ $activeTab === 'module' ? 'active' : '' }}" data-panel="module">
                    <form id="moduleForm" class="module-form"
                        action="{{ route('trainer.courses.studio.upload', $course->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="courseId" value="{{ $course->id }}">
                        <input type="hidden" name="target_modules" value="{{ $moduleTargetIds }}">
                        <input type="hidden" name="replace_module_id" value="">
                        <input type="hidden" name="module_content_html" id="moduleContentHtml" value="">

                        <div class="text-upload-shell">
                            <div class="text-upload-header">
                                <h3>Tulis Materi Seperti Modul Teks</h3>
                                <p>Susun penjelasan materi dalam bentuk teks, lalu sisipkan gambar jika perlu supaya materi
                                    lebih mudah dipahami admin dan peserta.</p>
                                <ul class="material-outline">
                                    <li>Awali dengan konteks pembelajaran atau studi kasus singkat.</li>
                                    <li>Tuliskan langkah-langkah atau poin pembahasan secara terstruktur.</li>
                                    <li>Tutup dengan rangkuman, lalu tambahkan gambar pendukung jika dibutuhkan.</li>
                                </ul>
                            </div>

                            <div class="text-editor-block">
                                <p class="text-editor-label mb-2">Editor Materi</p>
                                <div class="wysiwyg-toolbar" id="wysiwygToolbar">
                                    <button type="button" class="wysiwyg-btn" data-action="bold" title="Bold"><i
                                            class="bi bi-type-bold"></i></button>
                                    <button type="button" class="wysiwyg-btn" data-action="italic" title="Italic"><i
                                            class="bi bi-type-italic"></i></button>
                                    <button type="button" class="wysiwyg-btn" data-action="h1" title="Heading 1">H1</button>
                                    <button type="button" class="wysiwyg-btn" data-action="h2" title="Heading 2">H2</button>
                                    <button type="button" class="wysiwyg-btn" data-action="h3" title="Heading 3">H3</button>
                                    <button type="button" class="wysiwyg-btn" data-action="ul" title="Bullet List"><i
                                            class="bi bi-list-ul"></i></button>
                                    <button type="button" class="wysiwyg-btn" data-action="image" title="Insert Image"><i
                                            class="bi bi-image"></i></button>
                                    <button type="button" class="wysiwyg-btn" data-action="align-left" title="Rata Kiri"><i
                                            class="bi bi-text-left"></i></button>
                                    <button type="button" class="wysiwyg-btn" data-action="align-center"
                                        title="Rata Tengah"><i class="bi bi-text-center"></i></button>
                                    <button type="button" class="wysiwyg-btn" data-action="align-right"
                                        title="Rata Kanan"><i class="bi bi-text-right"></i></button>
                                    <button type="button" class="wysiwyg-btn" data-action="code"
                                        title="Insert Code Block"><i class="bi bi-code-square"></i></button>
                                </div>
                                <input type="file" id="moduleImageInput" accept="image/*" style="display:none;" />
                                <div id="moduleWysiwygEditor" class="wysiwyg-editor" contenteditable="true"
                                    spellcheck="true">
                                    <p>Tulis pengantar materi di sini...</p>
                                </div>
                                <p class="text-editor-note">Gunakan tombol <strong>Image</strong> untuk menyisipkan gambar
                                    di dalam teks, atau tombol <strong>Code Block</strong> untuk potongan kode.</p>
                            </div>
                        </div>

                        <div class="panel-footer">
                            <button type="button" class="secondary-btn" id="previewModuleBtn">
                                <i class="bi bi-eye"></i> PREVIEW MODUL
                            </button>
                            <button type="submit" class="primary-btn" id="uploadSubmitBtn">
                                <i class="bi bi-send"></i> SUBMIT FOR REVIEW
                            </button>
                        </div>
                    </form>
                </section>

                <section class="panel panel-video {{ $activeTab === 'video' ? 'active' : '' }}" data-panel="video">
                    <form id="videoForm" class="module-form"
                        action="{{ route('trainer.courses.studio.upload', $course->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="courseId" value="{{ $course->id }}">
                        <input type="hidden" name="target_modules" value="{{ $videoTargetIds }}">
                        <input type="hidden" name="replace_module_id" value="">
                        <input type="hidden" name="module_content_html" value="">

                        <div class="text-upload-shell">
                            <div class="text-upload-header">
                                <h3>Unggah Video Pembelajaran</h3>
                                <p>Tab ini khusus untuk video. File lain tidak akan diproses di sini, sehingga alur upload
                                    lebih jelas dan cepat.</p>
                                <ul class="material-outline">
                                    <li>Gunakan video dengan penjelasan singkat dan jelas.</li>
                                    <li>Pastikan durasi dan resolusi sesuai kebutuhan kelas.</li>
                                    <li>Tambahkan judul file yang mudah dikenali admin.</li>
                                </ul>
                            </div>

                            <div class="dropzone" id="videoDropzone">
                                <input type="file" id="videoFileInput" multiple accept=".mp4" name="files[]"
                                    style="display: none" />
                                <i class="bi bi-camera-video"></i>
                                <h2>Lampiran Video</h2>
                                <p>Format: MP4</p>
                                <p style="font-size: 12px; color: #64748b; margin-top: 2px">Klik atau drag-and-drop file
                                    video ke area ini</p>
                            </div>
                        </div>

                        <div id="videoFileList" class="file-list" style="margin-top: 20px; display: none">
                            <h3>Video yang Diunggah</h3>
                            <ul id="videoUploadedFiles" style="list-style: none; padding: 0; margin: 0"></ul>
                        </div>

                        @php
                            $existingVideoMaterials = $activeUnitModules->filter(function ($module) {
                                return (string) ($module->type ?? '') === 'video' && !empty($module->content_url);
                            });
                        @endphp

                        <div id="existingVideoMaterialsBlock" class="file-list"
                            style="margin-top: 16px; display: {{ $existingVideoMaterials->isNotEmpty() ? 'block' : 'none' }};">
                            <h3>Video Tersimpan Sebelumnya</h3>
                            <ul id="existingVideoMaterialsList" style="list-style: none; padding: 0; margin: 0;">
                                @foreach($existingVideoMaterials as $material)
                                    <li
                                        style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--main-navy-clr);">
                                        <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                            <i class="bi bi-camera-video"
                                                style="font-size: 20px; color: var(--main-navy-clr);"></i>
                                            <div>
                                                <p
                                                    style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);">
                                                    {{ $material->file_name ?: basename($material->content_url) }}
                                                </p>
                                                <p style="margin: 0; font-size: 12px; color: #999;">VIDEO • Slot
                                                    {{ $material->order_no }}
                                                </p>
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 6px;">
                                            <button type="button" class="preview-material-btn"
                                                data-view-url="{{ route('trainer.courses.studio.material.view', [$course->id, $material->id]) }}"
                                                data-material-type="video"
                                                data-file-name="{{ $material->file_name ?: basename($material->content_url) }}"
                                                title="Preview File"
                                                style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border-radius: 4px; text-decoration: none; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                            <button type="button" class="select-replace-btn"
                                                data-module-id="{{ $material->id }}" data-module-type="video"
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

                        <div class="panel-footer">
                            <button type="submit" class="primary-btn" id="videoUploadSubmitBtn">
                                <i class="bi bi-send"></i> SUBMIT VIDEO
                            </button>
                        </div>
                    </form>
                </section>

                <section class="panel panel-quiz {{ $activeTab === 'quiz' ? 'active' : '' }}" data-panel="quiz">
                    @php
                        $existingQuizModules = $activeUnitModules->filter(function ($module) {
                            return $module->type === 'quiz';
                        })->values();

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
                            <h3>Slot Quiz Unit Ini</h3>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                @foreach($existingQuizModules as $quizModule)
                                    @php
                                        $questionCount = (int) ($quizModule->quiz_questions_count ?? 0);
                                        $isFilledQuiz = $questionCount > 0;
                                    @endphp
                                    <li
                                        style="padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--main-navy-clr);">
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                                <i class="bi bi-patch-check"
                                                    style="font-size: 20px; color: var(--main-navy-clr);"></i>
                                                <div>
                                                    <p
                                                        style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);">
                                                        {{ $quizModule->title ?: ('Quiz Unit ' . ($unitIndex + 1)) }}
                                                    </p>
                                                    <p style="margin: 0; font-size: 12px; color: #999;">
                                                        {{ $questionCount }} Soal • Slot
                                                        {{ $quizModule->order_no }}
                                                        @if($quizModule->updated_at)
                                                            • Update terakhir {{ $quizModule->updated_at->format('d M Y H:i') }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div style="display: inline-flex; align-items: center; gap: 6px;">
                                                <span
                                                    style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border-radius: 999px; background: rgba(27, 23, 99, 0.1); color: var(--main-navy-clr); font-size: 12px; font-weight: 600;">
                                                    <i class="bi bi-clock-history"></i>
                                                    {{ $isFilledQuiz ? 'Tersimpan' : 'Belum Diisi' }}
                                                </span>
                                                <button type="button" class="quiz-edit-btn" data-module-id="{{ $quizModule->id }}"
                                                    title="{{ $isFilledQuiz ? 'Edit Quiz' : 'Buat Quiz' }}"
                                                    style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border: none; border-radius: 6px; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                    onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                    <i class="bi {{ $isFilledQuiz ? 'bi-pencil-square' : 'bi-plus-lg' }}"></i>
                                                </button>
                                                @if($quizModule->quizQuestions->isNotEmpty())
                                                    <button type="button" class="quiz-history-toggle"
                                                        data-target="quiz-history-{{ $quizModule->id }}" title="Lihat Riwayat Soal"
                                                        style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border: none; border-radius: 6px; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                        onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        @if($quizModule->quizQuestions->isNotEmpty())
                                            <div id="quiz-history-{{ $quizModule->id }}"
                                                style="margin-top: 10px; display: none; flex-direction: column; gap: 8px;">
                                                @foreach($quizModule->quizQuestions as $questionIndex => $question)
                                                    <div
                                                        style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px;">
                                                        <p
                                                            style="margin: 0 0 6px 0; font-size: 13px; font-weight: 600; color: var(--main-navy-clr);">
                                                            {{ $questionIndex + 1 }}. {{ $question->question }}
                                                        </p>
                                                        <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: #64748b;">
                                                            @foreach($question->answers as $answer)
                                                                <li
                                                                    style="margin-bottom: 4px; color: {{ $answer->is_correct ? '#0f766e' : '#64748b' }}; font-weight: {{ $answer->is_correct ? '600' : '400' }};">
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
        </section>
    </main>

    <div id="modulePreviewModal"
        style="display:none; position:fixed; inset:0; z-index:10020; background:rgba(2,6,23,.62); align-items:center; justify-content:center; padding:20px;">
        <div
            style="background:#fff; border-radius:14px; width:min(980px, 96vw); max-height:90vh; overflow:hidden; box-shadow:0 24px 60px rgba(15,23,42,.3); display:flex; flex-direction:column;">
            <div
                style="padding:14px 16px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; gap:12px;">
                <div>
                    <h3 style="margin:0; font-size:16px; color:var(--main-navy-clr);">Preview Modul</h3>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;">Tampilan yang akan dilihat peserta LMS</p>
                </div>
                <button type="button" id="modulePreviewCloseBtn"
                    style="width:34px; height:34px; border:1px solid #d1d5db; border-radius:10px; background:#fff; color:#334155; cursor:pointer;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div id="modulePreviewBody" style="padding:18px; overflow:auto; background:#f8fafc;"></div>
        </div>
    </div>

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
        <div
            style="background: #fff; border-radius: 12px; width: min(980px, 100%); max-height: 92vh; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.25); display: flex; flex-direction: column;">
            <div
                style="padding: 14px 16px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                <div style="min-width: 0;">
                    <h3 style="margin: 0; font-size: 16px; color: var(--main-navy-clr);">Preview Materi</h3>
                    <p id="materialPreviewName"
                        style="margin: 2px 0 0 0; font-size: 12px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    </p>
                </div>
                <button type="button" id="materialPreviewCloseBtn"
                    style="border: none; background: #f3f4f6; color: #334155; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
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
            } else if (type === 'warning') {
                icon.className = 'bi bi-exclamation-triangle-fill';
                icon.style.color = '#f59e0b';
                titleEl.style.color = '#b45309';
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
            const schemePermissions = @json($schemePermissions);
            const activeSchemeType = @json($activeSchemeType);

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
                tab.addEventListener("click", () => {
                    if (tab.dataset.locked === '1') {
                        showNotificationModal('Fitur Dikunci Skema', `Tab ${String(tab.dataset.tab || '').toUpperCase()} tidak tersedia pada skema aktif.`, 'warning');
                        return;
                    }
                    setTab(tab.dataset.tab);
                });
            });

            const requestedTab = new URLSearchParams(window.location.search).get('tab');
            const serverActiveTab = @json($activeTab);
            if (requestedTab === 'module' || requestedTab === 'video' || requestedTab === 'quiz') {
                if (requestedTab === 'module' && !schemePermissions.can_module) {
                    setTab(schemePermissions.can_video ? 'video' : 'quiz', false);
                } else if (requestedTab === 'video' && !schemePermissions.can_video) {
                    setTab(schemePermissions.can_module ? 'module' : 'quiz', false);
                } else if (requestedTab === 'quiz' && !schemePermissions.can_quiz) {
                    setTab(schemePermissions.can_module ? 'module' : 'video', false);
                } else {
                    setTab(requestedTab, false);
                }
            } else {
                if (serverActiveTab === 'module' && schemePermissions.can_module) {
                    setTab('module', false);
                } else if (serverActiveTab === 'video' && schemePermissions.can_video) {
                    setTab('video', false);
                } else if (serverActiveTab === 'quiz' && schemePermissions.can_quiz) {
                    setTab('quiz', false);
                } else if (schemePermissions.can_module) {
                    setTab('module', false);
                } else if (schemePermissions.can_video) {
                    setTab('video', false);
                } else {
                    setTab('quiz', false);
                }
            }

            // --- UPLOAD LOGIC ---
            let videoUploadedFiles = [];
            let persistedMaterials = @json($uploadedMaterials);
            const activeUnitModules = @json($activeUnitModules->map(function ($module) {
                return ['id' => $module->id, 'type' => $module->type];
            })->values());
            const existingQuizPayloads = @json($existingQuizPayloads);
            const quizSlotModuleIds = activeUnitModules
                .filter((module) => module.type === 'quiz')
                .map((module) => Number(module.id));
            const videoForm = document.getElementById('videoForm');
            const videoDropzone = document.getElementById('videoDropzone');
            const videoFileInput = document.getElementById('videoFileInput');
            const videoFileList = document.getElementById('videoFileList');
            const videoUploadedFilesList = document.getElementById('videoUploadedFiles');
            const existingVideoMaterialsBlock = document.getElementById('existingVideoMaterialsBlock');
            const existingVideoMaterialsList = document.getElementById('existingVideoMaterialsList');
            const videoUploadBtn = document.getElementById('videoUploadSubmitBtn');
            const moduleForm = document.getElementById("moduleForm");
            const uploadBtn = document.getElementById("uploadSubmitBtn");
            const targetModulesInput = moduleForm.querySelector('input[name="target_modules"]');
            const materialDraftInput = document.getElementById('materialDraftInput');
            const materialDraftStorageKey = `trainer-course-draft-${{ (int) $course->id }}`;
            const moduleEditor = document.getElementById('moduleWysiwygEditor');
            const moduleContentInput = document.getElementById('moduleContentHtml');
            const moduleImageInput = document.getElementById('moduleImageInput');
            const editorImageUploadUrl = @json(route('trainer.courses.studio.editor-image', $course->id));
            const previewModuleBtn = document.getElementById('previewModuleBtn');
            const modulePreviewModal = document.getElementById('modulePreviewModal');
            const modulePreviewBody = document.getElementById('modulePreviewBody');
            const modulePreviewCloseBtn = document.getElementById('modulePreviewCloseBtn');
            const toolbar = document.getElementById('wysiwygToolbar');
            let selectedModuleImage = null;

            const codeLangOptions = `
                            <option value="html">HTML</option>
                            <option value="css">CSS</option>
                            <option value="javascript">JavaScript</option>
                            <option value="php">PHP</option>
                        `;

            function escapeCode(raw) {
                return String(raw ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            function insertCodeBlock() {
                const codeBlockHtml = `
                                <div class="module-code-block" contenteditable="false">
                                    <div class="module-code-top">
                                        <select class="module-code-lang">${codeLangOptions}</select>
                                        <button type="button" class="module-code-copy">Copy Code</button>
                                    </div>
                                    <pre><code class="language-html" contenteditable="true" spellcheck="false"></code></pre>
                                </div>
                                <p><br></p>
                            `;
                document.execCommand('insertHTML', false, codeBlockHtml);
            }

            async function insertImageFromFile(file) {
                if (!file || !file.type || !file.type.startsWith('image/')) {
                    showNotificationModal('Tipe File Tidak Sesuai', 'Silakan pilih file gambar yang valid.', 'error');
                    return;
                }

                const selection = window.getSelection();
                const savedRange = selection && selection.rangeCount > 0 ? selection.getRangeAt(0).cloneRange() : null;

                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('image', file);

                try {
                    const response = await fetch(editorImageUploadUrl, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok || !data.success || !data.url) {
                        throw new Error(data.error || data.message || 'Gagal mengunggah gambar.');
                    }

                    if (savedRange && selection) {
                        selection.removeAllRanges();
                        selection.addRange(savedRange);
                    }

                    const imageHtml = `
                                    <figure class="module-inline-image" data-image-align="center">
                                        <img src="${String(data.url || '')}" alt="Gambar materi" data-image-width="560" style="width:560px; max-width:100%;" />
                                    </figure>
                                `;
                    document.execCommand('insertHTML', false, imageHtml);
                    syncEditorContentToInput();
                } catch (error) {
                    showNotificationModal('Gagal', error.message || 'Gagal mengunggah gambar.', 'error');
                }
            }

            function setSelectedImageAlignment(alignment) {
                if (!selectedModuleImage) {
                    showNotificationModal('Pilih Gambar', 'Klik gambar terlebih dahulu, lalu pilih perataan yang diinginkan.', 'warning');
                    return;
                }

                const targetFigure = selectedModuleImage.classList?.contains('module-inline-image')
                    ? selectedModuleImage
                    : selectedModuleImage.closest('.module-inline-image');

                if (!targetFigure) return;

                targetFigure.dataset.imageAlign = alignment;
                syncEditorContentToInput();
            }

            function syncEditorContentToInput() {
                if (!moduleEditor || !moduleContentInput) return;
                moduleContentInput.value = moduleEditor.innerHTML.trim();
                try {
                    localStorage.setItem(materialDraftStorageKey, moduleContentInput.value);
                } catch (_) {
                }
            }

            if (moduleEditor && moduleContentInput) {
                try {
                    const savedRichDraft = localStorage.getItem(materialDraftStorageKey);
                    if (
                        savedRichDraft &&
                        savedRichDraft.trim() !== '' &&
                        !savedRichDraft.includes('data:image/') &&
                        savedRichDraft.length <= 60000
                    ) {
                        moduleEditor.innerHTML = savedRichDraft;
                    } else if (savedRichDraft && (savedRichDraft.includes('data:image/') || savedRichDraft.length > 60000)) {
                        localStorage.removeItem(materialDraftStorageKey);
                    }
                } catch (_) {
                }

                moduleEditor.addEventListener('input', syncEditorContentToInput);
                moduleEditor.addEventListener('change', syncEditorContentToInput);
            }

            if (toolbar) {
                toolbar.addEventListener('click', function (event) {
                    const btn = event.target.closest('.wysiwyg-btn');
                    if (!btn) return;
                    const action = btn.dataset.action;

                    if (action === 'bold') document.execCommand('bold');
                    if (action === 'italic') document.execCommand('italic');
                    if (action === 'ul') document.execCommand('insertUnorderedList');
                    if (action === 'h1') document.execCommand('formatBlock', false, 'h1');
                    if (action === 'h2') document.execCommand('formatBlock', false, 'h2');
                    if (action === 'h3') document.execCommand('formatBlock', false, 'h3');
                    if (action === 'image' && moduleImageInput) moduleImageInput.click();
                    if (action === 'align-left') setSelectedImageAlignment('left');
                    if (action === 'align-center') setSelectedImageAlignment('center');
                    if (action === 'align-right') setSelectedImageAlignment('right');
                    if (action === 'code') insertCodeBlock();

                    syncEditorContentToInput();
                });
            }

            if (moduleImageInput) {
                moduleImageInput.addEventListener('change', function (event) {
                    const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
                    if (file) {
                        insertImageFromFile(file);
                    }
                    moduleImageInput.value = '';
                });
            }

            if (moduleEditor) {
                moduleEditor.addEventListener('click', function (event) {
                    const clickedImage = event.target.closest('.module-inline-image, .module-inline-image img');
                    if (clickedImage) {
                        selectedModuleImage = clickedImage.classList.contains('module-inline-image')
                            ? clickedImage
                            : clickedImage.closest('.module-inline-image');

                        moduleEditor.querySelectorAll('.module-inline-image').forEach((figure) => {
                            figure.classList.toggle('is-selected', figure === selectedModuleImage);
                        });
                    }

                    const copyBtn = event.target.closest('.module-code-copy');
                    if (!copyBtn) return;
                    const codeEl = copyBtn.closest('.module-code-block')?.querySelector('code');
                    const text = codeEl ? codeEl.textContent || '' : '';
                    navigator.clipboard.writeText(text).then(() => {
                        const old = copyBtn.textContent;
                        copyBtn.textContent = 'Copied';
                        setTimeout(() => copyBtn.textContent = old, 1000);
                    }).catch(() => {
                    });
                });

                moduleEditor.addEventListener('change', function (event) {
                    const langSelect = event.target.closest('.module-code-lang');
                    if (!langSelect) return;
                    const codeEl = langSelect.closest('.module-code-block')?.querySelector('code');
                    if (codeEl) {
                        codeEl.className = `language-${langSelect.value}`;
                    }
                    syncEditorContentToInput();
                }, true);
            }

            function openModulePreview() {
                if (!moduleEditor || !modulePreviewModal || !modulePreviewBody) return;
                syncEditorContentToInput();
                const rawHtml = moduleContentInput.value || '';
                if (rawHtml.trim() === '') {
                    showNotificationModal('Preview Kosong', 'Silakan isi materi terlebih dahulu sebelum preview.', 'warning');
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'module-preview-article';
                wrapper.innerHTML = rawHtml;

                wrapper.querySelectorAll('.module-code-block').forEach((block) => {
                    const lang = block.querySelector('.module-code-lang')?.value || 'plaintext';
                    const codeText = block.querySelector('code')?.textContent || '';
                    const pre = document.createElement('pre');
                    const code = document.createElement('code');
                    code.className = `language-${lang}`;
                    code.textContent = codeText;
                    pre.appendChild(code);

                    const copyBtn = document.createElement('button');
                    copyBtn.type = 'button';
                    copyBtn.className = 'module-code-copy';
                    copyBtn.textContent = 'Copy Code';
                    copyBtn.style.margin = '8px 0 0';
                    copyBtn.addEventListener('click', () => {
                        navigator.clipboard.writeText(codeText);
                    });

                    const holder = document.createElement('div');
                    holder.className = 'module-code-block';
                    holder.appendChild(pre);
                    holder.appendChild(copyBtn);

                    block.replaceWith(holder);
                });

                modulePreviewBody.innerHTML = '';
                modulePreviewBody.appendChild(wrapper);

                modulePreviewBody.querySelectorAll('pre code').forEach((el) => {
                    if (window.hljs) {
                        window.hljs.highlightElement(el);
                    }
                });

                modulePreviewModal.style.display = 'flex';
            }

            if (previewModuleBtn) {
                previewModuleBtn.addEventListener('click', openModulePreview);
            }

            if (modulePreviewCloseBtn) {
                modulePreviewCloseBtn.addEventListener('click', () => {
                    modulePreviewModal.style.display = 'none';
                });
            }

            if (modulePreviewModal) {
                modulePreviewModal.addEventListener('click', (e) => {
                    if (e.target === modulePreviewModal) {
                        modulePreviewModal.style.display = 'none';
                    }
                });
            }

            function escapeHtml(raw) {
                return String(raw ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function renderVideoMaterials() {
                const items = Array.isArray(persistedMaterials)
                    ? [...persistedMaterials].filter((material) => String(material.type || '') === 'video')
                    : [];
                items.sort((a, b) => (a.order_no || 0) - (b.order_no || 0));

                if (!existingVideoMaterialsBlock || !existingVideoMaterialsList) {
                    return;
                }

                if (items.length === 0) {
                    existingVideoMaterialsBlock.style.display = 'none';
                    existingVideoMaterialsList.innerHTML = '';
                    return;
                }

                existingVideoMaterialsBlock.style.display = 'block';
                existingVideoMaterialsList.innerHTML = items.map((material) => {
                    const slot = material.order_no ?? '-';
                    const fileName = escapeHtml(material.file_name || 'file');
                    const viewUrl = escapeHtml(material.view_url || '#');
                    const moduleId = Number(material.module_id || 0);

                    return `
                                                <li style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--main-navy-clr);">
                                                    <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                                        <i class="bi bi-camera-video" style="font-size: 20px; color: var(--main-navy-clr);"></i>
                                                        <div>
                                                            <p style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);">${fileName}</p>
                                                            <p style="margin: 0; font-size: 12px; color: #999;">VIDEO • Slot ${slot}</p>
                                                        </div>
                                                    </div>
                                                    <div style="display: flex; gap: 6px;">
                                                        <button type="button" class="preview-material-btn"
                                                            data-view-url="${viewUrl}" data-material-type="video" data-file-name="${fileName}"
                                                            title="Preview File"
                                                            style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--main-navy-clr); color: var(--white-clr); border-radius: 4px; border: none; text-decoration: none; cursor: pointer; transition: opacity 0.2s; font-size: 12px;"
                                                            onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </button>
                                                        <button type="button" class="select-replace-btn"
                                                            data-module-id="${moduleId}" data-module-type="video" data-file-name="${fileName}"
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

            renderVideoMaterials();

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

                    if (Array.isArray(quizPayload) && quizPayload.length > 0) {
                        quizQuestions = quizPayload.map((q) => ({
                            id: questionCounter++,
                            text: q.text || '',
                            weight: Number(q.weight || 10),
                            options: Array.isArray(q.options) && q.options.length ? q.options : ['', '', '', ''],
                            correctAnswer: Number(q.correctAnswer || 0),
                        }));
                    } else {
                        quizQuestions = createDefaultQuestions(5);
                    }

                    document.getElementById('quizModuleId').value = String(moduleId);
                    renderAllQuestions();
                    saveQuizDraft();
                    setTab('quiz');
                    showNotificationModal(
                        'Editor Quiz Siap',
                        (Array.isArray(quizPayload) && quizPayload.length > 0)
                            ? 'Quiz tersimpan dimuat ke editor. Kamu bisa ubah lalu simpan ulang.'
                            : 'Slot quiz kosong dibuka otomatis dengan 5 soal default. Silakan isi kontennya.',
                        'success'
                    );
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
                            renderVideoMaterials();
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
            window.showReplacementPreview = function (file) {
                document.getElementById('replacementFileName').textContent = file.name;
                document.getElementById('replacementFileSize').textContent = `${(file.size / 1024 / 1024).toFixed(2)} MB`;
                document.getElementById('replacementPreview').style.display = 'block';
                replacementConfirmBtn.disabled = false;
                replacementConfirmBtn.style.opacity = '1';
                replacementConfirmBtn.style.cursor = 'pointer';
            };

            if (videoDropzone && videoFileInput) {
                videoDropzone.addEventListener("click", () => videoFileInput.click());
                videoDropzone.addEventListener("dragover", (e) => { e.preventDefault(); videoDropzone.style.borderColor = "#1a237e"; videoDropzone.style.background = "#f0f5fc"; });
                videoDropzone.addEventListener("dragleave", () => { videoDropzone.style.borderColor = "#dfe6f2"; videoDropzone.style.background = "#f8fafc"; });
                videoDropzone.addEventListener("drop", (e) => {
                    e.preventDefault();
                    videoDropzone.style.borderColor = "#dfe6f2";
                    videoDropzone.style.background = "#f8fafc";
                    handleVideoFiles(e.dataTransfer.files);
                });
                videoFileInput.addEventListener("change", (e) => handleVideoFiles(e.target.files));
            }

            function handleVideoFiles(files) {
                if (!schemePermissions.can_video) {
                    showNotificationModal('Fitur Dikunci Skema', 'Skema aktif tidak mengizinkan upload video.', 'warning');
                    return;
                }

                Array.from(files).forEach((file) => {
                    if (getUploadType(file) !== 'video') {
                        return;
                    }
                    videoUploadedFiles.push(file);
                });

                if (Array.from(files).length > 0 && videoUploadedFiles.length === 0) {
                    showNotificationModal('Tidak Sesuai Skema', 'Hanya file MP4 yang bisa diupload di tab video.', 'warning');
                }

                updateVideoFileList();
                if (videoFileInput) {
                    videoFileInput.value = '';
                }
            }

            function getUploadType(file) {
                const ext = (file.name.split('.').pop() || '').toLowerCase();
                return ext === 'mp4' ? 'video' : 'pdf';
            }

            function updateVideoFileList() {
                if (videoUploadedFiles.length > 0) {
                    videoFileList.style.display = "block";
                    videoUploadedFilesList.innerHTML = videoUploadedFiles.map((file, index) => `
                                                    <li style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 8px; border-left: 3px solid var(--main-navy-clr);">
                                                        <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                                            <i class="bi bi-camera-video" style="font-size: 20px; color: var(--main-navy-clr);"></i>
                                                            <div>
                                                                <p style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr);">${file.name}</p>
                                                                <p style="margin: 0; font-size: 12px; color: #999;">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="delete-video-file" data-index="${index}" style="background: #ff6b6b; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">HAPUS</button>
                                                    </li>
                                                `).join("");

                    document.querySelectorAll(".delete-video-file").forEach(btn => {
                        btn.addEventListener("click", (e) => {
                            videoUploadedFiles.splice(parseInt(e.target.dataset.index), 1);
                            updateVideoFileList();
                        });
                    });
                } else {
                    videoFileList.style.display = "none";
                }
            }

            if (videoForm) {
                videoForm.addEventListener("submit", (e) => {
                    e.preventDefault();

                    if (videoUploadedFiles.length === 0) {
                        showNotificationModal('Perhatian', 'Silakan pilih minimal 1 file video untuk diupload.', 'error');
                        return;
                    }

                    const selectedTypes = [...new Set(videoUploadedFiles.map(getUploadType))];
                    if (selectedTypes.some((type) => type !== 'video')) {
                        showNotificationModal('Tipe Materi Tidak Sesuai', 'Tab video hanya menerima file MP4.', 'error');
                        return;
                    }

                    videoUploadBtn.disabled = true;
                    videoUploadBtn.innerHTML = '<i class="spinner-border spinner-border-sm"></i> UPLOADING...';

                    const formData = new FormData(videoForm);
                    formData.delete('files[]');
                    formData.set('replace_module_id', '');
                    formData.set('module_content_html', '');

                    const filteredVideoIds = activeUnitModules
                        .filter(module => module.type === 'video')
                        .map(module => module.id);

                    if (filteredVideoIds.length > 0) {
                        const dynamicTargetModules = filteredVideoIds.join(',');
                        formData.set('target_modules', dynamicTargetModules);
                    }

                    videoUploadedFiles.forEach(file => formData.append('files[]', file));

                    fetch(videoForm.action, {
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
                                renderVideoMaterials();
                                videoUploadedFiles = [];
                                updateVideoFileList();
                                showNotificationModal('Berhasil', data.message || 'Video berhasil disubmit ke Admin!', 'success');
                                return;
                            }

                            showNotificationModal('Gagal', data.error || data.message || 'Unknown error', 'error');
                        })
                        .catch(err => {
                            showNotificationModal('Gagal', err.message || 'Terjadi kesalahan koneksi.', 'error');
                        })
                        .finally(() => {
                            videoUploadBtn.disabled = false;
                            videoUploadBtn.innerHTML = '<i class="bi bi-send"></i> SUBMIT VIDEO';
                        });
                });
            }

            // AJAX SUBMIT UPLOAD
            moduleForm.addEventListener("submit", (e) => {
                e.preventDefault();
                syncEditorContentToInput();
                const hasEditorContent = !!(moduleContentInput && moduleContentInput.value.trim() !== '');
                if (!hasEditorContent) {
                    return showNotificationModal('Perhatian', 'Silakan isi materi di editor terlebih dahulu.', 'error');
                }

                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<i class="spinner-border spinner-border-sm"></i> UPLOADING...';

                const formData = new FormData(moduleForm);
                formData.set('replace_module_id', '');

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
                            if (moduleEditor) {
                                moduleEditor.innerHTML = '<p>Tulis pengantar materi di sini...</p>';
                            }
                            if (moduleContentInput) {
                                moduleContentInput.value = '';
                            }
                            showNotificationModal('Berhasil', data.message || 'Materi berhasil disubmit ke Admin!', 'success');
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

            function createDefaultQuestions(count = 5) {
                questionCounter = 1;
                return Array.from({ length: count }, () => ({
                    id: questionCounter++,
                    text: "",
                    weight: 10,
                    options: ["", "", "", ""],
                    correctAnswer: 0,
                }));
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

            // Initialize from draft, fallback to template default slot and default questions
            if (!loadQuizDraft()) {
                const firstQuizSlotId = quizSlotModuleIds.length > 0 ? quizSlotModuleIds[0] : null;
                if (firstQuizSlotId) {
                    document.getElementById('quizModuleId').value = String(firstQuizSlotId);
                }
                quizQuestions = createDefaultQuestions(5);
                renderAllQuestions();
                saveQuizDraft();
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