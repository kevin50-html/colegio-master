<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Colegio Master</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            :root {
                --color-royal: #1d6dfc;
                --color-indigo: #351dfc;
                --color-sky: #10a0fc;
                --color-cyan: #00ddfc;
                --color-purple: #ad0cfc;
                --color-violet: #6b04ff;
                --color-dark: #070716;
                --color-light: #f8f8ff;
                --glass-surface: rgba(8, 10, 26, 0.65);
                --glass-strong: rgba(10, 13, 33, 0.82);
                --border-soft: rgba(255, 255, 255, 0.14);
                --shadow-strong: 0 25px 60px rgba(6, 10, 32, 0.45);
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: 'Instrument Sans', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                background: radial-gradient(130% 140% at 0% 0%, rgba(29, 109, 252, 0.65) 0%, rgba(173, 12, 252, 0.6) 45%, rgba(0, 221, 252, 0.35) 100%),
                    linear-gradient(160deg, #080a1d 0%, #090f2a 45%, #06081a 100%);
                color: var(--color-light);
                min-height: 100vh;
            }

            body.app-body {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 3rem 1.5rem;
                background-attachment: fixed;
            }

            .app-wrapper {
                width: 100%;
                max-width: 1100px;
                display: flex;
                flex-direction: column;
                gap: 2.5rem;
            }

            .app-header {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }

            .branding {
                display: flex;
                align-items: center;
                gap: 1.25rem;
            }

            .brand-mark {
                width: 3.5rem;
                height: 3.5rem;
                border-radius: 1.5rem;
                background: linear-gradient(135deg, var(--color-sky), var(--color-violet));
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                letter-spacing: 0.08em;
                font-size: 1.05rem;
                color: var(--color-light);
                box-shadow: 0 18px 30px rgba(29, 109, 252, 0.35);
            }

            .brand-copy {
                display: flex;
                flex-direction: column;
                gap: 0.35rem;
            }

            .brand-name {
                font-size: 1.35rem;
                font-weight: 600;
                letter-spacing: 0.04em;
            }

            .brand-tagline {
                font-size: 0.95rem;
                color: rgba(248, 248, 255, 0.7);
            }

            .auth-links {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            a {
                text-decoration: none;
                color: inherit;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.45rem;
                border-radius: 999px;
                padding: 0.8rem 1.6rem;
                font-weight: 600;
                letter-spacing: 0.02em;
                font-size: 0.95rem;
                border: 1px solid transparent;
                transition: all 180ms ease;
                cursor: pointer;
                background: transparent;
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--color-royal), var(--color-violet));
                color: var(--color-light);
                box-shadow: 0 15px 30px rgba(53, 29, 252, 0.35);
            }

            .btn-primary:hover {
                transform: translateY(-1px);
                box-shadow: 0 18px 40px rgba(53, 29, 252, 0.45);
            }

            .btn-outline {
                border-color: rgba(255, 255, 255, 0.25);
                color: rgba(248, 248, 255, 0.85);
                backdrop-filter: blur(2px);
            }

            .btn-outline:hover {
                background: rgba(255, 255, 255, 0.12);
                color: var(--color-light);
            }

            .btn-ghost {
                border-color: transparent;
                color: rgba(248, 248, 255, 0.86);
                padding: 0.65rem 1.4rem;
            }

            .btn-ghost:hover {
                border-color: rgba(255, 255, 255, 0.18);
                background: rgba(255, 255, 255, 0.08);
                color: var(--color-light);
            }

            .app-card {
                background: linear-gradient(155deg, var(--glass-strong), rgba(13, 16, 45, 0.55));
                border-radius: 24px;
                border: 1px solid rgba(255, 255, 255, 0.12);
                padding: 2.8rem 2.4rem;
                box-shadow: var(--shadow-strong);
                backdrop-filter: blur(18px);
                display: flex;
                flex-direction: column;
                gap: 2.5rem;
            }

            .hero h1 {
                font-size: clamp(2.1rem, 4vw, 2.9rem);
                line-height: 1.2;
                margin-bottom: 1rem;
                font-weight: 600;
            }

            .hero p {
                color: rgba(248, 248, 255, 0.75);
                max-width: 38rem;
                line-height: 1.7;
            }

            .actions {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                margin-top: 1.75rem;
            }

            .info-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 1.5rem;
            }

            .info-card {
                padding: 1.8rem;
                border-radius: 20px;
                border: 1px solid rgba(255, 255, 255, 0.08);
                background: rgba(255, 255, 255, 0.04);
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                transition: transform 200ms ease, box-shadow 200ms ease;
            }

            .info-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 35px rgba(6, 10, 32, 0.35);
            }

            .info-card h3 {
                margin: 0;
                font-size: 1.1rem;
                font-weight: 600;
            }

            .info-card p {
                margin: 0;
                color: rgba(248, 248, 255, 0.72);
                line-height: 1.6;
            }

            .info-icon {
                width: 2.4rem;
                height: 2.4rem;
                border-radius: 1rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                background: rgba(255, 255, 255, 0.08);
                color: var(--color-light);
            }

            .palette {
                display: flex;
                flex-direction: column;
                gap: 1.75rem;
            }

            .palette-header h2 {
                margin: 0;
                font-size: clamp(1.6rem, 3vw, 2.1rem);
            }

            .palette-header p {
                margin: 0.6rem 0 0;
                color: rgba(248, 248, 255, 0.7);
                max-width: 40rem;
                line-height: 1.6;
            }

            .palette-grid {
                display: grid;
                gap: 1rem;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }

            .color-card {
                border-radius: 18px;
                padding: 1.4rem 1.2rem;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.08);
                display: flex;
                flex-direction: column;
                gap: 0.85rem;
                position: relative;
                overflow: hidden;
                transition: transform 200ms ease, background 200ms ease;
            }

            .color-card::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(160deg, rgba(255, 255, 255, 0.14), transparent 65%);
                opacity: 0;
                transition: opacity 200ms ease;
            }

            .color-card:hover {
                transform: translateY(-3px);
                background: rgba(255, 255, 255, 0.08);
            }

            .color-card:hover::after {
                opacity: 1;
            }

            .swatch {
                width: 100%;
                height: 72px;
                border-radius: 14px;
                background: var(--swatch);
                box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.15);
            }

            .code {
                font-family: 'JetBrains Mono', 'Fira Code', monospace;
                font-size: 0.95rem;
                letter-spacing: 0.08em;
            }

            .color-card p {
                margin: 0;
                color: rgba(248, 248, 255, 0.7);
                font-size: 0.9rem;
                line-height: 1.5;
            }

            .cta {
                border-radius: 22px;
                padding: 2rem 2.2rem;
                border: 1px solid rgba(255, 255, 255, 0.12);
                background: linear-gradient(135deg, rgba(29, 109, 252, 0.18), rgba(173, 12, 252, 0.24));
                display: flex;
                flex-direction: column;
                gap: 1rem;
                text-align: left;
            }

            .cta h2 {
                margin: 0;
                font-size: clamp(1.6rem, 3vw, 2.2rem);
            }

            .cta p {
                margin: 0;
                color: rgba(248, 248, 255, 0.75);
                line-height: 1.6;
            }

            .btn-gradient {
                align-self: flex-start;
                background: linear-gradient(120deg, var(--color-cyan), var(--color-purple));
                color: var(--color-dark);
                box-shadow: 0 18px 35px rgba(0, 221, 252, 0.35);
                font-weight: 600;
            }

            .btn-gradient:hover {
                color: var(--color-dark);
                box-shadow: 0 24px 45px rgba(0, 221, 252, 0.45);
                transform: translateY(-1px);
            }

            .app-footer {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                align-items: center;
                color: rgba(248, 248, 255, 0.65);
                font-size: 0.9rem;
            }

            .app-footer a {
                color: rgba(248, 248, 255, 0.85);
                text-decoration: underline;
                text-decoration-thickness: 1px;
                text-decoration-color: rgba(248, 248, 255, 0.35);
            }

            .app-footer a:hover {
                color: var(--color-light);
            }

            @media (min-width: 768px) {
                .app-header {
                    flex-direction: row;
                    align-items: center;
                    justify-content: space-between;
                }

                .brand-name {
                    font-size: 1.5rem;
                }

                .app-card {
                    padding: 3.2rem 3rem;
                }

                .cta {
                    flex-direction: row;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1.5rem;
                }

                .cta p {
                    max-width: 32rem;
                }

                .btn-gradient {
                    align-self: auto;
                }
            }

            @media (max-width: 639px) {
                body.app-body {
                    padding: 2.5rem 1.1rem;
                }

                .app-card {
                    padding: 2.2rem 1.8rem;
                }

                .swatch {
                    height: 64px;
                }
            }
        </style>
    </head>
    <body class="app-body">
        <div class="app-wrapper">
            <header class="app-header">
                <div class="branding">
                    <span class="brand-mark">CM</span>
                    <div class="brand-copy">
                        <span class="brand-name">Colegio Master</span>
                        <span class="brand-tagline">Gestión académica con una paleta vibrante y moderna.</span>
                    </div>
                </div>
                @if (Route::has('login'))
                    <nav class="auth-links">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-ghost">Ir al panel</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-ghost">Iniciar sesión</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary">Crear cuenta</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <main class="app-card">
                <section class="hero">
                    <h1>Explora nuestra nueva atmósfera de color</h1>
                    <p>
                        Renovamos la portada del sistema con una identidad cromática inspirada en tonos azules y violetas que
                        transmiten confianza, innovación y energía. Cada módulo del colegio puede adoptar esta paleta para ofrecer
                        una experiencia elegante y cohesionada.
                    </p>
                    <div class="actions">
                        <a href="https://laravel.com/docs" target="_blank" rel="noopener" class="btn btn-primary">Ver documentación</a>
                        <a href="https://laracasts.com" target="_blank" rel="noopener" class="btn btn-outline">Aprender con Laracasts</a>
                    </div>
                </section>

                <section class="info-grid">
                    <article class="info-card">
                        <span class="info-icon">📊</span>
                        <h3>Panel académico elegante</h3>
                        <p>Presenta calificaciones, matrículas y reportes con bloques limpios que se adaptan al nuevo esquema.</p>
                    </article>
                    <article class="info-card">
                        <span class="info-icon">📅</span>
                        <h3>Organización intuitiva</h3>
                        <p>Horarios, actividades y periodos se benefician del contraste entre azules profundos y acentos luminosos.</p>
                    </article>
                    <article class="info-card">
                        <span class="info-icon">🔐</span>
                        <h3>Experiencia confiable</h3>
                        <p>Botones con gradientes suaves guían a docentes y estudiantes a las acciones más importantes.</p>
                    </article>
                </section>

                <section class="palette">
                    <div class="palette-header">
                        <h2>Nuestra paleta protagonista</h2>
                        <p>
                            Combina matices fríos y vibrantes para lograr una apariencia tecnológica y sofisticada. Puedes mezclar
                            los tonos para encabezados, botones y fondos secundarios.
                        </p>
                    </div>
                    <div class="palette-grid">
                        <article class="color-card" style="--swatch: #1d6dfc">
                            <span class="swatch"></span>
                            <span class="code">#1D6DFC</span>
                            <p>Azul principal para títulos y barras de navegación.</p>
                        </article>
                        <article class="color-card" style="--swatch: #351dfc">
                            <span class="swatch"></span>
                            <span class="code">#351DFC</span>
                            <p>Indigo profundo ideal para paneles laterales.</p>
                        </article>
                        <article class="color-card" style="--swatch: #10a0fc">
                            <span class="swatch"></span>
                            <span class="code">#10A0FC</span>
                            <p>Azul cielo perfecto para estados informativos.</p>
                        </article>
                        <article class="color-card" style="--swatch: #00ddfc">
                            <span class="swatch"></span>
                            <span class="code">#00DDFC</span>
                            <p>Toque cian que resalta acciones secundarias.</p>
                        </article>
                        <article class="color-card" style="--swatch: #ad0cfc">
                            <span class="swatch"></span>
                            <span class="code">#AD0CFC</span>
                            <p>Morado vibrante para etiquetas especiales.</p>
                        </article>
                        <article class="color-card" style="--swatch: #6b04ff">
                            <span class="swatch"></span>
                            <span class="code">#6B04FF</span>
                            <p>Violeta intenso que aporta profundidad al fondo.</p>
                        </article>
                    </div>
                </section>

                <section class="cta">
                    <div>
                        <h2>¿Listo para aplicar el nuevo estilo?</h2>
                        <p>
                            Integra esta identidad cromática en tus módulos favoritos y sorprende a la comunidad educativa con una
                            experiencia digital elegante y coherente.
                        </p>
                    </div>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-gradient">Crear una cuenta</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-gradient">Ir al inicio de sesión</a>
                    @endif
                </section>
            </main>

            <footer class="app-footer">
                <span>Diseño potenciado por la paleta Colegio Master.</span>
                <span>Personaliza los módulos en <a href="https://laravel.com" target="_blank" rel="noopener">Laravel</a> y mantén tu identidad visual.</span>
            </footer>
        </div>
    </body>
</html>
