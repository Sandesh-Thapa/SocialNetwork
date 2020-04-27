<?php
include("includes/classes/User.php");
include("includes/header.php");

$user_obj = new User($con, $userLoggedIn);

if (isset($_GET['up'])) {
    if ($_GET['up'] == 'cover') {
        $uploadPic = 'Cover Photo';
        $cover_pic = $user_obj->getCoverPic();
    } elseif ($_GET['up'] == 'profile') {
        $uploadPic = 'Profile Picture';
        $profile_pic = $user_obj->getProfilePic();
    } else {
        header("Location: index.php");
    }
}

?>

<div class="upload-container">
    <div class="upload-title">
        <h2>Upload <?php echo $uploadPic; ?></h2>
    </div>
    <div class="upload-info">
        <?php
        if ($uploadPic == 'Cover Photo') { ?>
            <div class="img-container cover-img">
                <img src="<?php echo $cover_pic; ?>">
            </div>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="cover_pic">
                <button type="submit" name="upload_cover"><i class="fas fa-upload"></i> Upload</button>
            </form>
        <?php
        } //end if
        elseif ($uploadPic == 'Profile Picture') { ?>
            <div class="img-container profile-img">
                <img src="<?php echo $profile_pic ?>">
            </div>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="cover_pic">
                <button type="submit" name="upload_profile"><i class="fas fa-upload"></i> Upload</button>
            </form>
        <?php
        } //end elseif
        ?>
    </div>
</div>