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


function getLatestMessage($userLoggedIn, $user2, $connection)
{
    $details_array = array();

    $query = mysqli_query($connection, "SELECT body, user_to, date FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$user2') OR (user_to='$user2' AND user_from='$userLoggedIn') ORDER BY id DESC LIMIT 1");
    $row = mysqli_fetch_array($query);
    $sent_by = ($row['user_to'] == $userLoggedIn) ? "" : "You: ";

    //Timeframe
    $date_time_now = date("Y-m-d H:i:s");
    $start_date = new DateTime($row['date']); //Time of post
    $end_date = new DateTime($date_time_now); //Current time
    $interval = $start_date->diff($end_date); //Difference between dates 
    if ($interval->y >= 1) {
        if ($interval == 1)
            $time_message = $interval->y . " yr"; //1 year ago
        else
            $time_message = $interval->y . " yrs"; //1+ year ago
    } else if ($interval->m >= 1) {
        if ($interval->d == 0) {
            $days = " ago";
        } else if ($interval->d == 1) {
            $days = $interval->d . " d";
        } else {
            $days = $interval->d . " d";
        }


        if ($interval->m == 1) {
            $time_message = $interval->m . " mos" . $days;
        } else {
            $time_message = $interval->m . " mos" . $days;
        }
    } else if ($interval->d >= 1) {
        if ($interval->d == 1) {
            $time_message = "Yesterday";
        } else {
            $time_message = $interval->d . " days";
        }
    } else if ($interval->h >= 1) {
        if ($interval->h == 1) {
            $time_message = $interval->h . " h";
        } else {
            $time_message = $interval->h . " h";
        }
    } else if ($interval->i >= 1) {
        if ($interval->i == 1) {
            $time_message = $interval->i . " min";
        } else {
            $time_message = $interval->i . " min";
        }
    } else {
        if ($interval->s < 30) {
            $time_message = "Just now";
        } else {
            $time_message = $interval->s . " sec";
        }
    }

    array_push($details_array, $sent_by);
    array_push($details_array, $row['body']);
    array_push($details_array, $time_message);

    return $details_array;
}


$return_string = "";
$convos = array();

$query = mysqli_query($con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");

while ($row = mysqli_fetch_array($query)) {
    $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

    if (!in_array($user_to_push, $convos)) {
        array_push($convos, $user_to_push);
    }
}
foreach ($convos as $username) {
    $user_found_obj = new User($con, $username);
    $latest_message_details = getLatestMessage($userLoggedIn, $username, $con);

    $dots = (strlen($latest_message_details[1] >= 12)) ? "..." : "";
    $split = str_split($latest_message_details[1], 12);
    $split = $split[0] . $dots;

    $return_string .= "<div class='chatlist'>
                            <a href='messages.php?u=$username'>
                                <img src='" . $user_found_obj->getProfilePic() . "'>
                                <div class='chat-details'>
                                    <h5>" . $user_found_obj->getFirstAndLastName() . " </h5>
                                    <div class='chatDetails'>
                                        <p>" . $latest_message_details[0] . $split . "</p>
                                        <span class='timestamp-smaller'>" . $latest_message_details[2] . "</span>
                                    </div>
                                </div>
                            </a>
                        </div>";
}

echo $return_string;
