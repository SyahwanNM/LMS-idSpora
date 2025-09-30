@include ('partials.navbar-before-login')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-split-horizontal">
    <!-- Breadcrumb -->
      <nav aria-label="breadcrumb">
        <div class="container">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Course</a></li>
            <li class="breadcrumb-item active">Learn Artificial Intelligence with Python</li>
          </ol>
        </div>
      </nav>
      
</body>
</html>