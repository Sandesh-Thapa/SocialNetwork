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

        echo "<p style='text-align: center;'>Try searching for : <a href='search.php?q=" . $query . "&type=name'>Names</a>, <a href='search.php?q=" . $query . "&type=username'>Usernames</a></p>";

        while ($row = mysqli_fetch_array($usersReturnedQuery)) {
            $user_obj = new User($con, $user['username']);

            $mutual_friends = $user_obj->getMutualFriends($row['username']) . " mutual friends";

            echo "<a href='" . $row['username'] . "' class='search_result'>
                    <div class='result-profile-pic'>
                        <img src='" . $row['profile_pic'] . "'>
                    </div>
                    <h3>" . $row['first_name'] . " " . $row['last_name'] . "</h3>
                    <p>" . $mutual_friends . "</p>
                </a>";
        }
    }
    ?>

</div>