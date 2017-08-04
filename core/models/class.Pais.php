<?php 

class Pais {
	private $db;
	private $id;
	private $nombre;
	private $id_moneda;
	private $acronimo;
	
	public function __construct() {
		$this->db = new Conexion();
	}

	public function Listar(){
		$db = new Conexion();
		$paises	= $db->get("pais",null,"*");
		if ($paises) {
			while( $fila = $paises->fetch_array() )
			{
				$opciones.='<option value="'.$fila["id"].'">'.utf8_encode($fila["nombre"]).'</option>';
			}
			/*$id     = $paises[0]['id'];
		    $nombre	= $paises[0]['nombre'];
		    $datos 	= [$id,$nombre];*/
		} else {
			$paises = false;
		}

		return $opciones;
	}
}
 ?>