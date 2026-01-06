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
    @include("partials.navbar-admin-course-bootstrap")
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="box_luar_add_course">
                    <div class="box_link d-flex align-items-center gap-2 text-muted small mb-2">
                        <a href="">Course Builder</a>
                        <span>/</span>
                        <a href="">Add Course</a>
                    </div>
                    <div class="box_judul mb-3">
                        <h1 class="h3 mb-1">Tambah Course</h1>
                        <p class="text-muted mb-0">Atur detail course sebelum dipublikasikan</p>
                    </div>

                    <div class="box_form">
                        <h4 class="h5 mb-3">Formulir Pengaturan Course</h4>

                        <div class="mb-3">
                            <label class="form-label text-dark">Judul Course</label>
                            <input type="text" class="form-control" placeholder="Masukkan Judul Course">
                        </div>

                        <div class="row g-3 box_select_level_status">
                            <div class="col-md-6">
                                <label class="form-label text-dark">Status</label>
                                <select class="form-select">
                                    <option selected disabled>Choose your Status</option>
                                    <option value="active">Active</option>
                                    <option value="archive">Archive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark">Level Course</label>
                                <select class="form-select">
                                    <option selected disabled>Choose your level</option>
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advance">Advance</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark">Harga</label>
                            <input type="text" class="form-control" placeholder="Masukkan Harga Course">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark">Deskripsi Course</label>
                            <textarea class="form-control" placeholder="Deskripsikan course secara lengkap"></textarea>
                        </div>

                        <div class="mb-1">
                            <label class="form-label text-dark">Thumbnail Course</label>
                            <input type="file" class="form-control">
                        </div>
                    </div>

                    <div class="box_button d-flex justify-content-end gap-2 mt-3">
                        <button class="cancel btn btn-outline-secondary">Cancel</button>
                        <button class="save_add btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>