@extends('layouts.trainer')

@section('title', 'Event Studio - ' . $event->title)

@php
    $pageTitle = 'Event Studio';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Events', 'url' => route('trainer.events')],
        ['label' => 'Studio']
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
            margin-bottom: 24px;
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

        .option-container {
            display: flex;
            gap: var(--spacing-sm);
            align-items: stretch;
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


        /* Responsive */
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
                <a class="back-btn" href="{{ route('trainer.events.show', $event->id) }}">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <p class="kicker">EVENT STUDIO • MATERIAL MANAGEMENT</p>
                    <h1>{{ $event->title }}</h1>
                </div>
            </div>

            <div class="studio-tabs" role="tablist">
                <button class="studio-tab active" data-tab="module" type="button">
                    MODUL &amp; ASSETS
                </button>
                <button class="studio-tab" data-tab="quiz" type="button">
                    QUIZ EVENT
                </button>
            </div>
        </header>

        <section class="studio-layout">
            <div class="studio-left">
                <section class="panel panel-module active" data-panel="module">
                    <form id="moduleForm" class="module-form"
                        action="{{ route('trainer.events.studio.upload', $event->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        {{-- Menggunakan eventId --}}
                        <input type="hidden" name="eventId" value="{{ $event->id }}">

                        <div class="dropzone" id="dropzone">
                            <input type="file" id="fileInput" multiple
                                accept=".pdf,.mp4,.pptx,.ppt,.docx,.doc,.jpg,.png,.jpeg" name="files[]"
                                style="display: none" />
                            <i class="bi bi-cloud-arrow-up"></i>
                            <h2>Drop Event Assets Here</h2>
                            <p>SUPPORT: PDF, MP4, PPTX</p>
                            <p style="font-size: 12px; color: #999; margin-top: 8px">
                                atau klik untuk memilih file
                            </p>
                        </div>

                        <div id="fileList" class="file-list" style="margin-top: 20px; display: none">
                            <h3>Materi yang Diunggah</h3>
                            <ul id="uploadedFiles" style="list-style: none; padding: 0; margin: 0"></ul>
                        </div>

                        <div class="panel-footer">
                            <button type="submit" class="primary-btn" id="submitBtn">
                                SUBMIT FOR AUDIT <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </form>
                </section>

                <section class="panel panel-quiz" data-panel="quiz">
                    <form id="quizForm" class="quiz-form" action="{{ route('trainer.events.studio.quiz', $event->id) }}"
                        method="POST">
                        @csrf
                        {{-- Menggunakan eventId --}}
                        <input type="hidden" name="eventId" value="{{ $event->id }}">

                        <div class="quiz-meta">
                            <div class="meta-box" id="passingGradeBox">
                                <p>BATAS KELULUSAN (PASSING GRADE)</p>
                                <div class="meta-value">
                                    <input type="text" id="passingGradeInput" class="meta-input" value="70"
                                        inputmode="numeric" pattern="[0-9]*" style="display:none;" />
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

                        <div class="quiz-actions">
                            <button type="button" id="addQuestionBtn" class="primary-btn quiz-add-btn">
                                <i class="bi bi-plus-lg"></i> TAMBAH SOAL
                            </button>
                            <button type="submit" class="primary-btn quiz-save-btn">
                                SIMPAN QUIZ <i class="bi bi-check-lg"></i>
                            </button>
                        </div>
                    </form>
                </section>
            </div>

            <aside class="studio-right">
                <div class="validation-card">
                    <h3>ALUR VALIDASI EVENT</h3>
                    <ol>
                        <li>
                            <span>1</span>
                            <div>
                                <h4>ASSET SUBMISSION</h4>
                                <p>Upload materi presentasi dan handout H-1 acara.</p>
                            </div>
                        </li>
                        <li>
                            <span>2</span>
                            <div>
                                <h4>ADMIN AUDIT</h4>
                                <p>Tim admin akan memeriksa format dan konten materi.</p>
                            </div>
                        </li>
                        <li>
                            <span>3</span>
                            <div>
                                <h4>READY TO STREAM</h4>
                                <p>Materi akan tersedia di dashboard peserta saat acara dimulai.</p>
                            </div>
                        </li>
                    </ol>
                </div>
            </aside>
        </section>
    </main>

    <script>
        let uploadedFiles = [];
        let quizQuestions = [];
        let questionCounter = 1;

        document.addEventListener("DOMContentLoaded", function () {
            // --- Tab Logic ---
            const tabs = document.querySelectorAll(".studio-tab");
            const panels = document.querySelectorAll("[data-panel]");
            const url = new URL(window.location.href);
            const queryTab = url.searchParams.get("tab");
            const initialTab = queryTab === "quiz" ? "quiz" : "module";

            const setTab = (targetTab) => {
                tabs.forEach(tab => tab.classList.toggle("active", tab.dataset.tab === targetTab));
                panels.forEach(panel => panel.classList.toggle("active", panel.dataset.panel === targetTab));
                url.searchParams.set("tab", targetTab);
                window.history.replaceState({}, "", url);
            };

            setTab(initialTab);
            tabs.forEach(tab => tab.addEventListener("click", () => setTab(tab.dataset.tab)));

            // --- File Upload Logic ---
            const dropzone = document.getElementById("dropzone");
            const fileInput = document.getElementById("fileInput");
            const fileList = document.getElementById("fileList");
            const uploadedFilesList = document.getElementById("uploadedFiles");
            const moduleForm = document.getElementById("moduleForm");
            const submitBtn = document.getElementById("submitBtn");

            dropzone.addEventListener("click", () => fileInput.click());

            dropzone.addEventListener("dragover", (e) => {
                e.preventDefault();
                dropzone.style.borderColor = "#1a237e";
                dropzone.style.backgroundColor = "#e0e7ff";
            });

            dropzone.addEventListener("dragleave", () => {
                dropzone.style.borderColor = "#dfe6f2";
                dropzone.style.backgroundColor = "#f8fafc";
            });

            dropzone.addEventListener("drop", (e) => {
                e.preventDefault();
                dropzone.style.borderColor = "#dfe6f2";
                dropzone.style.backgroundColor = "#f8fafc";
                handleFiles(e.dataTransfer.files);
            });

            fileInput.addEventListener("change", (e) => handleFiles(e.target.files));

            function handleFiles(files) {
                Array.from(files).forEach((file) => {
                    if (!uploadedFiles.some(f => f.name === file.name)) {
                        uploadedFiles.push(file);
                    }
                });
                updateFileList();
                fileInput.value = '';
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
                                        <p style="margin: 0; font-size: 12px; color: #999;">${(file.size / 1024).toFixed(2)} KB</p>
                                    </div>
                                </div>
                                <button type="button" class="delete-file" data-index="${index}" style="background: #ff6b6b; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">HAPUS</button>
                            </li>
                        `).join("");

                    uploadedFilesList.querySelectorAll(".delete-file").forEach(btn => {
                        btn.addEventListener("click", (e) => {
                            const index = parseInt(e.target.dataset.index);
                            uploadedFiles.splice(index, 1);
                            updateFileList();
                        });
                    });
                } else {
                    fileList.style.display = "none";
                }
            }

            moduleForm.addEventListener("submit", (e) => {
                e.preventDefault();
                if (uploadedFiles.length === 0) {
                    alert("Silakan upload minimal 1 file sebelum submit.");
                    return;
                }

                const formData = new FormData(moduleForm);
                uploadedFiles.forEach(file => {
                    formData.append('files[]', file);
                });

                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...';

                fetch(moduleForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`Success! ${data.files ? data.files.length : 'Files'} assets uploaded.`);
                            window.location.href = "{{ route('trainer.events.show', $event->id) }}";
                        } else {
                            alert('Upload failed: ' + (data.error || 'Unknown error'));
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred during upload.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    });
            });

            // --- Quiz Logic (Exactly same as Course Studio) ---
            const quizForm = document.getElementById("quizForm");
            const questionsContainer = document.getElementById("questionsContainer");
            const addQuestionBtn = document.getElementById("addQuestionBtn");
            const passingGradeBox = document.getElementById("passingGradeBox");
            const passingGradeInput = document.getElementById("passingGradeInput");
            const passingGradeDisplay = document.getElementById("passingGrade");
            const totalWeightDisplay = document.getElementById("totalWeight");
            const verifyStatusDisplay = document.getElementById("verifyStatus");

            addQuestionBtn.addEventListener("click", (e) => { e.preventDefault(); addQuestion(); });

            function addQuestion() {
                const questionIndex = quizQuestions.length;
                const question = { id: questionCounter++, text: "", weight: 10, options: ["", "", "", ""], correctAnswer: 0 };
                quizQuestions.push(question);
                renderQuestion(questionIndex);
                updateTotalWeight();
            }

            function renderQuestion(index) {
                const question = quizQuestions[index];
                const questionEl = document.createElement("article");
                questionEl.className = "quiz-editor";
                questionEl.setAttribute("data-question-id", question.id);
                questionEl.innerHTML = `
                        <div class="q-head">
                            <div class="q-number">${index + 1}</div>
                            <div class="q-inputs">
                                <label>PERTANYAAN</label>
                                <input type="text" class="question-input" data-index="${index}" placeholder="Masukkan pertanyaan..." value="${question.text}" />
                            </div>
                            <div class="q-score">
                                <label>BOBOT</label>
                                <input type="number" class="weight-input" data-index="${index}" value="${question.weight}" min="1" />
                            </div>
                            <button type="button" class="delete-question" data-index="${index}"><i class="bi bi-trash"></i> HAPUS</button>
                        </div>
                        <div class="options-section">
                            <p class="options-label">PILIHAN JAWABAN</p>
                            <div class="options-grid">
                                ${question.options.map((opt, i) => `
                                    <div class="option-container">
                                        <button type="button" class="option-btn ${question.correctAnswer === i ? "is-correct" : ""}" data-q="${index}" data-opt="${i}">
                                            <i class="bi ${question.correctAnswer === i ? "bi-check-circle-fill" : "bi-circle"}"></i>
                                            <span>Opsi ${i + 1}</span>
                                        </button>
                                        <input type="text" class="option-input" data-q="${index}" data-opt="${i}" placeholder="Jawaban..." value="${opt}" />
                                    </div>
                                `).join("")}
                            </div>
                        </div>`;

                const existing = questionsContainer.querySelector(`[data-question-id="${question.id}"]`);
                if (existing) existing.replaceWith(questionEl);
                else questionsContainer.appendChild(questionEl);

                questionEl.querySelector(".question-input").addEventListener("input", e => quizQuestions[index].text = e.target.value);
                questionEl.querySelector(".weight-input").addEventListener("input", e => { quizQuestions[index].weight = parseInt(e.target.value) || 0; updateTotalWeight(); });
                questionEl.querySelector(".delete-question").addEventListener("click", () => { quizQuestions.splice(index, 1); questionsContainer.innerHTML = ""; quizQuestions.forEach((_, i) => renderQuestion(i)); updateTotalWeight(); });
                questionEl.querySelectorAll(".option-btn").forEach(btn => btn.addEventListener("click", () => { quizQuestions[index].correctAnswer = parseInt(btn.dataset.opt); renderQuestion(index); }));
                questionEl.querySelectorAll(".option-input").forEach(inp => inp.addEventListener("input", e => quizQuestions[index].options[parseInt(e.target.dataset.opt)] = e.target.value));
            }

            function updateTotalWeight() {
                const total = quizQuestions.reduce((sum, q) => sum + q.weight, 0);
                totalWeightDisplay.textContent = total;
                verifyStatusDisplay.textContent = total > 0 ? "VERIFIED" : "PENDING";
                verifyStatusDisplay.style.background = total > 0 ? "#2c237f" : "#ff6b6b";
            }

            passingGradeBox.addEventListener("click", () => {
                passingGradeDisplay.style.display = "none"; passingGradeInput.style.display = "inline-block"; passingGradeInput.focus();
            });
            passingGradeInput.addEventListener("blur", () => {
                passingGradeDisplay.textContent = passingGradeInput.value; passingGradeDisplay.style.display = "inline"; passingGradeInput.style.display = "none";
            });

            quizForm.addEventListener("submit", (e) => {
                e.preventDefault();
                if (quizQuestions.length === 0) return alert("Minimal 1 soal.");

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'questions';
                hiddenInput.value = JSON.stringify(quizQuestions);
                quizForm.appendChild(hiddenInput);

                const pgInput = document.createElement('input');
                pgInput.type = 'hidden';
                pgInput.name = 'passingGrade';
                pgInput.value = passingGradeInput.value;
                quizForm.appendChild(pgInput);

                quizForm.submit();
            });
        });
    </script>
@endsection