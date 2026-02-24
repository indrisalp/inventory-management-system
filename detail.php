<?php
session_start();
require 'function.php';
if(!isset($_SESSION['log'])){ header("location:login.php"); exit; }

$idb = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if(!$idb){ header("location:index.php"); exit; }

$barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'"));
if(!$barang){ header("location:index.php"); exit; }

$pageTitle = 'Detail Barang';
$imgPath = 'uploads/' . ($barang['image'] ?? 'no-image.png');
$imgExist = ($barang['image'] && $barang['image'] !== 'no-image.png' && file_exists($imgPath));

// Riwayat masuk
$rMasuk  = mysqli_query($conn, "SELECT * FROM masuk WHERE idbarang='$idb' ORDER BY tanggal DESC LIMIT 10");
// Riwayat keluar
$rKeluar = mysqli_query($conn, "SELECT * FROM keluar WHERE idbarang='$idb' ORDER BY tanggal DESC LIMIT 10");
// Peminjaman aktif
$rPinjam = mysqli_query($conn, "SELECT * FROM pinjam WHERE idbarang='$idb' ORDER BY idpinjam DESC LIMIT 10");

$totalMasuk  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as t FROM masuk WHERE idbarang='$idb'"))['t'] ?? 0;
$totalKeluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as t FROM keluar WHERE idbarang='$idb'"))['t'] ?? 0;
$totalPinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pinjam WHERE idbarang='$idb' AND status='Dipinjam'"))['t'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail — <?= htmlspecialchars($barang['namabarang']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <style>
    .detail-img {
      width: 100%; aspect-ratio: 1/1; object-fit: cover;
      border-radius: 12px; border: 1px solid var(--border);
    }
    .detail-img-placeholder {
      width: 100%; aspect-ratio: 1/1;
      background: var(--surface); border-radius: 12px;
      border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      color: #d1d5db; font-size: 3rem;
    }
    .info-row { display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0; border-bottom: 1px solid var(--border); font-size: 0.85rem; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: var(--text-muted); font-size: 0.78rem; }
    .history-table td { font-size: 0.8rem; padding: 0.6rem 0.75rem !important; vertical-align: middle; }
    .history-table th { font-size: 0.7rem; background: var(--surface); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; padding: 0.5rem 0.75rem !important; }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
  <div>
    <h1><?= htmlspecialchars($barang['namabarang']) ?></h1>
    <p><?= htmlspecialchars($barang['deskripsi']) ?></p>
  </div>
  <a href="index.php" class="export-btn"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="row g-4 mb-4">
  <!-- Foto & Info -->
  <div class="col-12 col-md-4">
    <div class="card-modern p-3">
      <?php if($imgExist): ?>
        <img src="<?= $imgPath ?>" class="detail-img mb-3" alt="<?= htmlspecialchars($barang['namabarang']) ?>">
      <?php else: ?>
        <div class="detail-img-placeholder mb-3"><i class="bi bi-image"></i></div>
      <?php endif; ?>
      <div class="info-row">
        <span class="info-label">Nama Barang</span>
        <span style="font-weight:600"><?= htmlspecialchars($barang['namabarang']) ?></span>
      </div>
      <div class="info-row">
        <span class="info-label">Deskripsi</span>
        <span><?= htmlspecialchars($barang['deskripsi']) ?></span>
      </div>
      <div class="info-row">
        <span class="info-label">Stok Saat Ini</span>
        <?php $s=(int)$barang['stock']; $bc=$s>50?'stock-high':($s>10?'stock-medium':'stock-low'); ?>
        <span class="stock-badge <?= $bc ?>"><?= number_format($s) ?></span>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="col-12 col-md-8">
    <div class="row g-3 mb-4">
      <div class="col-4">
        <div class="stat-card">
          <div class="stat-icon green"><i class="bi bi-box-arrow-in-down"></i></div>
          <div><div class="stat-label">Total Masuk</div><div class="stat-value"><?= number_format($totalMasuk) ?></div></div>
        </div>
      </div>
      <div class="col-4">
        <div class="stat-card">
          <div class="stat-icon red"><i class="bi bi-box-arrow-up"></i></div>
          <div><div class="stat-label">Total Keluar</div><div class="stat-value"><?= number_format($totalKeluar) ?></div></div>
        </div>
      </div>
      <div class="col-4">
        <div class="stat-card">
          <div class="stat-icon amber"><i class="bi bi-clipboard-check"></i></div>
          <div><div class="stat-label">Sedang Dipinjam</div><div class="stat-value"><?= $totalPinjam ?></div></div>
        </div>
      </div>
    </div>

    <!-- Riwayat Masuk -->
    <div class="card-modern mb-3">
      <div class="card-modern-header"><h5><i class="bi bi-box-arrow-in-down" style="color:#059669"></i> Riwayat Masuk</h5></div>
      <div class="card-modern-body p-0">
        <table class="table table-bordered mb-0 history-table">
          <thead><tr><th>Tanggal</th><th>Jumlah</th><th>Keterangan</th></tr></thead>
          <tbody>
          <?php while($r=mysqli_fetch_array($rMasuk)): ?>
          <tr>
            <td style="color:var(--text-muted)"><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
            <td><span class="stock-badge stock-high" style="font-size:0.7rem">+<?= $r['jumlah'] ?></span></td>
            <td><?= htmlspecialchars($r['keterangan']) ?></td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Riwayat Keluar -->
    <div class="card-modern mb-3">
      <div class="card-modern-header"><h5><i class="bi bi-box-arrow-up" style="color:#ef4444"></i> Riwayat Keluar</h5></div>
      <div class="card-modern-body p-0">
        <table class="table table-bordered mb-0 history-table">
          <thead><tr><th>Tanggal</th><th>Jumlah</th><th>Penerima</th></tr></thead>
          <tbody>
          <?php while($r=mysqli_fetch_array($rKeluar)): ?>
          <tr>
            <td style="color:var(--text-muted)"><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
            <td><span class="stock-badge stock-low" style="font-size:0.7rem">-<?= $r['jumlah'] ?></span></td>
            <td><?= htmlspecialchars($r['penerima']) ?></td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Peminjaman -->
    <div class="card-modern">
      <div class="card-modern-header"><h5><i class="bi bi-clipboard-check" style="color:#d97706"></i> Riwayat Peminjaman</h5></div>
      <div class="card-modern-body p-0">
        <table class="table table-bordered mb-0 history-table">
          <thead><tr><th>Tanggal</th><th>Jumlah</th><th>Peminjam</th><th>Status</th></tr></thead>
          <tbody>
          <?php while($r=mysqli_fetch_array($rPinjam)): ?>
          <tr>
            <td style="color:var(--text-muted)"><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
            <td><?= $r['jumlah'] ?></td>
            <td><?= htmlspecialchars($r['kepada']) ?></td>
            <td>
              <?php if($r['status']==='Dipinjam'): ?>
                <span class="stock-badge stock-medium" style="font-size:0.7rem"><i class="bi bi-clock-fill"></i> Dipinjam</span>
              <?php else: ?>
                <span class="stock-badge stock-high" style="font-size:0.7rem"><i class="bi bi-check-circle-fill"></i> Dikembalikan</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>