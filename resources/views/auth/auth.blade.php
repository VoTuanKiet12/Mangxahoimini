<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Xác thực - Đăng nhập/Đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('public/css/dangnhap.css') }}" />

</head>

<body>
    <div id="particles-js"></div>
    <section class="dangnhap">
        <div class="container1" id="container1">

            {{-- FORM ĐĂNG KÝ --}}
            <div class="form-container1 register-container1">
                <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <h1>Đăng ký</h1>

                    {{-- Họ và tên --}}
                    <div class="form-control">
                        <input type="text" name="name" placeholder="Họ và tên" value="{{ old('name') }}" required />
                    </div>

                    {{-- Email --}}
                    <div class="form-control">
                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required />
                    </div>

                    {{-- Username --}}
                    <div class="form-control">
                        <input type="text" name="username" placeholder="Tên đăng nhập" value="{{ old('username') }}" required />
                    </div>

                    {{-- Số điện thoại --}}
                    <div class="form-control">
                        <input type="text" name="so_dien_thoai" placeholder="Số điện thoại" value="{{ old('so_dien_thoai') }}" required />
                    </div>

                    {{-- Ngày sinh --}}
                    <div class="form-control">
                        <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh') }}" required />
                    </div>


                    {{-- Mật khẩu --}}
                    <div class="form-control">
                        <input type="password" id="MatKhau" name="password" placeholder="Mật khẩu" required>
                        <button class="btn-dk" type="button" id="togglePassword">
                            <i class="bi bi-eye-slash" id="iconToggle"></i>
                        </button>
                    </div>

                    {{-- Xác nhận mật khẩu --}}
                    <div class="form-control">
                        <input type="password" id="XacNhanMatKhau" name="password_confirmation" placeholder="Xác nhận mật khẩu" required>
                        <button class="btn-dk" type="button" id="togglePasswordConfirm">
                            <i class="bi bi-eye-slash" id="iconToggleConfirm"></i>
                        </button>
                    </div>

                    <button type="submit">Đăng ký</button>
                </form>
            </div>


            {{-- FORM ĐĂNG NHẬP --}}
            <div class="form-container1 login-container1">
                <form action="{{ route('login') }}" method="POST" class="form-lg" novalidate>
                    @csrf
                    <h1>Đăng nhập</h1>



                    {{-- Username --}}
                    <div class="form-control2">
                        <input type="text" name="username" placeholder="Tên đăng nhập" value="{{ old('username') }}" required />
                    </div>
                    {{-- Mật khẩu đăng nhập --}}
                    <div class="form-control2" style="position:relative;">
                        <input type="password" id="MatKhauLogin" name="password" placeholder="Mật khẩu" required />
                        <button class="btn-dn" type="button" id="togglePasswordLogin">
                            <i class="bi bi-eye-slash" id="iconToggleLogin"></i>
                        </button>
                    </div>



                    <button type="submit">Đăng nhập</button>
                    @if ($errors->any())
                    <div class="text-danger mb-2">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <span>Hoặc sử dụng tài khoản của bạn</span>
                    <div class="social-container1">
                        <a href="google-login.php" class="social">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/2048px-Google_%22G%22_logo.svg.png"
                                alt="Google" style="width: 20px; margin-right: 10px;">
                            Đăng nhập bằng Gmail
                        </a>
                    </div>
                </form>
            </div>

            {{-- OVERLAY --}}
            <div class="overlay-container1">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <h1 class="title">Xin chào!</h1>
                        <p>Nếu bạn đã có tài khoản, hãy đăng nhập tại đây.</p>
                        <button class="ghost" id="login">Đăng nhập</button>
                    </div>
                    <div class="overlay-panel overlay-right">
                        <h1 class="title">Chào mừng!</h1>
                        <p>Nếu bạn chưa có tài khoản, hãy đăng ký để bắt đầu hành trình.</p>
                        <button class="ghost" id="register">Đăng ký</button>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <script src="{{ asset('public/js/dangnhap.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            particles: {
                number: {
                    value: 80,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: ["#dafd65", "#ffda88", "#659dfd"]
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#70b8fa",
                    opacity: 0.4,
                    width: 1
                },
                shape: {
                    type: "circle",
                    stroke: {
                        width: 0,
                        color: "#000000"
                    },
                    polygon: {
                        nb_sides: 5
                    }
                },
                opacity: {
                    value: 0.5,
                    random: false
                },
                size: {
                    value: 3,
                    random: true
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: "none",
                    random: false,
                    straight: false,
                    out_mode: "out",
                    bounce: false
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: {
                        enable: true,
                        mode: "grab"
                    },
                    onclick: {
                        enable: true,
                        mode: "push"
                    },
                    resize: true
                }
            },
            retina_detect: true
        });

        // ==== Đăng ký ====
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('MatKhau');
        const iconToggle = document.getElementById('iconToggle');

        togglePassword.addEventListener('click', () => {
            const type = password.type === 'password' ? 'text' : 'password';
            password.type = type;
            iconToggle.classList.toggle('bi-eye');
            iconToggle.classList.toggle('bi-eye-slash');
        });

        // ==== Xác nhận mật khẩu ====
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const passwordConfirm = document.getElementById('XacNhanMatKhau');
        const iconToggleConfirm = document.getElementById('iconToggleConfirm');

        togglePasswordConfirm.addEventListener('click', () => {
            const type = passwordConfirm.type === 'password' ? 'text' : 'password';
            passwordConfirm.type = type;
            iconToggleConfirm.classList.toggle('bi-eye');
            iconToggleConfirm.classList.toggle('bi-eye-slash');
        });

        // ==== Đăng nhập ====
        const togglePasswordLogin = document.getElementById('togglePasswordLogin');
        const passwordLogin = document.getElementById('MatKhauLogin');
        const iconToggleLogin = document.getElementById('iconToggleLogin');

        togglePasswordLogin.addEventListener('click', () => {
            const type = passwordLogin.type === 'password' ? 'text' : 'password';
            passwordLogin.type = type;
            iconToggleLogin.classList.toggle('bi-eye');
            iconToggleLogin.classList.toggle('bi-eye-slash');
        });
    </script>

</body>

</html>