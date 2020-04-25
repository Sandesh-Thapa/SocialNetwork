<?php
include("../../config/config.php");

if (isset($_GET['postid'])) {
    $post_id = $_GET['postid'];
    $query = mysqli_query($con, "UPDATE posts SET deleted = 'yes' WHERE id='$post_id'");
}

if ($query) {
    echo "Post Deleted";
} else {
    echo "Failed to delete!";
}

// if (isset($_POST['result'])) {
//     if ($_POST['result'] == 'true')
//         $query = mysqli_query($con, "UPDATE posts SET deleted = 'yes' WHERE id='$post_id'");
// }
