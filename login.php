<?php
session_start();
require 'function.php';
// Jangan redirect kalau session ada tapi rusak
if(isset($_SESSION['log']) && !empty($_SESSION['log'])){ 
    header("location:index.php"); exit; 
}

if(isset($_POST['login'])){
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cek      = mysqli_query($conn, "SELECT * FROM login WHERE email='$email' AND password='$password'");
    if(mysqli_num_rows($cek) > 0){
        $user = mysqli_fetch_assoc($cek);
        $_SESSION['log'] = $user['email'];
        header("location:index.php"); exit;
    } else {
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — DrizStock</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'DM Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      background: #f9fafb;
    }

    /* LEFT */
    .login-left {
      flex: 0 0 420px;
      background: #1a2e2a;
      display: flex; flex-direction: column;
      justify-content: space-between;
      padding: 2.5rem;
      position: relative;
      overflow: hidden;
    }
    .login-left::before {
      content: '';
      position: absolute;
      bottom: -100px; right: -100px;
      width: 300px; height: 300px;
      border-radius: 50%;
      background: rgba(52,211,153,0.06);
    }
    .login-left::after {
      content: '';
      position: absolute;
      top: 30%; left: -60px;
      width: 180px; height: 180px;
      border-radius: 50%;
      background: rgba(52,211,153,0.04);
    }
    .brand {
      display: flex; align-items: center; gap: 0.75rem;
    }
    .brand-icon {
      width: 38px; height: 38px;
      background: #34d399;
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-weight: 800; color: #1a2e2a; font-size: 0.85rem; letter-spacing: -0.5px;
    }
    .brand-name {
      font-family: 'Instrument Serif', serif;
      font-size: 1.25rem; color: #fff;
    }
    .brand-name span { color: #34d399; }

    .left-middle { position: relative; z-index: 1; }
    .left-middle h2 {
      font-family: 'Instrument Serif', serif;
      font-size: 2.2rem; font-weight: 400;
      color: #fff; line-height: 1.25; margin-bottom: 1rem;
    }
    .left-middle h2 em { color: #34d399; font-style: italic; }
    .left-middle p { color: #6b9e8f; font-size: 0.85rem; line-height: 1.65; }

    .feature-list { display: flex; flex-direction: column; gap: 0.6rem; margin-top: 1.5rem; }
    .feature-item {
      display: flex; align-items: center; gap: 0.6rem;
      color: #6b9e8f; font-size: 0.82rem;
    }
    .feature-item i { color: #34d399; font-size: 0.9rem; }

    .left-footer { color: #2d5248; font-size: 0.72rem; position: relative; z-index: 1; }

    /* RIGHT */
    .login-right {
      flex: 1;
      display: flex; align-items: center; justify-content: center;
      padding: 2rem;
    }
    .login-box { width: 100%; max-width: 360px; }
    .login-box h3 {
      font-family: 'Instrument Serif', serif;
      font-size: 1.6rem; font-weight: 400;
      color: #0f172a; margin-bottom: 0.3rem;
    }
    .login-box p { color: #6b7280; font-size: 0.83rem; margin-bottom: 2rem; }

    .form-group { margin-bottom: 1rem; }
    .form-label { font-size: 0.75rem; font-weight: 600; color: #374151; text-transform: uppercase; letter-spacing: 0.4px; display: block; margin-bottom: 0.35rem; }
    .input-wrap { position: relative; }
    .input-wrap i.icon-left { position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.9rem; }
    .form-control {
      width: 100%; border: 1px solid #e5e7eb; border-radius: 8px;
      padding: 0.6rem 0.85rem 0.6rem 2.5rem;
      font-size: 0.875rem; font-family: 'DM Sans', sans-serif;
      background: #f9fafb; transition: all 0.15s; outline: none;
    }
    .form-control:focus { border-color: #34d399; background: #fff; box-shadow: 0 0 0 3px rgba(52,211,153,0.12); }
    /* Hilangkan mata bawaan browser */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear,
    input[type="password"]::-webkit-credentials-auto-fill-button { display: none !important; }

    .toggle-pw { position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); color: #9ca3af; cursor: pointer; font-size: 0.9rem; }

    .btn-login {
      width: 100%; padding: 0.65rem;
      background: #059669; color: #fff;
      border: none; border-radius: 8px;
      font-size: 0.875rem; font-weight: 600;
      font-family: 'DM Sans', sans-serif; cursor: pointer;
      transition: all 0.15s; margin-top: 0.5rem;
    }
    .btn-login:hover { background: #047857; box-shadow: 0 4px 12px rgba(5,150,105,0.3); transform: translateY(-1px); }

    .error-msg {
      background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;
      border-radius: 8px; padding: 0.6rem 0.85rem;
      font-size: 0.8rem; margin-bottom: 1rem;
      display: flex; align-items: center; gap: 0.5rem;
    }

    @media (max-width: 700px) { .login-left { display: none; } }
  </style>
</head>
<body>
  <div class="login-left">
    <div class="brand">
      <div class="brand-icon">DS</div>
      <div class="brand-name">Driz<span>Stock</span></div>
    </div>
    <div class="left-middle">
      <h2>Kelola stok dengan <em>efisien</em> dan akurat.</h2>
      <p>Sistem manajemen inventaris modern untuk perpustakaan dan gudang. Pantau, catat, dan ekspor data kapan saja.</p>
      <div class="feature-list">
        <div class="feature-item"><i class="bi bi-check2-circle"></i> Pantau stok secara real-time</div>
        <div class="feature-item"><i class="bi bi-check2-circle"></i> Upload gambar setiap barang</div>
        <div class="feature-item"><i class="bi bi-check2-circle"></i> Export ke Excel, PDF & CSV</div>
        <div class="feature-item"><i class="bi bi-check2-circle"></i> Notifikasi stok menipis</div>
      </div>
    </div>
    <div class="left-footer">© 2025 DrizStock by Indris</div>
  </div>

  <div class="login-right">
    <div class="login-box">
      <h3>Selamat datang</h3>
      <p>Masuk ke akun administrator Anda</p>

      <?php if(isset($error)): ?>
      <div class="error-msg"><i class="bi bi-exclamation-circle-fill"></i> Email atau password salah!</div>
      <?php endif; ?>

      <form method="post">
        <div class="form-group">
          <label class="form-label">Email</label>
          <div class="input-wrap">
            <i class="bi bi-envelope icon-left"></i>
            <input type="email" class="form-control" name="email" placeholder="Masukkan email" required autocomplete="off" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrap">
            <i class="bi bi-lock icon-left"></i>
            <input type="password" class="form-control" name="password" id="pw" placeholder="Masukkan password" required autocomplete="new-password" style="-webkit-text-security:disc;">
            <span class="toggle-pw" onclick="togglePw()"><i class="bi bi-eye" id="eyeIcon"></i></span>
          </div>
        </div>
        <button type="submit" class="btn-login" name="login">Masuk ke DrizStock</button>
      </form>
    </div>
  </div>

<script>
function togglePw(){
  var pw=document.getElementById('pw'), ic=document.getElementById('eyeIcon');
  pw.type = pw.type==='password' ? 'text' : 'password';
  ic.className = pw.type==='password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
</body>
</html>