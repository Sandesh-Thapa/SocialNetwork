<?php
include("includes/header.php");
include("includes/classes/User.php");
include("includes/classes/Message.php");

$message_obj = new Message($con, $userLoggedIn);

if (isset($_GET['u']))
    $user_to = $_GET['u'];
else {
    $user_to = $message_obj->getMostRecentUser();
    if ($user_to == false)
        $user_to = 'new';
}

?>

<div class="chat-header">
    <div class="chat-container">
        <div class="left-panel">
            <div class="user-details">
                <a href="<?php echo $userLoggedIn ?>"> <img src="<?php echo $user['profile_pic']; ?>"> </a>
                <h2>Chats</h2>
            </div>
        </div>
        <div class="main-panel">
            <div class="chat-head">
                <?php
                if ($user_to != "new") {
                    $user_to_obj = new User($con, $user_to);
                    echo "<a href='$user_to'><img src=" . $user_to_obj->getProfilePic() . "> </a>";
                    echo "<h3>" . $user_to_obj->getFirstAndLastName() . "</h3>";
                ?>
            </div>
        </div>
    </div>
</div>

<div class="chat-body">
    <div class="chat-lists" id="getChatlists"></div>
    <div class="chat-message">
        <div class="display-chat">
            <div class="chat-message-body" id="getMessages"></div>
            <div class="send-message">
                <input id="chatMessage" type="text" placeholder="Type a message...">
                <button id="sendMessage" title="Send Message"><i class="far fa-paper-plane"></i></button>

            </div>
        </div>
        <div class="chat-user">
            <div class="chat-user-details">
                <img src="<?php echo $user_to_obj->getProfilePic(); ?>">
                <h2><?php echo $user_to_obj->getFirstAndLastName(); ?></h2>
                <p class="display-active">Active Now</p>
                <div class="user-info">
                    <p class="num-posts"><i class="fas fa-newspaper"></i>&nbsp;&nbsp;&nbsp;Posts: <?php echo $user_to_obj->getNumPosts(); ?></p>
                    <p class="num-posts"><i class="fas fa-thumbs-up"></i>&nbsp;&nbsp;&nbsp;Likes: <?php echo $user_to_obj->getNumLikes(); ?></p>
                    <p class="num-posts"><i class="fas fa-users"></i>&nbsp;&nbsp;&nbsp;Friends: <?php echo $user_to_obj->getNumFriends(); ?></p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php } else {
                    echo "<h4 style='margin-top: 10px;'>Search the friend you would like to message</h4>";
                    echo "<input type='text' placeholder='Search friend...' autocomplete='off'>";
                    echo "<div class='search-results'></div>";
                }

?>

<script>
    var userTo = '<?php echo $user_to; ?>';
    var div = document.getElementById('getMessages');

    function getChatlists() {
        $.ajax({
            url: "includes/handlers/ajax_display_chatlists.php",
            type: "GET",
            cache: false,
            success: function(response) {
                $('#getChatlists').html(response);
            }
        });
    }


    function sendMessage() {
        $('#sendMessage').on('click', function() {
            //alert('Message: ' + $('#chatMessage').val());
            $.ajax({
                url: "includes/handlers/ajax_send_messages.php",
                type: "POST",
                data: {
                    messageBody: $('#chatMessage').val(),
                    userTo: userTo
                },
                cache: false,
                success: function(response) {
                    if (response == "success") {
                        $('#chatMessage').val("");
                        $('#sendMessage').css('display', 'none');
                    } else {
                        alert('Failed to send Message! Try Again');
                    }
                }
            });
        });
    }


    function displayMessage() {
        $.ajax({
            url: "includes/handlers/ajax_display_messages.php",
            type: "GET",
            data: {
                userTo: userTo
            },
            cache: false,
            success: function(response) {
                //console.log(response);
                var result = $.parseJSON(response);
                $('#getMessages').html(result);
            }
        });

        var position = $('#getMessages').scrollTop();
        $('#getMessages').scroll(function() {
            var scroll = $('#getMessages').scrollTop();
            if (scroll < position) {
                div.scrollTop != div.scrollHeight;
            } else {
                div.scrollTop = div.scrollHeight;
            }
            position = scroll;
        });
    }


    $(document).ready(function() {


        $('#chatMessage').on('keyup', function() {
            if ($(this).val().length > 0) {
                //alert('hello');
                $('#sendMessage').css('display', 'block');
            } else {
                $('#sendMessage').css('display', 'none');
            }
        });

        //function call
        sendMessage();
        setInterval(displayMessage, 1000);
        setInterval(getChatlists, 1000);
    });
</script>