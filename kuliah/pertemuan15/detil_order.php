<?php
require "functions.php";

if (isset($_GET["id_order"])) {
    $id_order = $_GET["id_order"];
    $order = query("SELECT `order`.*, pelayan.nama_pelayan FROM `order` JOIN `pelayan` ON order.id_pelayan = pelayan.id_pelayan WHERE id_order = $id_order")[0];
    $order_detail = query("SELECT order_detil.id_order_detil, order_detil.status_order_detil, order_detil.id_order, order_detil.subtotal, order_detil.id_menu, menu.nama, menu.jenis, order_detil.harga, order_detil.jumlah
    FROM order_detil
    LEFT JOIN menu ON menu.id_menu = order_detil.id_menu
    WHERE order_detil.id_order = $id_order
    ORDER BY order_detil.id_order");
    $tanggal = $order["tgl_order"];
    $jam = $order["jam_order"];
    $no = $order["no_meja"];
}

$status_order_detil = query("SELECT status_order_detil FROM order_detil WHERE id_order = $id_order");
$jumlah_selesai = 0;
$jumlah_diproses = 0;

foreach ($status_order_detil as $status) {
    if ($status["status_order_detil"] == "selesai") {
        $jumlah_selesai++;
    } elseif ($status["status_order_detil"] == "diproses") {
        $jumlah_diproses++;
    }
}

if ($jumlah_selesai == count($order_detail)) {
    updateStatusOrder($id_order, "Selesai");
} elseif ($jumlah_selesai > 0 || $jumlah_diproses > 0) {
    updateStatusOrder($id_order, "Diproses");
} else {
    updateStatusOrder($id_order, "Baru");
}



if (isset($_GET["hapus_order_detil"])) {
    $id_order = $_GET["id_order"];
    $id_order_detil = $_GET["hapus_order_detil"];
    hapusOrderDetil($id_order_detil);
    updateTotalBayar($id_order);
    header("Location: detil_order.php?id_order=" . $id_order);
}



if (isset($_POST["id_order_detil"]) && isset($_POST["status"])) {
    $id_order_detil = $_POST["id_order_detil"];
    $status = $_POST["status"];
    $id_order = $_POST["id_order"];
    updateStatusDetilOrder($id_order_detil, $status);
    header("Location: detil_order.php?id_order=" . $id_order);
}

$status = ["baru", "diproses", "selesai"];

$title = "DETAIL ORDER";
?>
<?php include "layout/header.php" ?>
<div class="container mt-4">
    <div class="card">
        <h5 class="card-header">
            Detail Order
        </h5>
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <?php if (isset($_SESSION["login"])) : ?>
                    <a href="data_order.php" class="btn btn-secondary btn-sm mb-3">Kembali Ke Data Order</a>
                <?php endif; ?>
                <a href="form_order_detil.php?id_order=<?= $id_order ?>&tambahlagi=''" class="btn btn-primary btn-sm mb-3">Tambah Order Detil</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>ID ORDER</th>
                            <th>TANGGAL ORDER</th>
                            <th>JAM ORDER</th>
                            <th>PELAYAN</th>
                            <th>NO MEJA</th>
                            <th>TOTAL BAYAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $order["id_order"] ?></td>
                            <td><?= $order["tgl_order"] ?></td>
                            <td><?= $order["jam_order"] ?></td>
                            <td><?= $order["nama_pelayan"] ?></td>
                            <td><?= $order["no_meja"] ?></td>
                            <td><?= formatHarga($order["total_bayar"]) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-responsive mt-4">
                <table class="table table-hover table-bordered">
                    <?php if (!empty($order_detail)) : ?>
                        <thead class="table-secondary">
                            <tr>
                                <th>NO</th>
                                <th>ID ORDER DETIL</th>
                                <th>NAMA MENU</th>
                                <th>JENIS MENU</th>
                                <th>HARGA</th>
                                <th>JUMLAH</th>
                                <th>SUB TOTAL</th>
                                <?php if (isset($_SESSION["login"])) : ?>
                                    <th>STATUS</th>
                                <?php endif; ?>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($order_detail as $order) :
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $order["id_order_detil"] ?></td>
                                    <td><?= $order["nama"] ?></td>
                                    <td><?= $order["jenis"] ?></td>
                                    <td><?= formatHarga($order["harga"]) ?></td>
                                    <td><?= $order["jumlah"] ?></td>
                                    <td><?= formatHarga($order["subtotal"]) ?></td>
                                    <?php if (isset($_SESSION["login"])) : ?>
                                        <td>
                                            <form action="" method="post">
                                                <input type="hidden" value="<?= $id_order ?>" name="id_order">
                                                <?php if ($order["status_order_detil"] == "baru" || $order["status_order_detil"] == "diproses") : ?>
                                                    <select class="form-select" name="status" onchange="this.form.submit()">
                                                        <?php foreach ($status as $s) : ?>
                                                            <option value="<?= $s ?>" <?= ($order["status_order_detil"] == $s) ? "selected" : "" ?>><?= $s ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else : ?>
                                                    <span class="btn btn-success btn-sm">Selesai</span>
                                                <?php endif; ?>
                                                <input type="hidden" name="id_order_detil" value="<?= $order["id_order_detil"] ?>">
                                            </form>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <a class="btn btn-danger btn-sm" href="detil_order.php?hapus_order_detil=<?= $order["id_order_detil"] ?>&id_order=<?= $id_order ?>" onclick="return confirm('Apakah anda yakin ingin id order ini?')">
                                            hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8">Tidak ada Order detil</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
            <?php if (!isset($_SESSION["login"])) : ?>
                <a class="btn btn-success btn-sm" href="login.php">Selesai & Keluar</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</script>
<?php include "layout/footer.php" ?>