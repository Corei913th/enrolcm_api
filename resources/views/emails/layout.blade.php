<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EnrolCM')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f3f4f6;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .email-header {
            background-color: #059669;
            padding: 32px 24px;
            text-align: center;
        }

        .email-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }

        .email-header p {
            color: #d1fae5;
            font-size: 14px;
            margin-top: 8px;
        }

        .email-body {
            padding: 40px 24px;
        }

        .email-body h2 {
            color: #111827;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .email-body p {
            color: #4b5563;
            font-size: 16px;
            margin-bottom: 16px;
        }

        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #059669;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 24px 0;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #047857;
        }

        .info-box {
            background-color: #f0fdf4;
            border-left: 4px solid #059669;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
        }

        .info-box p {
            margin: 0;
            color: #065f46;
            font-size: 14px;
        }

        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 32px 0;
        }

        .email-footer {
            background-color: #f9fafb;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .email-footer p {
            color: #6b7280;
            font-size: 14px;
            margin: 8px 0;
        }

        .email-footer a {
            color: #059669;
            text-decoration: none;
        }

        .email-footer a:hover {
            text-decoration: underline;
        }

        @media only screen and (max-width: 600px) {
            .email-header {
                padding: 24px 16px;
            }

            .email-header h1 {
                font-size: 24px;
            }

            .email-body {
                padding: 24px 16px;
            }

            .button {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>EnrolCM</h1>
            <p>Plateforme de Gestion des Concours</p>
        </div>

        <div class="email-body">
            @yield('content')
        </div>

        <div class="email-footer">
            <p><strong>EnrolCM</strong></p>
            <p>Plateforme de gestion des inscriptions et concours</p>
            <p style="margin-top: 16px;">
                Besoin d'aide ? <a href="mailto:support@enrolcm.com">Contactez notre support</a>
            </p>
            <p style="margin-top: 16px; font-size: 12px; color: #9ca3af;">
                © {{ date('Y') }} EnrolCM. Tous droits réservés.
            </p>
        </div>
    </div>
</body>

</html>
