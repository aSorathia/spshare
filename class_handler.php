<?php

    require_once 'class/BaseController.php';
    require_once 'class/UserController.php';
    require_once 'class/AdminController.php';
    require_once 'config/app_config.php';
    
    $dbConnectionObj = DBConnect::getDbConnect();
    $baseController = new BaseController($dbConnectionObj);
    $userController = new UserController($dbConnectionObj,$baseController);
    $adminController = new AdminController($dbConnectionObj);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_REQUEST['action'])){
            if($_REQUEST['action']=="register"){
                $baseController->register($_POST["name"], $_POST["login"], $_POST["pass"], $_POST["groups_choice"]);
            }
            
            if($_REQUEST['action']=="requestNewGrp"){
                if(isset($_POST['g_name'])&& !empty($_POST['g_name'])){
                    echo $baseController->requestNewGrp($_POST['u_login'],$_POST['g_name']);                 
                }else{
                    echo 'please check if all the fields are filled';
                }
            }
            if($_REQUEST['action']=="joinNewGrp"){
                if(isset($_POST['groups_choice'])){
                    $groups = $_POST['groups_choice'];
                    foreach($groups as $group){
                        $isGrpPresent = $adminController->checkgrpStatusById($group);
                        $isUsrPresent = $adminController->checkUserStatus($_POST['u_login']);
                        $isUserPresentInGrp = $adminController->checkUserPresentInGrp($isUsrPresent,$isGrpPresent);
                        if(!$isUserPresentInGrp && $isGrpPresent && $isUsrPresent){
                            echo $adminController->add_user_to_grp($isUsrPresent,$isGrpPresent);
                        }else{
                            echo 'Already present in group';
                        }
                    }
                }else{
                    echo 'Please select a group name to join';
                }                
                //echo $baseController->requestNewGrp($_POST['u_id'],$_POST['u_login'],$_POST['g_name'],$_POST['g_limit']);
            }
            if($_REQUEST['action']=="getGroupInfo"){
                echo $adminController->getGroupInfo($_POST['gid']);
            }
            if($_REQUEST['action']=="getUserInfo"){
                echo $adminController->getUserInfo($_POST['uid']);
            }
            if($_REQUEST['action']=="update_group"){
                echo $adminController->update_group($_POST['gid'],$_POST['g_name'],$_POST["g_limit"]);
            }

            if($_REQUEST['action']=="update_user"){
                echo $adminController->update_user($_POST['uid'],$_POST['u_login'],$_POST['fileLimit'],$_POST['spaceLimit'],$_POST['u_type']);
            }

            if($_REQUEST['action']=="create_group"){
                echo $adminController->create_group($_POST['g_name'],$_POST["g_limit"]);
            }

            if($_REQUEST['action']=="create_user"){
                $isGrpPresent = $adminController->checkgrpStatus($_POST['g_name']);
                $adminController->create_user($_POST['u_login'],$_POST['request_id'],$_POST['fileLimit'],$_POST['spaceLimit'],$_POST['u_type']);
                $isUsrPresent = $adminController->checkUserStatus($_POST['u_login']);
                $isUserPresentInGrp = $adminController->checkUserPresentInGrp($isUsrPresent,$isGrpPresent);
                if(!$isUserPresentInGrp && $isGrpPresent && $isUsrPresent){
                    echo $adminController->add_user_to_grp($isUsrPresent,$isGrpPresent);
                }
            }
            if($_REQUEST['action']=="uploadFile"){
                $userController->uploadFile($_FILES,$_POST['u_id'],$_POST['g_id'],$_POST['p_fileName'],$_POST['p_desc']);
            }  
            if (isset($_REQUEST['action']) && $_REQUEST['action']=="remove"){
                if(isset($_POST['pid'])){
                    $userController->removePost($_POST['pid']);
                }else{
                    header("Location: {$_SERVER["HTTP_REFERER"]}");
                }        
            }
            if (isset($_REQUEST['action']) && $_REQUEST['action']=="getFileInfo"){
                if(isset($_POST['pid'])){
                    $userController->getFileInfo($_POST['pid']);
                }else{
                    header("Location: {$_SERVER["HTTP_REFERER"]}");
                }        
            }
        
            if (isset($_REQUEST['action']) && $_REQUEST['action']=="editPost"){
                if(isset($_POST['p_id'])){
                    $userController->editPost($_POST['p_id'],$_POST['p_fileName'],$_POST['p_desc']);
                }else{
                    header("Location: {$_SERVER["HTTP_REFERER"]}");
                }        
            }  
            if (isset($_REQUEST['action']) && $_REQUEST['action']=="removeUser"){
                if(isset($_POST['uid'])){
                    $adminController->removeUserTransaction($_POST['uid']);
                }else{
                    header("Location: {$_SERVER["HTTP_REFERER"]}");
                }        
            } 
            if (isset($_REQUEST['action']) && $_REQUEST['action']=="removeGrp"){
                if(isset($_POST['gid'])){
                    $adminController->removegrp($_POST['gid']);
                }else{
                    header("Location: {$_SERVER["HTTP_REFERER"]}");
                }        
            }        
        }
    }

    if (isset($_GET['download']) && preg_match( "/^[0-9]{1,2}$/",$_GET['download'])){
        $userController->downloadFile($_GET['download']);
    }

?>
