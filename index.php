<?php
session_start();
require 'function.php';
if(!isset($_SESSION['log'])){ header("location:login.php"); exit; }
$pageTitle = 'Stock Barang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Stock Barang — DrizStock</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page-header">
  <h1>Stock Barang</h1>
  <p>Kelola dan pantau inventaris barang perpustakaan</p>
</div>

<?php
$alertstok = mysqli_query($conn, "SELECT * FROM stock WHERE stock < 1");
while($al = mysqli_fetch_array($alertstok)): ?>
<div class="alert-modern danger mb-2">
  <i class="bi bi-exclamation-triangle-fill"></i>
  <span>Stock <strong><?= htmlspecialchars($al['namabarang']) ?></strong> telah habis!</span>
</div>
<?php endwhile;

$total_items = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM stock"));
$low_stock   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM stock WHERE stock <= 10 AND stock > 0"));
$habis       = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM stock WHERE stock < 1"));
$ts          = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stock) as t FROM stock"));
$total_stock = $ts['t'] ?? 0;
?>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon green"><i class="bi bi-grid-fill"></i></div>
      <div><div class="stat-label">Total Item</div><div class="stat-value"><?= $total_items ?></div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="bi bi-stack"></i></div>
      <div><div class="stat-label">Total Stock</div><div class="stat-value"><?= number_format($total_stock) ?></div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon amber"><i class="bi bi-exclamation-triangle-fill"></i></div>
      <div><div class="stat-label">Menipis</div><div class="stat-value"><?= $low_stock ?></div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
      <div><div class="stat-label">Habis</div><div class="stat-value"><?= $habis ?></div></div>
    </div>
  </div>
</div>

<div class="card-modern">
  <div class="card-modern-header">
    <h5><i class="bi bi-table"></i> Daftar Barang</h5>
    <div class="d-flex gap-2 flex-wrap align-items-center">
      <div class="export-bar">
        <button class="export-btn copy"  onclick="triggerBtn(0)"><i class="bi bi-clipboard"></i> Salin</button>
        <button class="export-btn csv"   onclick="triggerBtn(1)"><i class="bi bi-filetype-csv"></i> CSV</button>
        <button class="export-btn excel" onclick="triggerBtn(2)"><i class="bi bi-file-earmark-excel"></i> Excel</button>
        <button class="export-btn pdf"   onclick="triggerBtn(3)"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
        <button class="export-btn print" onclick="triggerBtn(4)"><i class="bi bi-printer"></i> Print</button>
      </div>
      <button class="btn-primary-modern" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg"></i> Tambah Barang
      </button>
    </div>
  </div>
  <div class="card-modern-body">
    <table id="stockTable" class="table table-bordered w-100">
      <thead>
        <tr>
          <th width="50">No</th>
          <th width="60">Foto</th>
          <th>Nama Barang</th>
          <th>Deskripsi</th>
          <th width="120">Stock</th>
          <th width="120">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $rows = mysqli_query($conn, "SELECT * FROM stock ORDER BY namabarang ASC");
        $i = 1;
        while($d = mysqli_fetch_array($rows)):
          $nama   = htmlspecialchars($d['namabarang']);
          $desk   = htmlspecialchars($d['deskripsi']);
          $stok   = (int)$d['stock'];
          $idb    = $d['idbarang'];
          $image  = $d['image'] ?? 'no-image.png';
          $imgPath = 'uploads/' . $image;
          $imgExist = ($image !== 'no-image.png' && file_exists($imgPath));

          if($stok > 50)     { $bc='stock-high';   $bi='bi-check-circle-fill'; }
          elseif($stok > 10) { $bc='stock-medium'; $bi='bi-dash-circle-fill'; }
          else               { $bc='stock-low';    $bi='bi-exclamation-circle-fill'; }
      ?>
      <tr>
        <td class="text-center text-muted"><?= $i++ ?></td>
        <td class="text-center">
          <?php if($imgExist): ?>
            <img src="<?= $imgPath ?>" class="img-thumb" alt="<?= $nama ?>" data-bs-toggle="modal" data-bs-target="#imgModal<?= $idb ?>">
          <?php else: ?>
            <div class="img-placeholder"><i class="bi bi-image"></i></div>
          <?php endif; ?>
        </td>
        <td style="font-weight:500"><?= $nama ?></td>
        <td style="color:var(--text-muted); font-size:0.82rem"><?= $desk ?></td>
        <td><span class="stock-badge <?= $bc ?>"><i class="bi <?= $bi ?>"></i> <?= number_format($stok) ?></span></td>
        <td>
          <button class="btn btn-sm" style="border-radius:6px;border:1px solid #e5e7eb;color:#6b7280;font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $idb ?>">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm" style="border-radius:6px;border:1px solid #fecaca;color:#ef4444;font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $idb ?>">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>

      <?php if($imgExist): ?>
      <div class="modal fade modal-modern" id="imgModal<?= $idb ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><?= $nama ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body text-center p-3">
              <img src="<?= $imgPath ?>" style="max-width:100%;border-radius:8px;" alt="<?= $nama ?>">
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade modal-modern" id="modalTambah" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Tambah Barang</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" enctype="multipart/form-data"><div class="modal-body">
      <div class="mb-3"><label class="form-label">Nama Barang</label><input type="text" class="form-control" name="namabarang" placeholder="Nama barang" required></div>
      <div class="mb-3"><label class="form-label">Deskripsi</label><input type="text" class="form-control" name="deskripsi" placeholder="Deskripsi" required></div>
      <div class="mb-3"><label class="form-label">Stock Awal</label><input type="number" class="form-control" name="stock" placeholder="0" min="0" required></div>
      <div class="mb-3">
        <label class="form-label">Foto Barang <span style="color:#9ca3af;font-size:0.7rem;text-transform:none;">(opsional, maks 2MB)</span></label>
        <div class="upload-area" onclick="document.getElementById('imgInput').click()">
          <i class="bi bi-cloud-upload"></i>
          <p>Klik untuk upload foto</p>
          <small>JPG, PNG, GIF, WEBP</small>
          <img id="imgPreview" class="upload-preview">
        </div>
        <input type="file" id="imgInput" name="image" accept="image/*" style="display:none" onchange="previewImg(this,'imgPreview')">
      </div>
      <button type="submit" class="btn-primary-modern w-100 justify-content-center" name="addnewbarang"><i class="bi bi-check-lg"></i> Simpan</button>
    </div></form>
  </div></div>
</div>

<!-- Modal Edit & Hapus -->
<?php
$rows2 = mysqli_query($conn, "SELECT * FROM stock");
while($d = mysqli_fetch_array($rows2)):
  $nama=$d['namabarang']; $desk=$d['deskripsi']; $idb=$d['idbarang'];
  $image=$d['image']??'no-image.png'; $imgPath='uploads/'.$image;
?>
<div class="modal fade modal-modern" id="modalEdit<?= $idb ?>" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Edit Barang</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" enctype="multipart/form-data"><div class="modal-body">
      <div class="mb-3"><label class="form-label">Nama Barang</label><input type="text" class="form-control" name="namabarang" value="<?= htmlspecialchars($nama) ?>" required></div>
      <div class="mb-3"><label class="form-label">Deskripsi</label><input type="text" class="form-control" name="deskripsi" value="<?= htmlspecialchars($desk) ?>" required></div>
      <div class="mb-3">
        <label class="form-label">Ganti Foto <span style="color:#9ca3af;font-size:0.7rem;text-transform:none;">(kosongkan jika tidak diganti)</span></label>
        <?php if($image!=='no-image.png' && file_exists($imgPath)): ?>
          <div class="mb-2"><img src="<?= $imgPath ?>" style="height:50px;border-radius:7px;border:1px solid #e5e7eb;"></div>
        <?php endif; ?>
        <div class="upload-area" onclick="document.getElementById('imgEdit<?= $idb ?>').click()">
          <i class="bi bi-cloud-upload"></i><p>Klik untuk upload foto baru</p>
          <img id="prevEdit<?= $idb ?>" class="upload-preview">
        </div>
        <input type="file" id="imgEdit<?= $idb ?>" name="image" accept="image/*" style="display:none" onchange="previewImg(this,'prevEdit<?= $idb ?>')">
      </div>
      <input type="hidden" name="idb" value="<?= $idb ?>">
      <button type="submit" class="btn-primary-modern w-100 justify-content-center" name="updatebarang"><i class="bi bi-check-lg"></i> Simpan</button>
    </div></form>
  </div></div>
</div>

<div class="modal fade modal-modern" id="modalHapus<?= $idb ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Hapus Barang</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post"><div class="modal-body text-center py-3">
      <div style="width:52px;height:52px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;"><i class="bi bi-trash" style="font-size:1.3rem;color:#ef4444;"></i></div>
      <p style="font-size:0.88rem;font-weight:600;margin-bottom:0.25rem;">Hapus <strong><?= htmlspecialchars($nama) ?></strong>?</p>
      <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:1rem;">Data dan foto akan dihapus permanen.</p>
      <input type="hidden" name="idb" value="<?= $idb ?>">
      <button type="submit" class="btn btn-danger w-100" style="border-radius:8px;font-size:0.85rem;font-weight:600;" name="deletebarang"><i class="bi bi-trash me-1"></i> Hapus</button>
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
  dt=$('#stockTable').DataTable({dom:'Blfrtip',language:{search:"",searchPlaceholder:"Cari barang...",lengthMenu:"Tampilkan _MENU_ data",info:"_START_–_END_ dari _TOTAL_ barang",infoEmpty:"Tidak ada data",paginate:{previous:'<i class="bi bi-chevron-left"></i>',next:'<i class="bi bi-chevron-right"></i>'}},buttons:[{extend:'copy',text:'Salin',className:'btn btn-sm'},{extend:'csv',text:'CSV',className:'btn btn-sm',filename:'drizstock-barang'},{extend:'excel',text:'Excel',className:'btn btn-sm',filename:'drizstock-barang',title:'DrizStock — Stock Barang'},{extend:'pdf',text:'PDF',className:'btn btn-sm',filename:'drizstock-barang',title:'DrizStock — Stock Barang'},{extend:'print',text:'Print',className:'btn btn-sm'}],columnDefs:[{orderable:false,targets:[1,5]}]});
});
function triggerBtn(i){dt.button(i).trigger();}
function previewImg(input,id){if(input.files&&input.files[0]){var r=new FileReader();r.onload=function(e){var img=document.getElementById(id);img.src=e.target.result;img.style.display='block';};r.readAsDataURL(input.files[0]);}}
</script>
</body>
</html>