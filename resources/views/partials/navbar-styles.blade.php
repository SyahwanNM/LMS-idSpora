<style>
.navbar-gradient {background: linear-gradient(90deg,#0d6efd,#6610f2);} 
.navbar-gradient .nav-link {color:#fff; font-weight:500; position:relative; padding-bottom:6px; transition:color .25s ease;} 
/* Underline animation */
.navbar-gradient .nav-link::after {content:''; position:absolute; left:50%; bottom:2px; width:0; height:2px; background:linear-gradient(90deg,#ffe259,#ffa751); border-radius:2px; opacity:.9; transition:width .35s cubic-bezier(.65,.05,.36,1), left .35s cubic-bezier(.65,.05,.36,1);} 
.navbar-gradient .nav-link:hover::after, .navbar-gradient .nav-link:focus::after {width:70%; left:15%;}
.navbar-gradient .nav-link.active {font-weight:600; color:#ffd966!important;} 
.navbar-gradient .nav-link.active::after {width:70%; left:15%;}
.navbar-gradient .nav-link:hover, .navbar-gradient .nav-link:focus {color:#ffe8b3!important;}
.navbar-gradient .btn-primary {background:#ffd966; border-color:#ffd966; color:#212529; font-weight:600;} 
.navbar-gradient .btn-primary:hover {background:#ffcd39; border-color:#ffcd39;} 
.navbar-gradient .btn-secondary {background:transparent; border:1px solid #fff; color:#fff; font-weight:600;} 
.navbar-gradient .btn-secondary:hover {background:#fff; color:#212529;} 
@media (hover:none){ .navbar-gradient .nav-link::after {display:none;} }
</style>
