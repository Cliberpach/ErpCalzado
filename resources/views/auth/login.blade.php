<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CalzadoPro | Iniciar Sesión</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,600;0,700;1,600&display=swap"
        rel="stylesheet">

    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --blue-deep: #0d2a6e;
            --blue-main: #1a56db;
            --blue-mid: #2563eb;
            --blue-bright: #3b82f6;
            --blue-light: #93c5fd;
            --blue-pale: #dbeafe;
            --blue-xpale: #eff6ff;
            --cyan: #0ea5e9;
            --white: #ffffff;
            --gray-50: #f8faff;
            --gray-100: #eef2fb;
            --gray-400: #94a3b8;
            --gray-600: #475569;
            --gray-800: #1e293b;
        }

        html,
        body {
            height: 100%;
            font-family: 'Outfit', sans-serif;
            background: var(--gray-50);
        }

        /* ── LOADER ── */
        .loader-spinner {
            position: fixed;
            inset: 0;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .centrado {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 14px;
        }

        .loader-ring {
            width: 44px;
            height: 44px;
            border: 3px solid var(--blue-pale);
            border-top-color: var(--blue-main);
            border-radius: 50%;
            animation: spin 0.85s linear infinite;
        }

        .loader-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--blue-main);
            letter-spacing: 0.1em;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── MAIN LAYOUT ── */
        #content-system {
            display: flex;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        /* ══ LEFT ══ */
        .left-panel {
            flex: 1;
            position: relative;
            overflow: hidden;
            min-width: 0;
        }

        .bg-image {
            position: absolute;
            inset: 0;
            background-image: url('img/login_fondo.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .bg-fallback {
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, #0b1f5e 0%, #1a4fc8 50%, #0ea5e9 100%);
        }

        .bg-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(140deg,
                    rgba(10, 30, 90, 0.82) 0%,
                    rgba(26, 86, 219, 0.65) 55%,
                    rgba(14, 165, 233, 0.48) 100%);
        }

        /* decorative rings */
        .deco-ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.07);
            pointer-events: none;
        }

        .deco-ring-1 {
            width: 360px;
            height: 360px;
            top: -80px;
            left: -80px;
        }

        .deco-ring-2 {
            width: 220px;
            height: 220px;
            top: 40px;
            left: 40px;
        }

        .deco-ring-3 {
            width: 560px;
            height: 560px;
            bottom: -140px;
            right: -140px;
        }

        .deco-ring-4 {
            width: 320px;
            height: 320px;
            bottom: 0;
            right: 0;
        }

        /* left content */
        .left-content {
            position: relative;
            z-index: 5;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 50px 56px;
        }

        .left-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(10px);
            border-radius: 100px;
            padding: 6px 16px;
            margin-bottom: 22px;
            width: fit-content;
        }

        .left-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #7dd3fc;
            box-shadow: 0 0 8px #7dd3fc;
            animation: blink 2s ease-in-out infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .35
            }
        }

        .left-badge span {
            font-size: 11px;
            font-weight: 500;
            color: rgba(255, 255, 255, .88);
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .left-heading {
            font-family: 'Lora', serif;
            font-size: clamp(26px, 3vw, 46px);
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 22px;
        }

        .left-heading em {
            font-style: normal;
            color: #7dd3fc;
        }

        /* ── STATS GRID (3 columnas) ── */
        .left-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            max-width: 740px;
        }

        .stat-card {
            background: rgba(0, 20, 80, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(16px);
            border-radius: 16px;
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            transition: background 0.25s, transform 0.2s;
            cursor: default;
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-3px);
        }

        .stat-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-icon {
            width: 34px;
            height: 34px;
            background: rgba(125, 211, 252, 0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .stat-badge {
            font-size: 9px;
            font-weight: 600;
            background: rgba(125, 211, 252, 0.2);
            color: #7dd3fc;
            border-radius: 100px;
            padding: 2px 8px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .stat-num {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        .stat-num sup {
            font-size: 12px;
            color: #7dd3fc;
            font-weight: 600;
        }

        .stat-label {
            font-size: 10.5px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.95);
            letter-spacing: 0.04em;
            text-transform: uppercase;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
        }

        .stat-sub {
            font-size: 10.5px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.75);
            margin-top: 1px;
            line-height: 1.4;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
        }

        /* ══ RIGHT – WHITE LOGIN PANEL ══ */
        .right-panel {
            width: 480px;
            min-width: 340px;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 36px;
            position: relative;
            overflow: hidden;
            box-shadow: -8px 0 60px rgba(13, 42, 110, 0.12);
        }

        /* mesh gradient background */
        .right-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 40% at 105% -5%, rgba(59, 130, 246, 0.07) 0%, transparent 60%),
                radial-gradient(ellipse 50% 50% at -10% 108%, rgba(14, 165, 233, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse 40% 30% at 50% 50%, rgba(219, 234, 254, 0.3) 0%, transparent 70%);
            pointer-events: none;
        }

        /* top gradient bar */
        .right-panel::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--blue-deep), var(--blue-bright), var(--cyan));
        }

        /* subtle dot pattern */
        .right-dots {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background-image: radial-gradient(circle, rgba(59, 130, 246, 0.06) 1px, transparent 1px);
            background-size: 24px 24px;
            mask-image: radial-gradient(ellipse 70% 70% at 50% 50%, black 0%, transparent 100%);
        }

        .login-card {
            width: 100%;
            max-width: 380px;
            position: relative;
            z-index: 2;
            animation: cardIn 0.55s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* LOGO */
        .logo-wrap {
            text-align: center;
            margin-bottom: 22px;
        }

        .logo-wrap img {
            width: 60%;
            object-fit: contain;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(26, 86, 219, 0.14);
            transition: box-shadow 0.3s, transform 0.3s;
        }

        .logo-wrap img:hover {
            box-shadow: 0 8px 32px rgba(26, 86, 219, 0.24);
            transform: translateY(-2px);
        }

        /* header */
        .card-header-text {
            text-align: center;
            margin-bottom: 4px;
        }

        .card-header-text h3 {
            font-size: 19px;
            font-weight: 700;
            color: var(--gray-800);
            letter-spacing: -0.02em;
        }

        .card-desc {
            text-align: center;
            font-size: 13px;
            color: var(--gray-400);
            margin-bottom: 24px;
        }

        /* feature pills row */
        .feature-pills {
            display: flex;
            gap: 6px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--blue-xpale);
            border: 1px solid var(--blue-pale);
            border-radius: 100px;
            padding: 4px 10px;
            font-size: 10.5px;
            font-weight: 600;
            color: var(--blue-main);
            letter-spacing: 0.03em;
        }

        .pill-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--blue-bright);
        }

        /* divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: var(--gray-100);
        }

        .divider-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--blue-pale);
        }

        /* FORM */
        .form-login {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .form-group.input_login {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .field-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--gray-600);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--blue-light);
            pointer-events: none;
            transition: color 0.2s;
            display: flex;
            align-items: center;
        }

        .input-wrap:focus-within .input-icon {
            color: var(--blue-main);
        }

        .form-control {
            width: 100%;
            background: var(--gray-50);
            border: 1.5px solid #e2eaf8;
            border-radius: 12px;
            padding: 13px 14px 13px 40px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            color: var(--gray-800);
            outline: none;
            transition: border-color .22s, box-shadow .22s, background .22s;
        }

        .form-control::placeholder {
            color: #b8c8e0;
        }

        .form-control:hover {
            border-color: var(--blue-light);
        }

        .form-control:focus {
            background: var(--white);
            border-color: var(--blue-main);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.10);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.10);
        }

        .invalid-feedback {
            font-size: 12px;
            color: #ef4444;
            margin-top: 2px;
        }

        .eye-btn {
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--blue-light);
            font-size: 15px;
            padding: 4px;
            transition: color 0.2s;
            display: flex;
            align-items: center;
        }

        .eye-btn:hover {
            color: var(--blue-main);
        }

        /* submit button */
        .btn-iniciar.input_login {
            margin-top: 4px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--blue-deep) 0%, var(--blue-main) 55%, var(--blue-bright) 100%);
            border: none;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--white);
            cursor: pointer;
            letter-spacing: 0.03em;
            position: relative;
            overflow: hidden;
            transition: transform 0.18s, box-shadow 0.18s;
            box-shadow: 0 4px 20px rgba(26, 86, 219, 0.38), 0 1px 4px rgba(26, 86, 219, 0.18);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(26, 86, 219, 0.48);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 55%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.22), transparent);
            animation: shimmer 3.5s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% {
                left: -100%
            }

            65%,
            100% {
                left: 160%
            }
        }

        /* forgot */
        .forgot-link {
            display: block;
            text-align: center;
            margin-top: 14px;
            font-size: 12.5px;
            color: var(--gray-400);
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: var(--blue-main);
            text-decoration: underline;
        }

        /* footer */
        .card-footer-text {
            text-align: center;
            margin-top: 26px;
            font-size: 11px;
            color: #cbd5e1;
            letter-spacing: 0.04em;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 860px) {
            .left-panel {
                display: none;
            }

            .right-panel {
                width: 100%;
                min-width: unset;
            }

            #content-system {
                overflow: auto;
                height: auto;
                min-height: 100vh;
            }
        }
    </style>
</head>

<body>

    <!-- LOADER -->
    <div class="loader-spinner">
        <div class="centrado">
            <div class="loader-ring"></div>
            <div class="loader-text">CALZADOPRO</div>
        </div>
    </div>

    <!-- MAIN -->
    <div id="content-system" style="display:none;">

        <!-- ══ LEFT ══ -->
        <div class="left-panel">
            <div class="bg-fallback"></div>
            <div class="bg-image"></div>
            <div class="bg-overlay"></div>
            <div class="deco-ring deco-ring-1"></div>
            <div class="deco-ring deco-ring-2"></div>
            <div class="deco-ring deco-ring-3"></div>
            <div class="deco-ring deco-ring-4"></div>

            <div class="left-content">
                <div class="left-badge">
                    <div class="left-badge-dot"></div>
                    <span>Sistema exclusivo para tienda de calzado</span>
                </div>

                <h2 class="left-heading">CalzadoPro</h2>

                <!-- STATS GRID 3 TARJETAS -->
                <div class="left-stats">

                    <!-- Tarjeta 1: Stock -->
                    <div class="stat-card">
                        <div class="stat-card-top">
                            <div class="stat-icon">📦</div>
                            <div class="stat-badge">Live</div>
                        </div>
                        <div class="stat-num">100<sup>%</sup></div>
                        <div class="stat-label">Stock en tiempo real</div>
                        <div class="stat-sub">Inventario sincronizado al instante</div>
                    </div>

                    <!-- Tarjeta 2: Ventas & CRM -->
                    <div class="stat-card">
                        <div class="stat-card-top">
                            <div class="stat-icon">🛒</div>
                            <div class="stat-badge">Online</div>
                        </div>
                        <div class="stat-num">24<sup>h</sup></div>
                        <div class="stat-label">Ventas & CRM</div>
                        <div class="stat-sub">Tienda online, clientes y pedidos</div>
                    </div>

                    <!-- Tarjeta 3: Promociones & Reportes -->
                    <div class="stat-card">
                        <div class="stat-card-top">
                            <div class="stat-icon">🏷️</div>
                            <div class="stat-badge">Pro</div>
                        </div>
                        <div class="stat-num">360<sup>°</sup></div>
                        <div class="stat-label">Promos & Reportes</div>
                        <div class="stat-sub">Cotiza veloz, descuentos y análisis</div>
                    </div>

                    <!-- Tarjeta 4: Reservas -->
                    <div class="stat-card">
                        <div class="stat-card-top">
                            <div class="stat-icon">📋</div>
                            <div class="stat-badge">Reservas</div>
                        </div>
                        <div class="stat-num">∞<sup> </sup></div>
                        <div class="stat-label">Reserva y Atiende</div>
                        <div class="stat-sub">Vende sin stock y cumple pedidos poco a poco</div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ══ RIGHT – LOGIN FORM ══ -->
        <div class="right-panel">
            <div class="right-dots"></div>
            <div class="login-card">

                <div class="logo-wrap">
                    <img draggable="false" src="img/logo.webp" alt="CalzadoPro Logo">
                </div>

                <div class="card-header-text">
                    <h3>Sistema de Calzados</h3>
                </div>
                <p class="card-desc">Ingresa tus datos para iniciar sesión</p>

                <div class="divider">
                    <div class="divider-line"></div>
                    <div class="divider-dot"></div>
                    <div class="divider-line"></div>
                </div>

                <form class="form-login" role="form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Email -->
                    <div class="form-group input_login">
                        <div class="field-label">Correo electrónico</div>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="2" />
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                                </svg>
                            </span>
                            <input id="email" type="email" class="form-control" name="email" required
                                autocomplete="email" autofocus placeholder="tucorreo@ejemplo.com">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group input_login">
                        <div class="field-label">Contraseña</div>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                            <input id="password" type="password" class="form-control" name="password" required
                                autocomplete="current-password" placeholder="••••••••">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <button type="button" class="eye-btn" onclick="togglePassword()"
                                title="Mostrar / Ocultar">
                                <span id="eye-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="btn-iniciar input_login">
                        <button type="submit" class="btn-submit">
                            Iniciar Sesión
                        </button>
                    </div>

                    <a class="forgot-link" href="#">¿Olvidaste tu contraseña?</a>

                </form>

                <div class="card-footer-text">
                    CalzadoPro &copy; 2026 &middot; Sistema de Comercialización
                </div>

            </div>
        </div>

    </div>

    <script>
        window.addEventListener("load", function() {
            document.querySelector('.loader-spinner').style.display = 'none';
            document.getElementById("content-system").style.display = 'flex';
        });

        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            const hide = pwd.type === 'password';
            pwd.type = hide ? 'text' : 'password';
            icon.innerHTML = hide ?
                `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>` :
                `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>`;
        }
    </script>

</body>

</html>
