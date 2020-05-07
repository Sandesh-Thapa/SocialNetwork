<?php
include("includes/header.php");

if (isset($_POST['cancel'])) {
    header("Location: settings.php");
}

if (isset($_POST['close_account'])) {
    $close_query = mysqli_query($con, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
    session_destroy();
    header("Location: register.php");
}
?>
<div class="close-account-wrapper">
    <div class="close-account-container">
        <h3>Deactivate Account</h3>
        <div class="close-message">
            <h4>Are you sure you want to Deactivate your Account?</h4>
            <p>Account Deactivation will hide your profile and all your activity from other users.</p>
            <p>You can Activate your account anytime by simply logging in.</p>
        </div>
        <form action="close_account.php" method="post">
            <input type="submit" name="close_account" value="Yes, Deactivate it!">
            <input type="submit" name="cancel" value="No Way!">
        </form>
    </div>

</div>