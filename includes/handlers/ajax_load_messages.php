<?php
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Message.php");

$limit = 4;

$message = new Message($con, $_REQUEST['userLoggedIn']);
echo $message->getConvosDropdown($_REQUEST, $limit);
