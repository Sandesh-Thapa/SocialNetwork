<?php
include("includes/header.php");
//include("includes/settings_handler.");
?>
<div class="settings-wrapper">
    <div class="settings-container">
        <h4>Account Settings</h4>
        <div class="config">

            <div class="profile-images">
                <?php echo "<img src='" . $user['profile_pic'] . "'>" ?>
                <div class="change-button">
                    <a href="upload.php?up=profile" class="btn"><i class="fas fa-camera"></i> Change Profile Picture</a>
                    <a href="upload.php?up=cover" class="btn"><i class="fas fa-camera"></i> Change Cover Picture</a>
                </div>
            </div>

            <div class="profile-details">
                <h5>Modify the value and click 'Update Details'</h5>
                <form action="settings.php" method="post">
                    <label for="firstName">First Name:</label>
                    <input type="text" name="first_name" id="firstName" value="<?php echo $user['first_name']; ?>">
                    <label for="lastName">Last Name:</label>
                    <input type="text" name="last_name" id="lastName" value="<?php echo $user['last_name']; ?>">
                    <label for="userEmail">Email:</label>
                    <input type="email" name="email" id="userEmail" value="<?php echo $user['email']; ?>">
                </form>
            </div>

            <div class="password">
                <h5>Change Password</h5>
                <form action="settings.php" method="post">
                    <label for="oldPassword">Old Password:</label>
                    <input type="password" name="old_password" id="oldPassword">
                    <label for="newPassword">New Password:</label>
                    <input type="password" name="new_password_1" id="newPassword">
                    <label for="userEmail">Repeat New Password:</label>
                    <input type="passowrd" name="new_password_2" id="userEmail">
                </form>
            </div>

            <div class="close-account">
                <h5>Deactivate Account</h5>
                <form action="settings.php" method="post">
                    <input type="submit" name="close_account" id="closeAccount" value="Deactivate Account">
                </form>
            </div>

        </div>
    </div>
</div>