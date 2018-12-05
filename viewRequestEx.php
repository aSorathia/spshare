<?php
    require_once 'class_handler.php';
    
    $status = $baseController->checkLoginState();
    if(!$status[0]){
        header("location:index.php");
        exit();
    }else if($status[1]==1){
        header("location: dashboard.php");
        exit();
    }
    date_default_timezone_set('America/Chicago');
?>
<html>
    <head>
        <script src="web_resources/js/jquery.min.js"></script>
        <script src="web_resources/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="web_resources/css/bootstrap.min.css">
    </head>
    <body>
        <nav class="navbar bg-primary">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">SP Share</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-item"><a href="groups.php" class=" btn btn-lg btn-primary">View Groups</a></li>
                        <li class="nav-item"><a href="users.php" class=" btn btn-lg btn-primary">View Users</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Request<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li class="nav-item"><a href="viewrequestEx.php">View Request Existing</a></li>
                                <li class="nav-item"><a href="admin_dashboard.php" >View Request New</a></li>                
                            </ul>
                        </li>
                        <?php if($baseController->checkLoginState()){echo '<li class="nav-item"><a href="logout.php" class=" btn btn-lg btn-primary">logout</a></li>';}?>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
        
        <div class="container-fluid">
            <table border=1>
                <tr><td colspan="5">Existing user Request</td></tr>
                <tr><th>User LoginName</th><th>Group Name</th><th>GroupLimit</th><th>Create</th><th>User-Group Status</th></tr>
                <?php
                    $req_stmt = $baseController->getPDO()->prepare("SELECT * FROM `requestex`");
                    $req_stmt->execute();
                    while($req_row = $req_stmt->fetch(PDO::FETCH_ASSOC)){
                        echo "<tr><td>".$req_row['u_login']."</td><td>".$req_row['g_name']."</td><td>".$req_row['g_limit']."</td>";
                        $isGrpPresent = $adminController->checkgrpStatus($req_row['g_name']);
                        $isUsrPresent = $adminController->checkUserStatus($req_row['u_login']);
                        $isUserPresentInGrp = $adminController->checkUserPresentInGrp($isUsrPresent,$isGrpPresent);
                        if($isUserPresentInGrp){
                            echo "<td colspan=3>Accepted</td></tr>";
                        }else{
                            echo '<script>console.log("'.$req_row['g_name'].':'.$isGrpPresent.'")</script>';
                            if($isGrpPresent){
                                echo '<td>Group Created</td>';
                            }else{
                                echo '<td><button type="button" class="btn btn-warning btn-lg create_Grp_pop" data-group-name="'.$req_row['g_name'].'" data-group-limit="'.$req_row['g_limit'].'">Create Group</button></td>';
                            } 
                            
                            if(!$isUserPresentInGrp && $isGrpPresent && $isUsrPresent){
                                echo '<td><button type="button" class="btn btn-warning btn-lg add_usr_grp_pop"  data-group-name="'.$req_row['g_name'].'" data-user-name="'.$req_row['u_login'].'" data-user-id="'.$isUsrPresent.'" data-grp-id="'.$isGrpPresent.'">Not Present</button></td></tr>';
                            }else{
                                echo '<td><button type="button" class="btn btn-warning btn-lg disabled" data-toggle="tooltip" data-placement="top" title="Please check if group already created">Not Present</button></td></tr>';
                               
                            }  
                        }
                    }
                    $req_stmt=null;
                ?>
            </table>
        </div>
        <div class="modal fade" id="groupCreateModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> Create Group</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="grp_creation_form">
                            <div class="form-group">
                                <label for="g_name"><span class="glyphicon glyphicon-user"></span> Group Name</label>
                                <input type="text" class="form-control" id="g_name" name="g_name" placeholder="Enter Group Name">
                            </div>
                            <div class="form-group">
                                <label for="g_limit"><span class="glyphicon glyphicon-eye-open"></span>Max Group Space Limit</label>
                                <input type="number" id="g_limit" class="form-control" name="g_limit" placeholder="Min: 1KB, max: 10240KB" min="1" max="10240" />
                            </div>
                            <button type="button" class="btn btn-default btn-success btn-block" id="create_grp_submit"><span class="glyphicon glyphicon-off" ></span>Create Group</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addUserGrpModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span>Add User to the Group</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="add_user_grp_form">
                            <div class="form-group">
                                <input type="hidden" id="ug_uid" name="ug_uid" value="">
                                <input type="hidden" id="ug_gid" name="ug_gid" value="">
                                <input type="hidden" id="r_id" name="r_id" value="">
                            </div>
                            <div class="form-group">
                                <label for="ug_login"><span class="glyphicon glyphicon-user"></span>User Login</label>
                                <input type="text" disabled class="form-control" id="ug_login" name="ug_login" placeholder="Enter User Name">
                            </div>
                            <div class="form-group">
                                <label for="ug_grp"><span class="glyphicon glyphicon-user"></span>Group Name</label>
                                <input type="text" disabled class="form-control" id="ug_grp" name="ug_grp" placeholder="Enter Group Name">
                            </div>
                            <button type="button" class="btn btn-default btn-success btn-block" id="add_usr_grp_submit"><span class="glyphicon glyphicon-off" ></span>Add User to Group</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            $(".create_Grp_pop").click(function(){
                $("#g_name").val($(this).attr("data-group-name"))  
                $("#g_limit").val($(this).attr("data-group-limit"))  
                $("#groupCreateModal").modal();
            });

            $("#create_grp_submit").on('click', function(e) {                   
                $form = $("#grp_creation_form");
                $.ajax({
                    type: "POST",
                    url: "class_handler.php?action=create_group",
                    data: $form.serialize(),
                    success: function(response){
                        console.log(response);
                        if (response == "success"){
                            $('#groupCreateModal').modal('hide');
                            location.reload();
                        }else{
                            alert('There was some error in creating group');
                        }
                    }
                });                    
            });

            $(".add_usr_grp_pop").click(function(){
                $("#ug_login").val($(this).attr("data-user-name"))  
                $("#ug_grp").val($(this).attr("data-group-name"))  
                $("#ug_uid").val($(this).attr("data-user-id"))  
                $("#ug_gid").val($(this).attr("data-grp-id"))  
                $("#addUserGrpModal").modal();
            });

            $("#add_usr_grp_submit").on('click', function(e) {
                $form = $("#add_user_grp_form");
                $.ajax({
                    type: "POST",
                    url: "class_handler.php?action=add_user_grp",
                    data: $form.serialize(),
                    success: function(response,request){      
                        console.log(response);                      
                        if (response == "success"){
                            $('#addUserGrpModal').modal('hide');
                            location.reload();
                        }else{
                            alert('There was some error in creating Users');
                        }
                    }
                });                    
            });
        </script>
    </body>    
</html>