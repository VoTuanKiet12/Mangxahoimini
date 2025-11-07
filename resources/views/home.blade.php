<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang chủ</title>
</head>

<body>
    <h1>Xin chào, {{ Auth::user()->name }}!</h1>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Đăng xuất</button>
    </form>
</body>

</html>