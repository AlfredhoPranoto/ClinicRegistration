<?php
session_start();
if(!isset($_GET['id'])){
    die('ID does not eexist');
}
// kalo access user bukan admin atau dokter, redirect ke page daftar
if($_SESSION['access'] != "admin" && $_SESSION['access'] != "dokter"){
    header('location: daftar.php');
}

require_once('include/dbh.inc.php');

$id = $_GET['id'];
// select semua data dari table credentials dimana value row id == id
$select = "SELECT * FROM `credentials` WHERE id = '$id' ";
$result = mysqli_query($conn, $select);
// kalo jumlah row lbh kecil dr 1, id tidak di database
if(mysqli_num_rows($result) < 1){
    die('ID is not in database');
}
// fetch data
$data = mysqli_fetch_assoc($result);
?>
<?php
require_once 'include/dbh.inc.php';

$error = "";
$sukses = "";
// kalo id ada dan edit button di klik
if(isset($_GET['id']) && isset($_POST['edit'])){
    $id = $_GET['id'];
    // store value post ke local variable
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $passwd = mysqli_real_escape_string($conn,$_POST['passwd']);
    $access = mysqli_real_escape_string($conn,$_POST['access']);
    $nama = mysqli_real_escape_string($conn,$_POST['nama']);
    $lahir = mysqli_real_escape_string($conn,$_POST['lahir']);
    $perusahaan = mysqli_real_escape_string($conn,$_POST['perusahaan']);
    $dept = mysqli_real_escape_string($conn,$_POST['dept']);
    // update query ke table credentials
    $updateCred = "UPDATE `credentials` SET
                `email` = '$email',
                `passwd` = '$passwd',
                `access` = '$access',
                `nama` = '$nama',
                `lahir` = '$lahir',
                `perusahaan` = '$perusahaan',
                `dept` = '$dept'
                WHERE id = '$id' ";
    // execute query, kalo true display sukses message
    if(mysqli_query($conn, $updateCred) == true){
        $sukses = "Data pasien berhasil di update";
    }else{
        // kalo gagal display error message
        $error = "Data paseien gagal di update. Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/jpg" href="image/clinic-logo-bg.jpg">
    <title>Edit</title>
    <style>
    @media(max-width: 768px){
        .fs-sm{
            font-size: 0.85rem;
        }
    }
    @media(max-width: 500px){
        .header-sm{
            font-size: 1.25rem;
        }
    }
    </style>
</head>
<body>
    <div class="container my-lg-5 p-5">
        <div class="d-flex row justify-content-center align-items-center">
            <div class="col-lg-1 col-md-1 col-2 p-0">
                <a href="manage_user.php" class="link-danger icon-link icon-link-hover link-underline link-underline-opacity-0 link-opacity-75-hover mt-lg-5 mb-4 fs-sm" style="--bs-icon-link-transform: translate3d(-.125rem, 0, 0);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                    </svg>Return
                </a>
            </div>
            <div class="col-lg-7 col-md-11 col-10 ps-0">
                <h3 class="text-center mt-lg-5 mb-4 fs-header header-sm">Edit Pasient Data</h3>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12 card">
                <div class="card-body">
                    <form action="./edit_admin.php?id=<?php echo $id ?>" method="post">
                        <?php
                        if($error != ""){
                            echo '<div class="d-flex m-4 align-items-center justify-content-between alert alert-danger alert-dismissable fade show" role="alert">'.$error.'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'.'</div>';
                        }elseif($sukses != "") {
                            echo '<div class="d-flex m-4 align-items-center justify-content-between alert alert-success alert-dismissable fade show" role="alert">'.$sukses.'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'.'</div>';
                        }
                        ?>
                        <div class="row px-sm-4 px-0 mb-sm-3 mb-2">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <label for="email" class="form-label fs-sm">Username</label>
                                <input type="text" class="form-control fs-sm" id="email" name="email" value="<?php echo $data['email'] ?>">
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-sm-3 mb-2">
                            <div class="col-lg-6 col-md-6 col-sm-6 mb-sm-0 mb-2">
                                <label for="passwd" class="form-label fs-sm">Password</label>
                                <input type="text" class="form-control fs-sm" id="passwd" name="passwd" value="<?php echo $data['passwd'] ?>">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <label for="access" class="form-label fs-sm">Role</label>
                                <input type="text" class="form-control fs-sm" id="access" name="access" value="<?php echo $data['access'] ?>">
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-3">
                            <div class="col-lg-12 col-md-12 col-12">
                                <label for="nama" class="form-label link-sm">Full Name</label>
                                <input type="text" class="form-control link-sm" id="nama" name="nama" value="<?php echo $data['nama'] ?>">
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-3">
                            <div class="col-lg-6 col-md-6 col-12 mb-lg-0 mb-md-0 mb-3">
                                <label for="lahir" class="form-label link-sm">Birth Date</label>
                                <input type="date" class="form-control link-sm" id="lahir" name="lahir" value="<?php echo $data['lahir'] ?>">
                            </div>
                            <div class="col-lg-6 col-md-6 col-12">
                                <label for="perusahaan" class="form-label link-sm">Company</label>
                                <input type="text" class="form-control link-sm" id="perusahaan" name="perusahaan" value="<?php echo $data['perusahaan'] ?>">
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-3">
                            <div class="col-lg-12 col-md-12 col-12">
                                <label for="dept" class="form-label link-sm">Department</label>
                                <input type="text" class="form-control link-sm" id="dept" name="dept" value="<?php echo $data['dept'] ?>">
                            </div>
                        </div>
                        <div class="row justify-content-end px-sm-4 px-0 my-3">
                            <div class="d-grid col-6">
                                <button class="btn btn-primary fs-sm mt-2" type="button" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>
                                <div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h3 class="modal-title" id="editModalLabel">Edit Data Pasien</h3>
                                                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="fs-5 fs-sm">Are you sure you want to edit this pasien data?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-secondary fs-sm" type="button" data-bs-dismiss="modal">Cancel</button>
                                                <button class="btn btn-primary fs-sm" type="submit" name="edit">Ok</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>