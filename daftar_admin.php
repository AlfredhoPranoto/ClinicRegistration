<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
session_start();
include 'include/dbh.inc.php';
// define variable
$error = "";
$sukses = "";
$lahir = "";
$disable = "";
$hideDate = "";
$hideText = "";
$email = mysqli_real_escape_string($conn, $_SESSION['email']);

$select = "SELECT * FROM credentials WHERE email = '$email' ";
$result = mysqli_query($conn, $select);
$fetch = mysqli_fetch_assoc($result);
$count = mysqli_num_rows($result);

$nama = @mysqli_real_escape_string($conn, $fetch['nama']);
$perusahaan = @mysqli_real_escape_string($conn, $fetch['perusahaan']);
$dept = @mysqli_real_escape_string($conn, $fetch['dept']);
// kalo access user bukan admin/dokter, redirect ke page pendaftaran
if($_SESSION['access'] != "admin" && $_SESSION['access'] != "dokter"){
    header('location: daftar.php');
}
// kalo access user dokter, redirect ke page history dokter
elseif($_SESSION['access'] == "dokter"){
    header('location: history_dokter.php');
}

if($count == "0"){
    $disable = "";
    $hideText = "hidden";
    $lahir = @mysqli_real_escape_string($conn, $_POST['lahir']);
}elseif($count == "1"){
    $disable = "disabled";
    $hideDate = "hidden";
    $lahir = @mysqli_real_escape_string($conn, $fetch['lahir']);
}

date_default_timezone_set('Asia/Jakarta');
$time = time();

// kalo klik confirm, assign value yg di post ke local variable
if(isset($_POST["confirm"])){
    $email = "admin registered";
    $waktu = mysqli_real_escape_string($conn, $_POST['waktu']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $perusahaan = mysqli_real_escape_string($conn, $_POST['perusahaan']);
    $dept = mysqli_real_escape_string($conn, $_POST['dept']);
    $keluhan = mysqli_real_escape_string($conn, $_POST['keluhan']);
    // kalo field lahir belum diisi, display error message
    if($lahir == "" || $_POST['keluhan'] == ""){
        $error = "Please fill all data"; 
    }else{
    // kalo field error sudah diisi siapin sql statement buat update data lahir
        $insert = "INSERT INTO `daftar` 
            (`email`,`waktu`,`nama`,`lahir`,`perusahaan`,`dept`,`keluhan`) VALUES 
            ('$email','$waktu','$nama','$lahir','$perusahaan','$dept','$keluhan') ";
        // kalo query berhasil diexecute, redirect ke page daftar
        if(mysqli_query($conn, $insert) == true){
            $sukses = "Pasient registered successfully";
        }else{
            $error = "Error: " . $conn->error;
        }
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
    <title>Data Diri</title>
    <style>
        .noscroll{
            position: static;
            overflow-y: auto;
        }
        .bg{
            background-image: url(image/Kantor-CP-bg.jpg);
            height: 100vh;
            width: auto;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        #waktu option[disabled]{
            display: none;
        }
        @media(max-width: 1600px){
            .bg{
                background-image: url(image/Kantor-CP-bg.jpg);
                max-height: 120vh;
                width: auto;
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
            }
        }
        @media(max-width: 1200px){
            .bg{
                background-image: url(image/Kantor-CP-bg.jpg);
                max-height: 140vh;
                width: auto;
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
            }
        }
        @media(max-width: 768px){
            .link-sm{
                font-size: 0.85rem;
            }
            .bg{
                background-image: url(image/Kantor-CP-bg.jpg);
                max-height: 160vh;
                width: auto;
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
            }
        }
        @media(max-width: 500px){
            .header-sm{
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body class="bg noscroll">
    <div class="container p-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12 card">
                <div class="card-body">
                    <div class="d-flex row justify-content-md-around justify-content-around align-items-center mx-3">
                        <div class="d-flex justify-content-md-center col-lg-1 col-md-1 col-2 ps-lg-4 ps-md-3 pe-0">
                            <a href="history.php" class="link-danger icon-link icon-link-hover link-underline link-underline-opacity-0 link-opacity-75-hover sm-link my-3" style="--bs-icon-link-transform: translate3d(-.250rem, 0, 0);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                </svg>Kembali
                            </a>
                        </div>
                        <div class="d-flex justify-content-lg-center justify-content-md-start col-lg-8 col-md-8 col-6 ps-lg-0 p-0">
                            <h3 class="text-center my-md-3 mt-3 mb-0 header-sm">Lengkapi Data Pasien</h3>
                        </div>
                    </div>
                    <form action="daftar_admin.php" method="post">
                        <?php
                        // kalo error ga kosong, display error message
                        if($error != ""){
                            echo '<div class="d-flex m-4 align-items-center justify-content-between alert alert-danger alert-dismissable fade show" role="alert">'.$error.'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'.'</div>';
                        }elseif($sukses != ""){
                            echo '<div class="d-flex m-4 align-items-center justify-content-between alert alert-success alert-dismissable fade show" role="alert">'.$sukses.'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'.'</div>';
                        }
                        ?>
                        <div class="row px-sm-4 px-0 mb-3">
                            <!-- input field buat username -->
                            <div class="col-lg-12 col-md-12 col-12">
                                <label for="nama" class="form-label link-sm">Username</label>
                                <input type="text" class="form-control link-sm" id="nama" name="nama" value="<?php echo $email;?>" <?php echo $disable ?>>
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-3">
                            <!-- input field buat nama -->
                            <div class="col-lg-12 col-md-12 col-12">
                                <label for="nama" class="form-label link-sm">Full Name</label>
                                <input type="text" class="form-control link-sm" id="nama" name="nama" value="<?php echo $nama;?>" <?php echo $disable ?>>
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-3">
                            <!-- input field buat tgl lahir -->
                            <div class="col-lg-6 col-md-6 col-12 mb-lg-0 mb-md-0 mb-3" <?php echo $hideText ?>>
                                <label for="lahir" class="form-label link-sm">Birth Date</label>
                                <input type="text" class="form-control link-sm" id="lahir" name="lahir" value="<?php echo $lahir ?>" <?php echo $disable ?>>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 mb-lg-0 mb-md-0 mb-3" <?php echo $hideDate ?>>
                                <label for="lahir" class="form-label link-sm">Birth Date</label>
                                <input type="date" class="form-control link-sm" id="lahir" name="lahir">
                            </div>
                            <!-- input field buat perusahaan -->
                            <div class="col-lg-6 col-md-6 col-12">
                                <label for="perusahaan" class="form-label link-sm">Company</label>
                                <input type="text" class="form-control link-sm" id="perusahaan" name="perusahaan" value="<?php echo $perusahaan;?>" <?php echo $disable ?>>
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-3">
                            <!-- input field buat departemen -->
                            <div class="col-lg-12 col-md-12 col-12">
                                <label for="dept" class="form-label link-sm">Department</label>
                                <input type="text" class="form-control link-sm" id="dept" name="dept" value="<?php echo $dept;?>" <?php echo $disable ?>>
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-3">
                            <!-- input field waktu -->
                            <div class="col-lg-12 col-md-12 col-12">
                                <label for="waktu" class="d-flex form-label link-sm">Time</label>
                                <input type="time" class="form-control link-sm" id="waktu" name="waktu">
                                <!-- <div class="d-flex row justify-content-between">
                                    <div class="col">
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 my-3">
                            <!-- text area keluhan -->
                            <div class="col-lg-12 col-md-12 col-12">
                                <label for="keluhan" class="form-label link-sm">Complaints</label>
                                <textarea class="form-control link-sm" id="keluhan" name="keluhan" rows="3"></textarea>
                                <div class="form-text">*To enter a new line please press "Enter" or "Shift + Enter"</div>
                            </div>
                        </div>
                        <div class="row justify-content-end px-sm-4 px-0 mt-4 mb-3">
                            <div class="d-grid col-6">
                                <button class="btn btn-success link-sm" name="confirm" type="submit">Confirm</button>
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