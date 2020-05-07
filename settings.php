<?php
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");
?>
<div class="settings-wrapper">
    <div class="settings-container">
        <h4>Account Settings</h4>
        <div class="config">

            <div class="profile-images">
                <?php echo "<img src='" . $user['profile_pic'] . "'>" ?>
                <div class="change-button">
                    <a href="upload.php?up=profile" class="btn"><i class="fas fa-camera"></i> Change Profile Picture</a><br><br>
                    <a href="upload.php?up=cover" class="btn"><i class="fas fa-camera"></i> Change Cover Picture</a>
                </div>
            </div>

            <?php
            $user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
            $row = mysqli_fetch_array($user_data_query);

            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $email = $row['email'];
            ?>



            <div class="profile-details">
                <h5>Modify the value and click 'Update Details'</h5>
                <form action="settings.php" method="post">
                    <label for="firstName">First Name:</label>
                    <input type="text" name="first_name" id="firstName" value="<?php echo $first_name; ?>">
                    <label for="lastName">Last Name:</label>
                    <input type="text" name="last_name" id="lastName" value="<?php echo $last_name; ?>">
                    <label for="userEmail">Email:</label>
                    <input type="email" name="email" id="userEmail" value="<?php echo $email; ?>">

                    <p style="color: #ccc;"><?php echo $message; ?></p>

                    <input type="submit" name="update_details" value="Update Details">
                </form>
            </div>

            <div class="password">
                <h5>Change Password</h5>
                <form action="settings.php" method="post">
                    <label for="oldPassword">Old Password:</label>
                    <input type="password" name="old_password" id="oldPassword">
                    <label for="newPassword">New Password:</label>
                    <input type="password" name="new_password_1" id="newPassword">
                    <label for="newPassword2">Repeat New Password:</label>
                    <input type="password" name="new_password_2" id="newPassword2">

                    <p style="color: #ccc;"><?php echo $password_message; ?></p>

                    <input type="submit" name="change_password" value="Change Password">
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