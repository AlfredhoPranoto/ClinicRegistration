<?php
session_start();
$error = "";
$creden = @$_SESSION['email'];
$_SESSION['email'] = "";
$_SESSION['passwd'] = "";

if(isset($_POST["regis"])){
    $error = "";
    include 'include/dbh.inc.php';

    $email = mysqli_real_escape_string($conn,$_POST["email"]);
    $passwd = mysqli_real_escape_string($conn,$_POST["passwd"]);
    $nama = mysqli_real_escape_string($conn,$_POST["nama"]);
    $lahir = mysqli_real_escape_string($conn,$_POST["lahir"]);
    $perusahaan = mysqli_real_escape_string($conn,$_POST["perusahaan"]);
    $dept = mysqli_real_escape_string($conn,$_POST["dept"]);

    $select = "SELECT * FROM credentials WHERE email = '$email' ";
    
    $result = mysqli_query($conn, $select);

    if(empty(($_POST["passwd"]))){
        $error = "Password must be filled";
    }elseif(empty(trim($_POST["nama"])) || empty(trim($_POST["lahir"])) || empty(trim($_POST["perusahaan"]))
        || empty(trim($_POST["email"])) || empty(trim($_POST["dept"]))){
        $error = "All data must be filled";
    }else{
        if(mysqli_num_rows($result) > 0){
            $error = "Email has been registered";
        }else{
            $sql = "INSERT INTO `credentials` (`email`,`passwd`,`nama`,`lahir`,`perusahaan`,`dept`) 
            VALUES ('$email','$passwd','$nama','$lahir','$perusahaan','$dept')";

            if(mysqli_query($conn, $sql) == true){
                $_SESSION['email'] = $email;
                $_SESSION['passwd'] = $passwd;
                header('location:daftar.php');
            }else{
                $error = "Error: " . $conn->error;
            }
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/jpg" href="image/clinic-logo-bg.jpg">
    <title>Register</title>
</head>
<body>
    <div class="container my-5 px-5">
        <div class="row justify-content-center">
            <div class="col-lg-2 col-md-3 col-4">
                <img src="image/clinic-logo.png" class="img-fluid text-center" alt="Logo CP">
            </div>
            <h3 class="text-center mt-3">Internal Clinic</h3>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12 card mt-3">
                <div class="card-body">
                    <h3 class="text-center my-3">Register</h3>
                    <form action="register.php" method="post">
                        <?php
                        if($error != ""){
                            echo '<div class="d-flex m-4 align-items-center justify-content-between alert alert-danger alert-dismissable fade show" role="alert">'.$error.'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'.'</div>';
                        }
                        ?>
                        <div class="row px-sm-4 px-0 mb-3">
                            <div class="col-lg-12 col-md-12 col-12">
                                <label for="nik" class="form-label">Username</label>
                                <input type="text" class="form-control" id="email" name="email" value="<?php echo $creden; ?>" placeholder="Input Username" required>
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-3">
                            <div class="col-lg-12 col-md-12 col-12">
                                <label for="passwd" class="form-label">Password</label>
                                <input type="password" class="form-control" id="passwd" name="passwd" placeholder="Input Password" required>
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-3">
                            <div class="col-lg-12 col-md-12 col-12">
                                <label for="nama" class="form-label link-sm">Full Name</label>
                                <input type="text" class="form-control link-sm" id="nama" name="nama" placeholder="Input Full Name" required>
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-3">
                            <div class="col-lg-12 col-md-12 col-12">
                                <label for="perusahaan" class="form-label link-sm">Company</label>
                                <input type="text" class="form-control link-sm" id="perusahaan" name="perusahaan" placeholder="Input Compnay Name" required>
                            </div>
                        </div>
                        <div class="row px-sm-4 px-0 mb-3">
                            <div class="col-lg-6 col-md-6 col-12">
                                <label for="dept" class="form-label link-sm">Department</label>
                                <input type="text" class="form-control link-sm" id="dept" name="dept" placeholder="Input Department Name" required>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 mb-lg-0 mb-md-0 mb-3">
                                <label for="lahir" class="form-label link-sm">Birth Date</label>
                                <input type="date" class="form-control link-sm" id="lahir" name="lahir" required>
                            </div>
                        </div>
                        <div class="d-grid col-lg-12 px-4 mt-5 mb-2">
                            <button class="btn btn-primary" name="regis" type="submit">
                                Register
                            </button>
                            <p class="text-center mt-3 fw-semibold">Have an account? 
                                <a href="index.php" class="link-underline link-underline-opacity-0">login here</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>