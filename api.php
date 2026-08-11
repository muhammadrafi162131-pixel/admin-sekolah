<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_kasir_tu";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Koneksi Database Gagal: " . $conn->connect_error]);
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// 1. GET ALL TRANSAKSI
if ($action === 'get_transaksi') {
    $sql = "SELECT id_transaksi AS id, jenis_kas AS jenisKas, tanggal, kategori, nama_pihak AS nama, nominal AS jumlah, keterangan, waktu_input AS waktuInput FROM transaksi_tu ORDER BY tanggal ASC, waktu_input ASC";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['jumlah'] = (float)$row['jumlah'];
        $row['waktuInput'] = (int)$row['waktuInput'];
        $data[] = $row;
    }
    echo json_encode($data);
    exit();
}

// 2. ADD TRANSAKSI
if ($action === 'add_transaksi' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = uniqid('TRX-');
    $jenisKas = $conn->real_escape_string($input['jenisKas']);
    $tanggal = $conn->real_escape_string($input['tanggal']);
    $kategori = $conn->real_escape_string($input['kategori']);
    $nama = $conn->real_escape_string($input['nama']);
    $jumlah = (float)$input['jumlah'];
    $keterangan = $conn->real_escape_string($input['keterangan']);
    $waktuInput = time() * 1000;

    $sql = "INSERT INTO transaksi_tu (id_transaksi, jenis_kas, tanggal, kategori, nama_pihak, nominal, keterangan, waktu_input) 
            VALUES ('$id', '$jenisKas', '$tanggal', '$kategori', '$nama', $jumlah, '$keterangan', $waktuInput)";
            
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "id" => $id]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit();
}

// 3. DELETE TRANSAKSI
if ($action === 'delete_transaksi' && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "DELETE FROM transaksi_tu WHERE id_transaksi = '$id'";
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit();
}

// 4. GET MASTER SISWA
if ($action === 'get_siswa') {
    $sql = "SELECT id_siswa AS id, nama_siswa AS `Nama Siswa`, kelas AS Kelas FROM master_siswa ORDER BY nama_siswa ASC";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit();
}

// 5. ADD MASTER SISWA
if ($action === 'add_siswa' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $nama = $conn->real_escape_string($input['Nama Siswa']);
    $kelas = $conn->real_escape_string($input['Kelas']);

    $sql = "INSERT INTO master_siswa (nama_siswa, kelas) VALUES ('$nama', '$kelas')";
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit();
}

// 6. DELETE SISWA
if ($action === 'delete_siswa' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "DELETE FROM master_siswa WHERE id_siswa = $id";
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit();
}

// 7. GET MASTER KATEGORI
if ($action === 'get_kategori') {
    $sql = "SELECT id_kategori AS id, nama_kategori AS namaKategori, jenis_kas AS jenisKas FROM master_kategori ORDER BY nama_kategori ASC";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit();
}

// 8. DELETE KATEGORI
if ($action === 'delete_kategori' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "DELETE FROM master_kategori WHERE id_kategori = $id";
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    exit();
}

$conn->close();
?>
