<?php 
    //require_once("classes/Db.class.php");
    //require_once("classes/Post.class.php");
    require_once("bootstrap.php");
   
    //$userName = $_SESSION['UserName'];
    if( isset($_SESSION['User']) ){
        //logged in user
        //echo "😎";
    }else{
        //no logged in user
        header('Location: login.php');
    }

    if(!isset($_GET['showitems'])){
        $itemCount = 3;
    } else {
        $itemCount = $_GET['showitems'];
    }
    // $itemCount = $_GET['showitems'] ?  : 3;

    $conn = Db::getInstance();
    $stmnt = $conn->prepare('select user_id, post_img_dir,post_description,username from posts, users where posts.user_id = users.id LIMIT  :itemCount');
    $stmnt->bindValue(':itemCount', $itemCount, PDO::PARAM_INT);
    $stmnt->execute();
    $result= $stmnt->fetchAll(PDO::FETCH_ASSOC);

    

    // // $stmnt2 = $conn->prepare('select user_id, post_img_dir,post_description,username from posts, users where posts.user_id = users.id LIMIT 3 OFFSET '.$loading);
    // $stmnt2 = $conn->prepare('select user_id, post_img_dir,post_description,username from posts, users where posts.user_id = users.id LIMIT :itemCount');
    // $stmnt2->bindValue(':itemCount', $itemCount, PDO::PARAM_INT);
    // $stmnt2->execute();
    // $result2= $stmnt2->fetchAll(PDO::FETCH_ASSOC);

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>includeFood - Home</title>
    <script src="https://code.jquery.com/jquery-3.4.0.min.js" integrity="sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg=" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <?php require_once("nav.inc.php"); ?>
    </header>
    <div class="feed">
    <div class="addContent"><a href="newPost.php">Add some fresh content here</a></div>
    <?php $counter = 0; ?>
    <!-- start lus -->
    <?php foreach($result as $r): ?>
   
    <div class="post" id="<?php echo $counter; ?>">
    <img src="<?php echo $r['post_img_dir'] ?>" alt="">
    <p class="description"><?php echo $r['post_description']?></p>
    <p><strong><?php echo $r['username'] ?></strong></p>
    </div>

    <div class="fullView" id="full-<?= $counter; ?>">
        <span class="x">X</span>
        <img src="<?php echo $r['post_img_dir'] ?>" alt="">
    </div>
    
    <?php $counter++; ?>
    <?php endforeach;?>


    <a href="index.php?showitems=<?php echo $counter + 3; ?>' class="load">Load More</a>
    
    
    </div>
    <script>
        //full view
        $('.post').on('click', function(){
            const bigImg = $(this).attr('id');
            $('#full-' + bigImg).fadeIn();
       });

       $('.x').on('click', function(){
           $('.fullView').fadeOut();
       });

       

    </script>
</body>
</html>