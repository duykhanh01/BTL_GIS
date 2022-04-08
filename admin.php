<?php



session_start();
if ($_SESSION['level'] == 0 and !isset($_SESSION['id'])) {
    header("Location: index.php");
}


include('config/db_connect.php');

$query = "SELECT * FROM db_donvi";
$res = mysqli_query($conn, $query);
$units =  $unitsList = mysqli_fetch_all($res, MYSQLI_ASSOC);


if (isset($_GET['name'])) {
    if ($_GET['name'] === "nguoi-dung") {
        $sql = "SELECT nv.manv, nv.tennv, dv.tendv as tendv, nv.chucvu, nv.sodidong, nv.email, nv.image FROM db_nhanvien nv , db_donvi dv where nv.madv = dv.madv order by nv.manv";
        $res = mysqli_query($conn, $sql);
        $phoneBooks = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
        $query = "SELECT * FROM db_donvi";
        $res = mysqli_query($conn, $query);
        $phoneBooks = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
}
if (isset($_GET['filter_dv'])) {
    $filter = $_GET['filter_dv'];
    $query = "SELECT * FROM db_donvi where tendv like '$filter%'";
    $res = mysqli_query($conn, $query);
    $units = mysqli_fetch_all($res, MYSQLI_ASSOC);
}
if (isset($_GET['filter_staffs'])) {
    $filter = $_GET['filter_staffs'];
    $sql = "SELECT nv.manv, nv.tennv, dv.tendv, nv.chucvu, nv.sodidong, nv.email, nv.image FROM db_nhanvien nv , db_donvi dv where nv.madv = dv.madv and dv.tendv = '$filter' order by nv.manv";
    $res = mysqli_query($conn, $sql);
    $phoneBooks = mysqli_fetch_all($res, MYSQLI_ASSOC);
}


?>




<!DOCTYPE html>
<html lang="en">

<?php include('templates/header.php'); ?>

<div class="container">

    <div class="row">
        <div class="col-xl-3 col-md-12  bg-light">
            <div class="treeview-animated mr-4 my-4">
                <ul class="treeview-animated-list mb-3">
                    <li class="treeview-animated-items">
                        <a class="closed" href="#">
                            <i class="fas fa-angle-right"></i>
                            <span><i class="far fa-folder-open ic-w mx-1"></i> Danh bạ đơn vị</span>
                        </a>
                        <ul class="nested">
                            <a href="admin.php?name=<?php echo "don-vi" ?>">
                                <li>
                                    <div class=" treeview-animated-element "><i class=" far fa-folder-open ic-w mx-1"></i>Tất cả đơn vị
                                </li>
                            </a>

                            <a href="admin.php?filter_dv=<?php echo "Khoa" ?>">
                                <li>

                                    <div class=" treeview-animated-element "><i class=" far fa-folder-open ic-w mx-1"></i>Khoa
                                </li>
                            </a>
                            <a href="admin.php?filter_dv=<?php echo "Bộ môn" ?>">
                                <li>

                                    <div class="treeview-animated-element "><i class="far fa-folder-open ic-w mx-1"></i>Bộ môn
                                </li>

                            </a>
                            <a href="admin.php?filter_dv=<?php echo "Phòng ban" ?>">
                                <li>
                                    <div class="treeview-animated-element "><i class="far fa-folder-open ic-w mx-1"></i>Phòng Ban
                                </li>
                            </a>

                        </ul>
                    </li>
                    <li class="treeview-animated-items">
                        <a class="closed" href="#">
                            <i class=" fas fa-angle-right"></i>
                            <span> <i class="far fa-folder-open ic-w mx-1"></i>Danh bạ người dùng</span>
                        </a>
                        <ul class="nested">
                            <a href="admin.php?name=<?php echo "nguoi-dung" ?>">
                                <li>
                                    <div class=" treeview-animated-element "><i class=" far fa-folder-open ic-w mx-1"></i>Tất cả người dùng
                                </li>
                            </a>
                            <?php foreach ($unitsList as $unitList) : ?>
                                <a href="admin.php?filter_staffs=<?php echo $unitList['tendv'] ?> ">
                                    <li>
                                        <div class="treeview-animated-element "><i class="far fa-folder-open ic-w mx-1"></i><?php echo $unitList['tendv'] ?>
                                    </li>
                                </a>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-xl-9 col-md-12 bg-body shadow-sm rounded">
            <?php if (isset($_GET['errors'])) : ?>
                <?php $noti = $_GET['errors'] == 0 ? "Vui lòng upload file .xlsx hoặc .csv" : "Vui lòng chọn file"  ?>
                <div class="alert alert-danger mt-2 text-center" role="alert">
                    <?php echo $noti; ?>
                </div>
            <?php endif; ?>
            <!-- Hiển thị bảng đơn vị nếu không chọn hiển thị gì -->
            <?php if (isset($_GET['name']) or isset($_GET['filter_staffs'])) : ?>

                <!--  Hiển thị bảng theo người  -->
                <?php if (isset($_GET['filter_staffs']) or $_GET['name'] == "nguoi-dung") : ?>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="addStaff.php" class="btn btn-success ">Thêm danh bạ người dùng</a>
                        <div>
                            <a href="#" type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exampleModal">Import</a>
                            <a href="core/export-staffs.php" type="button" class="btn btn-outline-success">Export</a>
                            <!-- Button trigger modal -->



                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Tải file lên</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <form method="post" action="core/import-staffs.php" enctype="multipart/form-data">
                                                    <label for="formFile" class="form-label">Chọn file</label>
                                                    <input id="input-staffs" class="form-control" name="file-staffs" type="file" id="formFile">
                                                    <div class="table-responsive staffs-preview">
                                                        <div id="preview-staff-fail" class="alert alert-danger d-none mt-2" role="alert">

                                                        </div>
                                                        <table id="staffs-preview" class='table table-hover mt-3 mb-0'>

                                                        </table>
                                                    </div>
                                                    <div class="mt-3 text-center">
                                                        <button type="submit" name="import-staffs" class="btn btn-primary">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal -->
                        </div>
                    </div>
                    <!-- Button trigger modal -->

                    <div class="table-responsive">

                        <table class="table table-striped table-hover mt-3 mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">STT</th>
                                    <th scope="col">Họ và tên</th>
                                    <th scope="col">Đơn vị</th>
                                    <th scope="col">Chức vụ</th>
                                    <th scope="col">SĐT</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Edit</th>
                                    <th scope="col">Delete</th>


                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($phoneBooks as $i => $phoneBook) : ?>
                                    <tr>
                                        <th scope="row"><?php echo $i + 1 ?></th>
                                        <td><?php echo $phoneBook['tennv']; ?></td>
                                        <td><?php echo $phoneBook['tendv']; ?></td>
                                        <td><?php echo $phoneBook['chucvu']; ?></td>
                                        <td><?php echo $phoneBook['sodidong']; ?></td>
                                        <td><?php echo $phoneBook['email']; ?></td>

                                        <td><a class="text-primary" href="editStaff.php?id=<?php echo $phoneBook['manv']; ?>"><i class="fas fa-edit "></i></a></td>
                                        <td><a class="text-danger" href="deleteStaff.php?id=<?php echo $phoneBook['manv']; ?>"><i class="fas fa-trash"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>
        </div>

        <!-- Hiển thị bảng đơn vị -->
    <?php elseif ($_GET['name'] === "don-vi") : ?>
        <div class="d-flex justify-content-between mt-3">
            <a href="addUnit.php" class="btn btn-success ">Thêm danh bạ đơn vị</a>

            <div>
                <a href="#" type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalUnits">Import</a>
                <a href="core/export-units.php" type="button" class="btn btn-outline-success">Export</a>
                <!-- Button trigger modal -->



                <!-- Modal -->
                <div class="modal fade" id="modalUnits" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Tải file lên</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <form method="post" action="core/import-units.php" enctype="multipart/form-data">

                                        <label for="formFile" class="form-label">Chọn file</label>
                                        <input class="form-control" name="file-units" type="file" id="formFile">
                                        <div class="mt-3 text-center">

                                            <button type="submit" name="import-units" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover mt-3 mb-0">
                <thead>
                    <tr>
                        <th scope="col">STT</th>
                        <th scope="col">Tên đơn vị</th>
                        <th scope="col">Địa chỉ</th>
                        <th scope="col">Email</th>
                        <th scope="col">SĐT</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($phoneBooks as $i => $phoneBook) : ?>
                        <tr>
                            <th scope="row"><?php echo $i + 1 ?></th>
                            <td><?php echo $phoneBook['tendv']; ?></td>
                            <td><?php echo $phoneBook['diachi']; ?></td>
                            <td><?php echo $phoneBook['email']; ?></td>
                            <td><?php echo $phoneBook['dienthoai']; ?></td>
                            <td><a class="text-primary" href="editUnit.php?id=<?php echo $phoneBook['madv']; ?>"><i class="fas fa-edit "></i></a></td>
                            <td><a class="text-danger" href="deleteUnit.php?id=<?php echo $phoneBook['madv']; ?>"><i class="fas fa-trash"></i></a></td>


                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>

    <?php else : header("Location: index.php"); ?>
    <?php endif; ?>

    <!-- ---------------------------------- -->
<?php else : ?>
    <div class="d-flex justify-content-between mt-3">
        <a href="addUnit.php" class="btn btn-success">Thêm danh bạ đơn vị</a>

        <div>
            <a href="#" type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalUnits">Import</a>
            <a href="core/export-units.php" type="button" class="btn btn-outline-success">Export</a>
            <!-- Button trigger modal -->



            <!-- Modal -->
            <div class="modal fade" id="modalUnits" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Tải file lên</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <form method="post" action="core/import-units.php" enctype="multipart/form-data">

                                    <label for="formFile" class="form-label">Chọn file</label>
                                    <input class="form-control" name="file-units" type="file" id="formFile">
                                    <div class="mt-3 text-center">

                                        <button type="submit" name="import-units" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
        </div>
    </div>
    <div class="table-responsive">

        <table class="table table-striped table-hover mt-3 mb-0">
            <thead>
                <tr>
                    <th scope="col">STT</th>
                    <th scope="col">Tên đơn vị</th>
                    <th scope="col">Địa chỉ</th>
                    <th scope="col">Email</th>
                    <th scope="col">SĐT</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($units as $i => $unit) : ?>
                    <tr>
                        <th scope="row"><?php echo $i + 1 ?></th>
                        <td><?php echo $unit['tendv']; ?></td>
                        <td><?php echo $unit['diachi']; ?></td>
                        <td><?php echo $unit['email']; ?></td>
                        <td><?php echo $unit['dienthoai']; ?></td>
                        <td><a class="text-primary" href="editUnit.php?id=<?php echo $unit['madv']; ?>"><i class="fas fa-edit "></i></a></td>
                        <td><a class="text-danger" href="deleteUnit.php?id=<?php echo $unit['madv']; ?>"><i class="fas fa-trash"></i></a></td>

                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>

<?php endif; ?>




    </div>
</div>



</div>



<?php include('templates/footer.php'); ?>


</html>