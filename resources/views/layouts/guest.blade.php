<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GlobalRisk') }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #0f172a;
            color: #cbd5e1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        }
        .auth-logo {
            font-size: 32px;
            font-weight: 700;
            color: #fff;
            text-align: center;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .auth-logo i {
            color: #2563eb;
        }
        .form-label {
            font-weight: 500;
            color: #94a3b8;
            font-size: 14px;
        }
        .form-control {
            background: #0f172a;
            border: 1px solid #334155;
            color: #fff;
            border-radius: 12px;
            padding: 12px 16px;
        }
        .form-control:focus {
            background: #0f172a;
            border-color: #2563eb;
            color: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25);
        }
        .btn-primary {
            background: #2563eb;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }
        .text-link {
            color: #2563eb;
            text-decoration: none;
            font-size: 13px;
        }
        .text-link:hover {
            color: #60a5fa;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-logo">
            <i class="bi bi-globe2"></i> GlobalRisk
        </div>
        {{ $slot }}
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
