<?php
session_start();
require 'function.php';
if(!isset($_SESSION['log'])){ header("location:login.php"); exit; }
$pageTitle = 'Barang Keluar';

$filterFrom = isset($_GET['dari']) && $_GET['dari'] !== '' ? $_GET['dari'] : '';
$filterTo   = isset($_GET['sampai']) && $_GET['sampai'] !== '' ? $_GET['sampai'] : '';
$whereDate  = '';
if($filterFrom && $filterTo) $whereDate = "AND DATE(k.tanggal) BETWEEN '$filterFrom' AND '$filterTo'";
elseif($filterFrom) $whereDate = "AND DATE(k.tanggal) >= '$filterFrom'";
elseif($filterTo)   $whereDate = "AND DATE(k.tanggal) <= '$filterTo'";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barang Keluar — DrizStock</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page-header">
  <h1>Barang Keluar</h1>
  <p>Catat setiap pengeluaran stok barang</p>
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
      <a href="keluar.php" class="export-btn"><i class="bi bi-x"></i> Reset</a>
      <?php endif; ?>
    </form>
  </div>
</div>

<div class="card-modern">
  <div class="card-modern-header">
    <h5><i class="bi bi-box-arrow-up"></i> Riwayat Keluar</h5>
    <div class="d-flex gap-2 flex-wrap align-items-center">
      <div class="export-bar">
        <button class="export-btn copy"  onclick="triggerBtn(0)"><i class="bi bi-clipboard"></i> Salin</button>
        <button class="export-btn csv"   onclick="triggerBtn(1)"><i class="bi bi-filetype-csv"></i> CSV</button>
        <button class="export-btn excel" onclick="triggerBtn(2)"><i class="bi bi-file-earmark-excel"></i> Excel</button>
        <button class="export-btn pdf"   onclick="triggerBtn(3)"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
        <button class="export-btn print" onclick="triggerBtn(4)"><i class="bi bi-printer"></i> Print</button>
      </div>
      <button class="btn-primary-modern" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg"></i> Tambah
      </button>
    </div>
  </div>
  <div class="card-modern-body">
    <table id="keluarTable" class="table table-bordered w-100">
      <thead>
        <tr>
          <th width="50">No</th>
          <th width="55">Foto</th>
          <th>Nama Barang</th>
          <th width="100">Jumlah</th>
          <th>Penerima</th>
          <th>Tanggal</th>
          <th width="100">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $rows = mysqli_query($conn, "SELECT k.*, s.namabarang, s.image FROM keluar k LEFT JOIN stock s ON k.idbarang=s.idbarang WHERE 1=1 $whereDate ORDER BY k.idkeluar DESC");
        $i=1;
        while($d = mysqli_fetch_array($rows)):
          $imgPath  = 'uploads/' . ($d['image'] ?? 'no-image.png');
          $imgExist = ($d['image'] && $d['image'] !== 'no-image.png' && file_exists($imgPath));
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
        <td><span class="stock-badge stock-low"><i class="bi bi-dash-circle-fill"></i> <?= number_format($d['jumlah']) ?></span></td>
        <td style="font-size:0.82rem;color:var(--text-muted)"><?= htmlspecialchars($d['penerima']) ?></td>
        <td style="font-size:0.82rem;color:var(--text-muted)"><?= date('d M Y', strtotime($d['tanggal'])) ?></td>
        <td>
          <button class="btn btn-sm me-1" style="border-radius:6px;border:1px solid #e5e7eb;color:#6b7280;font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $d['idkeluar'] ?>"><i class="bi bi-pencil"></i></button>
          <button class="btn btn-sm" style="border-radius:6px;border:1px solid #fecaca;color:#ef4444;font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $d['idkeluar'] ?>"><i class="bi bi-trash"></i></button>
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
    <div class="modal-header"><h5 class="modal-title">Tambah Barang Keluar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
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
      <div class="mb-3"><label class="form-label">Tanggal</label><input type="datetime-local" class="form-control" name="tanggal" value="<?= date('Y-m-d\TH:i') ?>" required></div>
      <div class="mb-3"><label class="form-label">Jumlah</label><input type="number" class="form-control" name="jumlah" placeholder="0" min="1" required></div>
      <div class="mb-3"><label class="form-label">Penerima</label><input type="text" class="form-control" name="penerima" placeholder="Nama penerima..."></div>
      <button type="submit" class="btn-primary-modern w-100 justify-content-center" name="addkeluar"><i class="bi bi-check-lg"></i> Simpan</button>
    </div></form>
  </div></div>
</div>

<!-- Modal Edit & Hapus -->
<?php
$rows2 = mysqli_query($conn, "SELECT k.*, s.namabarang FROM keluar k LEFT JOIN stock s ON k.idbarang=s.idbarang ORDER BY k.idkeluar DESC");
while($d = mysqli_fetch_array($rows2)):
?>
<div class="modal fade modal-modern" id="modalEdit<?= $d['idkeluar'] ?>" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Edit Barang Keluar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post"><div class="modal-body">
      <div class="mb-3"><label class="form-label">Tanggal</label><input type="datetime-local" class="form-control" name="tanggal" value="<?= date('Y-m-d\TH:i', strtotime($d['tanggal'])) ?>" required></div>
      <div class="mb-3"><label class="form-label">Jumlah</label><input type="number" class="form-control" name="jumlah" value="<?= $d['jumlah'] ?>" min="1" required></div>
      <div class="mb-3"><label class="form-label">Penerima</label><input type="text" class="form-control" name="penerima" value="<?= htmlspecialchars($d['penerima']) ?>"></div>
      <input type="hidden" name="idkeluar" value="<?= $d['idkeluar'] ?>">
      <input type="hidden" name="idbarang" value="<?= $d['idbarang'] ?>">
      <input type="hidden" name="oldjumlah" value="<?= $d['jumlah'] ?>">
      <button type="submit" class="btn-primary-modern w-100 justify-content-center" name="updatekeluar"><i class="bi bi-check-lg"></i> Simpan</button>
    </div></form>
  </div></div>
</div>
<div class="modal fade modal-modern" id="modalHapus<?= $d['idkeluar'] ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Hapus Data</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post"><div class="modal-body text-center py-3">
      <div style="width:52px;height:52px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;"><i class="bi bi-trash" style="font-size:1.3rem;color:#ef4444;"></i></div>
      <p style="font-size:0.88rem;font-weight:600;margin-bottom:0.25rem;">Hapus data keluar <strong><?= htmlspecialchars($d['namabarang']) ?></strong>?</p>
      <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:1rem;">Stok akan dikembalikan <?= $d['jumlah'] ?> unit.</p>
      <input type="hidden" name="idkeluar" value="<?= $d['idkeluar'] ?>">
      <input type="hidden" name="idbarang" value="<?= $d['idbarang'] ?>">
      <input type="hidden" name="jumlah" value="<?= $d['jumlah'] ?>">
      <button type="submit" class="btn btn-danger w-100" style="border-radius:8px;font-size:0.85rem;font-weight:600;" name="deletekeluar"><i class="bi bi-trash me-1"></i> Hapus</button>
    </div></form>
  </div></div>
</div>
<?php endwhile; ?>

</div></div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script>
var dt;
$(document).ready(function(){
  dt=$('#keluarTable').DataTable({dom:'Blfrtip',language:{search:"",searchPlaceholder:"Cari...",lengthMenu:"Tampilkan _MENU_ data",info:"_START_–_END_ dari _TOTAL_ data",paginate:{previous:'<i class="bi bi-chevron-left"></i>',next:'<i class="bi bi-chevron-right"></i>'}},buttons:[{extend:'copy',text:'Salin',className:'btn btn-sm'},{extend:'csv',text:'CSV',className:'btn btn-sm',filename:'drizstock-keluar'},{extend:'excel',text:'Excel',className:'btn btn-sm',filename:'drizstock-keluar',title:'DrizStock — Barang Keluar'},{extend:'pdf',text:'PDF',className:'btn btn-sm',filename:'drizstock-keluar',title:'DrizStock — Barang Keluar'},{extend:'print',text:'Print',className:'btn btn-sm'}],columnDefs:[{orderable:false,targets:[1,6]}]});
});
function triggerBtn(i){dt.button(i).trigger();}
</script>
</body>
</html>