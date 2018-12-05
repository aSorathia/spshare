<?php
	require_once 'class_handler.php';
?>

<html>
	<body>
		<form method="post" action="class_handler.php?action=register">
			name:
			<input type="text" name="name">
			login:
			<input type="text" name="login">
			pass:
			<input type="text" name="pass">
			group:
			<select class="form-control" id="groups_choice" name="groups_choice">
                <?php
                    $stmt = $baseController->getPDO()->prepare("SELECT `g_id`,`g_name` from `groups`");                      
                    $stmt->bindParam( 1, $ui['u_id'],PDO::PARAM_INT );
                    $stmt->execute();
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        echo '<option value="'.htmlspecialchars($row['g_id'], ENT_QUOTES , 'UTF-8').'">'.htmlspecialchars($row['g_name'], ENT_QUOTES , 'UTF-8').'</option>';
                    }
                ?>                
            </select>
			<input type="hidden" name="destination" value="<?php echo $_SERVER["REQUEST_URI"]; ?>"/>
			<input type="submit">
		</form>
	</body>
</html>
