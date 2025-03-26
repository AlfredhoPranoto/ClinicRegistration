<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
session_start();
include 'include/dbh.inc.php';
// declare variable
$error = "";
$sukses = "";
$hideCard = "";
$hideAlert = "";
$currentUser = "";
$currentPass = "";
$waktuRow = array();
$userData = array();
$alertHead = "";
$checkData = "";
// kalo session variable email ada isi, assign value ke currentUser
if(isset($_SESSION['email'])){
    $currentUser = $_SESSION['email'];
}
// kalo session variable password ada isi, assign value ke currentPass
if(isset($_SESSION['passwd'])){
    $currentPass = $_SESSION['passwd'];
}
// kalo currentUser/currentPass kosong, destroy session & redirect ke login page
if($currentUser == "" || $currentPass == ""){
    session_destroy();
    header('location: index.php');
}
// select semua data dari table credentials dimana value row email == currentUser
$checkData = "SELECT * FROM `credentials` WHERE email = '$currentUser' ";
$result = mysqli_query($conn, $checkData);
// kalo belum ada data di database, redirect ke page data diri
if(mysqli_num_rows($result) == 0){
    header('location: data_diri.php');
}
$rowCheck = array();
$timeQuery = "SELECT * FROM list_waktu";
$timeResult = mysqli_query($conn,$timeQuery);
// select semua data dr table daftar dimana value row berobat == current date (memastikan data yg ditampilkan sesuai hari)
$selectHari = "SELECT * FROM daftar WHERE berobat = CURDATE() ";
$queryHari = mysqli_query($conn, $selectHari);
// get jumlah row dan + 1 sebagai no antrian
$rowCount = mysqli_num_rows($queryHari);
$antrian = $rowCount + 1;
// fetch data sesuai queryHari, store ke array waktuRow buat cek waktu
while($cekWaktu = mysqli_fetch_assoc($queryHari)){
    $waktuRow[] = $cekWaktu['waktu'];
}
// set timezone ke jakarta, format tgl berobat ke d/m/Y
date_default_timezone_set('Asia/Jakarta');
$berobat = date('d/m/Y');
// define waktu form dibuka, convert ke unix timestamp
$open = "07:00";
$tOpen = strtotime($open);
// define waktu pendaftaran pertama, convert ke unix timestamp
$start = "10:20";
$tStart = strtotime($start);
// define waktu isitirahat dimulai, convert ke unix timestamp
$breakStart = "11:40";
$tBreakStart = strtotime($breakStart);
// define waktu istirahat selesai, convert ke unix timestamp
$breakEnd = "13:00";
$tBreakEnd = strtotime($breakEnd);
// define waktu form ditutup, convert ke unix timestamp
$end = "15:40";
$tEnd = strtotime($end);
$timeSelect = "";
// algorithm biar current time selalu snap ke interval 20 menit terdekat
$time = time();
$interval = 20 * 60;
$last = $time - ($time % $interval);
$next = $last + $interval;
$waktu = date('H:i',$next);
if($time < $tOpen){
    $hideCard = "hidden";
    $alertHead = "Pendaftaran belum dibuka";
}
// cek kalo current time lbh kecil dr start time, current time selalu 10:20
elseif($time < $tStart){
    $hideAlert = "hidden";
    $timeSelect = date_format(date_create("10:20"),"H:i");
    while(in_array($timeSelect,$waktuRow) && strtotime($timeSelect) < $tEnd){
        if(strtotime($timeSelect) >= $tBreakStart && strtotime($timeSelect) < $tBreakEnd){
            $timeSelect = date_format(date_create("12:40"),"H:i");
        }
        $adjust = strtotime($timeSelect) + 1200;
        $timeSelect = date('H:i',$adjust);
    }
}
// cek kalo current time lbh besar dr break start & lbh kecil dr break end, current time selalu 13:00
elseif($time > $tBreakStart && $time < $tBreakEnd){
    $hideAlert = "hidden";
    $timeSelect = date_format(date_create("13:00"),"H:i");
    while(in_array($timeSelect,$waktuRow) && strtotime($timeSelect) < $tEnd){
        $adjust = strtotime($timeSelect) + 1200;
        $timeSelect = date('H:i',$adjust);
    }
}
elseif($time == $tEnd){
    $hideAlert = "hidden";
    $timeSelect = date_format(date_create("15:40"),"H:i");
}
elseif($time > $tEnd){
    $hideCard = "hidden";
    $alertHead = "Pendaftaran sudah ditutup";
}
// diluar kondisi diatas waktu akan snap ke interval 20 menit terdekat dari current time
else{
    $hideAlert = "hidden";
    $timeSelect = $waktu;
    while(in_array($timeSelect,$waktuRow) && strtotime($timeSelect) < $tEnd){
        if(strtotime($timeSelect) >= $tBreakStart && strtotime($timeSelect) < $tBreakEnd){
            $timeSelect = date_format(date_create("12:40"),"H:i");
        }
        $adjust = strtotime($timeSelect) + 1200;
        $timeSelect = date('H:i',$adjust);
    }
}
// select semua data dr table credentials dimana value row email == currentUser
// $select = "SELECT * FROM credentials WHERE email = '$currentUser' ";
$query = mysqli_query($conn, $checkData);
// fetch data & cek kalo row lahir kosong, redirect ke page data_diri
$getData = mysqli_fetch_assoc($query);
if($getData['lahir'] == ""){
    header('location: data_diri.php');
}

$selectPasien = "SELECT * FROM daftar WHERE berobat = CURDATE()";
$queryPasien = mysqli_query($conn, $selectPasien);
while($cekPasien = mysqli_fetch_assoc($queryPasien)){
    $pasien[] = $cekPasien['email'];
}
// kalo daftar di klik, store value waktu & keluhan ke local variable
if(isset($_POST["daftar"])){
    $email = mysqli_real_escape_string($conn,$getData['email']);
    $waktuSelect = mysqli_real_escape_string($conn,$timeSelect);
    $nama = mysqli_real_escape_string($conn,$getData['nama']);
    $lahir = mysqli_real_escape_string($conn,$getData['lahir']);
    $perusahaan = mysqli_real_escape_string($conn,$getData['perusahaan']);
    
    $dept = mysqli_real_escape_string($conn,$getData['dept']);
    $keluhan = mysqli_real_escape_string($conn,$_POST['keluhan']);
    // kalo field keluhan kosong, display message lengkapi data
    if(empty($_POST['keluhan'])){
        $error = "Keluhan harus dilengkapi"; 
    }
    // kalo jumlah row hari ini lebih dari 14, antrian penuh
    // elseif(in_array($currentUser,$pasien)){
    //     $error = "Hanya bisa daftar 1 kali dalam 1 hari";
    // }
    elseif($rowCount >= 14){
        $error = "Antrian sudah penuh";
    }
    // kalo waktu yg dipilih sudah ada di database, slot waktu terpilih
    elseif(in_array($timeSelect,$waktuRow)){
        $error = "Slot waktu sudah dipilih, silahkan pilih waktu lain";
    }
    // kalo current time lebih kecil dari open time, form belum dibuka
    elseif($time < $tOpen){
        $error = "Pendaftaran belum dibuka untuk hari ini";
    }
    // kalo current time lebih besar dari end time, form sudah ditutup
    elseif($time >= $tEnd || strtotime($timeSelect) > $tEnd){
        $error = "Pendaftaran sudah ditutup untuk hari ini";
    }else{
    // kalo tidak masuk ke kondisi diatas, siapin sql statement buat insert
        $insert = "INSERT INTO `daftar` 
                (`email`, `waktu`,`nama`,`lahir`,`perusahaan`,`dept`,`keluhan`) VALUES 
                ('$email', '$waktuSelect','$nama','$lahir','$perusahaan','$dept','$keluhan') ";
        // execute query, kalo true display success message
        if($conn->query($insert) == true){
            $sukses = "Pendaftaran berhasil";
        }else{
            // kalo gagal display error
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
    <title>Pendaftaran</title>
    <style>
        .noscroll{
            position: static;
            overflow-y: auto;
        }
        .bg{
            background-image: url(image/Kantor-CP-bg.jpg);
            height: 94.75vh;
            width: auto;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        #waktu option[disabled]{
            display: none;
        }
        @media(max-width: 768px){
            .link-sm{
                font-size: 0.85rem;
            }
        }
        @media(max-width: 500px){
            .header-sm{
                font-size: 1rem;
            }
        }
    </style>
</head>
<body class="bg noscroll">
    <div class="container p-5 my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12 card alert alert-danger text-center p-0" <?php echo $hideAlert; ?>>
                <div class="card-body">
                    <div class="d-flex row justify-content-center">
                        <div class="col-lg-8 col-md-12 col-8 my-3 p-0">
                            <h3><?php echo $alertHead; ?></h3>
                        </div>
                        <div class="col-lg-8 col-md-12 col-8 mb-3 p-0">
                            <p style="font-size: 1.18rem;">Registration is open starting at 07:00 and will be closed at 15:40.</p>
                            <p style="font-size: 1.18rem;">Available time slots starts at 10:20 up to 15:40.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-12 card" <?php echo $hideCard; ?>>
                <div class="card-body">                   
                    <div class="d-flex row justify-content-md-center justify-content-end align-items-center ms-md-4 me-md-3 mx-0">
                        <div class="col-lg-2 col-md-2 col-2 p-0 pt-md-0 pt-3">
                            <form action="include/logout.inc.php" method="post">
                                <button type="submit" class="btn btn-danger"
                                        style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                    Logout
                                </button>
                            </form>
                        </div>
                        <div class="col-lg-8 col-md-8 col-8 p-0">
                            <h3 class="text-center my-md-3 mt-3 mb-0 header-sm">Clinic Registration</h3>
                        </div>
                        <div class="col-lg-2 col-md-2 col-2 p-0 pt-md-0 pt-3">
                            <a href="history_pasien.php" class="icon-link icon-link-hover link-opacity-75-hover link-underline link-underline-opacity-0 link-sm" style="--bs-icon-link-transform: translate3d(.250rem, 0, 0);">
                                History
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right d-flex align-items-center" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <form action="daftar.php" method="post">
                        <?php
                        // kalo error ga kosong, display error message
                        if($error != ""){
                            echo '<div class="d-flex m-4 align-items-center justify-content-between alert alert-danger alert-dismissable fade show" role="alert">'.$error.'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'.'</div>';
                        }
                        // kalo sukses ga kosong, display sukses message
                        elseif($sukses != "") {
                            echo '<div class="d-flex m-4 align-items-center justify-content-between alert alert-success alert-dismissable fade show" role="alert">'.$sukses.'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'.'</div>';
                        }
                        ?>
                        <div class="row px-sm-4 px-0 my-3">
                            <!-- input field tanggal -->
                            <div class="col-lg-6 col-md-6 col-7">
                                <label for="berobat" class="form-label link-sm">Date</label>
                                <input type="text" class="form-control link-sm" id="berobat" name="berobat" value="<?php echo $berobat; ?>" disabled>
                            </div>
                            <!-- input field antrian -->
                            <div class="col-lg-6 col-md-6 col-5">
                                <label for="antrian" class="form-label link-sm">Queue</label>
                                <input type="number" class="form-control link-sm" id="antrian" name="antrian" value="<?php echo $antrian; ?>" disabled>
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-3">
                            <!-- input field waktu -->
                            <div class="col-lg-12 col-md-12 col-12">
                                <label for="waktu" class="d-flex form-label link-sm">Time</label>
                                <input type="text" class="form-control link-sm" id="waktu" name="waktu" value="<?php echo $timeSelect; ?>" disabled>
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
                                <button class="btn btn-primary link-sm" name="daftar" type="submit">Register</button>
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