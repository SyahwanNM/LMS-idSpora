<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Course</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include("partials.navbar-admin-course")
    <div class="box_luar_add_course">
        <div class="box_link">
            <a href="">Course Builder</a>
            <p>/</p>
            <a href="">Add Course</a>
        </div>
        <div class="box_judul">
            <h1>Tambah Course</h1>
            <p>Atur detail course sebelum dipublkasi</p>
        </div>
        <div class="box_form">
            <h4>Formulir Pengaturan Course</h4>
            <p>Judul Course</p>
            <input type="text" placeholder="Masukkan Judul Course">
            <div class="box_select_level_status">
                <div>
                    <p>Level Course</p>
                    <div class="select_box dropdown">
                        <button class="select_level btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Choose your level
                        </button>
                        <ul class="choose_level dropdown-menu">
                            <li><a class="dropdown-item" href="#">Beginner</a></li>
                            <li><a class="dropdown-item" href="#">Intermediate</a></li>
                            <li><a class="dropdown-item" href="#">Advance</a></li>
                        </ul>
                    </div>
                </div>
                <div class="box_select_status">
                    <p>Status</p>
                    <div class="select_box dropdown">
                        <button class="select_level btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Choose your Status
                        </button>
                        <ul class="choose_level dropdown-menu">
                            <li><a class="dropdown-item" href="#">Active</a></li>
                            <li><a class="dropdown-item" href="#">Archive</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <p>Harga</p>
            <input type="text" placeholder="Masukkan Harga Course">
            <p>Deskrpsi Course</p>
            <textarea name="" placeholder="Deskripsikan course secara lengkap" id=""></textarea>
            <p>Thumbnail Course</p>
            <input type="file">
            <div class="course_module_header">
                <p class="judul_header_pengisian">Course Modules</p>
                <p class="deskripsi_header_pengisian">Add learning materials to structure your course content</p>
            </div>
        </div>
        <div class="box_luar_pdf_upload">
            <p class="judul_file_box">PDF Document</p>

            <div class="box_dalam_file_upload">
                <div class="box_isi_pdf_upload">

                    <div class="file_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                            <path d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2" />
                        </svg>
                    </div>

                    <div class="isi_file_content">
                        <div class="isi_file_header">
                            <h5>Pemrograman</h5>
                            <span class="badge">#1</span>
                        </div>
                        <p class="isi_file_desc">Pemrograman berbasis website</p>
                        <p class="isi_file_name">Pemrograman berbasis website.pdf</p>
                    </div>

                </div>
            </div>
        </div>
        <div class="box_luar_pdf_upload">
            <p class="judul_file_box_video">Video Lesson</p>

            <div class="box_dalam_file_upload">
                <div class="box_isi_pdf_upload">

                    <div class="file_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera-video-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M0 5a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 4.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 13H2a2 2 0 0 1-2-2z" />
                        </svg>
                    </div>

                    <div class="isi_file_content">
                        <div class="isi_file_header">
                            <h5>Pemrograman</h5>
                            <span class="badge">#1</span>
                        </div>
                        <p class="isi_file_desc">Pemrograman berbasis website</p>
                        <p class="isi_file_name">Pemrograman berbasis website.mp3</p>
                    </div>

                </div>
            </div>
        </div>
        <div class="box_luar_pdf_upload">
            <p class="judul_file_box_kuis">Quizzes</p>

            <div class="box_dalam_file_upload">
                <div class="box_isi_pdf_upload">

                    <div class="file_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286m1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94" />
                        </svg>
                    </div>

                    <div class="isi_file_content">
                        <div class="isi_file_header">
                            <h5>Pemrograman</h5>
                            <span class="badge">#1</span>
                        </div>
                        <p class="isi_file_desc">Pemrograman berbasis website</p>
                        <p class="isi_file_name">10 Question</p>
                    </div>

                </div>
            </div>
        </div>


    </div>
    <div class="box_untuk_unggah">
        <button class="add_pdf_unggah" data-bs-toggle="modal" data-bs-target="#addPdfModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
            </svg>
            <p>Add PDF Module</p>
        </button>
        <button class="add_video_unggah" data-bs-toggle="modal" data-bs-target="#addVideoModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
            </svg>
            <p>Add Video</p>
        </button>
        <button class="add_kuis_unggah" data-bs-toggle="modal" data-bs-target="#addKuisModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
            </svg>
            <p>Add Quiz</p>
        </button>

    </div>
    <div class="box_button">
        <button class="cancel">Cancel</button>
        <button class="save_add">Save</button>
    </div>
    </div>
    <div class="modal fade" id="addPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <div class="modal-header custom-modal-header">
                    <div class="modal-header-text">
                        <h5 class="modal-title">Add PDF Module</h5>
                        <p class="modal-desc">
                            Add learning materials to structure your course content
                        </p>
                    </div>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="judul_course">
                        <h5>Judul Course</h5>
                        <p>Masukkan Judul Course</p>
                    </div>
                    <div class="deskripsi_modul">
                        <h5>Modul Deskripsi</h5>
                        <textarea class="isi_deskripsi" name="" id="" placeholder="What will students learn from this module"></textarea>
                    </div>
                    <div class="module_order">
                        <h5>Module Order</h5>
                        <p>Default: 1</p>
                    </div>
                    <div class="">
                        <h5>Upload PDF</h5>
                        <div class="unggah_file">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#6A4CFF" class="bi bi-file-earmark" viewBox="0 0 16 16">
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z" />
                            </svg>
                            <h5>Drag and drop your PDF file here</h5>
                            <p>or click to browser</p>
                            <input type="file">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Add Module</button>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="addVideoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <div class="modal-header custom-modal-header">
                    <div class="modal-header-text">
                        <h5 class="modal-title">Add Video Module</h5>
                        <p class="modal-desc">
                            Add learning materials to structure your course content
                        </p>
                    </div>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="judul_course">
                        <h5>Judul Course</h5>
                        <p>Masukkan Judul Course</p>
                    </div>
                    <div class="deskripsi_modul">
                        <h5>Modul Deskripsi</h5>
                        <textarea class="isi_deskripsi" name="" id="" placeholder="What will students learn from this module"></textarea>
                    </div>
                    <div class="module_order">
                        <h5>Module Order</h5>
                        <p>Default: 1</p>
                    </div>
                    <div class="">
                        <h5>Upload Video</h5>
                        <div class="unggah_file">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#6A4CFF" class="bi bi-file-earmark" viewBox="0 0 16 16">
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z" />
                            </svg>
                            <h5>Drag and drop your Video file here</h5>
                            <p>or click to browser</p>
                            <input type="file">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Add Video</button>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="addKuisModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <div class="modal-header custom-modal-header">
                    <div class="modal-header-text">
                        <h5 class="modal-title">Create Quiz</h5>
                        <p class="modal-desc">
                            Set up quiz details
                        </p>
                    </div>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="judul_course">
                        <h5>Quiz Tittle</h5>
                        <p>Masukkan Judul Quiz</p>
                    </div>
                    <div class="deskripsi_modul">
                        <h5>Quiz Description</h5>
                        <textarea class="isi_deskripsi" name="" id="" placeholder="What will students learn from this module"></textarea>
                    </div>
                    <div class="tambah_pertanyaan">
                        <div class="nambah_soal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                            </svg>
                            <p>Add Question</p>
                        </div>
                    </div>
                    <div class="box_luar_tambah_kuis">
                        <div class="box_dalam_pertanyaan_kuis">
                            <h5>Quiz #1</h5>
                            <p>Fill all fields to add</p>
                        </div>
                        <h5>Question Text</h5>
                        <div class="isi_pertanyaan_kuis">
                            <p>Enter Your Question... </p>
                        </div>
                        <div>
                            <h5>Answer Option</h5>
                        </div>
                        <div class="box_luar_answer_option">
                            <div class="answer_option">
                                <div class="box_option">
                                    <p>Option 1</p>
                                </div>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="radioDisabled" id="radioCheckedDisabled" checked disabled>
                                        <label class="text_label form-check-label" for="radioCheckedDisabled">
                                            Correct
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box_luar_answer_option">
                            <div class="answer_option">
                                <div class="box_option">
                                    <p>Option 2</p>
                                </div>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="radioDisabled" id="radioCheckedDisabled" checked disabled>
                                        <label class="text_label form-check-label" for="radioCheckedDisabled">
                                            Correct
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box_luar_answer_option">
                            <div class="answer_option">
                                <div class="box_option">
                                    <p>Option 3</p>
                                </div>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="radioDisabled" id="radioCheckedDisabled" checked disabled>
                                        <label class="text_label form-check-label" for="radioCheckedDisabled">
                                            Correct
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box_luar_answer_option">
                            <div class="answer_option">
                                <div class="box_option">
                                    <p>Option 4</p>
                                </div>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="radioDisabled" id="radioCheckedDisabled" checked disabled>
                                        <label class="text_label form-check-label" for="radioCheckedDisabled">
                                            Correct
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Next Add Question</button>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>