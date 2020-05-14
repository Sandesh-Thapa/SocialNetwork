<?php

include("../../config/config.php");
include("../classes/User.php");

if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
} else {
    header("Location: register.php");
}

$user_from = $_GET['userTo'];
$data = array();

$user_from_obj = new User($con, $user_from);
// $user_logged_obj = new User($con, $userLoggedIn);

$user_from_profilepic = $user_from_obj->getProfilePic();
// $user_logged_profilepic = $user_logged_obj->getProfilePic();

$query = mysqli_query($con, "UPDATE messages SET opened='yes' WHERE user_to='$userLoggedIn' AND user_from='$user_from'");

$get_messages_query = mysqli_query($con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$user_from') OR (user_from='$userLoggedIn' AND user_to='$user_from')");

while ($row = mysqli_fetch_array($get_messages_query)) {
    $message_to = $row['user_to'];
    $message_from = $row['user_from'];
    $body = $row['body'];

    if ($message_to == $userLoggedIn) {
        $div = "<img src='$user_from_profilepic'>
                <div class='message-body message-to'>";
    } else {
        $div = "<div class='message-body message-from'>";
    }

    //$data = $data.$div.$body."</div><br><br>";
    array_push($data, $div . $body . "</div><br><br>");
}
echo json_encode($data);
