<?php
    require_once 'bootstrap/bootstrap.php';

    if (isset($_SESSION['user'])) {
        //logged in user
        //echo "😎";
    } else {
        //no logged in user
        header('Location: login.php');
    }
    if (Post::getAllFollows($_SESSION['user']['id'])) {
        $result = Post::getAllFollows($_SESSION['user']['id']);
    } else {
        $noFollowers = true;
    }

    if (isset($_GET['color'])) {
        $colorCode = $_GET['color'];
        $result = Post::getPostsByColor($colorCode);
    }
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cssgram/0.1.10/cssgram.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>IncludeFood - Your Follows</title>
    
</head>
<body>
    <header>
        <?php require_once 'nav.inc.php'; ?>
    </header>
    <div class="homepage">
        <!-- <a href="newPost.php" class="btn">Add some fresh content here</a> -->
        <div class="feed">
        <?php if (isset($noFollowers)): ?>
			<div class="noFollows">
				<h2 class="noFollows--title">
					Sorry, we hebben nog geen vrienden gevonden.
                </h2>
                <div class="emptyFollower">
                    <div class="emptyState"></div>
                </div>
		    </div>
        <?php endif; ?>
        
        <?php if (isset($result)): ?>
            <!-- start lus posts-->
            <?php foreach ($result as $r): ?>
                
                <div class="post post--home" id="<?php echo $r['id']; ?>">
                    <a class="postImgLink" href="single.php?post=<?php echo $r['id']; ?>">
                        <figure class="imgFilter <?php echo $r['filter']; ?>" >
                            <img src="images/posts/mini-<?php echo $r['post_img_dir']; ?>" alt="">
                        </figure>
                    </a>
                    
                    <div class="colors-wrapper">
                        <a class="colors" href="index.php?color=<?php echo str_replace('#', '', $r['color1']); ?>" style="background-color:<?php echo $r['color1']; ?>"></a>
                        <a class="colors" href="index.php?color=<?php echo str_replace('#', '', $r['color2']); ?>" style="background-color:<?php echo $r['color2']; ?>"></a>
                        <a class="colors" href="index.php?color=<?php echo str_replace('#', '', $r['color3']); ?>" style="background-color:<?php echo $r['color3']; ?>"></a>
                        <a class="colors" href="index.php?color=<?php echo str_replace('#', '', $r['color4']); ?>" style="background-color:<?php echo $r['color4']; ?>"></a>
                    </div>
                    <div class="post_info">
                        <a href="profileDetails.php?id=<?php echo $r['user_id']; ?>" class="post__item"><span class="infoBlock"><strong><?php echo $r['username']; ?></strong></span></a>
                        <p class="description"><?php echo $r['post_description']; ?></p>
                    </div>
                </div>

            <?php endforeach; ?>
            <!-- einde lus -->
        
        </div>
        <a href="#" class="load btn">Load More</a>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.0.min.js" integrity="sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg=" crossorigin="anonymous"></script>
    <script>
        function whichPage() {
            if (top.location.pathname === '/index.php')
                {
                    return "index";
                } else {
                    return false;
                }
        }
        let page = whichPage();
        $(".load").on("click", function(e){

            let counter = $(".post").length + 5;
            
            $.ajax({
                method: "POST",
                url: "ajax/load_more.php",
                data: { 
                    showitems: counter,
                    page: page
                },
                dataType: 'json'
            })
            .done( function( res ){
                console.log(res);
                if(res.status == "success"){
                    const posts = res.data.posts;
                    let newImages = [];

                    $('.feed').empty();

                    posts.map(post => {
                        //regex hashtag
                        //timing

                        newImages.push(`<div class="post post--home" id="${post.id}">
                                    <a class="postImgLink" href="single.php?post=${post.id}">
                                        <figure class="imgFilter ${post.filter}" >
                                            <img src="images/posts/mini-${post.post_img_dir}" alt="">
                                        </figure>
                                    </a>
                                <div class="colors-wrapper">
                                    <a class="colors" href="index.php?color=${post.color1.replace('#', '')}" style="background-color:${post.color1}"></a>
                                    <a class="colors" href="index.php?color=${post.color2.replace('#', '')}" style="background-color:${post.color2}"></a>
                                    <a class="colors" href="index.php?color=${post.color3.replace('#', '')}" style="background-color:${post.color3}"></a>
                                    <a class="colors" href="index.php?color=${post.color4.replace('#', '')}" style="background-color:${post.color4}"></a>
                                </div>
                                <div class="post_info">
                                    <a href="profileDetails.php?id=${post.user_id}" class="post__item"><span class="infoBlock"><strong>${post.username}</strong></span></a>
                                    <p class="description">${post.post_description}</p>
                                </div>
                                
                                
                            </div>`);
                    });

                    $('.feed').append(newImages);               
                }
            });
            e.preventDefault();
        });
    </script>
</body>
</html>