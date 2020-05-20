<?php
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Notification.php");

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
$user_to = $row['user_to'];
$date_time_now = date("Y-m-d H:i:s");

if (mysqli_query($con, "INSERT INTO comments VALUES ('', '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')")) {
    if ($posted_to != $userLoggedIn) {
        $notification = new Notification($con, $userLoggedIn);
        $notification->insertNotification($post_id, $posted_to, "comment");
    }
    if ($user_to != 'none' && $user_to != $userLoggedIn) {
        $notification = new Notification($con, $userLoggedIn);
        $notification->insertNotification($post_id, $user_to, "profile_comment");
    }

    $get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id = '$post_id'");
    $notified_users = array();
    while ($row = mysqli_fetch_array($get_commenters)) {
        if (
            $row['posted_by'] != $posted_to && $row['posted_by'] != $user_to && $row['posted_by'] != $userLoggedIn
            && !in_array($row['posted_by'], $notified_users)
        ) {
            $notification = new Notification($con, $userLoggedIn);
            $notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

            array_push($notified_users, $row['posted_by']);
        }
    }
    echo "Comment Posted!!";
} else {
    echo "Something went wrong";
}
