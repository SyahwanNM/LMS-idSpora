@include('partials.navbar-before-login')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<style>
    .biodata {
        max-width: 450px;
        margin: 20px auto auto 20px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #f9f9f9;
    }

    .biodata img {
        display: block;
        margin: 10px auto 20px;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
    }

    .biodata h4{
        text-align: center;
        margin-bottom: 1px;
        font-size: 18px;
        color: #000;
    }

    .biodata h6{
        text-align: center;
        margin-bottom: 15px;
        font-size: 15px;
        color: #333;
    }
</style>
<body>
    <section>
        <div class="biodata">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQtMBIDmu_hrilaIEg7wH9_nbdS4JnjhI8Vpw&s" alt="">
            <h4>Zayn Malik</h4>

            <button>
                
            </button>
            <h6>zaynmalik@gmail.com</h6>
            <h2>Biodata</h2>
            <p>Name: </p>
            <p>Email: </p>
            <p>Role: </p>
        </div>
    </section>
</body>
</html>
