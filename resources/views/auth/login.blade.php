<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk / Daftar - PawPaw Pet Care</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=2">
</head>
<body>
  <div class="auth-page">
    <a href="{{ route('home') }}" class="auth-home-link">
      🐾 PawPaw Pet Care
    </a>

    <div class="auth-wrapper">
      <div class="mascot" id="mascot">
        <div class="mascot-ear left"></div>
        <div class="mascot-ear right"></div>
        <div class="mascot-face">
          <div class="mascot-eye left">
            <div class="mascot-pupil" id="pupilLeft"></div>
          </div>
          <div class="mascot-eye right">
            <div class="mascot-pupil" id="pupilRight"></div>
          </div>
          <div class="mascot-nose"></div>
        </div>
      </div>

      <div class="auth-flip-container {{ old('form_type') === 'register' ? 'flipped' : '' }}" id="authFlip">
        <div class="auth-flip-inner">
          <!-- SISI LOGIN -->
          <div class="auth-card auth-front" id="authCardFront" @if(old('form_type') === 'register') inert aria-hidden="true" @endif>
            <h1>Selamat Datang Kembali!</h1>
            <p class="auth-subtitle">Masuk untuk lanjut rawat si bulu <span aria-hidden="true">🐶</span></p>

            @if(session('success'))
              <div class="auth-message success" role="status" aria-live="polite">{{ session('success') }}</div>
            @endif

            @if($errors->any() && old('form_type') !== 'register')
              <div class="auth-message error" role="alert">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
              @csrf
              <input type="hidden" name="form_type" value="login">

              <div class="form-group">
                <label for="loginEmail">Email atau Username</label>
                <input type="text" id="loginEmail" name="email" value="{{ old('email') }}" placeholder="admin@pawpaw.com atau arini" autocomplete="username" required>
              </div>

              <div class="form-group">
                <label for="loginPassword">Kata Sandi</label>
                <input type="password" id="loginPassword" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
              </div>

              <div class="auth-row">
                <label class="auth-checkbox">
                  <input type="checkbox" name="remember" value="1">
                  <span>Ingat saya</span>
                </label>
                <span class="small text-muted">Demo: admin@pawpaw.com / admin123</span>
              </div>

              <button type="submit" class="auth-submit-button">
                Masuk
              </button>
            </form>

            <p class="auth-switch">
              Belum punya akun?
              <button type="button" class="auth-switch-button" data-target="signup">
                Daftar di sini
              </button>
            </p>
          </div>

          <!-- SISI DAFTAR (SIGNUP) -->
          <div class="auth-card auth-back" id="authCardBack" @if(old('form_type') !== 'register') inert aria-hidden="true" @endif>
            <h1>Gabung Yuk!</h1>
            <p class="auth-subtitle">Daftar buat mulai adopsi &amp; rawat hewan <span aria-hidden="true">🐰</span></p>

            @if($errors->any() && old('form_type') === 'register')
              <div class="auth-message error" role="alert">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST">
              @csrf
              <input type="hidden" name="form_type" value="register">

              <div class="form-group">
                <label for="signupName">Nama Lengkap</label>
                <input type="text" id="signupName" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" autocomplete="name" required>
              </div>

              <div class="form-group">
                <label for="signupEmail">Email</label>
                <input type="email" id="signupEmail" name="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required>
              </div>

              <div class="form-group">
                <label for="signupPassword">Kata Sandi</label>
                <input type="password" id="signupPassword" name="password" placeholder="Minimal 6 karakter" autocomplete="new-password" required>
              </div>

              <div class="form-group">
                <label for="signupConfirm">Konfirmasi Kata Sandi</label>
                <input type="password" id="signupConfirm" name="password_confirmation" placeholder="Ulangi kata sandi" autocomplete="new-password" required>
              </div>

              <button type="submit" class="auth-submit-button">
                Daftar Sekarang
              </button>
            </form>

            <p class="auth-switch">
              Sudah punya akun?
              <button type="button" class="auth-switch-button" data-target="login">
                Masuk di sini
              </button>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
