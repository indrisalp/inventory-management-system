<?php
session_start();
require 'function.php';
if(!isset($_SESSION['log'])){ header("location:login.php"); exit; }
$pageTitle = 'Peminjaman';

// Filter tanggal
$filterFrom = isset($_GET['dari']) && $_GET['dari'] !== '' ? $_GET['dari'] : '';
$filterTo   = isset($_GET['sampai']) && $_GET['sampai'] !== '' ? $_GET['sampai'] : '';
$whereDate  = '';
if($filterFrom && $filterTo) $whereDate = "AND DATE(p.tanggal) BETWEEN '$filterFrom' AND '$filterTo'";
elseif($filterFrom) $whereDate = "AND DATE(p.tanggal) >= '$filterFrom'";
elseif($filterTo)   $whereDate = "AND DATE(p.tanggal) <= '$filterTo'";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Peminjaman — DrizStock</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page-header">
  <h1>Peminjaman Barang</h1>
  <p>Kelola peminjaman dan pengembalian barang</p>
</div>

<!-- Filter Tanggal -->
<div class="card-modern mb-4">
  <div class="card-modern-body">
    <form method="get" class="d-flex align-items-end gap-3 flex-wrap">
      <div>
        <label style="font-size:0.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.4px;display:block;margin-bottom:0.3rem;">Dari Tanggal</label>
        <input type="date" class="form-control" name="dari" value="<?= htmlspecialchars($filterFrom) ?>" style="font-size:0.83rem;border-radius:7px;border:1px solid var(--border);">
      </div>
      <div>
        <label style="font-size:0.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.4px;display:block;margin-bottom:0.3rem;">Sampai Tanggal</label>
        <input type="date" class="form-control" name="sampai" value="<?= htmlspecialchars($filterTo) ?>" style="font-size:0.83rem;border-radius:7px;border:1px solid var(--border);">
      </div>
      <button type="submit" class="btn-primary-modern"><i class="bi bi-funnel"></i> Filter</button>
      <?php if($filterFrom || $filterTo): ?>
      <a href="pinjam.php" class="export-btn"><i class="bi bi-x"></i> Reset</a>
      <?php endif; ?>
    </form>
  </div>
</div>

<div class="card-modern">
  <div class="card-modern-header">
    <h5><i class="bi bi-clipboard-check"></i> Daftar Peminjaman</h5>
    <button class="btn-primary-modern" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-lg"></i> Tambah Pinjam
    </button>
  </div>
  <div class="card-modern-body">
    <table id="pinjamTable" class="table table-bordered w-100">
      <thead>
        <tr>
          <th width="50">No</th>
          <th width="55">Foto</th>
          <th>Nama Barang</th>
          <th width="80">Jumlah</th>
          <th>Kepada</th>
          <th>No HP</th>
          <th>Tanggal</th>
          <th width="120">Status</th>
          <th width="80">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $rows = mysqli_query($conn, "SELECT p.*, s.namabarang, s.image FROM pinjam p LEFT JOIN stock s ON p.idbarang=s.idbarang WHERE 1=1 $whereDate ORDER BY p.idpinjam DESC");
        $i=1;
        while($d = mysqli_fetch_array($rows)):
          $imgPath = 'uploads/' . ($d['image'] ?? 'no-image.png');
          $imgExist = ($d['image'] && $d['image'] !== 'no-image.png' && file_exists($imgPath));
          $isDipinjam = $d['status'] === 'Dipinjam';
      ?>
      <tr>
        <td class="text-center text-muted"><?= $i++ ?></td>
        <td class="text-center">
          <?php if($imgExist): ?>
            <img src="<?= $imgPath ?>" class="img-thumb" alt="">
          <?php else: ?>
            <div class="img-placeholder"><i class="bi bi-image"></i></div>
          <?php endif; ?>
        </td>
        <td style="font-weight:500"><?= htmlspecialchars($d['namabarang']) ?></td>
        <td class="text-center"><?= $d['jumlah'] ?></td>
        <td><?= htmlspecialchars($d['kepada']) ?></td>
        <td style="font-size:0.82rem;color:var(--text-muted)"><?= htmlspecialchars($d['nohp']) ?></td>
        <td style="font-size:0.82rem;color:var(--text-muted)"><?= date('d M Y', strtotime($d['tanggal'])) ?></td>
        <td>
          <?php if($isDipinjam): ?>
            <span class="stock-badge stock-medium"><i class="bi bi-clock-fill"></i> Dipinjam</span>
          <?php else: ?>
            <span class="stock-badge stock-high"><i class="bi bi-check-circle-fill"></i> Dikembalikan</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if($isDipinjam): ?>
          <form method="post" style="display:inline">
            <input type="hidden" name="idpinjam" value="<?= $d['idpinjam'] ?>">
            <input type="hidden" name="idbarang" value="<?= $d['idbarang'] ?>">
            <input type="hidden" name="jumlah" value="<?= $d['jumlah'] ?>">
            <button type="submit" name="selesaipinjam" class="btn btn-sm" style="border-radius:6px;border:1px solid #a7f3d0;color:#065f46;font-size:0.72rem;font-weight:600;padding:0.25rem 0.5rem;" onclick="return confirm('Tandai sebagai dikembalikan?')">
              <i class="bi bi-check2"></i> Selesai
            </button>
          </form>
          <?php endif; ?>
          <button class="btn btn-sm" style="border-radius:6px;border:1px solid #fecaca;color:#ef4444;font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $d['idpinjam'] ?>"><i class="bi bi-trash"></i></button>
        </td>
      </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade modal-modern" id="modalTambah" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Tambah Peminjaman</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post"><div class="modal-body">
      <div class="mb-3">
        <label class="form-label">Pilih Barang</label>
        <select class="form-select" name="idbarang" required>
          <option value="">— Pilih barang —</option>
          <?php $opt=mysqli_query($conn,"SELECT * FROM stock WHERE stock > 0 ORDER BY namabarang"); while($o=mysqli_fetch_array($opt)): ?>
          <option value="<?= $o['idbarang'] ?>"><?= htmlspecialchars($o['namabarang']) ?> (Stok: <?= $o['stock'] ?>)</option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3"><label class="form-label">Nama Peminjam</label><input type="text" class="form-control" name="kepada" placeholder="Nama lengkap" required></div>
      <div class="mb-3"><label class="form-label">No HP</label><input type="text" class="form-control" name="nohp" placeholder="08xxxxxxxxxx" required></div>
      <div class="mb-3"><label class="form-label">Jumlah</label><input type="number" class="form-control" name="jumlah" placeholder="1" min="1" required></div>
      <div class="mb-3"><label class="form-label">Tanggal Pinjam</label><input type="datetime-local" class="form-control" name="tanggal" value="<?= date('Y-m-d\TH:i') ?>" required></div>
      <button type="submit" class="btn-primary-modern w-100 justify-content-center" name="addpinjam"><i class="bi bi-check-lg"></i> Simpan</button>
    </div></form>
  </div></div>
</div>

<!-- Modal Hapus -->
<?php
$rows2 = mysqli_query($conn, "SELECT p.*, s.namabarang FROM pinjam p LEFT JOIN stock s ON p.idbarang=s.idbarang ORDER BY p.idpinjam DESC");
while($d = mysqli_fetch_array($rows2)):
?>
<div class="modal fade modal-modern" id="modalHapus<?= $d['idpinjam'] ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Hapus Data</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post"><div class="modal-body text-center py-3">
      <div style="width:52px;height:52px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;"><i class="bi bi-trash" style="font-size:1.3rem;color:#ef4444;"></i></div>
      <p style="font-size:0.88rem;font-weight:600;margin-bottom:0.25rem;">Hapus data pinjam <strong><?= htmlspecialchars($d['namabarang']) ?></strong>?</p>
      <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:1rem;">
        <?= $d['status']==='Dipinjam' ? 'Stok akan dikembalikan karena masih berstatus Dipinjam.' : 'Data akan dihapus permanen.' ?>
      </p>
      <input type="hidden" name="idpinjam" value="<?= $d['idpinjam'] ?>">
      <input type="hidden" name="idbarang" value="<?= $d['idbarang'] ?>">
      <input type="hidden" name="jumlah" value="<?= $d['jumlah'] ?>">
      <input type="hidden" name="status" value="<?= $d['status'] ?>">
      <button type="submit" class="btn btn-danger w-100" style="border-radius:8px;font-size:0.85rem;font-weight:600;" name="deletepinjam"><i class="bi bi-trash me-1"></i> Hapus</button>
    </div></form>
  </div></div>
</div>
<?php endwhile; ?>

</div></div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
  $('#pinjamTable').DataTable({language:{search:"",searchPlaceholder:"Cari peminjam...",lengthMenu:"Tampilkan _MENU_ data",info:"_START_–_END_ dari _TOTAL_ data",paginate:{previous:'<i class="bi bi-chevron-left"></i>',next:'<i class="bi bi-chevron-right"></i>'}},columnDefs:[{orderable:false,targets:[1,8]}]});
});
</script>
</body>
</html>