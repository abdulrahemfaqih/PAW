<?php
require("functions.php");

if (isset($_GET["id_order"])) {
    $id_order = $_GET["id_order"];
    $data_order_id = query("SELECT `order`.*, pelayan.nama_pelayan FROM `order` JOIN `pelayan` ON order.id_pelayan = pelayan.id_pelayan WHERE id_order = $id_order")[0];
    $tanggal = $data_order_id["tgl_order"];
    $jam = $data_order_id["jam_order"];
    $no_meja = $data_order_id["no_meja"];
    $pelayan = $data_order_id["nama_pelayan"];
    $total_bayar = $data_order_id["total_bayar"];
}

if (isset($_POST["menu"]) && isset($_POST["jumlah"]) && isset($_POST["tambahMenu"])) {
    $id_menu = $_POST["menu"];
    $stok_menu = query("SELECT stok FROM `menu` WHERE id_menu = $id_menu")[0];
    $stok_menu = $stok_menu["stok"];
    $jumlah = $_POST["jumlah"];
    if ($jumlah < $stok_menu) {
        $ambilHarga = query("SELECT harga FROM menu WHERE id_menu = $id_menu")[0];
        if (count($ambilHarga) > 0) {
            $harga = $ambilHarga["harga"];
            $subtotal = $jumlah * $harga;
            if (tambahOrderDetail($id_order, $id_menu, $harga, $jumlah, $subtotal) <= 0) {
                echo "menu gagal ditambahkan";
            }
        }
        $stok_menu = $stok_menu - $jumlah;
        updateStok($id_menu, $stok_menu);
        updateTotalBayar($id_order);
        $total_bayar = query("SELECT total_bayar FROM `order` WHERE id_order = $id_order")[0];
        $total_bayar = $total_bayar["total_bayar"];
    } else {
        echo "<script>alert('jumlah melebihi stok')</script>";
    }
}



if (isset($_GET["id_order_detil"])) {
    $id_order_detil = $_GET["id_order_detil"];
    hapusOrderDetil($id_order_detil);
    updateTotalBayar($id_order);
    if (isset($_GET["qr"]) && isset($_GET["tambahlagi"])) {
        header("Location: form_order_detil.php?id_order=" . $id_order . "&qr=true&tambahlagi=''");
    } else if (isset($_GET["tambahlagi"])) {
        header("Location: form_order_detil.php?id_order=" . $id_order . "&tambahlagi=''");
    } else if (isset($_GET["qr"])) {
        header("Location: form_order_detil.php?id_order=" . $id_order . "&qr=true");
    } else {
        header("Location: form_order_detil.php?id_order=" . $id_order);
    }
}


if (isset($_POST["batal"])) {
    hapusOrderByOrderId($id_order);
    header("Location: data_order.php");
}

if (isset($_POST["selesai"])) {
    if (isset($_GET["qr"])) {
        header("Location: detil_order_qr.php?id_order=" . $id_order . "&qr=true");
        exit;
    }
    header("Location: detil_order.php?id_order=" . $id_order);
}

$listMenu = query("SELECT * FROM menu ");
?>


<?php
$title = "FORM ORDER";
include "layout/header.php"
?>
<div class="container">
    <div class="card my-4">
        <h5 class="card-header">Form Order Detil</h5>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>ID ORDER</th>
                            <th>TANGGAL ORDER</th>
                            <th>JAM ORDER</th>
                            <th>NO MEJA</th>
                            <th>PELAYAN</th>
                            <th>TOTAL BAYAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $id_order ?></td>
                            <td><?= $tanggal ?></td>
                            <td><?= $jam ?></td>
                            <td><?= $no_meja ?></td>
                            <td><?= $pelayan ?></td>
                            <td><?= formatHarga($total_bayar) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <form action="" method="post">
                <p class="fw-bold text-primary">FORM TAMBAH MENU</p>
                <div class="table table-responsive">
                    <table class="table table-responsive table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th>PILIH MENU</th>
                                <th>JUMLAH</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select class="form-select" name="menu" id="menu">
                                        <option value="" disabled selected>--- Pilih Menu ---</option>
                                        <?php foreach ($listMenu as $menu) : ?>
                                            <option value="<?= $menu["id_menu"] ?>"><?= $menu["nama"] ?> - <?= $menu["harga"] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control" type="number" name="jumlah" value="1" min="1">
                                </td>
                                <td colspan="2">
                                    <button class="btn btn-primary btn-sm" type="submit" name="tambahMenu">Tambah</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="fw-bold text-success">MENU YANG BARU DITAMBAHKAN</p>
                <div class="table table-responsive">
                    <table class="table  table-bordered">
                        <?php
                        $data_order_detil = query("SELECT  order_detil.id_order_detil, order_detil.id_order, order_detil.subtotal, order_detil.id_menu, menu.nama, menu.harga, order_detil.jumlah
                            FROM order_detil
                            LEFT JOIN menu ON menu.id_menu = order_detil.id_menu
                            WHERE order_detil.id_order = $id_order
                            ORDER BY order_detil.id_order");
                        if (!empty($data_order_detil)) : ?>
                            <thead class="table-secondary">
                                <tr>
                                    <th>ID ORDER DETIL</th>
                                    <th>NAMA MENU</th>
                                    <th>JUMLAH</th>
                                    <th>HARGA SATUAN</th>
                                    <th>SUBTOTAL</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <?php
                            foreach ($data_order_detil as $detil) :
                            ?>
                                <tbody>
                                    <tr>
                                        <td><?= $detil["id_order_detil"] ?></td>
                                        <td><?= $detil["nama"] ?></td>
                                        <td><?= $detil["jumlah"] ?></td>
                                        <td><?= formatHarga($detil["harga"]) ?></td>
                                        <td><?= formatHarga($detil["subtotal"]) ?></td>
                                        <td>
                                            <?php if (isset($_GET["qr"]) && isset($_GET["tambahlagi"])) : ?>
                                                <a class="btn btn-danger btn-sm" href="form_order_detil.php?id_order=<?= $id_order ?>&id_order_detil=<?= $detil["id_order_detil"] ?>&qr=true&tambahlagi=''">Hapus</a>
                                            <?php elseif (isset($_GET["qr"])) : ?>
                                                <a class="btn btn-danger btn-sm" href="form_order_detil.php?id_order=<?= $id_order ?>&id_order_detil=<?= $detil["id_order_detil"] ?>&qr=true">Hapus</a>
                                            <?php elseif (isset($_GET["tambahlagi"])) : ?>
                                                <a class="btn btn-danger btn-sm" href="form_order_detil.php?id_order=<?= $id_order ?>&id_order_detil=<?= $detil["id_order_detil"] ?>&tambahlagi=''">Hapus</a>
                                            <?php else : ?>
                                                <a class="btn btn-danger btn-sm" href="form_order_detil.php?id_order=<?= $id_order ?>&id_order_detil=<?= $detil["id_order_detil"] ?>">Hapus</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            <?php endforeach; ?>
                        <?php else :  ?>
                            <tr>
                                <td class="fw-bold text-center">BELUM ADA MENU YANG DITAMBAHKAN</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <button class="btn btn-warning" type="submit" name="selesai">Selesai</button>
                <?php if (!isset($_GET["tambahlagi"]) && !isset($_GET["qr"])) : ?>
                    <button class="btn btn-danger" type="submit" name="batal">Batal</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
<?php include "layout/footer.php" ?>