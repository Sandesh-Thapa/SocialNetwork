<?php
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

// upload profile picture
if (isset($_POST['upload_profile'])) {
    if (empty($_FILES['file']['name'])) {
        echo "<script>alert('Failed: Choose file to upload');</script>";
    } else {
        $fileName = $_FILES['file']['name'];
        $fileTmpName = $_FILES['file']['tmp_name'];
        $fileSize = $_FILES['file']['size'];
        $fileError = $_FILES['file']['error'];
        $fileType = $_FILES['file']['type'];

        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));

        $allowed = array('jpg', 'jpeg', 'png', 'tiff', 'jfif', 'gif');

        if (in_array($fileActualExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize < 10000000) //less than 10mb 
                {
                    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                    $fileDestination = 'assets/images/profile_pics/' . $fileNameNew;

                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        $update_profile_picture = mysqli_query($con, "UPDATE users SET profile_pic = '$fileDestination' WHERE username = '$userLoggedIn'");
                        header("Location: $userLoggedIn");
                    } else {
                        echo "<script>alert('Failed to Upload!');</script>";
                    }
                } else {
                    echo "<script>alert('Failed: File Size Too Big! Photo should be less than 10mb');</script>";
                }
            } else {
                echo "<script>alert('Failed: Error uploading file!');</script>";
            }
        } else {
            echo "<script>alert('Failed: You cannot upload files of this type!');</script>";
        }
    }
}

// upload cover picture
if (isset($_POST['upload_cover'])) {
    if (empty($_FILES['file']['name'])) {
        echo "<script>alert('Failed: Choose file to upload');</script>";
    } else {
        $fileName = $_FILES['file']['name'];
        $fileTmpName = $_FILES['file']['tmp_name'];
        $fileSize = $_FILES['file']['size'];
        $fileError = $_FILES['file']['error'];
        $fileType = $_FILES['file']['type'];

        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));

        $allowed = array('jpg', 'jpeg', 'png', 'tiff', 'jfif', 'gif');

        if (in_array($fileActualExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize < 10000000) //less than 10mb 
                {
                    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                    $fileDestination = 'assets/images/profile_pics/' . $fileNameNew;

                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        $update_profile_picture = mysqli_query($con, "UPDATE users SET cover_pic = '$fileDestination' WHERE username = '$userLoggedIn'");
                        header("Location: $userLoggedIn");
                    } else {
                        echo "<script>alert('Failed to Upload!');</script>";
                    }
                } else {
                    echo "<script>alert('Failed: File Size Too Big! Photo should be less than 10mb');</script>";
                }
            } else {
                echo "<script>alert('Failed: Error uploading file!');</script>";
            }
        } else {
            echo "<script>alert('Failed: You cannot upload files of this type!');</script>";
        }
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
                <img src="<?php echo $cover_pic; ?>" class="img-frame">
            </div>
            <form action="upload.php?up=cover" method="POST" enctype="multipart/form-data">
                <input type="file" name="file" onchange="displayImage(this)">
                <button type=" submit" name="upload_cover"><i class="fas fa-upload"></i> Upload</button>
            </form>
        <?php
        } //end if
        elseif ($uploadPic == 'Profile Picture') { ?>
            <div class="img-container profile-img">
                <img src="<?php echo $profile_pic ?>" class="img-frame">
            </div>
            <form action="upload.php?up=profile" method="POST" enctype="multipart/form-data">
                <input type="file" name="file" onchange="displayImage(this)">
                <button type="submit" name="upload_profile"><i class="fas fa-upload"></i> Upload</button>
            </form>
        <?php
        } //end elseif
        ?>
    </div>
</div>
<script>
    function displayImage(e) {
        if (e.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                document.querySelector('.img-frame').setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(e.files[0]);
        }
    }
</script>