<?php 
	$db = new Conexion();
	if (isset($_SESSION['app_id'])) {
		include(HTML_DIR . 'main/principal.php');
	} else {
		include(HTML_DIR . 'index/index.php');
	}
	$db->disconnect();
?>