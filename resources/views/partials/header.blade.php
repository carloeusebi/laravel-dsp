<header id="top-header">
    <div class="container d-flex justify-space-between align-center">
        <div id="hdr-logo">
            <a href="https://www.dellasantapsicologo.it" target="_blank">
                <img class="fluid-img" src="/images/Logo.webp" alt="logo del sito">
            </a>
        </div>
        <div id="hamburger-menu" class="m-20">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <nav id="top-navbar">
            <ul>
                <li><a href="{{ Route('home') }}" @if (Route::is('home')) class="active" @endif>Home</a></li>
                <li><a href="{{ Route('chi-sono') }}" @if (Route::is('chi-sono')) class="active" @endif>Chi
                        Sono</a></li>
                <li><a href="{{ Route('cosa-aspettarsi') }}" @if (Route::is('cosa-aspettarsi')) class="active" @endif>
                        Cosa aspettarsi dalla Terapia</a></li>
                <li><a href="{{ Route('di-cosa-mi-occupo') }}" @if (Route::is('di-cosa-mi-occupo')) class="active" @endif>
                        Di cosa mi Occupo</a></li>
                <li><a href="{{ Route('contatti') }}"
                        @if (Route::is('contatti')) class="active" @endif>Contatti</a></li>
            </ul>
        </nav>
    </div>
</header>
