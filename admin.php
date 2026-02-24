<?php
session_start();
require 'function.php';
if(!isset($_SESSION['log'])){ header("location:login.php"); exit; }
$pageTitle = 'Kelola Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Admin — DrizStock</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page-header">
  <h1>Kelola Admin</h1>
  <p>Manajemen akun administrator DrizStock</p>
</div>

<div class="card-modern">
  <div class="card-modern-header">
    <h5><i class="bi bi-people-fill"></i> Daftar Admin</h5>
    <button class="btn-primary-modern" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-lg"></i> Tambah Admin
    </button>
  </div>
  <div class="card-modern-body">
    <table id="adminTable" class="table table-bordered w-100">
      <thead>
        <tr>
          <th width="50">No</th>
          <th>Email</th>
          <th width="120">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $rows = mysqli_query($conn, "SELECT * FROM login ORDER BY iduser ASC");
        $i=1;
        while($d = mysqli_fetch_array($rows)):
      ?>
      <tr>
        <td class="text-center text-muted"><?= $i++ ?></td>
        <td>
          <div class="d-flex align-items-center gap-2">
            <div style="width:30px;height:30px;border-radius:50%;background:rgba(52,211,153,0.12);border:1px solid rgba(52,211,153,0.3);display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#059669;">
              <?= strtoupper(substr($d['email'],0,1)) ?>
            </div>
            <span style="font-size:0.85rem"><?= htmlspecialchars($d['email']) ?></span>
          </div>
        </td>
        <td>
          <button class="btn btn-sm me-1" style="border-radius:6px;border:1px solid #e5e7eb;color:#6b7280;font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $d['iduser'] ?>"><i class="bi bi-pencil"></i></button>
          <button class="btn btn-sm" style="border-radius:6px;border:1px solid #fecaca;color:#ef4444;font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $d['iduser'] ?>"><i class="bi bi-trash"></i></button>
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
    <div class="modal-header"><h5 class="modal-title">Tambah Admin</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post"><div class="modal-body">
      <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" placeholder="admin@email.com" required></div>
      <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" placeholder="••••••••" required></div>
      <button type="submit" class="btn-primary-modern w-100 justify-content-center" name="addadmin"><i class="bi bi-check-lg"></i> Simpan</button>
    </div></form>
  </div></div>
</div>

<!-- Modal Edit & Hapus -->
<?php
$rows2 = mysqli_query($conn, "SELECT * FROM login ORDER BY iduser ASC");
while($d = mysqli_fetch_array($rows2)):
?>
<div class="modal fade modal-modern" id="modalEdit<?= $d['iduser'] ?>" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Edit Admin</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post"><div class="modal-body">
      <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= htmlspecialchars($d['email']) ?>" required></div>
      <div class="mb-3">
        <label class="form-label">Password <span style="color:#9ca3af;font-size:0.7rem;text-transform:none;">(kosongkan jika tidak diganti)</span></label>
        <input type="password" class="form-control" name="password" placeholder="••••••••">
      </div>
      <input type="hidden" name="iduser" value="<?= $d['iduser'] ?>">
      <input type="hidden" name="oldpassword" value="<?= $d['password'] ?>">
      <button type="submit" class="btn-primary-modern w-100 justify-content-center" name="updateadmin"><i class="bi bi-check-lg"></i> Simpan</button>
    </div></form>
  </div></div>
</div>
<div class="modal fade modal-modern" id="modalHapus<?= $d['iduser'] ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Hapus Admin</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post"><div class="modal-body text-center py-3">
      <div style="width:52px;height:52px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;"><i class="bi bi-person-x" style="font-size:1.3rem;color:#ef4444;"></i></div>
      <p style="font-size:0.88rem;font-weight:600;margin-bottom:0.25rem;">Hapus admin <strong><?= htmlspecialchars($d['email']) ?></strong>?</p>
      <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:1rem;">Admin tidak bisa login setelah dihapus.</p>
      <input type="hidden" name="iduser" value="<?= $d['iduser'] ?>">
      <button type="submit" class="btn btn-danger w-100" style="border-radius:8px;font-size:0.85rem;font-weight:600;" name="deleteadmin"><i class="bi bi-trash me-1"></i> Hapus</button>
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
  $('#adminTable').DataTable({language:{search:"",searchPlaceholder:"Cari admin...",lengthMenu:"Tampilkan _MENU_ data",info:"_START_–_END_ dari _TOTAL_ admin",paginate:{previous:'<i class="bi bi-chevron-left"></i>',next:'<i class="bi bi-chevron-right"></i>'}},columnDefs:[{orderable:false,targets:[2]}]});
});
</script>
</body>
</html>