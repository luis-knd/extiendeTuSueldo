<?php 

require('core/core.php');

if(isset($_GET['view'])){
	if (file_exists('core/controllers/' . strtolower($_GET['view']) . 'Controller.php')) { 		//strtolower convierte lo que escribe el usuario en view en minusculas a fin de ser compatible en cualquier servidor
		include('core/controllers/' . strtolower($_GET['view']) . 'Controller.php');
	} else {
		include('core/controllers/errorController.php');
	}
} else {
	include('core/controllers/indexController.php');
}

?>