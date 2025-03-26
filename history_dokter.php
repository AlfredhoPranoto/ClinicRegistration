<?php
session_start();
require_once 'include/dbh.inc.php';

// kalo access user bukan aadmin/dokter, redirect ke page daftar
if($_SESSION['access'] != "admin" && $_SESSION['access'] != "dokter"){
    header('location: daftar.php');
}

$query = "";
$tgl1 = "";
$tgl2 = "";
$blnBerobat = "";
$_SESSION['filter'] = "";
$export1 = "";
$export2 = "";

$limit = 15;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;
// filter berdasarkan tgl berobat
if(isset($_POST['searchTgl'])){
    $tgl1 = $_POST['tglBerobat1'];
    $tgl2 = $_POST['tglBerobat2'];
    $query = "SELECT * FROM daftar WHERE berobat BETWEEN '$tgl1' AND '$tgl2' LIMIT $start, $limit ";
    $_SESSION['filter'] = $query;
}
// filter berdasarkan bulan berobat
elseif(isset($_POST['searchBln'])){
    $blnBerobat = $_POST['blnBerobat'];
    $query = "SELECT * FROM daftar WHERE MONTH(berobat) = '$blnBerobat' LIMIT $start, $limit ";
    $_SESSION['filter'] = $query;
}
elseif(isset($_POST['exportPdf'])){
    $pdf1 = $_POST['pdfExport1'];
    $pdf2 = $_POST['pdfExport2'];
    $query = "SELECT waktu, nik, perusahaan, dept, nama, lahir, berobat, diagnosa, obat, 
			tindak, keterangan, dokter, keluhan FROM daftar WHERE berobat BETWEEN '$pdf1' 
            AND '$pdf2' ";
    $_SESSION['filter'] = $query;
    header('location: include/pdf.inc.php');
}
// clear search filter
elseif(isset($_POST['clear'])){
    unset($_POST['searchTgl']);
    unset($_POST['searchBln']);
    unset($_POST['clear']);
    $query = "SELECT * FROM daftar ORDER BY id DESC LIMIT $start, $limit";
    $_SESSION['filter'] = $query;
}else{
// display semua data
    $query = "SELECT * FROM daftar ORDER BY id DESC LIMIT $start, $limit";
    $_SESSION['filter'] = $query;
}
// execute query
$result = mysqli_query($conn, $_SESSION['filter']);
$getData = mysqli_fetch_all($result, MYSQLI_ASSOC);

$countQuery = mysqli_query($conn, "SELECT COUNT(id) AS id FROM daftar");
$pasienCount = mysqli_fetch_all($countQuery, MYSQLI_ASSOC);
$total = $pasienCount[0]['id'];
$pages = ceil($total / $limit);

$Previous = $page - 1;
$Next = $page + 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="300">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/jpg" href="image/clinic-logo-bg.jpg">
    <title>History</title>
    <style>
    @media(max-width: 1200px){
        .fs-md{
            font-size: 0.9rem;
        }
    }
    @media(max-width: 768px){
        .fs-header{
            font-size: 1.25rem;
        }
        .link-sm{
            font-size: 0.75rem;
        }
    }
    @media(max-width: 500px){
        .fs-sm{
            font-size: 0.7rem;
        }
    }
    </style>
</head>
<body>
    <div class="container-lg mt-3 mb-0 py-0">
        <div class="d-flex row justify-content-between align-items-center">
            <div class="d-flex justify-content-center col-lg-2 col-md-2 col-2">
                <a href="#offcanvas" class="btn btn-secondary link-sm my-3" data-bs-toggle="offcanvas" role="button" aria-controls="offcanvas">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel mb-1" viewBox="0 0 16 16">
                        <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
                    </svg>
                </a>
            </div>
            <div class="offcanvas offcanvas-start pt-2" tabindex="-1" id="offcanvas" aria-labelledby="offcanvasLabel">
                <div class="d-flex justify-content-center offcanvas-header pb-0">
                    <h3 class="offcanvas-title mt-2 mb-3" id="offcanvasLabel">Filter Data</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="row">
                        <form action="history_dokter.php" method="post">
                            <div class="col-lg-12">
                                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#searchTanggal" aria-expanded="false" aria-controls="searchTanggal">
                                    Filter by Treatment Date
                                </button>
                                <div class="collapse mt-2" id="searchTanggal">
                                    <div class="input-group">
                                        <div class="col">
                                            <label for="tanggal1" class="form-label ps-1">Start</label>
                                            <input type="date" name="tglBerobat1" class="m-0 form-control rounded-bottom-0 rounded-end-0" id="tanggal1">
                                        </div>
                                        <div class="col">
                                            <label for="tanggal2" class="form-label ps-1">End</label>
                                            <input type="date" name="tglBerobat2" class="m-0 form-control rounded-bottom-0 rounded-start-0" id="tanggal2">
                                        </div>
                                        <div class="col d-grid" style="padding-left: 0.05rem;">
                                            <button type="submit" name="searchTgl" class="btn btn-primary rounded-top-0" id="tanggalSearch">
                                                Search Date
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button class="btn btn-primary mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#searchBulan" aria-expanded="false" aria-controls="searchBulan">
                                    Filter by Month
                                </button>
                                <div class="collapse mt-2" id="searchBulan">
                                    <div class="input-group">
                                        <input type="number" name="blnBerobat" class="form-control" aria-label="Search Bulan" aria-describedby="bulanSearch" placeholder="Input month numerically">
                                        <button type="submit" name="searchBln" class="btn btn-primary" id="bulanSearch">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search mb-1" viewBox="0 0 16 16">
                                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" name="clear" class="btn btn-danger mt-3">Clear Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center col-lg-8 col-md-8 col-8">
                <h3 class="text-center fs-header my-3">Clinic Registration History</h3>
            </div>
            <div class="d-flex justify-content-center col-lg-2 col-md-2 col-2">
                <a href="#offcanvasMenu" class="btn btn-outline-dark my-3" data-bs-toggle="offcanvas" role="button" aria-controls="offcanvasMenu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                    </svg>
                </a>
            </div>
            <div class="offcanvas offcanvas-end pt-2" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasLabel">
                <div class="d-flex justify-content-center offcanvas-header pb-0">
                    <h3 class="offcanvas-title mt-2 mb-3" id="offcanvasLabel">Menu</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#offcanvasMenu" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12">
                            <button class="btn btn-primary mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#pdfCollapse" aria-expanded="false" aria-controls="pdfCollapse">
                                Export to PDF
                            </button>
                            <div class="collapse mt-2" id="pdfCollapse">
                                <form action="history_dokter.php" method="post">
                                    <div class="input-group">
                                        <div class="col-12">
                                            <h6 class="mb-3 fw-normal">Export Date Range</h6>
                                        </div>
                                        <div class="col">
                                            <input type="date" name="pdfExport1" class="m-0 form-control rounded-bottom-0 rounded-end-0" id="pdfExport1">
                                        </div>
                                        <div class="col">
                                            <input type="date" name="pdfExport2" class="m-0 form-control rounded-bottom-0 rounded-start-0" id="pdfExport2">
                                        </div>
                                        <div class="col d-grid">
                                            <button type="submit" name="exportPdf" class="btn btn-primary rounded-top-0" id="tanggalPDF">
                                                Export
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-12">
                            <!-- <button class="btn btn-primary mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#excelCollapse" aria-expanded="false" aria-controls="excelCollapse">
                                Export to Excel
                            </button> -->
                            <div class="collapse mt-2" id="excelCollapse">
                                <form action="include/excel.inc.php" method="post">
                                    <div class="input-group">
                                        <div class="col-12">
                                            <h6 class="mb-3 fw-normal">Range Tanggal Export</h6>
                                        </div>
                                        <div class="col">
                                            <input type="date" name="tglExport1" class="m-0 form-control rounded-bottom-0 rounded-end-0" id="export1">
                                        </div>
                                        <div class="col">
                                            <input type="date" name="tglExport2" class="m-0 form-control rounded-bottom-0 rounded-start-0" id="export2">
                                        </div>
                                        <div class="col d-grid">
                                            <button type="submit" name="exportExcel" class="btn btn-primary rounded-top-0" id="tanggalExcel">
                                                Export
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-12">
                            <form action="include/logout.inc.php" method="post">
                                <button class="btn btn-danger mt-3" type="submit">Logout</button>
                            </form>
                        </div>
                    </div>  
                </div>
            </div>
            <?php
                $sukses = "";
                $error = "";
                if(!empty($_SESSION["sukses"])){
                    $sukses = $_SESSION["sukses"];
                    echo '<div class="d-flex m-4 align-items-center justify-content-between alert alert-success alert-dismissable fade show" role="alert">'.$sukses.
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'.'</div>';
                    unset($_SESSION['sukses']);
                }else if(!empty($_SESSION["error"])){
                    $error = $_SESSION["error"];
                    echo '<div class="d-flex m-4 align-items-center justify-content-between alert alert-danger alert-dismissable fade show" role="alert">'.$error.
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'.'</div>';
                    unset($_SESSION['error']);
                }
            ?>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <!-- table header -->
                    <tr>
                        <th class="text-center fs-md fs-sm" scope="col">Num.</th>
                        <th class="text-center fs-md fs-sm" scope="col">Time</th>
                        <th class="text-center fs-md fs-sm" scope="col">Company</th>
                        <th class="text-center fs-md fs-sm" scope="col">Department</th>
                        <th class="text-center fs-md fs-sm" scope="col">Pasient Name</th>
                        <th class="text-center fs-md fs-sm" scope="col">Birth Date</th>
                        <th class="text-center fs-md fs-sm" scope="col">Treatment Date</th>
                        <th class="text-center fs-md fs-sm" scope="col">Diagnosis</th>
                        <th class="text-center fs-md fs-sm" scope="col">Medicine</th>
                        <th class="text-center fs-md fs-sm" scope="col">Action</th>
                        <th class="text-center fs-md fs-sm" scope="col">Description</th>
                        <th class="text-center fs-md fs-sm" scope="col">Doctor</th>
                        <th class="text-center fs-md fs-sm" scope="col">Complaints</th>
                        <th class="text-center fs-md fs-sm" scope="col">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <!-- display semua data -->
                    <?php
                    $antrian = 0;
                    if($page == 1){
                        $antrian = 1;
                    }else{
                        $antrian = $start + 1;
                    }
                    foreach($getData as $row){
                        $waktu = date_create($row['waktu']);
                        $lahir = date_create($row['lahir']);
                        $berobat = date_create($row['berobat']);
                    ?>
                        <td class="text-center fs-md fs-sm"><?php echo $antrian++; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo date_format($waktu,"G:i"); ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['nik']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['perusahaan']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['dept']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['nama']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo date_format($lahir, "j/n/Y"); ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo date_format($berobat, "j/n/Y"); ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['diagnosa']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['obat']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['tindak']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['keterangan']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['dokter']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['keluhan']; ?></td>
                        <td class="text-center">
                            <?php
                                echo "<a class='btn btn-primary fs-md fs-sm' href='./edit.php?id=".$row['id']."'>Edit</a>";
                            ?>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="row">
            <nav aria-label="Page navigation">
                <ul class="mt-2 pagination justify-content-center">
                    <li class="page-item <?php echo $page == 1 ? 'disabled' : ""; ?>">
                        <a href="history_dokter.php?page=<?= $Previous; ?>" class="page-link" aria-label="Previous">
                            <span aria-hidden="true">&laquo; Previous</span>
                        </a>
                    </li>
                    <?php for($i = 1; $i<= $pages; $i++){ ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ""; ?>"><a href="history_dokter.php?page=<?= $i; ?>" class="page-link"><?= $i; ?></a></li>
                    <?php } ?>
                    <li class="page-item <?php echo $page == $pages ? 'disabled' : ""; ?>">
                        <a href="history_dokter.php?page=<?= $Next; ?>" class="page-link" aria-label="Next">
                            <span aria-hidden="true">Next &raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>