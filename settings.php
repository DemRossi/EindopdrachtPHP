<?php
    require_once("classes/Db.class.php");
    require_once("classes/Upload.class.php");

    session_start();
    if( isset($_SESSION['User']) ){
        //logged in user
        echo "😁";
    }else{
        //no logged in user
        echo "😒";
    }
    if( isset($_POST['uploadImage']) ){
        //echo "test ";
        //echo $_SERVER['REQUEST_METHOD'] . " ";
        $upload = new Upload;
        $upload->setFileName($_FILES['imageFile']['name']);
        $upload->setFileType($_FILES['imageFile']['type']);
        $upload->setFileTempName($_FILES['imageFile']['tmp_name']);
        $upload->setFileSize($_FILES['imageFile']['size']);
        $upload->setTargetDir("images/profilePics/");

        $result = $upload->uploadImage();


    }
    $conn = Db::getInstance();
    $stmnt = $conn->prepare('select img_dir FROM `users` WHERE email = "wesleywijsen@hotmail.com"');
    $stmnt->execute();
    $result = $stmnt->fetch(PDO::FETCH_OBJ);
    //echo $result->img_dir;

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>includeFood - Settings</title>
</head>
<body>
    <header>
        <?php require_once("nav.inc.php"); ?>
    </header>
    <div class="settings">
        <h1>Accountinstellingen</h1>

        <!--Start ProfPic-->
        <div class="setProfPic">
            <div class="edit">
                <h2>Chance profile picture</h2>
                <img class="currentPic" src="<?php echo $result->img_dir; ?>">
                <br><br>
                <form action="" method="post" enctype="multipart/form-data">
                    <p>
                        File: <input type="file" name="imageFile">
                    </p>
                    <br>
                    <input type="submit" name="uploadImage" value="Upload image">
                </form>
                <hr>
            </div>
        </div>
         <!--End ProfPic-->
          <!--Start Description-->
        <div class="setDescr">
            <div class="edit">
                <h2>Chance your description</h2>
                <form action="">
                    <textarea name="myDiscr" id="myDiscr" cols="55" rows="10"></textarea>
                    <br><br>
                    <input type="submit" name="descrSave" value="Save description">
                </form>
                <hr>
            </div>
        </div>
    </div>

</body>
</html>