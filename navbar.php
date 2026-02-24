<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap');

  :root {
    --sidebar-bg: #1a2e2a;
    --sidebar-hover: #243d38;
    --sidebar-active: #34d399;
    --sidebar-text: #6b9e8f;
    --sidebar-text-active: #ffffff;
    --sidebar-border: #223530;
    --accent: #34d399;
    --accent-dark: #059669;
    --accent-soft: rgba(52,211,153,0.1);
    --surface: #f9fafb;
    --card-bg: #ffffff;
    --border: #e5e7eb;
    --text-main: #0f172a;
    --text-muted: #6b7280;
    --danger: #ef4444;
    --warning: #f59e0b;
    --sidebar-width: 64px;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--surface);
    color: var(--text-main);
    min-height: 100vh;
    display: flex;
  }

  /* ===== SIDEBAR ===== */
  .sidebar {
    width: var(--sidebar-width);
    background: var(--sidebar-bg);
    min-height: 100vh;
    position: fixed;
    left: 0; top: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 300;
    border-right: 1px solid var(--sidebar-border);
  }

  .sidebar-logo {
    width: 100%;
    padding: 1.1rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid var(--sidebar-border);
    margin-bottom: 0.5rem;
  }
  .logo-icon {
    width: 36px; height: 36px;
    background: var(--accent);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; color: #1a2e2a; font-weight: 800;
    letter-spacing: -1px;
  }

  .sidebar-nav {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 0;
    width: 100%;
  }

  .nav-item {
    width: 44px; height: 44px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 10px;
    color: var(--sidebar-text);
    text-decoration: none;
    font-size: 1.2rem;
    transition: all 0.15s ease;
    position: relative;
  }
  .nav-item:hover {
    background: var(--sidebar-hover);
    color: #fff;
  }
  .nav-item.active {
    background: var(--accent-soft);
    color: var(--accent);
  }
  .nav-item::after {
    content: attr(data-label);
    position: absolute;
    left: calc(100% + 12px);
    background: #0f2027;
    color: #fff;
    padding: 0.3rem 0.7rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s;
    font-family: 'DM Sans', sans-serif;
  }
  .nav-item:hover::after { opacity: 1; }

  .sidebar-divider {
    width: 32px; height: 1px;
    background: var(--sidebar-border);
    margin: 0.4rem 0;
  }

  .sidebar-bottom {
    padding: 0.75rem 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    border-top: 1px solid var(--sidebar-border);
    width: 100%;
  }
  .avatar-btn {
    width: 34px; height: 34px;
    background: var(--accent-soft);
    border: 1.5px solid var(--accent);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: var(--accent); font-size: 0.75rem; font-weight: 700;
    cursor: pointer; text-decoration: none;
    transition: all 0.15s;
    position: relative;
  }
  .avatar-btn:hover { background: var(--accent); color: #1a2e2a; }
  .avatar-btn::after {
    content: attr(data-label);
    position: absolute;
    left: calc(100% + 12px);
    background: #0f2027;
    color: #fff;
    padding: 0.3rem 0.7rem;
    border-radius: 6px;
    font-size: 0.75rem;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s;
  }
  .avatar-btn:hover::after { opacity: 1; }

  /* ===== MAIN ===== */
  .main-wrapper {
    margin-left: var(--sidebar-width);
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  /* ===== TOPBAR ===== */
  .topbar {
    background: var(--card-bg);
    border-bottom: 1px solid var(--border);
    padding: 0 1.75rem;
    height: 54px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 100;
  }
  .topbar-left { display: flex; align-items: center; gap: 0.75rem; }
  .topbar-brand {
    font-family: 'Instrument Serif', serif;
    font-size: 1.1rem;
    color: var(--text-main);
    letter-spacing: -0.3px;
  }
  .topbar-brand span { color: var(--accent-dark); }
  .topbar-page {
    font-size: 0.78rem;
    color: var(--text-muted);
    padding: 0.2rem 0.6rem;
    background: var(--surface);
    border-radius: 99px;
    border: 1px solid var(--border);
  }
  .topbar-right { display: flex; align-items: center; gap: 1rem; }
  .topbar-date { font-size: 0.78rem; color: var(--text-muted); }
  .topbar-user {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.8rem; color: var(--text-muted);
  }

  /* ===== PAGE ===== */
  .page-content { padding: 1.5rem 1.75rem; flex: 1; }
  .page-header { margin-bottom: 1.5rem; }
  .page-header h1 {
    font-family: 'Instrument Serif', serif;
    font-size: 1.6rem; font-weight: 400;
    color: var(--text-main); letter-spacing: -0.5px;
  }
  .page-header p { color: var(--text-muted); font-size: 0.83rem; margin-top: 0.2rem; }

  /* ===== STAT CARDS ===== */
  .stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.1rem 1.25rem;
    display: flex; align-items: center; gap: 1rem;
    transition: box-shadow 0.15s;
  }
  .stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
  .stat-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
  }
  .stat-icon.green { background: rgba(52,211,153,0.12); color: #059669; }
  .stat-icon.blue  { background: rgba(59,130,246,0.1);  color: #3b82f6; }
  .stat-icon.amber { background: rgba(245,158,11,0.1);  color: #d97706; }
  .stat-icon.red   { background: rgba(239,68,68,0.1);   color: #ef4444; }
  .stat-label { font-size: 0.7rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.6px; }
  .stat-value { font-size: 1.5rem; font-weight: 700; line-height: 1.1; color: var(--text-main); }

  /* ===== CARD ===== */
  .card-modern {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
  }
  .card-modern-header {
    padding: 0.9rem 1.25rem;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 0.75rem;
  }
  .card-modern-header h5 {
    font-size: 0.88rem; font-weight: 600; margin: 0;
    display: flex; align-items: center; gap: 0.4rem;
    color: var(--text-main);
  }
  .card-modern-body { padding: 1.1rem 1.25rem; }

  /* ===== BUTTONS ===== */
  .btn-primary-modern {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.42rem 0.9rem; border-radius: 8px;
    font-size: 0.8rem; font-weight: 600;
    background: var(--accent-dark); color: #fff; border: none;
    cursor: pointer; transition: all 0.15s; text-decoration: none;
    font-family: 'DM Sans', sans-serif;
  }
  .btn-primary-modern:hover { background: #047857; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(5,150,105,0.25); }

  /* ===== EXPORT BUTTONS ===== */
  .export-bar { display: flex; gap: 0.35rem; flex-wrap: wrap; }
  .export-btn {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.35rem 0.75rem; border-radius: 6px;
    font-size: 0.75rem; font-weight: 500;
    border: 1px solid var(--border); cursor: pointer;
    transition: all 0.15s; background: var(--surface);
    color: var(--text-muted); font-family: 'DM Sans', sans-serif;
  }
  .export-btn:hover { background: #fff; color: var(--text-main); border-color: #d1d5db; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
  .export-btn.excel:hover { color: #15803d; border-color: #bbf7d0; background: #f0fdf4; }
  .export-btn.pdf:hover   { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
  .export-btn.print:hover { color: #1d4ed8; border-color: #bfdbfe; background: #eff6ff; }

  /* ===== TABLE ===== */
  .dataTables_wrapper .dataTables_filter input {
    border: 1px solid var(--border); border-radius: 7px;
    padding: 0.35rem 0.75rem; font-size: 0.82rem; font-family: 'DM Sans', sans-serif;
    transition: border-color 0.15s; outline: none; background: var(--surface);
  }
  .dataTables_wrapper .dataTables_filter input:focus {
    border-color: var(--accent); box-shadow: 0 0 0 3px rgba(52,211,153,0.12);
  }
  .dataTables_wrapper .dataTables_length select {
    border: 1px solid var(--border); border-radius: 7px;
    padding: 0.32rem 2rem 0.32rem 0.65rem; font-family: 'DM Sans', sans-serif; font-size: 0.82rem;
    appearance: none; -webkit-appearance: none;
    background: var(--surface) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 16 16'%3E%3Cpath fill='%236b7280' d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E") no-repeat right 0.65rem center;
    color: var(--text-main); cursor: pointer; width: auto;
  }
  .dataTables_wrapper .dataTables_info,
  .dataTables_wrapper .dataTables_length label,
  .dataTables_wrapper .dataTables_filter label { font-size: 0.78rem; color: var(--text-muted); font-family: 'DM Sans', sans-serif; }

  table.dataTable thead th {
    background: var(--surface); color: var(--text-muted);
    font-size: 0.7rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.6px;
    border-bottom: 1px solid var(--border) !important; white-space: nowrap;
    font-family: 'DM Sans', sans-serif;
  }
  table.dataTable tbody tr { transition: background 0.1s; }
  table.dataTable tbody tr:hover td { background: #f0fdf9 !important; }
  table.dataTable tbody td {
    font-size: 0.83rem; vertical-align: middle;
    border-color: var(--border) !important; padding: 0.7rem 1rem !important;
    font-family: 'DM Sans', sans-serif;
  }

  /* Stock badge */
  .stock-badge {
    display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.22rem 0.65rem; border-radius: 999px;
    font-size: 0.75rem; font-weight: 600;
  }
  .stock-high   { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
  .stock-medium { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
  .stock-low    { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

  /* Gambar */
  .img-thumb {
    width: 40px; height: 40px; object-fit: cover;
    border-radius: 7px; border: 1px solid var(--border);
    cursor: pointer; transition: all 0.15s;
  }
  .img-thumb:hover { transform: scale(1.08); box-shadow: 0 2px 8px rgba(0,0,0,0.12); }
  .img-placeholder {
    width: 40px; height: 40px; background: var(--surface);
    border-radius: 7px; border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    color: #d1d5db; font-size: 1rem;
  }

  /* Modal */
  .modal-modern .modal-content {
    border: none; border-radius: 12px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.12);
  }
  .modal-modern .modal-header {
    border-bottom: 1px solid var(--border); padding: 0.9rem 1.25rem;
  }
  .modal-modern .modal-title { font-weight: 600; font-size: 0.9rem; font-family: 'DM Sans', sans-serif; }
  .modal-modern .modal-body { padding: 1.25rem; }
  .modal-modern .form-label { font-size: 0.77rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.3rem; text-transform: uppercase; letter-spacing: 0.4px; }
  .modal-modern .form-control, .modal-modern .form-select {
    border: 1px solid var(--border); border-radius: 8px;
    font-size: 0.85rem; padding: 0.5rem 0.75rem;
    font-family: 'DM Sans', sans-serif;
    transition: border-color 0.15s;
  }
  .modal-modern .form-control:focus, .modal-modern .form-select:focus {
    border-color: var(--accent); box-shadow: 0 0 0 3px rgba(52,211,153,0.12); outline: none;
  }

  /* Upload */
  .upload-area {
    border: 1.5px dashed #d1d5db; border-radius: 9px;
    padding: 1.5rem; text-align: center; cursor: pointer;
    transition: all 0.2s; background: var(--surface);
  }
  .upload-area:hover { border-color: var(--accent); background: #f0fdf9; }
  .upload-area i { font-size: 1.75rem; color: #9ca3af; margin-bottom: 0.4rem; display: block; }
  .upload-area p { font-size: 0.8rem; color: var(--text-muted); margin: 0; }
  .upload-area small { font-size: 0.7rem; color: #9ca3af; }
  .upload-preview { max-height: 100px; border-radius: 7px; margin-top: 0.65rem; display: none; }

  /* Alert */
  .alert-modern {
    display: flex; align-items: flex-start; gap: 0.65rem;
    padding: 0.75rem 1rem; border-radius: 8px;
    border: 1px solid; margin-bottom: 0.5rem; font-size: 0.82rem;
  }
  .alert-modern.danger { background: #fef2f2; border-color: #fecaca; color: #991b1b; }

  /* Pagination */
  .page-link {
    font-size: 0.78rem; border-radius: 6px !important;
    margin: 0 1px; border-color: var(--border); color: var(--text-muted);
    font-family: 'DM Sans', sans-serif;
  }
  .page-link:hover { color: var(--accent-dark); border-color: var(--accent); background: #f0fdf9; }
  .page-item.active .page-link { background: var(--accent-dark); border-color: var(--accent-dark); color: #fff; }

  .dt-buttons { display: none !important; }

  @media (max-width: 768px) {
    .main-wrapper { margin-left: 0; }
  }
</style>

<!-- Sidebar -->
<div class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">DS</div>
  </div>
  <nav class="sidebar-nav">
    <a href="index.php" class="nav-item <?= $current=='index.php'?'active':'' ?>" data-label="Stock Barang">
      <i class="bi bi-grid-fill"></i>
    </a>
    <a href="masuk.php" class="nav-item <?= $current=='masuk.php'?'active':'' ?>" data-label="Barang Masuk">
      <i class="bi bi-box-arrow-in-down"></i>
    </a>
    <a href="keluar.php" class="nav-item <?= $current=='keluar.php'?'active':'' ?>" data-label="Barang Keluar">
      <i class="bi bi-box-arrow-up"></i>
    </a>
    <a href="pinjam.php" class="nav-item <?= $current=='pinjam.php'?'active':'' ?>" data-label="Peminjaman">
      <i class="bi bi-clipboard-check"></i>
    </a>
    <div class="sidebar-divider"></div>
    <a href="admin.php" class="nav-item <?= $current=='admin.php'?'active':'' ?>" data-label="Kelola Admin">
      <i class="bi bi-people-fill"></i>
    </a>
  </nav>
  <div class="sidebar-bottom">
    <a href="logout.php" class="avatar-btn" data-label="Logout — <?= htmlspecialchars($_SESSION['log'] ?? 'Admin') ?>">
      <?= strtoupper(substr($_SESSION['log'] ?? 'A', 0, 1)) ?>
    </a>
  </div>
</div>

<!-- Main -->
<div class="main-wrapper">
  <div class="topbar">
    <div class="topbar-left">
      <span class="topbar-brand">Driz<span>Stock</span></span>
      <span class="topbar-page"><?= $pageTitle ?? 'Dashboard' ?></span>
    </div>
    <div class="topbar-right">
      <span class="topbar-date"><i class="bi bi-calendar3 me-1"></i><?= date('d M Y') ?></span>
      <span class="topbar-user"><i class="bi bi-person-circle"></i><?= htmlspecialchars($_SESSION['log'] ?? 'Admin') ?></span>
    </div>
  </div>
  <div class="page-content">