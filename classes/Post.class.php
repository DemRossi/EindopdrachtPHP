<?php

class Post
{
    public static function getSinglePost($idSinglePost)
    {
        $conn = Db::getInstance();
        $stmnt = $conn->prepare('select posts.id, user_id, post_img_dir,post_description,username,date_created, filter from posts, users where posts.user_id = users.id and posts.id = :idSinglePost and posts.statusReport = 0');
        $stmnt->bindValue(':idSinglePost', $idSinglePost, PDO::PARAM_INT);
        $stmnt->execute();

        return $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
        // print_r($result); exit();
    }

    public static function getPostsByColor($colorCode)
    {
        if (!isset($_POST['showitems'])) {
            $itemCount = 10;
        } else {
            $itemCount = (int) $_POST['showitems'];
            //echo $itemCount;
        }
        $conn = Db::getInstance();
        $stmnt = $conn->prepare('select posts.* ,username from posts, users where posts.user_id = users.id and posts.statusReport = 0 and (color1 = :colorCode or color2 = :colorCode or color3 = :colorCode or color4 = :colorCode)');
        $stmnt->bindValue(':colorCode', '#'.$colorCode);
        $stmnt->execute();

        return $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll()
    {
        if (!isset($_POST['showitems'])) {
            $itemCount = 5;
        } else {
            $itemCount = (int) $_POST['showitems'];
            //echo $itemCount;
        }

        $conn = Db::getInstance();
        $stmnt = $conn->prepare('select posts.* ,username from posts, users where posts.user_id = users.id and posts.statusReport = 0 ORDER BY id DESC LIMIT :itemCount');
        $stmnt->bindValue(':itemCount', $itemCount, PDO::PARAM_INT);
        $stmnt->execute();

        return $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllFollows($userId)
    {
        try {
            if (!isset($_POST['showitems'])) {
                $itemCount = 5;
            } else {
                // $itemCount = (int) $_POST['showitems'];
                $itemCount = (int) $_POST['showitems'];
                //echo $itemCount;
            }
            $conn = Db::getInstance();
            $stmnt = $conn->prepare('select posts.*, users.username from posts, users where (user_id in (select follower.follows from follower where user_id = :userId and follower.type = 1) or user_id in (select follower.follows from follower where follower.follower = :userId and follower.type = 1)) and (posts.user_id =  users.id ) and posts.statusReport = 0 ORDER BY id DESC LIMIT :itemCount');
            $stmnt->bindValue(':userId', $userId);
            $stmnt->bindValue(':itemCount', $itemCount, PDO::PARAM_INT);
            $stmnt->execute();
            $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (Trowable $t) {
            return false;
        }
    }

    public static function getAllById($usedId)
    {
        if (!isset($_POST['showitems'])) {
            $itemCount = 20;
        } else {
            $itemCount = (int) $_POST['showitems'];
            //echo $itemCount;
        }
        $conn = Db::getInstance();
        $stmnt = $conn->prepare('select posts.id, user_id, post_img_dir,post_description,username,date_created, filter from posts, users where posts.user_id = users.id and posts.user_id = :id and posts.statusReport = 0 ORDER BY id DESC LIMIT :itemCount');
        $stmnt->bindValue(':id', $usedId);
        $stmnt->bindValue(':itemCount', $itemCount, PDO::PARAM_INT);
        $stmnt->execute();

        return $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getSearchResults()
    {
        if (!isset($_POST['showitems'])) {
            $itemCount = 10;
        } else {
            $itemCount = (int) $_POST['showitems'];
            //echo $itemCount;
        }

        $search = '%'.$_GET['searchResult'].'%';

        $conn = Db::getInstance();
        $stmnt = $conn->prepare('select posts.id, user_id, post_img_dir, post_description, username from posts, users where posts.user_id = users.id and posts.statusReport = 0 AND post_description LIKE :hashtag ORDER BY id DESC LIMIT :itemCount ');
        $stmnt->bindValue(':itemCount', $itemCount, PDO::PARAM_INT);
        $stmnt->bindParam(':hashtag', $search);
        $stmnt->execute();

        return $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deletePost($idPost) {
        $conn = Db::getInstance();
        $stmnt = $conn->prepare('delete from posts where id = :postId');
        $stmnt->bindParam(':postId', $idPost);
        $stmnt->execute();
    }

    public static function changeDescription($postId)
        {
            //echo "test";
            $myDescr = htmlspecialchars($_POST['myDescr'], ENT_QUOTES);

            try {
                $conn = Db::getInstance();
                $stmntDesc = $conn->prepare('update posts SET post_description = :desc WHERE id = :postId');
                $stmntDesc->bindParam(':desc', $myDescr);
                $stmntDesc->bindParam(':postId', $postId);
                $result = $stmntDesc->execute();

                // return $result;
            } catch (Throwable $t) {
                echo $t;
            }
        }

    public static function timeAgo($timestamp)
    {
        date_default_timezone_set('Europe/Brussels');
        $time_ago = strtotime($timestamp);
        $current_time = time();
        $time_difference = $current_time - $time_ago;
        $seconds = $time_difference;

        $minutes = round($seconds / 60); // value 60 is seconds
        $hours = round($seconds / 3600); //value 3600 is 60 minutes * 60 sec
        $days = round($seconds / 86400); //86400 = 24 * 60 * 60;
        $weeks = round($seconds / 604800); // 7*24*60*60;
        $months = round($seconds / 2629440); //((365+365+365+365+366)/5/12)*24*60*60
        $years = round($seconds / 31553280); //(365+365+365+365+366)/5 * 24 * 60 * 60

        if ($seconds <= 60) {
            return 'Zonet';
        } elseif ($minutes <= 60) {
            if ($minutes == 1) {
                return '1 minuut geleden';
            } else {
                return "$minutes minuten geleden";
            }
        } elseif ($hours <= 24) {
            if ($hours == 1) {
                return 'Een uur geleden';
            } else {
                return "$hours uur geleden";
            }
        } elseif ($days <= 7) {
            if ($days == 1) {
                return 'Gisteren';
            } else {
                return "$days dagen geleden";
            }
        } elseif ($weeks <= 4.3) {
            if ($weeks == 1) {
                return 'Een week geleden';
            } else {
                return "$weeks weken geleden";
            }
        } elseif ($months <= 12) {
            if ($months == 1) {
                return 'Een maand geleden';
            } else {
                return "$months maanden geleden";
            }
        } else {
            if ($years == 1) {
                return 'Een jaar geleden';
            } else {
                return "$years jaren geleden";
            }
        }
    }

    public static function getUniqueViews($visitorIp, $postId)
    {
        $conn = Db::getInstance();
        $stmnt = $conn->prepare('select * from view_counter where ip_adress = :visitorIp and post_id = :postId');
        $stmnt->bindValue(':visitorIp', $visitorIp);
        $stmnt->bindParam(':postId', $postId);
        $stmnt->execute();

        if ($stmnt->rowCount() < 1)
        {
            $query = $conn->prepare('insert into view_counter (`post_id`,`ip_adress`,`visit_date`) VALUES (:postId, :visitorIp, :time)');
            $query->bindValue(':visitorIp', $visitorIp);
            $query->bindValue(':postId', $postId);
            date_default_timezone_set('Europe/Brussels');
            $current_time = date('Y-m-d H:i:s');
            $query->bindValue(':time', $current_time);
            $query->execute();
        }

        
        $getStmnt = $conn->prepare('select * from view_counter where post_id = :postId');
        $getStmnt->bindValue(':postId', $postId);
        $getStmnt->execute();
        return $getStmnt->rowCount();
    }
}
