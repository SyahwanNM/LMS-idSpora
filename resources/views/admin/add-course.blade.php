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
        </div>

    </div>
    <div class="box_button">
        <button class="cancel">Cancel</button>
        <button class="save_add">Save</button>
    </div>
    </div>
</body>
</head>

</html>