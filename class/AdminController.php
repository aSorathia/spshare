<?php
/*
combine gropname and grp list JOIN
*/ 


    date_default_timezone_set('UTC');

    class AdminController{
        private $pdo;
        public function __construct($pdo){
            if(!is_null($pdo)){
                $this->pdo = $pdo;
            }else{
                echo "db connection erro";
                return false;
            }
        }

        public function getRequest(){
            $req_stmt = $this->pdo->prepare("SELECT `r`.`r_id`,`r`.`r_name`,`r`.`r_login`,`g`.`g_name` FROM `request` `r` inner join `groups` `g` on `g`.`g_id`=`r`.`g_id`");
            $req_stmt->execute();
            $req_row = $req_stmt->fetchAll(PDO::FETCH_ASSOC);
            $req_stmt = null;
            return $req_row;
        }
        public function getRequestEx(){
            $req_stmt = $this->pdo->prepare("SELECT `u_login`,`g_name` FROM `requestex`");
            $req_stmt->execute();
            $req_row = $req_stmt->fetchAll(PDO::FETCH_ASSOC);
            $req_stmt = null;
            return $req_row;
        }

        public function checkUserStatus($usrLogin){
            $grp_stmt = $this->pdo->prepare("SELECT `u_id` from `users` WHERE `u_login` = ? LIMIT 1");
            $grp_stmt->bindParam( 1, $usrLogin,PDO::PARAM_STR,16);
            $grp_stmt->execute();
            $row = $grp_stmt->fetch();
            $grp_stmt = null;
            return $row['u_id'];
        }

        public function checkgrpStatus($grpName){
            $grp_stmt = $this -> pdo->prepare("SELECT `g_id` from `groups` WHERE `g_name` = ? LIMIT 1");
            $grp_stmt->bindParam( 1, $grpName,PDO::PARAM_STR,68 );
            $grp_stmt->execute();
            $row = $grp_stmt->fetch();
            $grp_stmt = null;
            return $row['g_id'];
        }  
        public function checkgrpStatusById($g_id){
            $grp_stmt = $this -> pdo->prepare("SELECT `g_id` from `groups` WHERE `g_id` = ? LIMIT 1");
            $grp_stmt->bindParam( 1, $g_id,PDO::PARAM_INT );
            $grp_stmt->execute();
            $row = $grp_stmt->fetch();
            $grp_stmt = null;
            return $row['g_id'];
        }        

        public function checkUserPresentInGrp($uid,$gid){
            if($uid==0 || $gid==0){
                return null;
            }
            $grp_stmt = $this->pdo->prepare("SELECT `ug_id` from `user_group` WHERE `u_id` = ? AND `g_id` = ? LIMIT 1");
            $grp_stmt->bindParam( 1, $uid,PDO::PARAM_INT );
            $grp_stmt->bindParam( 2, $gid, PDO::PARAM_INT);
            $grp_stmt->execute();
            $row = $grp_stmt->fetch();
            $grp_stmt = null;
            return $row['ug_id'];
        }


        public function create_group($grpName,$grpLimit){
            $isPresent = $this->checkgrpStatus($grpName);
            $g_limit = ($grpLimit*1024);
            if(!$isPresent){
                $stmt = $this->pdo->prepare('INSERT INTO `groups`( `g_name`, `g_limit`) VALUES (?,?)');
                $stmt->bindParam( 1, $grpName,PDO::PARAM_STR, 80 );
                $stmt->bindParam( 2,$g_limit,PDO::PARAM_INT );
                if($stmt->execute()){
                    return "success";
                }else{
                    return "wrong";
                }
            }
        }


        public function create_user($loginName,$request_id,$fileLimit,$spaceLimit,$uType){
            $isPresent = $this->checkUserStatus($loginName);
            $timeCreated = time();
            $fileLimit = ($fileLimit*1024);
            $spaceLimit = ($spaceLimit*1024);
            if(!$isPresent){
                $rd = $this->getRequestDetails($request_id);
                if($loginName==$rd["r_login"]){
                    $stmt = $this->pdo->prepare('INSERT INTO `users`(`u_name`, `u_login`, `u_pass`, `r_id`, `created`, `fileLimit`, `spaceLimit`)
                    VALUES (?,?,?,?,?,?,?)'
                    );
                    $stmt->bindParam( 1, $rd["r_name"],PDO::PARAM_STR, 80 );
                    $stmt->bindParam( 2, $rd["r_login"],PDO::PARAM_STR, 16 );
                    $stmt->bindParam( 3, $rd["r_pass"],PDO::PARAM_STR, 100 );
                    $stmt->bindParam( 4, $uType,PDO::PARAM_INT);
                    $stmt->bindParam( 5, $timeCreated,PDO::PARAM_INT );
                    $stmt->bindParam( 6, $fileLimit,PDO::PARAM_INT );
                    $stmt->bindParam( 7, $spaceLimit,PDO::PARAM_INT );
                    if($stmt->execute()){
                        echo "success";
                    }else{
                        echo "wrong";
                    }
                }else{
                    echo 'wrong';
                }
                
            }
        }

        public function add_user_to_grp($uid,$gid){
            $isPresent = $this->checkUserPresentInGrp($uid,$gid);
            $creationtime = time();
            if(!$isPresent){
                $stmt = $this->pdo->prepare('INSERT INTO `user_group`(`u_id`, `g_id`, `create_epoch`)
                VALUES (?,?,?)'
                );
                $stmt->bindParam( 1, $uid,PDO::PARAM_INT );
                $stmt->bindParam( 2, $gid,PDO::PARAM_INT );
                $stmt->bindParam( 3, $creationtime,PDO::PARAM_INT );
                if($stmt->execute()){
                    echo "success";
                    $stmt=null;
                }else{
                    echo "wrong";
                }
                $stmt=null;
            }
        }

        public function update_group($gid,$grpName,$grpLimit){
            $grpLimit = $grpLimit*1024;
            $stmt = $this->pdo->prepare('UPDATE `groups` SET `g_name`=?,`g_limit`=? WHERE `g_id`=?');
            $stmt->bindParam( 1, $grpName, PDO::PARAM_STR,68);
            $stmt->bindParam( 2, $grpLimit,PDO::PARAM_INT );
            $stmt->bindParam( 3 ,$gid ,PDO::PARAM_INT);
            if($stmt->execute()){
                return "success";
            }else{
                return "wrong";
            }
        }

        public function update_user($uid,$loginName,$fileLimit,$spaceLimit,$uType){
            $fileLimit = ($fileLimit*1024);
            $spaceLimit = ($spaceLimit*1024);
            $stmt = $this->pdo->prepare('UPDATE `users` SET `u_login`=?,`r_id`=?,`fileLimit`=?,`spaceLimit`=? WHERE `u_id`=?');
            $stmt->bindParam( 1, $loginName,PDO::PARAM_STR,16 );
            $stmt->bindParam( 2, $uType,PDO::PARAM_INT );
            $stmt->bindParam( 3, $fileLimit,PDO::PARAM_INT );
            $stmt->bindParam( 4, $spaceLimit,PDO::PARAM_INT );
            $stmt->bindParam( 5, $uid,PDO::PARAM_INT );
            if($stmt->execute()){
                echo "success";
            }else{
                echo "wrong";
            }
        }

        public function removeUserTransaction($uid){
            try{
                $this->pdo->beginTransaction();
                $this->removeUser($uid);
                $this->pdo->commit();
            }catch(PDOException $e){
                $this->pdo->rollBack();
                die($e->getMessage());
            }

        }

        public function removeUser($uid){                
                echo $uid.'df';   
                $stmt = $this->pdo->prepare('SELECT `p_new_fileName` FROM `posts` WHERE `u_id`=?');
                $stmt->bindParam( 1, $uid,PDO::PARAM_INT );
                $stmt->execute();
                $stmt_row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt=null; 
                $fileUploadFolder = 'doc_uploads/';
                foreach($stmt_row as $filename){
                    $filePath = $fileUploadFolder.$filename['p_new_fileName'];
                    if (file_exists($filePath) && unlink($filePath)) {
                        $logstring = "Deleted $filePath\n"; 
                    } else {
                        $logstring = "Failed to delete $filePath\n";
                    };
                }
                $stmt = $this->pdo->prepare('DELETE FROM `posts` WHERE `u_id`=?');
                $stmt->bindParam( 1,$uid,PDO::PARAM_INT  );
                $stmt->execute();
                $stmt=null;
                
                $stmt = $this->pdo->prepare('DELETE FROM `user_group` WHERE `u_id`=?');
                $stmt->bindParam( 1, $uid,PDO::PARAM_INT );
                $stmt->execute();
                $stmt=null;

                $stmt = $this->pdo->prepare('DELETE FROM `users` WHERE `u_id`=?');
                $stmt->bindParam( 1,$uid, PDO::PARAM_INT );
                $stmt->execute();
                $stmt=null;

                
        }

        public function removeGrp($gid){
            try{
                $this->pdo->beginTransaction();
                $stmt = $this->pdo->prepare('DELETE FROM `user_group` WHERE `g_id`=?');
                $stmt->bindParam( 1, $gid,PDO::PARAM_INT );
                $stmt->execute();
                $stmt=null; 
                $stmt = $this->pdo->prepare('DELETE FROM `groups` WHERE `g_id`=?');
                $stmt->bindParam( 1,$gid,PDO::PARAM_INT  );
                $stmt->execute();
                $stmt=null;
                $this->pdo->commit();
            }catch(PDOException $e){
                $this->pdo->rollBack();
                die($e->getMessage());
            }
        }

        public function getRequestDetails($uid){
            $grp_stmt = $this->pdo->prepare("SELECT * from `request` WHERE `r_id` = ? LIMIT 1");
            $grp_stmt->bindParam( 1, $uid,PDO::PARAM_INT );
            $grp_stmt->execute();
            $row = $grp_stmt->fetch();
            $grp_stmt = null;
            return $row;
        }

        public function getGroupInfo($gid){
            $stmt=$this->pdo->prepare('SELECT * FROM `groups` where `g_id` = ?');
            $stmt->bindParam( 1,$gid,PDO::PARAM_INT  );
            $stmt->execute();
            $group = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;
            echo json_encode($group);
        }

        public function getUserInfo($uid){
            $stmt=$this->pdo->prepare('SELECT * FROM `users` where `u_id` = ?');
            $stmt->bindParam( 1, $uid ,PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;
            echo json_encode($user);
        }
    }
?>