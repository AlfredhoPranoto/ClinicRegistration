<?php
session_start();
require_once 'include/dbh.inc.php';
// kalo access user bukan admin/dokter, redirect ke page daftar
if($_SESSION['access'] != "admin" && $_SESSION['access'] != "dokter"){
    header('location: daftar.php');
}
// kalo access user dokter, redirect ke page history dokter
elseif($_SESSION['access'] == "dokter"){
    header('location: history_dokter.php');
}

$query = "";
$roleAccount = "";
$_SESSION['filter'] = "";

$limit = 15;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;
// filter berdasarkan account role
if(isset($_POST['searchAcc'])){
    $roleAccount = $_POST['accSearch'];
    $query = "SELECT * FROM credentials WHERE access = '$roleAccount' ORDER BY id LIMIT $start, $limit";
    $_SESSION['filter'] = $query;
}
// clear filter
elseif(isset($_POST['clear'])){
    unset($_POST['searchAcc']);
    unset($_POST['clear']);
    $query = "SELECT * FROM credentials ORDER BY id LIMIT $start, $limit";
    $_SESSION['filter'] = $query;
}
// display semua data
else{
    $query = "SELECT * FROM credentials ORDER BY id LIMIT $start, $limit";
    $_SESSION['filter'] = $query;
}

$result = mysqli_query($conn, $_SESSION['filter']);
$getData = mysqli_fetch_all($result, MYSQLI_ASSOC);

$countQuery = mysqli_query($conn, "SELECT COUNT(id) AS id FROM credentials");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
        .sm-link{
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
        <div class="d-flex row justify-content-around align-items-center">
            <div class="d-flex justify-content-md-center col-lg-1 col-md-1 col-2 ps-lg-4 ps-md-3 pe-0">
                <a href="history.php" class="link-danger icon-link icon-link-hover link-underline link-underline-opacity-0 link-opacity-75-hover sm-link my-3" style="--bs-icon-link-transform: translate3d(-.250rem, 0, 0);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                    </svg>Return
                </a>
            </div>
            <div class="d-flex justify-content-center col-lg-1 col-md-1 col-2">
                <a href="#offcanvas" class="btn btn-secondary my-3" data-bs-toggle="offcanvas" role="button" aria-controls="offcanvas">
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
                        <form action="manage_user.php" method="post">
                            <div class="col-lg-12">
                                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#searchAcc" aria-expanded="false" aria-controls="searchAcc">
                                    Filter by Account Role
                                </button>
                                <div class="collapse mt-3" id="searchAcc">
                                    <div class="input-group">
                                        <input type="text" name="accSearch" class="form-control" placeholder="Account Role" aria-label="Search Account" aria-describedby="accSearch">
                                        <button type="submit" name="searchAcc" class="btn btn-primary" id="accSearch">
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
            <div class="d-flex justify-content-lg-center justify-content-md-center col-lg-8 col-md-5 col-6 ps-lg-0 p-0">
                <h3 class="text-center fs-header me-md-5 my-3 ps-0">Manage User Data</h3>
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
                    <div class="col-lg-12 col-md-12 col-12">
                        <a href="newuser.php" class="btn btn-primary">Add User</a>
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
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <!-- table header -->
                    <tr>
                        <th class="text-center fs-md fs-sm" scope="col">Num.</th>
                        <th class="text-center fs-md fs-sm" scope="col">Email</th>
                        <th class="text-center fs-md fs-sm" scope="col">Password</th>
                        <th class="text-center fs-md fs-sm" scope="col">Role</th>
                        <th class="text-center fs-md fs-sm" scope="col">Name</th>
                        <th class="text-center fs-md fs-sm" scope="col">Birth Date</th>
                        <th class="text-center fs-md fs-sm" scope="col">Company</th>
                        <th class="text-center fs-md fs-sm" scope="col">Department</th>
                        <th class="text-center fs-md fs-sm" scope="col">Edit</th>
                        <th class="text-center fs-md fs-sm" scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <!-- display semua data credentials -->
                    <?php
                    $antrian = 0;
                    if($page == 1){
                        $antrian = 1;
                    }else{
                        $antrian = $start + 1;
                    }
                    foreach($getData as $row){
                    ?>
                        <td class="text-center fs-md fs-sm"><?php echo $antrian++; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['email']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['passwd']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['access']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['nama']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['lahir']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['perusahaan']; ?></td>
                        <td class="text-center fs-md fs-sm"><?php echo $row['dept']; ?></td>
                        <td class="text-center">
                            <?php
                            // edit button
                                echo "<a class='btn btn-primary fs-md fs-sm' href='./edit_admin.php?id=".$row['id']."'>Edit</a>";
                            ?>
                        </td>
                        <td class="text-center">
                        <!-- delete modal trigger -->
                        <button type="button" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $row['id'] ?>" class="btn btn-danger fs-md fs-sm delete-btn">Delete</button>
                        </td>
                        <!-- delete modal -->
                        <div class="modal fade" id="deleteModal<?php echo $row['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title" id="deleteModalLabel">Delete Account</h3>
                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="fs-5 fs-sm">Are you sure you want to delete this account?</p>
                                        <input type="hidden" class="form-control delete_id">
                                    </div>
                                    <div class="modal-footer">
                                        <!-- cancel button -->
                                        <a class="btn btn-secondary fs-sm" href="#" type="button" data-bs-dismiss="modal">Cancel</a>
                                        <!-- delete button -->
                                        <a class="btn btn-danger delete-confirm fs-sm" href="./include/delete_admin.inc.php?id=<?php echo $row['id'] ?>">Ok</a>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                        <a href="manage_user.php?page=<?= $Previous; ?>" class="page-link" aria-label="Previous">
                            <span aria-hidden="true">&laquo; Previous</span>
                        </a>
                    </li>
                    <?php for($i = 1; $i<= $pages; $i++){ ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ""; ?>"><a href="manage_user.php?page=<?= $i; ?>" class="page-link"><?= $i; ?></a></li>
                    <?php } ?>
                    <li class="page-item <?php echo $page == $pages ? 'disabled' : ""; ?>">
                        <a href="manage_user.php?page=<?= $Next; ?>" class="page-link" aria-label="Next">
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