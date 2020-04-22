<?php
include("../../config/config.php");

if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
} else {
    header("Location: register.php");
}

$post_body = $_GET['postBody'];
$post_body = mysqli_escape_string($con, $post_body);
$post_id = $_GET['postid'];


$user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
$row = mysqli_fetch_array($user_query);

$posted_to = $row['added_by'];
$date_time_now = date("Y-m-d H:i:s");

if (mysqli_query($con, "INSERT INTO comments VALUES ('', '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')")) {
    echo "Comment Posted!!";
} else {
    echo "Something went wrong";
}
