<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'School Portal')</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:0;background:#f3f6fb;color:#111}
        .container{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px}
        .panel{width:420px;background:#fff;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.08);padding:28px}
        .logo{height:56px;display:block;margin:0 auto 8px}
        h2{margin:0 0 6px;font-size:20px;color:#0b5ed7;text-align:center}
        p.sub{color:#6b7280;text-align:center;margin:0 0 12px}
        .error{color:#b91c1c;background:#fee2e2;padding:8px;border-radius:6px;margin-bottom:12px}
        label{display:block;font-weight:600;color:#374151;margin-bottom:6px}
        .input{width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:6px}
        .row{display:flex;gap:10px}
        .btn{background:#0b5ed7;color:#fff;padding:10px 16px;border:none;border-radius:6px;cursor:pointer}
        .link{color:#0b5ed7;text-decoration:none}
        .small{font-size:13px;color:#6b7280;text-align:center;margin-top:12px}
    </style>
</head>
<body>
    <div class="container">
        <div class="panel">
            <img src="/logo.png" class="logo" alt="School logo">
            <h2>@yield('heading','Welcome to School Portal')</h2>
            @hasSection('subheading')
                <p class="sub">@yield('subheading')</p>
            @endif

            @yield('content')

            <p class="small">© {{ date('Y') }} School</p>
        </div>
    </div>
</body>
</html>
