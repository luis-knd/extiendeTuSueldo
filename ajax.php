<?php 
/**
 * 	Recupera el modo que enviamos por ajax en las páginas a través del js y lo redirecciona al php.
 */
if ($_POST) {
	require('core/core.php');
	switch (isset($_GET['mode']) ? $_GET['mode'] : null) {
		case 'login':
			require('core/bin/ajax/goLogin.php');
		break;	
		default:
			header('location: extiendeTuSueldo/?view=index');
		break;
	}
} else {
	header('location: extiendeTuSueldo/?view=index');
}

?>