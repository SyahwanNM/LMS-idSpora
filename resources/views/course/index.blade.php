@include ('partials.navbar-before-login')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<style>
    .slide-wrapper {
        position: relative;
        max-width: 100svh;
        max-height: 100svh;
        margin: 0 auto;
    }
    .slider {
        display: flex;
        aspect-ratio: 16/9;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scroll-behavior: smooth;
        box-shadow: 0 1.5rem 3rem -0.75rem hsla(0, 0%, 0%, 0.25);
    }

    .slider img {
        flex: 1 0 100%;
        scroll-snap-align: start;
        object-fit: cover;
    }

    .slider-nav {
        display: flex;
        column-gap: 1rem;
        position: absolute;
        bottom: 1.25rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1;   
    }
    .slider-nav a {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        background-color: var(--white);
        opacity: 0.75;
        transition: opacity ease 258ms;
    }

    .slider-nav a:hover {
        opacity: 1;
    }
</style>

<body>
    <section class="carousel-container">
        <div class="slider-wrapper">
            <div class="slider">
                <img id="slide-1" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRsvzTXamwsUAY8fHwBx3rXALqR0CYmNkaNzRI4UKdL5f4kLQ6bHO_4dB02jgiuJL-q4gM&usqp=CAU" alt="">
                <img id="slide-2" src="https://image.web.id/images/662e7197337907f2e6a404c5_figma-plugins.jpg" alt="">
                <img id="slide-3" src="https://bairesdev.mo.cloudinary.net/blog/2023/08/What-Is-JavaScript-Used-For.jpg" alt="">
            </div>
        </div>

        <div class="slider-nav">
            <a href="#slide-1"></a>
            <a href="#slide-2"></a>
            <a href="#slide-3"></a>
        </div>
    </section>
</body>

</html>