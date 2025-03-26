<?php
session_start();
require_once 'dbh.inc.php';

$currentUser = "";
$currentPass = "";

if(isset($_SESSION['email'])){
    $currentUser = $_SESSION['email'];
}
if(isset($_SESSION['passwd'])){
    $currentPass = $_SESSION['passwd'];
}
if($currentUser == "" || $currentPass == ""){
    session_destroy();
    header('location: index.php');
}

$query = "SELECT * FROM daftar WHERE email = '$currentUser' ";
$result = mysqli_query($conn,$query);
