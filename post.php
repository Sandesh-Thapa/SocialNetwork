<?php
include("includes/header.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $id = 0;
}
?>
<div class="posts-section post-page" style="margin-top: 60px">
    <?php
    $post = new Post($con, $userLoggedIn);
    $post->getSinglePost($id);
    ?>
</div>