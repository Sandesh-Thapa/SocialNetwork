<?php
class Message
{
    private $user_obj;
    private $con;

    public function __construct($con, $user)
    {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function getMostRecentUser()
    {
        $userLoggedIn = $this->user_obj->getUsername();

        $result = mysqli_query($this->con, "SELECT user_to,user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC LIMIT 1;");

        if (mysqli_num_rows($result) == 0)
            return false;

        $row = mysqli_fetch_array($result);
        $user_to = $row['user_to'];
        $user_from = $row['user_from'];

        if ($user_to != $userLoggedIn)
            return $user_to;
        else
            return $user_from;
    }
}
