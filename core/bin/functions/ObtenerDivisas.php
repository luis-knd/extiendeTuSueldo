<?php 

/**
 * Funcion de la API de Google para convertir el valor de las divisas
 * @param  [String] $moneda_origen  Código del país de origen
 * @param  [String] $moneda_destino Código del país de destino
 * @param  [Int] 	$cantidad       Cantidad a transformar
 */
function conversor_monedas($moneda_origen,$moneda_destino,$cantidad) {
    $get = file_get_contents("https://www.google.com/finance/converter?a=$cantidad&from=$moneda_origen&to=$moneda_destino");
    $get = explode("<span class=bld>",$get);
    $get = explode("</span>",$get[1]);  
    return preg_replace("/[^0-9\.]/", null, $get[0]);
}


/**
 * Funcion de la API de Dolar Today para obetener el precio del Dolar paralelo
 * @return [Object] Devuelve el objeto json, parseado en string
 */
function dolarToday()
{
  $url 	 ='https://s3.amazonaws.com/dolartoday/data.json';
  $json  = file_get_contents($url);
  $json  = utf8_encode($json);
  $array = json_decode($json,true);
  return $array;
}

 ?>