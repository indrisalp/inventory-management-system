<?php

$conn = mysqli_connect("localhost", "root", "", "stock_barang");
if(!$conn){ die("Koneksi gagal: " . mysqli_connect_error()); }

// ===================== HELPER UPLOAD =====================
function handleUpload($fieldName, $redirectTo='index.php'){
    if(isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === 0){
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $ext     = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
        $ukuran  = $_FILES[$fieldName]['size'];
        if(!in_array($ext, $allowed)){
            echo "<script>alert('Format gambar tidak didukung!');window.location.href='$redirectTo';</script>"; exit;
        }
        if($ukuran > 2 * 1024 * 1024){
            echo "<script>alert('Ukuran gambar maksimal 2MB!');window.location.href='$redirectTo';</script>"; exit;
        }
        if(!is_dir('uploads/')){ mkdir('uploads/', 0755, true); }
        $filename = md5(uniqid('', true) . time()) . '.' . $ext;
        move_uploaded_file($_FILES[$fieldName]['tmp_name'], 'uploads/' . $filename);
        return $filename;
    }
    return null;
}

// ===================== STOCK BARANG =====================

if(isset($_POST['addnewbarang'])){
    $namabarang = mysqli_real_escape_string($conn, $_POST['namabarang']);
    $deskripsi  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $stock      = (int)$_POST['stock'];
    $image      = handleUpload('image', 'index.php') ?? 'no-image.png';
    $q = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, image) VALUES ('$namabarang','$deskripsi','$stock','$image')");
    if($q){ header("location:index.php"); exit; }
}

if(isset($_POST['updatebarang'])){
    $idb        = (int)$_POST['idb'];
    $namabarang = mysqli_real_escape_string($conn, $_POST['namabarang']);
    $deskripsi  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $newImg     = handleUpload('image', 'index.php');
    if($newImg){
        $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM stock WHERE idbarang='$idb'"));
        if($old && $old['image'] !== 'no-image.png' && file_exists('uploads/'.$old['image'])) unlink('uploads/'.$old['image']);
        $q = mysqli_query($conn, "UPDATE stock SET namabarang='$namabarang', deskripsi='$deskripsi', image='$newImg' WHERE idbarang='$idb'");
    } else {
        $q = mysqli_query($conn, "UPDATE stock SET namabarang='$namabarang', deskripsi='$deskripsi' WHERE idbarang='$idb'");
    }
    if($q){ header("location:index.php"); exit; }
}

if(isset($_POST['deletebarang'])){
    $idb = (int)$_POST['idb'];
    $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM stock WHERE idbarang='$idb'"));
    if($old && $old['image'] !== 'no-image.png' && file_exists('uploads/'.$old['image'])) unlink('uploads/'.$old['image']);
    mysqli_query($conn, "DELETE FROM stock WHERE idbarang='$idb'");
    header("location:index.php"); exit;
}

// ===================== BARANG MASUK =====================

if(isset($_POST['addmasuk'])){
    $idb        = (int)$_POST['idbarang'];
    $tanggal    = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jumlah     = (int)$_POST['jumlah'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $row = mysqli_fetch_array(mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idb'"));
    $stokbaru = $row['stock'] + $jumlah;
    $q1 = mysqli_query($conn, "INSERT INTO masuk (idbarang, tanggal, jumlah, keterangan) VALUES ('$idb','$tanggal','$jumlah','$keterangan')");
    $q2 = mysqli_query($conn, "UPDATE stock SET stock='$stokbaru' WHERE idbarang='$idb'");
    if($q1 && $q2){ header("location:masuk.php"); exit; }
}

if(isset($_POST['updatemasuk'])){
    $idm       = (int)$_POST['idmasuk'];
    $idb       = (int)$_POST['idbarang'];
    $tanggal   = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jumlah    = (int)$_POST['jumlah'];
    $keterangan= mysqli_real_escape_string($conn, $_POST['keterangan']);
    $oldjumlah = (int)$_POST['oldjumlah'];
    $row = mysqli_fetch_array(mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idb'"));
    $stokbaru = $row['stock'] + ($jumlah - $oldjumlah);
    $q1 = mysqli_query($conn, "UPDATE masuk SET tanggal='$tanggal', jumlah='$jumlah', keterangan='$keterangan' WHERE idmasuk='$idm'");
    $q2 = mysqli_query($conn, "UPDATE stock SET stock='$stokbaru' WHERE idbarang='$idb'");
    if($q1 && $q2){ header("location:masuk.php"); exit; }
}

if(isset($_POST['deletemasuk'])){
    $idm    = (int)$_POST['idmasuk'];
    $idb    = (int)$_POST['idbarang'];
    $jumlah = (int)$_POST['jumlah'];
    $row = mysqli_fetch_array(mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idb'"));
    $stokbaru = $row['stock'] - $jumlah;
    mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk='$idm'");
    mysqli_query($conn, "UPDATE stock SET stock='$stokbaru' WHERE idbarang='$idb'");
    header("location:masuk.php"); exit;
}

// ===================== BARANG KELUAR =====================

if(isset($_POST['addkeluar'])){
    $idb      = (int)$_POST['idbarang'];
    $tanggal  = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jumlah   = (int)$_POST['jumlah'];
    $penerima = mysqli_real_escape_string($conn, $_POST['penerima']);
    $row = mysqli_fetch_array(mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idb'"));
    $stoksekarang = $row['stock'];
    if($jumlah > $stoksekarang){
        echo "<script>alert('Stok tidak mencukupi! Stok tersedia: $stoksekarang');window.location.href='keluar.php';</script>"; exit;
    }
    $stokbaru = $stoksekarang - $jumlah;
    $q1 = mysqli_query($conn, "INSERT INTO keluar (idbarang, tanggal, jumlah, penerima) VALUES ('$idb','$tanggal','$jumlah','$penerima')");
    $q2 = mysqli_query($conn, "UPDATE stock SET stock='$stokbaru' WHERE idbarang='$idb'");
    if($q1 && $q2){ header("location:keluar.php"); exit; }
}

if(isset($_POST['updatekeluar'])){
    $idk       = (int)$_POST['idkeluar'];
    $idb       = (int)$_POST['idbarang'];
    $tanggal   = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jumlah    = (int)$_POST['jumlah'];
    $penerima  = mysqli_real_escape_string($conn, $_POST['penerima']);
    $oldjumlah = (int)$_POST['oldjumlah'];
    $row = mysqli_fetch_array(mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idb'"));
    $stokbaru = $row['stock'] + $oldjumlah - $jumlah;
    if($stokbaru < 0){
        echo "<script>alert('Stok tidak mencukupi!');window.location.href='keluar.php';</script>"; exit;
    }
    $q1 = mysqli_query($conn, "UPDATE keluar SET tanggal='$tanggal', jumlah='$jumlah', penerima='$penerima' WHERE idkeluar='$idk'");
    $q2 = mysqli_query($conn, "UPDATE stock SET stock='$stokbaru' WHERE idbarang='$idb'");
    if($q1 && $q2){ header("location:keluar.php"); exit; }
}

if(isset($_POST['deletekeluar'])){
    $idk    = (int)$_POST['idkeluar'];
    $idb    = (int)$_POST['idbarang'];
    $jumlah = (int)$_POST['jumlah'];
    $row = mysqli_fetch_array(mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idb'"));
    $stokbaru = $row['stock'] + $jumlah;
    mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar='$idk'");
    mysqli_query($conn, "UPDATE stock SET stock='$stokbaru' WHERE idbarang='$idb'");
    header("location:keluar.php"); exit;
}

// ===================== PEMINJAMAN =====================

if(isset($_POST['addpinjam'])){
    $idb      = (int)$_POST['idbarang'];
    $tanggal  = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jumlah   = (int)$_POST['jumlah'];
    $kepada   = mysqli_real_escape_string($conn, $_POST['kepada']);
    $nohp     = mysqli_real_escape_string($conn, $_POST['nohp']);
    $row = mysqli_fetch_array(mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idb'"));
    $stoksekarang = $row['stock'];
    if($jumlah > $stoksekarang){
        echo "<script>alert('Stok tidak mencukupi! Stok tersedia: $stoksekarang');window.location.href='pinjam.php';</script>"; exit;
    }
    $stokbaru = $stoksekarang - $jumlah;
    $q1 = mysqli_query($conn, "INSERT INTO pinjam (idbarang, tanggal, jumlah, kepada, nohp, status) VALUES ('$idb','$tanggal','$jumlah','$kepada','$nohp','Dipinjam')");
    $q2 = mysqli_query($conn, "UPDATE stock SET stock='$stokbaru' WHERE idbarang='$idb'");
    if($q1 && $q2){ header("location:pinjam.php"); exit; }
}

if(isset($_POST['selesaipinjam'])){
    $idp    = (int)$_POST['idpinjam'];
    $idb    = (int)$_POST['idbarang'];
    $jumlah = (int)$_POST['jumlah'];
    $row = mysqli_fetch_array(mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idb'"));
    $stokbaru = $row['stock'] + $jumlah;
    $q1 = mysqli_query($conn, "UPDATE pinjam SET status='Dikembalikan' WHERE idpinjam='$idp'");
    $q2 = mysqli_query($conn, "UPDATE stock SET stock='$stokbaru' WHERE idbarang='$idb'");
    if($q1 && $q2){ header("location:pinjam.php"); exit; }
}

if(isset($_POST['deletepinjam'])){
    $idp    = (int)$_POST['idpinjam'];
    $idb    = (int)$_POST['idbarang'];
    $jumlah = (int)$_POST['jumlah'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    // Kembalikan stok hanya jika masih berstatus Dipinjam
    if($status === 'Dipinjam'){
        $row = mysqli_fetch_array(mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$idb'"));
        $stokbaru = $row['stock'] + $jumlah;
        mysqli_query($conn, "UPDATE stock SET stock='$stokbaru' WHERE idbarang='$idb'");
    }
    mysqli_query($conn, "DELETE FROM pinjam WHERE idpinjam='$idp'");
    header("location:pinjam.php"); exit;
}

// ===================== ADMIN =====================

if(isset($_POST['addadmin'])){
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $q = mysqli_query($conn, "INSERT INTO login (email, password) VALUES ('$email','$password')");
    if($q){ header("location:admin.php"); exit; }
}

if(isset($_POST['updateadmin'])){
    $id          = (int)$_POST['iduser'];
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $password    = mysqli_real_escape_string($conn, $_POST['password']);
    $oldpassword = mysqli_real_escape_string($conn, $_POST['oldpassword']);
    $pw = empty($password) ? $oldpassword : $password;
    $q = mysqli_query($conn, "UPDATE login SET email='$email', password='$pw' WHERE iduser='$id'");
    if($q){ header("location:admin.php"); exit; }
}

if(isset($_POST['deleteadmin'])){
    $id = (int)$_POST['iduser'];
    mysqli_query($conn, "DELETE FROM login WHERE iduser='$id'");
    header("location:admin.php"); exit;
}
?>