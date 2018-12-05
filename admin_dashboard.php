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
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
        <link rel="stylesheet" href="web_resources/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="web_resources/css/custom.css">
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
                        <li class="nav-item"><button type="button" class="btn btn-primary btn-lg create_Grp_pop">Create Group</button></li>
                        <?php if($baseController->checkLoginState()){echo '<li class="nav-item"><a href="logout.php" class=" btn btn-lg btn-primary">logout</a></li>';}?>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table table-hover table-bordered"  id="newRequest_table">
                        <thead>    
                            <tr>   
                                <th>Name</th>
                                <th>Login</th>
                                <th>group</th>
                                <th>User-status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $req_rows = $adminController->getRequest();
                                foreach ($req_rows as $req_row){
                                    echo "<tr><td>".htmlspecialchars($req_row['r_name'], ENT_QUOTES , 'UTF-8')."</td><td>".htmlspecialchars($req_row['r_login'], ENT_QUOTES , 'UTF-8')."</td><td>".htmlspecialchars($req_row['g_name'], ENT_QUOTES , 'UTF-8')."</td>";
                                    $isGrpPresent = $adminController->checkgrpStatus($req_row['g_name']);
                                    $isUsrPresent = $adminController->checkUserStatus($req_row['r_login']);
                                    $isUserPresentInGrp = $adminController->checkUserPresentInGrp($isUsrPresent,$isGrpPresent);
                                    if($isUserPresentInGrp){
                                        echo "<td colspan=3>Accepted</td></tr>";
                                    }else{                            
                                        if($isUsrPresent){
                                            echo '<td>User created</td>';
                                        }else{
                                            echo '<td><button type="button" class="btn btn-warning btn-lg create_usr_pop" data-user-name="'.htmlspecialchars($req_row['r_login'], ENT_QUOTES , 'UTF-8').'" data-request-user-id="'.htmlspecialchars($req_row['r_id'], ENT_QUOTES , 'UTF-8').'" data-grp-name="'.htmlspecialchars($req_row['g_name'], ENT_QUOTES , 'UTF-8').'">create user </button></td>';
                                        }  
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table table-hover table-bordered"  id="exRequest_table">
                        <thead>    
                            <tr>   
                                <th>UserName</th>
                                <th>GroupName</th>
                                <th>Create Group</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $req_rows = $adminController->getRequestEx();
                                foreach ($req_rows as $req_row){
                                    echo "<tr><td>".htmlspecialchars($req_row['u_login'], ENT_QUOTES , 'UTF-8')."</td><td>".htmlspecialchars($req_row['g_name'], ENT_QUOTES , 'UTF-8')."</td>";
                                    $isGrpPresent = $adminController->checkgrpStatus($req_row['g_name']);                                    
                                    if($isGrpPresent){
                                        echo "<td colspan=3>Created</td></tr>";
                                    }else{
                                        echo '<td><button type="button" class="btn btn-warning btn-lg create_Grp_pop" data-grp-name="'.htmlspecialchars($req_row['g_name'], ENT_QUOTES , 'UTF-8').'">create Group </button></td>';
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
                
        
        
        <!-- Modal -->
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

        <div class="modal fade" id="userCreateModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> Create User</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="user_creation_form">
                            <div class="form-group">
                                <input type="hidden" id="request_id" name="request_id" value="">
                                <input type="hidden" class="g_name" name="g_name" value="">
                            </div>
                            <div class="form-group">
                                <label for="u_login"><span class="glyphicon glyphicon-user"></span> User Login</label>
                                <input type="text" class="form-control" id="u_login" name="u_login" placeholder="Enter User Name">
                            </div>
                            <div class="form-group">
                                <label for="u_type"><span class="glyphicon glyphicon-eye-open"></span>User Type</label>
                                <select id="u_type" class="form-control" name="u_type">
                                    <option value="1" selected>general-user</option>
                                    <option value="2">admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ug_file_limit"><span class="glyphicon glyphicon-user"></span>Max Per File Limit</label>
                                <input type="number" id="fileLimit" class="form-control" name="fileLimit" placeholder="Min: 1KB, max: 10240KB" min="1" max="10240" />
                            </div>
                            <div class="form-group">
                                <label for="ug_file_limit"><span class="glyphicon glyphicon-user"></span>Max User Space Limit</label>
                                <input type="number" id="spaceLimit" class="form-control" name="spaceLimit" placeholder="Min: 1KB, max: 10240KB" min="1" max="10240" />
                            </div>
                            <button type="button" class="btn btn-default btn-success btn-block" id="create_usr_submit"><span class="glyphicon glyphicon-off" ></span>Create User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        ​<script>
            $(document).ready(function(){
                $('#newRequest_table').DataTable();
                $('#exRequest_table').DataTable();
                
                $('[data-toggle="tooltip"]').tooltip();
                $(".create_Grp_pop").click(function(){
                    $("#g_name").val($(this).attr("data-grp-name"))  
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

                $(".create_usr_pop").click(function(){
                    $("#u_login").val($(this).attr("data-user-name"))  
                    $("#request_id").val($(this).attr("data-request-user-id"))  
                    $(".g_name").val($(this).attr("data-grp-name"))  
                    $("#userCreateModal").modal();
                });

                $("#create_usr_submit").on('click', function(e) {
                    $form = $("#user_creation_form");
                    $.ajax({
                        type: "POST",
                        url: "class_handler.php?action=create_user",
                        data: $form.serialize(),
                        success: function(response){      
                            console.log(response);                      
                            if (response == "successsuccess"){
                                $('#userCreateModal').modal('hide');
                                location.reload();
                            }else{
                                alert('There was some error in creating Users');
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
            });
        </script>
    </body>
</html>