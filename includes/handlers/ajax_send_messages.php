<?php

include("../../config/config.php");

if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
} else {
    header("Location: register.php");
}

$body =  $_POST['messageBody'];
$body = mysqli_real_escape_string($con, $body);
$user_to = $_POST['userTo'];
$date = date("Y-m-d H:i:s");

$query = mysqli_query($con, "INSERT INTO messages VALUES('', '$user_to', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");

if ($query) {
    echo "success";
} else {
    echo "failed";
}
