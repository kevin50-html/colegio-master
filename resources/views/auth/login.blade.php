@extends('layouts.app')

@section('title', 'Iniciar Sesión - Colegio')

@push('styles')
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        :root {
            --color-royal: #1d6dfc;
            --color-indigo: #351dfc;
            --color-sky: #10a0fc;
            --color-cyan: #00ddfc;
            --color-purple: #ad0cfc;
            --color-violet: #6b04ff;
            --color-dark: #070716;
            --color-light: #f5f6ff;
        }

        body {
            min-height: 100vh;
            background: radial-gradient(120% 135% at 5% 0%, rgba(29, 109, 252, 0.55) 0%, rgba(173, 12, 252, 0.45) 45%, rgba(0, 221, 252, 0.28) 100%),
                linear-gradient(160deg, #080a1d 0%, #0a0f26 45%, #060818 100%);
            background-attachment: fixed;
            font-family: 'Instrument Sans', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--color-light);
        }

        a {
            color: inherit;
        }

        .login-container {
            min-height: 100vh;
            padding: 4rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .login-surface {
            width: min(100%, 960px);
            background: rgba(7, 10, 30, 0.75);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            box-shadow: 0 35px 80px rgba(7, 12, 38, 0.55);
            overflow: hidden;
            backdrop-filter: blur(18px);
            position: relative;
        }

        .login-surface::before {
            content: '';
            position: absolute;
            inset: -40% 60% 60% -40%;
            background: radial-gradient(circle at top left, rgba(29, 109, 252, 0.55) 0%, transparent 55%);
            opacity: 0.75;
        }

        .login-surface::after {
            content: '';
            position: absolute;
            inset: 65% -35% -35% 55%;
            background: radial-gradient(circle at bottom right, rgba(173, 12, 252, 0.45) 0%, transparent 60%);
            opacity: 0.8;
        }

        .login-visual,
        .login-form-wrapper {
            position: relative;
            z-index: 1;
        }

        .login-visual {
            padding: clamp(2.5rem, 5vw, 3.5rem);
            background: linear-gradient(145deg, rgba(13, 16, 45, 0.75) 10%, rgba(7, 10, 28, 0.9) 80%);
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
            justify-content: center;
            color: rgba(245, 246, 255, 0.92);
        }

        .brand-badge {
            width: 56px;
            height: 56px;
            border-radius: 22px;
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

        .login-visual h2 {
            font-size: clamp(1.8rem, 3vw, 2.35rem);
            font-weight: 600;
            line-height: 1.25;
            margin-bottom: 0.75rem;
        }

        .login-visual p {
            color: rgba(245, 246, 255, 0.75);
            line-height: 1.65;
            margin-bottom: 0;
        }

        .login-highlights {
            display: grid;
            gap: 1.1rem;
        }

        .login-highlight {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
        }

        .login-highlight i {
            width: 2.35rem;
            height: 2.35rem;
            border-radius: 1rem;
            background: rgba(16, 160, 252, 0.14);
            color: var(--color-cyan);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .login-highlight span {
            font-weight: 500;
            color: rgba(245, 246, 255, 0.88);
        }

        .login-form-wrapper {
            padding: clamp(2.5rem, 5vw, 3.75rem);
            background: rgba(9, 12, 34, 0.78);
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2rem;
        }

        .form-heading {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .form-heading h3 {
            font-size: 1.6rem;
            font-weight: 600;
        }

        .form-heading span {
            color: rgba(245, 246, 255, 0.7);
            font-size: 0.95rem;
        }

        .form-control {
            background: rgba(13, 16, 45, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: var(--color-light);
            border-radius: 14px;
            padding: 0.9rem 1.1rem;
        }

        .form-control:focus {
            border-color: rgba(16, 160, 252, 0.55);
            box-shadow: 0 0 0 0.25rem rgba(16, 160, 252, 0.18);
            background: rgba(13, 16, 45, 0.92);
            color: var(--color-light);
        }

        .form-label {
            font-weight: 500;
            color: rgba(245, 246, 255, 0.78);
            margin-bottom: 0.45rem;
        }

        .form-check-input {
            background-color: rgba(13, 16, 45, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .form-check-label {
            color: rgba(245, 246, 255, 0.7);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--color-royal), var(--color-violet));
            border: 0;
            border-radius: 16px;
            padding: 0.85rem 1.1rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            box-shadow: 0 18px 40px rgba(53, 29, 252, 0.32);
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 22px 46px rgba(53, 29, 252, 0.42);
        }

        .text-muted {
            color: rgba(245, 246, 255, 0.6) !important;
        }

        .alert {
            border-radius: 14px;
            background: rgba(229, 82, 105, 0.16);
            border: 1px solid rgba(229, 82, 105, 0.4);
            color: #ffccd5;
        }

        .alert ul {
            padding-left: 1.1rem;
            margin-bottom: 0;
        }

        .auth-footer a {
            color: rgba(0, 221, 252, 0.85);
        }

        @media (max-width: 991.98px) {
            .login-surface {
                border-radius: 24px;
            }

            .login-visual {
                text-align: center;
            }

            .login-highlight {
                justify-content: center;
                text-align: left;
            }
        }

        @media (max-width: 575.98px) {
            .login-container {
                padding: 3rem 1.15rem;
            }

            .login-form-wrapper,
            .login-visual {
                padding: 2.35rem 1.7rem;
            }
        }
    </style>
@endpush

@section('content')
<div class="login-container">
    <div class="login-surface">
        <div class="row g-0 align-items-stretch">
            <div class="col-lg-6 login-visual">
                <div class="brand-badge">CM</div>
                <div>
                    <h2>Plataforma Colegio Master</h2>
                    <p>Centraliza la gestión académica, administrativa y de seguimiento con una experiencia fluida y segura.</p>
                </div>
                <div class="login-highlights">
                    <div class="login-highlight">
                        <i class="fas fa-chart-line"></i>
                        <span>Indicadores en tiempo real para directivos y coordinadores.</span>
                    </div>
                    <div class="login-highlight">
                        <i class="fas fa-user-shield"></i>
                        <span>Acceso protegido para garantizar la integridad de la información.</span>
                    </div>
                    <div class="login-highlight">
                        <i class="fas fa-people-arrows"></i>
                        <span>Conectividad entre estudiantes, docentes y familias.</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 login-form-wrapper">
                <div class="form-heading">
                    <h3>Bienvenido de nuevo</h3>
                    <span>Ingresa tus credenciales para continuar</span>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Correo electrónico
                        </label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               autofocus
                               placeholder="tucorreo@colegio.edu">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Contraseña
                        </label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="••••••••">
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Recordarme
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-decoration-none" style="color: rgba(0, 221, 252, 0.85);">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </button>
                    </div>
                </form>

                <div class="text-center auth-footer mt-4">
                    @if (Route::has('register'))
                        <small class="text-muted">
                            ¿No tienes una cuenta?
                            <a href="{{ route('register') }}" class="text-decoration-none">Regístrate aquí</a>
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
