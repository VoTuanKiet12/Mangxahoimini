<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="text-center">
        <div class="alert alert-success fs-4">
            Đăng ký thành công!
        </div>
        <p>Bạn sẽ được chuyển tới trang đăng nhập trong giây lát...</p>
        <a href="{{ route('trangchu') }}" class="btn btn-primary mt-2">Đi ngay</a>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = "{{ route('trangchu') }}";
        }, 1000);
    </script>
</body>

</html>