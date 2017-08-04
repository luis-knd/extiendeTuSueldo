<?php 

$db 	= new Conexion();
$fecha 	= date('d/m/Y h:i a');
/**
 * 	Hago el update de la ultima visita cuando hago el logout
 */
$upd 	= array('ultima_conexion' => $fecha);
$db->where('id',$_SESSION['app_id']['id']);
$db->update('users', $upd);
$db->disconnect();

/**
 * 	Destruyo las variables de sesion creadas y redirecciono al inicio
 */
unset($_SESSION['app_id']);
header('location:?view=index')


 ?>