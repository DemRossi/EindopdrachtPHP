﻿<?php
    require_once 'bootstrap/bootstrap.php';

    if (isset($_SESSION['user'])) {
        //logged in user
        //echo "😎";
        $followsId = $_GET['id'];
        $followChecker = Follow::checkIfFollows($_SESSION['user']['id'], $followsId);
        $followers = Follow::getFollowers($followsId);
    } else {
        //no logged in user
        header('Location: login.php');
    }

    $result = Post::getAllById($followsId);
    $posFollow = User::getProfile($followsId);
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cssgram/0.1.10/cssgram.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>IncludeFood - Profile Details</title>
    
</head>
<body>
    <header>
        <?php require_once 'nav.inc.php'; ?>
    </header>
    <div class="container details">
        <div class="sideNav">
            <div class="upperSide">
                <img src="images/profilePics/<?php echo $posFollow['img_dir']; ?>" class="userPic" alt="Profile Picture">
                <h3 class="info info--name"><?php echo $posFollow['username']; ?></h3>

                <!-- start Follow -->

                <div class="followers">

                    <input type="button" value="Follow" class="follow" id="follow_<?php echo $posFollow['id']; ?>" class="follow" />
                    <input type="button" value="Unfollow" id="unfollow_<?php echo $posFollow['id']; ?>" class="unfollow" style="display: none;"/> 

                    <br>
                    <p class="volg" id="follows_<?php echo $posFollow['id']; ?>" data-type="<?php echo $followChecker; ?>"><?php echo $followers->cntFollowers; ?></p> <p class="volg">mensen volgen deze persoon.</p>
                </div>
                <!-- end Follow-->
            </div>
            <hr>
            <div class="underSide">
                <!-- <a href="#">Posts</a>
                <a href="#">Info</a> -->
                <p class="volg"><strong>Beschrijving: </strong></p>
                <h5 class="description" class="volg"><?php echo $posFollow['description']; ?></h5>
            </div>
            
        </div>
        <div class="content--details">

            <?php $counter = 0; ?>
                <!-- start lus -->
                <?php foreach ($result as $r):
                    $postId = $r['id'];
                    ?>
   
                <div class="post post2" id="<?php echo $postId; ?>" data-id="<?php echo $postId; ?>">
   
                    <a class="postImgLink" href="single.php?post=<?php echo $r['id']; ?>">
                        <figure class="imgFilter <?php echo $r['filter']; ?>" >
                            <img src="images/posts/mini-<?php echo $r['post_img_dir']; ?>" alt="">
                        </figure>
                    </a><br>
                    <p class= "time2"><strong><?php echo Post::timeAgo($r['date_created']); ?></strong></p>
                     <!-- start Likes -->

                <div class="like2">

                <input type="button" value="Like" id="like_<?php echo $postId; ?>" class="like" />
                <input type="button" value="Unlike" id="unlike_<?php echo $postId; ?>" class="unlike" style="display: none;"/> 

                <?php
                    $likes = Like::getLikes($postId);
                    $likeChecker = Like::checkIfLiked($_SESSION['user']['id'], $postId);
                ?>
                <p class="likesCnt volg" id="likes_<?php echo $postId; ?>" data-type="<?php echo $likeChecker; ?>"><?php echo $likes->cntLikes; ?></p> <p class="volg"></p>
                </div>
                <!-- end Likes -->
                    <a href="profileDetails.php?id=<?php echo $r['user_id']; ?>" class="post__item2"><span class="infoBlock2"><strong><?php echo $r['username']; ?></strong></span></a>
                <p class="description2">
                    <?php
                        $hashtag = $r['post_description'];
                        $linked_string = preg_replace("/#([^\s]+)/", '<a href="search.php?searchResult=$1">#$1</a>', $hashtag);
                        echo $linked_string;
                    ?>
                </p>
                
               
                <!--<p><strong><a href="#"><?php //echo $r['username'];?></a> </strong></p>-->
                
                
                
                <form method="post" action="">
                    <input type="text" placeholder="Comment Here" class="textcomm" name="comment"/>
                    <input type="submit" value="Post" class="btnSub sub2" />

                    <ul class="comments2">
                        <?php
                            //echo $r['id'];
                            $comments = Comment::getAll($postId);
                            if (is_array($comments) || is_object($comments)) {
                                foreach ($comments as $c) {
                                    echo '<li class="comment">'.htmlspecialchars($c['text'], ENT_QUOTES).'</li>';
                                }
                            }

                        ?>
                    </ul>
                </form>
            </div>
            <?php ++$counter; ?>
            <?php endforeach; ?>


            
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.0.min.js" integrity="sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(()=>{
            let currentFollowerType = $('#follows_<?php echo $posFollow['id']; ?>').data('type');
            if(currentFollowerType == 1){
                $('#follow_<?php echo $posFollow['id']; ?>').css("display", "none");
                $('#unfollow_<?php echo $posFollow['id']; ?>').css("display", "inline-block");
            }else{
                $('#unfollow_<?php echo $posFollow['id']; ?>').css("display", "none");
                $('#follow_<?php echo $posFollow['id']; ?>').css("display", "inline-block");
            }

            $(".follow, .unfollow").click(function(e){
                let id = this.id;                           // Getting Button id
                let split_id = id.split("_");               // split id on _
                let text = split_id[0];                     // first part of splitted id = text
                let followsId = split_id[1];                   // second part = followsId
                let currentFollowersCnt = $("#follows_" + followsId); 
                //console.log(currentFollowersCnt)
                let followersAmount = currentFollowersCnt.html();     // amount of current likes
                // Setting type
                var type = 0;
                if(text == "follow"){
                    type = 1;
                    //console.log(type)
                }else{
                    type = 0;
                    //console.log(type)
                }
                // AJAX Request
                $.ajax({
                    method: "POST",
                    url: "ajax/save_follow.php",
                    data: {
                        followsId: followsId,
                        type: type
                    },
                    dataType: 'json'
                    
                })
                .done( function ( res ){
                    if (res.status == "success"){
                        //change the buttons
                        $("#" + id).siblings().css("display", "inline-block");
                        $("#" + id).css("display", "none");
                        //if the button was like, likeAmount +1 else -1
                        if( text == "follow"){
                            followersAmount++;
                            currentFollowersCnt.html(followersAmount);
                        }else{
                            followersAmount--;
                            currentFollowersCnt.html(followersAmount);
                        }
                    }
                });
                e.preventDefault();
            })
            let likesCntArray = $('.likesCnt').each(function() {
                let currentLikeType = $(this).data('type');
                // console.log(currentLikeType )
                let id = this.id
                let split_id = id.split("_");               // split id on _
                let text = split_id[0];                     // first part of splitted id = text
                let likeId = split_id[1];
                if(currentLikeType == 1){
                    $('#like_' + likeId).css("display", "none");
                    $('#unlike_' + likeId).css("display", "inline-block");
                }else{
                    $('#unlike_' + likeId).css("display", "none");
                    $('#like_' + likeId).css("display", "inline-block");
                }
            });

            $(".like, .unlike").click(function(e){
                let id = this.id;                           // Getting Button id
                let split_id = id.split("_");               // split id on _
                let text = split_id[0];                     // first part of splitted id = text
                let postId = split_id[1];                   // second part = postid
                let currentLikeCnt = $("#likes_" + postId); 
                let likeAmount = currentLikeCnt.html();     // amount of current likes
                // Setting type
                var type = 0;
                if(text == "like"){
                    type = 1;
                    console.log(type)
                }else{
                    type = 0;
                    console.log(type)
                }
                // AJAX Request
                $.ajax({
                    method: "POST",
                    url: "ajax/save_like.php",
                    data: {
                        postId: postId,
                        type: type
                    },
                    dataType: 'json'
                    
                })
                .done( function ( res ){
                    if (res.status == "success"){
                        //change the buttons
                        $("#" + id).siblings().css("display", "inline-block");
                        $("#" + id).css("display", "none");
                        //if the button was like, likeAmount +1 else -1
                        if( text == "like"){
                            likeAmount++;
                            currentLikeCnt.html(likeAmount);
                            $('#likes_<?php echo $postId; ?>').data('type', 0);
                        }else{
                            likeAmount--;
                            currentLikeCnt.html(likeAmount);
                            $('#likes_<?php echo $postId; ?>').data('type', 1);
                        }
                    }
                });
                e.preventDefault();
            })
        });
    </script>
</body>
</html>