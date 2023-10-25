<?php
require "../functions.php";

$dataSupplier = query("SELECT * FROM supplier");

if (isset($_POST["batal"])) {
    header("Location: index.php");
}

if (isset($_POST["submit"])) {
    if (!empty($_POST["kode_barang"])
        && !empty($_POST["nama_barang"])
        && !empty($_POST["harga_barang"])
        && !empty($_POST["stok_barang"])
        && !empty($_POST["supplier"])) {
        $result = tambahDataBarang($_POST);
        if ($result > 0) {
            mysqli_close($conn);
            echo '<script>alert("Data berhasil ditambahkan"); window.location.href = "index.php";</script>';
        } else {
            echo '<script>alert("Data Gagal ditambahkan");</script>';
        }
    } else {
        echo '<script>alert("Semua inputan harus diisi");</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Supplier</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap");
        * {list-style: none; margin: 0;padding: 0;text-decoration: none;font-family: "poppins", sans-serif;}
        .container {height: 100vh;display: flex;justify-content: center;flex-direction: column;align-items: center;}
        h1 {margin: 1rem;}
        form {margin: 1rem;}
        td {padding: 0.8rem;}
        button {padding: 0.5rem 1rem;color: white;border: none;border-radius: 5px;cursor: pointer;}
        input {padding: 5px 0 10px 5px;width: 300px;}
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah data Barang</h1>
        <form action="" method="post">
            <table>
                <tr>
                    <td><label for="kode_barang">Kode Barang</label></td>
                    <td><input type="text" placeholder="kode barang" name="kode_barang" id="kode_barang"></td>
                </tr>
                <tr>
                    <td><label for="nama_barang">Nama Barang</label></td>
                    <td><input type="text" placeholder="nama barang" name="nama_barang" id="nama_barang"></td>
                </tr>
                <tr>
                    <td><label for="harga">Harga Barang</label></td>
                    <td><input type="number" placeholder="harga barang" name="harga_barang" id="harga_barang"></td>
                </tr>
                <tr>
                    <td><label for="stok">Stok Barang</label></td>
                    <td><input type="number" name="stok_barang" id="stok" placeholder="stok barang"></td>

                </tr>
                <tr>
                    <td><label for="supplier">Supplier</label></td>
                    <td>
                        <select name="supplier" id="supplier">
                            <option value="" disabled selected>---Pilih Supplier---</option>
                            <?php foreach( $dataSupplier as $supplier) : ?>
                                <option value="<?= $supplier["id"] ?>"><?= $supplier["id"] ?> - <?= $supplier["nama"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <button style="background-color: rgb(2, 142, 2);" type="submit" class="simpan" name="submit">Simpan</button>
                        <button style="background-color: rgb(203, 3, 3);" class="batal" name="batal">Batal</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>

</html>