<?php

if (isset($_POST['post'])) {
    $post = new Post($con, $userLoggedIn);
    $post->submitPost($_POST['post_text'], $_POST['user_to']);
    $username = $_POST['user_to'];
}

// header("Location: $username");
