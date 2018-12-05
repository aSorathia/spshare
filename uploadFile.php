<?php 
    require_once 'class_handler.php';

    $status = $baseController->checkLoginState();
    if(!$status[0]){
        header("location:index.php");
        exit();
    }else if($status[1]==2){
        header("location: admin_dashboard.php");
        exit();
    }
    $ui = $userController->getUserInfo($_SESSION['u_id'])[0];
?>

<html>
    <head>
        <script src="web_resources/js/jquery.min.js"></script>
        <script src="web_resources/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="web_resources/css/bootstrap.min.css">
        <link rel="stylesheet" href="web_resources/css/custom.css">
    
    </head>
    <body>
    <form role="form" class="file_upload_form" method ="POST" enctype="multipart/form-data" action="class_handler.php?action=uploadFile">
        <div class="form-group">
            <label for="Select Your Group">Example select</label>
            <select class="form-control" id="g_id" name="g_id">
                <?php
                    $stmt = $baseController->getPDO()->prepare("SELECT `groups`.`g_id`,`groups`.`g_name` from `groups` inner join `user_group` on `user_group`.`g_id` = `groups`.`g_id` where `user_group`.`u_id`=?");                      
                    $stmt->bindParam( 1, $ui['u_id'],PDO::PARAM_INT );
                    $stmt->execute();
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        echo '<option value="'.htmlspecialchars($row['g_id'], ENT_QUOTES , 'UTF-8').'">'.htmlspecialchars($row['g_name'], ENT_QUOTES , 'UTF-8').'</option>';
                    }
                ?>
                
            </select>
        </div>
        <div class="form-group">
            <input type="hidden" id="u_id" name="u_id" value="<?php echo htmlspecialchars($ui['u_id'], ENT_QUOTES , 'UTF-8')?>">
        </div>
        <div class="form-group">
            <label for="p_fileName">File Name</label>
            <input type="text" class="form-control" id="p_fileName" name="p_fileName" placeholder="Enter User Name">
        </div>
        <div class="form-group">
            <label for="p_desc">File Descriptione</label>
            <textarea class="form-control" id="p_desc" name="p_desc" placeholder="Enter File Description"></textarea>
        </div>
        <div class="form-group">
            Uploaded on :<span id="uploadEpoch"></span><br>
            Last Accessed on :<span id="accessEpoch"></span><br>
        </div>
        <div class="form-group">
            <label for="fileUpload">File to upload</label>
            <input type="file" name="fileUpload" class="form-control-file" id="fileUpload">
        </div>
        <button type="submit" class="btn btn-default btn-success btn-block file_upload_submit"><span class="glyphicon glyphicon-off" ></span>Upload</button>
    </form>
    </body>
</html>