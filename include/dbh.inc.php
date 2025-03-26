<?php

$hostname = "control.castor.idgx.net";
$dbusername = "u9805alf_medic";
$dbpassword = "recordclinic";
$dbname = "u9805alf_medic";

$conn = mysqli_connect($hostname, $dbusername, $dbpassword, $dbname);

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}
