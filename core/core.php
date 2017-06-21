<?php 

/**
 * 	NUCLEO DE TODA LA APLICACIÓN
 */

session_start();

#Define la Zona Horaria de la Aplicación
date_default_timezone_set('America/Caracas');

#Constantes de la Conexión a BD
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','candedb');

#Constantes de la APP
define('HTML_DIR','html/'); 						//Constante para enrutar a las vistas en el directorio
define('APP_TITLE','Extiende tu Sueldo'); 			//Constante con un string para los titulos
define('APP_COPY','Todos los derechos Reservados Copyright &copy; '. date('Y',time()). '. Elaborado por <a href src="http://www.lcdesign.com.ve" target="_blank"><img src="views/images/web/lcd.png" alt="LC Design" width="50" height="20"> </a>'); 	//Constante para el derecho de autor que muestra el año actual
define('APP_URL','http://localhost/extiendeTuSueldo/');		//Constante para la URL
define('APP_DESCRIPTION','Aplicación para llevar un registro de tus gastos e ingresos en el mes y de esta forma llevar un control de tus finanzas');					//Descripción del Web Site

#Constantes de PHPMailer
define('PHPMAILER_HOST', 'mail.lcdesign.com.ve');			//Host del Mail
define('PHPMAILER_USER', 'lcandelario@lcdesign.com.ve');	//Usuario del Mail
define('PHPMAILER_PASS', '088008quetepasa');				//Clave del Mail
define('PHPMAILER_PORT', 465);								//Puerto del Mail

#Constantes Básicas de Personalización


#Estructura
require('core/models/class.Conexion.php');			//incluye la clase de conexion a la BD
require('core/bin/functions/ObtenerDivisas.php');

#Constantes del Tipo de Cambio
$dolarPorPeso = conversor_monedas("USD","ARS",1);	// Dolar a Pesos Argentinos
settype($dolarPorPeso,'float');						// Parseamos la variable a Float

$euroPorPeso = conversor_monedas("EUR","ARS",1);	// Euros a Pesos Argentinos
settype($euroPorPeso,'float'); 						// Parseamos la variable a Float

$realPorPeso = conversor_monedas("BRL","ARS",1); 	// Reales a Pesos Argentinos
settype($realPorPeso,'float');						// Parseamos la variable a Float

$bolivarPorDolar = dolarToday()['USD']['transferencia']; // Bolívar por Dolar
settype($bolivarPorDolar,'float');						 // Parseamos la variable a Float
$bolivarPorPeso  = $bolivarPorDolar / $dolarPorPeso; 	 // Bolívar por Pesos Arg
?>