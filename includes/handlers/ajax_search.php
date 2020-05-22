<?php
include("../../config/config.php");
include("../classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

//if query contains an underscore, search for usernames
if (strpos($query, '_') !== false)
    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");

// if query contains two words, search for first name and last name respectively
else if (count($names) == 2)
    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no' LIMIT 8");

// if query has only one word, search for firstname or lastname
else
    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no' LIMIT 8");

if ($query != "") {
    while ($row = mysqli_fetch_array($usersReturnedQuery)) {
        $user = new User($con, $userLoggedIn);

        if ($row['username'] != $userLoggedIn)
            $mutual_friends = $user->getMutualFriends($row['username']) . " mutual friends";
        else
            $mutual_friends = "";

        echo "<div class='result-display'>
                <a href='" . $row['username'] . "'>
                    <img src='" . $row['profile_pic'] . "'>
                    <div class='live-search-text'>
                        <h4>" . $row['first_name'] . " " . $row['last_name'] . "</h4>
                        <p>" . $mutual_friends . "</p>
                    </div>
                </a>
          </div>";
    }
}
