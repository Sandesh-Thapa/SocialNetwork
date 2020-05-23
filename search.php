<?php

include("includes/header.php");

if (isset($_GET['q'])) {
    $query = $_GET['q'];
} else {
    $query = "";
}

if (isset($_GET['type'])) {
    $type = $_GET['type'];
} else {
    $type = "name";
}
?>

<div class="search-page">
    <?php
    if ($query == "")
        echo "<p style='text-align: center;'>You must enter something in the search box.<p>";
    else {

        //if query contains an underscore, search for usernames
        if ($type == "username")
            $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
        else {
            $names = explode(" ", $query);
            if (count($names) == 3)
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed='no'");

            else if (count($names) == 2)
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no'");
            else
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no'");
        }

        if (mysqli_num_rows($usersReturnedQuery) == 0)
            echo "<p style='text-align: center;'>No results found for " . $type . " like: " . $query . "</p>";
        else
            echo "<p style='text-align: center;'>" . mysqli_num_rows($usersReturnedQuery) . " results found.</p>";

        echo "<p style='text-align: center;'>Try searching for :</p><a href='search.php?q=" . $query . "&type=name'>Names</a>, <a href='search.php?q=" . $query . "&type=username'>Usernames</a>";

        while ($row = mysqli_fetch_array($usersReturnedQuery)) {
            $user_obj = new User($con, $user['username']);
            $button = "";
            $mutual_friends = "";

            if ($user['username'] != $row['username']) {
                if ($user_obj->isFriend($row['username']))
                    $button = "<button type='submit' name='" . $row['username'] . "' class='btn remove-friend'><i class='fas fa-user-minus'></i> Unfriend</button>";
                else if ($user_obj->didReceiveRequest($row['username']))
                    $button = "<button type='submit' name='" . $row['username'] . "' class='btn confirm-friend'><i class='fas fa-user-edit'></i> Respond to Friend Request</button>";
                else if ($user_obj->didSendRequest($user['username']))
                    $button = "<button type='submit' name='" . $row['username'] . "' class='btn remove-request'><i class='fas fa-user-times'></i> Cancel Request</button>";
                else
                    $button = "<button type='submit' name='" . $row['username'] . "'  class='btn send-request'><i class='fas fa-user-plus'></i> Add Friend</button>";

                $mutual_friends = $user_obj->getMutualFriends($row['username']) . " mutual friends";
            }

            echo "<div class='search_result'>
                    
                    <div class='result-profile-pic'>
                        <a href='" . $row['username'] . "'>
                            <img src='" . $row['profile_pic'] . "'>
                        </a>
                    </div>
                    <a href='" . $row['username'] . "'>" . $row['first_name'] . " " . $row['last_name'] . "</a>
                    <p>" . $mutual_friends . "</p>
                    <div class='searchPageFriendsButtons'>
                        <form method='POST'>" . $button . "</form>
                    </div>

                </div>";
        }
    }
    ?>

</div>