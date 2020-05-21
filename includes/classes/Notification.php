<?php
class Notification
{
    private $user_obj;
    private $con;

    public function __construct($con, $user)
    {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function getUnreadNumber()
    {
        $userLoggedIn = $this->user_obj->getUsername();
        $query = mysqli_query($this->con, "SELECT * FROM notifications WHERE viewed='no' AND user_to='$userLoggedIn'");
        return mysqli_num_rows($query);
    }

    public function getNotifications($data, $limit)
    {
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";

        if ($page == 1)
            $start = 0;
        else
            $start = ($page - 1) * $limit;

        $set_viewed_query = mysqli_query($this->con, "UPDATE notifications SET viewed='yes' WHERE user_to='$userLoggedIn'");

        $query = mysqli_query($this->con, "SELECT * FROM notifications WHERE user_to='$userLoggedIn' ORDER BY id DESC");

        if (mysqli_num_rows($query) == 0) {
            echo "You have no notifications!";
            return;
        }

        $num_iterations = 0;
        $count = 1;
        while ($row = mysqli_fetch_array($query)) {

            if ($num_iterations++ < $start)
                continue;

            if ($count > $limit)
                break;
            else
                $count++;

            $user_from = $row['user_from'];
            $user_data_query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$user_from'");
            $user_data = mysqli_fetch_array($user_data_query);

            //Timeframe
            $date_time_now = date("Y-m-d H:i:s");
            $start_date = new DateTime($row['datetime']); //Time of post
            $end_date = new DateTime($date_time_now); //Current time
            $interval = $start_date->diff($end_date); //Difference between dates 
            if ($interval->y >= 1) {
                if ($interval == 1)
                    $time_message = $interval->y . " yr"; //1 year ago
                else
                    $time_message = $interval->y . " yrs"; //1+ year ago
            } else if ($interval->m >= 1) {
                if ($interval->d == 0) {
                    $days = " ago";
                } else if ($interval->d == 1) {
                    $days = $interval->d . " d";
                } else {
                    $days = $interval->d . " d";
                }


                if ($interval->m == 1) {
                    $time_message = $interval->m . " mos" . $days;
                } else {
                    $time_message = $interval->m . " mos" . $days;
                }
            } else if ($interval->d >= 1) {
                if ($interval->d == 1) {
                    $time_message = "Yesterday";
                } else {
                    $time_message = $interval->d . " days";
                }
            } else if ($interval->h >= 1) {
                if ($interval->h == 1) {
                    $time_message = $interval->h . " h";
                } else {
                    $time_message = $interval->h . " h";
                }
            } else if ($interval->i >= 1) {
                if ($interval->i == 1) {
                    $time_message = $interval->i . " min";
                } else {
                    $time_message = $interval->i . " min";
                }
            } else {
                if ($interval->s < 30) {
                    $time_message = "Just now";
                } else {
                    $time_message = $interval->s . " sec";
                }
            }


            $opened = $row['opened'];
            $style = ($row['opened'] == 'no') ? "background: #d8e8fb" : "";

            $return_string .= "<a href='" . $row['link'] . "' class='result-display-notification' style='" . $style . "'>
                                    <img src='" . $user_data['profile_pic'] . "'>
                                    <p>" . $row['message'] . "</p>
                                    <span>" . $time_message . "</span>
                                </a>";
        }

        //if post were loaded
        if ($count > $limit)
            $return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'>
                                <input type='hidden' class='noMoreDropdownData' value='false'>";
        else
            $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'><p style='text-align: center;'>No more notifications to load!</p>";
        return $return_string;
    }

    public function insertNotification($post_id, $user_to, $type)
    {
        $userLoggedIn = $this->user_obj->getUsername();
        $userLoggedInName = $this->user_obj->getFirstAndLastName();

        $date_time = date("Y-m-d H:i:s");

        switch ($type) {
            case 'comment':
                $message = $userLoggedInName . "  commented on your post";
                break;

            case 'like':
                $message = $userLoggedInName . "  liked on your post";
                break;

            case 'profile_post':
                $message = $userLoggedInName . "  posted on your profile";
                break;

            case 'comment_non_owner':
                $message = $userLoggedInName . "  also comment on a post that you followed";
                break;

            case 'profile_comment':
                $message = $userLoggedInName . "  commented on your profile post";
                break;
        }

        $link = "post.php?id=" . $post_id;

        $insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES ('', '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");
    }
}
