<?php
// koneksi database
$conn = mysqli_connect("localhost","root","","db_menu");

function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function tambah($data) {
    global $conn;
    $nama = htmlspecialchars($data["nama"]);
    $jenis = htmlspecialchars($data["jenis"]);
    $harga = htmlspecialchars($data["harga"]);

    $query = "INSERT INTO menu VALUES('', '$jenis', '$nama', '$harga')";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function ubahMenu($data) {
    global $conn;
    $id = $data["id"];
    $nama = htmlspecialchars($data["nama"]);
    $jenis = htmlspecialchars($data["jenis"]);
    $harga = htmlspecialchars($data["harga"]);
    $query = "UPDATE menu SET
              nama = '$nama',
              jenis = '$jenis',
              harga = '$harga'
            WHERE id = $id
          ";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function hapusMenu($id) {
    global $conn;
    mysqli_query($conn, "DELETE FROM menu WHERE id = $id");
    return mysqli_affected_rows($conn);

}

function formatHarga($harga)
{
    return "Rp " . number_format($harga, 0, ",", ".");
}