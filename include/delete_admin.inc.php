<?php
session_start();
$_SESSION["sukses"] = "";
$_SESSION["error"] = "";
require_once 'dbh.inc.php';
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "DELETE FROM `credentials` WHERE id = '$id' ";
    $query = mysqli_query($conn,$sql);

    if($query === true){
        $_SESSION["sukses"] = "Pasient data deleted successfully";
        header('location: ../manage_user.php');
    }else{
        $_SESSION["error"] = "Failed to delete pasient data";
        header('location: ../manage_user.php');
    }
}
?>