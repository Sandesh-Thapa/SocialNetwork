<?php
include("../../config/config.php");
include("../classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

if (strpos($query, "_") !== false) {
    $usersReturned = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
} elseif (count($names) == 2) {
    $usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[1]%') AND user_closed='no' LIMIT 8");
} else {
    $usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND user_closed='no' LIMIT 8");
}

if ($query != "") {
    while ($row = mysqli_fetch_array($usersReturned)) {
        $user = new User($con, $userLoggedIn);

        if ($row['username'] != $userLoggedIn) {
            $mutual_friends = $user->getMutualFriends($row['username']) . " mutual friends";
        } else {
            $mutual_friends = "";
        }

        if ($user->isFriend($row['username'])) {
            $username = $row['username'];
            echo "<div class='result-display'>
                    <a href='messages.php?u=$username'>
                        <img src='" . $row['profile_pic'] . "'>
                        <div class='live-search-text'>
                            <h5>" . $row['first_name'] . " " . $row['last_name'] . "</h5>
                            <p>" . $mutual_friends . "</p>
                        </div>
                    </a>
                  </div>";
        }
    }
}
