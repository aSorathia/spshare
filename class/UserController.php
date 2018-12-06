<?php
/*
combine gropname and grp list JOIN
*/ 
    
    
    class UserController{
        private $pdo;
        private $baseController;
        public function __construct($pdo,$baseController){            
            if(!is_null($pdo) && !is_null($baseController)){
                $this->baseController = $baseController;
                $this->pdo = $pdo;
            }else{
                echo "db connection erro";
                return false;
            }
        }

        public function displayInfo($userInfo){
            echo sizeof($userInfo);
            print_r($userInfo);
        }

        public function getUserInfo($uid){
            $stmt=$this->pdo->prepare(
                'SELECT `u`.`u_id`,`u`.`u_login`,`u_name`,`u`.`last_login`,`u`.`fileLimit`,`u`.`spaceLimit`,`u`.`userUsedSpace`,`g`.`g_id`,`g`.`g_name`,`g`.`g_limit`,`ug`.`gUsedSpace`
                FROM `users` `u` 
                INNER JOIN `user_group` `ug` ON `u`.`u_id`= ? AND `u`.`u_id`=`ug`.`u_id` 
                INNER JOIN `groups` `g` ON `g`.`g_id`=`ug`.`g_id`'
            );
            $stmt->bindParam( 1, $uid ,PDO::PARAM_INT);
            $stmt->execute();
            $ui = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
            return $ui;
        }

        public function getUser($uid){
            $stmt=$this->pdo->prepare('SELECT * FROM `users` where `u_id` = ?');
            $stmt->bindParam( 1, $uid,PDO::PARAM_INT );
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;
            return $user;
        }

        public function uploadFile($uploadFile,$uid,$gid,$filename,$fileDesc){
            $limit = null;

            $userInfos = $this->getUserInfo($uid);
            foreach($userInfos as $userInfo){
                if($userInfo['g_id']==$gid){
                    $limit=$userInfo;
                    break;
                }                    
            }
            
            $fileUploadFolder = 'doc_uploads/';

            $fileSize = $uploadFile['fileUpload']['size'];
            $fileName = $filename;
            $fileType = $uploadFile['fileUpload']['type'];
            $fileDesc = $fileDesc;

            $fileLimit=$limit['fileLimit'];
            $spaceLimit=$limit['spaceLimit'];
            $g_limit=$limit['g_limit'];
            $userUsedSpace=$limit['userUsedSpace'];
            $gUsedSpace=$limit['gUsedSpace'];
            
            if($fileSize<=$fileLimit){   
                if(($spaceLimit-$userUsedSpace)>$fileSize){                             
                    if(($g_limit-$gUsedSpace)>$fileSize){
                        $userUsedSpace +=$fileSize;
                        $gUsedSpace +=$fileSize;
                        $fileExtension = explode('.',$uploadFile['fileUpload']['name']);
                        if(count($fileExtension)>1){
                            $fileExtension = $fileExtension[1];
                        }else{
                            $fileExtension = '';
                        }
                        $newFilename=$this->baseController->getCryptedString(8);
                        if (move_uploaded_file($uploadFile['fileUpload']['tmp_name'], $fileUploadFolder.$newFilename)) {
                            $this->addFileRecord($uid,$gid,$fileType,$fileExtension,$fileDesc,time(),$fileName,$newFilename,$fileSize,$userUsedSpace,$gUsedSpace);
                            echo "FileU uploaded.";
                        } else {
                            echo "Error uploading.";
                        }
                    }else{
                        echo 'Group space is full';
                    }                    
                }else{
                    echo 'Cannot upload. No sufficient space ';
                }
            }else{
                echo 'Filesize has to be equal to or less than '.($fileLimit/1024).' KB';
            }
            
            /**/
        }

        private function addFileRecord($u_id, $g_id, $p_type, $p_file_extension,$p_desc,$p_time_create,$p_filename ,$p_new_fileName,$fileSize,$userUsedSpace,$gUsedSpace){
            try{
                $this->pdo->beginTransaction();

                $stmt = $this->pdo->prepare('INSERT INTO `posts`(`u_id`, `g_id`, `p_type`,`p_file_extension`, `p_desc`, `p_time_create`, `p_filename`, `p_new_fileName`,`fileSize`)
                VALUES (?,?,?,?,?,?,?,?,?)'
                );
                $stmt->bindParam( 1, $u_id, PDO::PARAM_INT);
                $stmt->bindParam( 2, $g_id, PDO::PARAM_INT );
                $stmt->bindParam( 3, $p_type, PDO::PARAM_STR,20 );
                $stmt->bindParam( 4, $p_file_extension,PDO::PARAM_STR,4  );
                $stmt->bindParam( 5, $p_desc,PDO::PARAM_STR,500 );
                $stmt->bindParam( 6, $p_time_create,PDO::PARAM_INT );
                $stmt->bindParam( 7, $p_filename ,PDO::PARAM_STR,68);
                $stmt->bindParam( 8, $p_new_fileName,PDO::PARAM_STR,8 );
                $stmt->bindParam( 9, $fileSize,PDO::PARAM_INT );

                $stmt->execute();
                $stmt=null;
                
                $stmt = $this->pdo->prepare('UPDATE `users` SET `userUsedSpace`=? WHERE `u_id`=?');
                $stmt->bindParam( 1, $uid ,PDO::PARAM_INT);
                $stmt->execute([$userUsedSpace,$u_id]);
                $stmt=null;
    
                $stmt = $this->pdo->prepare('UPDATE `user_group` SET `gUsedSpace`=? WHERE `g_id`=? && `u_id`=?');
                $stmt->bindParam( 1, $gUsedSpace,PDO::PARAM_INT);
                $stmt->bindParam( 2, $g_id,PDO::PARAM_INT);
                $stmt->bindParam( 3, $u_id,PDO::PARAM_INT);
                $stmt->execute();
                $stmt=null;

                $this->pdo->commit();
            }catch(PDOException $e){
                $this->pdo->rollBack();
                die($e->getMessage());
            }
        }

        public function removePost($pid){  
            $stmt = $this->pdo->prepare('SELECT `g_id`,`u_id`,`p_new_fileName`,`fileSize` FROM `posts` WHERE `p_id`=?');     
            $stmt->bindParam( 1, $pid ,PDO::PARAM_INT);       
            $stmt->execute();
            $fileinfo = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;            

            $fileUploadFolder = 'doc_uploads/';
            $filePath = $fileUploadFolder.$fileinfo['p_new_fileName'];

            $uid = $fileinfo['u_id'];
            $gid = $fileinfo['g_id'];

            $limit = $this->getUserInfo($uid)[0];
            $userUsedSpace=$limit['userUsedSpace'];
            $gUsedSpace=$limit['gUsedSpace'];

            $fileName = $fileinfo['p_new_fileName'];
            $filesize = $fileinfo['fileSize'];
            $referer = 'https://sp-share.herokuapp.com/dashboard.php';
            if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']==$referer){
                $inGrp = FALSE;
                $userInfos = $this->getUserInfo($_SESSION['u_id']);
                foreach($userInfos as $userInfo){
                    if($userInfo['g_id']==$gid){$inGrp=TRUE;break;}                    
                }
                if($inGrp==$gid && $_SESSION['u_id']==$uid){  
                    try{
                        $this->pdo->beginTransaction();
                      
                        $stmt = $this->pdo->prepare('DELETE FROM `posts` WHERE `p_id`=? AND `u_id`=?');
                        $stmt->bindParam( 1, $pid,PDO::PARAM_INT );
                        $stmt->bindParam( 2, $uid,PDO::PARAM_INT );
                        $stmt->execute();
                        $stmt=null;
                        
                        $userUsedSpace = ($userUsedSpace-$filesize);
                        $stmt = $this->pdo->prepare('UPDATE `users` SET `userUsedSpace`=? WHERE `u_id`=?');
                        $stmt->bindParam( 1,$userUsedSpace ,PDO::PARAM_INT );
                        $stmt->bindParam( 2, $uid,PDO::PARAM_INT );
                        $stmt->execute();
                        $stmt=null;

                        $gUsedSpace = ($gUsedSpace-$filesize);
                        $stmt = $this->pdo->prepare('UPDATE `user_group` SET `gUsedSpace`=? WHERE `g_id`=? && `u_id`=?');
                        $stmt->bindParam( 1,$gUsedSpace ,PDO::PARAM_INT );
                        $stmt->bindParam( 2, $gid ,PDO::PARAM_INT);
                        $stmt->bindParam( 3,  $uid ,PDO::PARAM_INT);
                        $stmt->execute();
                        $stmt=null;      

                        if (file_exists($filePath) && unlink($filePath)) {
                            $logstring = "Deleted $filePath\n";
                        } else {
                            $logstring = "Failed to delete $filePath\n";
                        }

                        $this->pdo->commit();
                        echo 'success';
                    }catch(Exception $e){
                        $this->pdo->rollBack();
                        die($e->getMessage());
                    }
                }else{
                    echo 'wrong';
                }
            }else{
                echo 'wrong';
            }
        }   
        public function downloadFile($pid){  
            $stmt = $this->pdo->prepare('SELECT `g_id`,`p_new_fileName`,`p_filename`,`p_file_extension` FROM `posts` WHERE `p_id`=?');            
            $stmt->bindParam( 1, $pid ,PDO::PARAM_INT);
            $stmt->execute();
            $fileinfo = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;
            $filename = $fileinfo['p_new_fileName'];
            $path = 'doc_uploads/';
            $file = $path.$filename;
            $referer = 'https://sp-share.herokuapp.com/dashboard.php';
            $inGrp = FALSE;
            $userInfos = $this->getUserInfo($_SESSION['u_id']);
            foreach($userInfos as $userInfo){
                if($userInfo['g_id']==$fileinfo['g_id']){$inGrp=TRUE;break;}                    
            }
            if($inGrp){
                if (file_exists($file) && isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']==$referer) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.$fileinfo['p_filename'].'.'.$fileinfo['p_file_extension'].'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));
                    readfile($file);
                    exit;
                }else{
                    echo 'External / Direct download is not allowed';
                }
            }
        }

        public function getFileInfo($pid){
            $stmt = $this->pdo->prepare('SELECT `p_filename`,`p_desc` FROM `posts` WHERE `p_id`=?');            
            $stmt->bindParam( 1, $pid,PDO::PARAM_INT );
            $stmt->execute();
            $fileinfo = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;
            echo json_encode(array('filename' => $fileinfo['p_filename'], 'desc' => $fileinfo['p_desc']));
        }

        public function editPost($pid,$filename,$desc){        
            $stmt = $this->pdo->prepare('UPDATE `posts` SET `p_desc`=?,`p_filename`=? WHERE `p_id`=?');            
            $stmt->bindParam( 1, $desc ,PDO::PARAM_STR,500);
            $stmt->bindParam( 2, $filename ,PDO::PARAM_STR,68);
            $stmt->bindParam( 3, $pid ,PDO::PARAM_INT);
            $stmt->execute();   
            $stmt = null;
            echo 'success';
        }
    }
?>