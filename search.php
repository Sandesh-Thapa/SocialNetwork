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

<div>
    <?php
    if ($query == "")
        echo "You must enter something in the search box.";
    else {


        //if query contains an underscore, search for usernames
        if ($type == "username")
            $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
        else {
            // if query contains two words, search for first name and last name respectively
            if (count($names) == 2)
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no' LIMIT 8");

            // if query has only one word, search for firstname or lastname
            else
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no' LIMIT 8");
        }


        $names = explode(" ", $query);
    }
    ?>

</div>