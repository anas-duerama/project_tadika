<!doctype html>
<html>
<body>
    <h1>User Home</h1>
    <p>Welcome, {{ auth()->user()->name }}</p>
    <form method="POST" action="{{ route('logout') }}">@csrf <button>Logout</button></form>
</body>
</html>
