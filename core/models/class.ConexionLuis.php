<?php 

class Conexion extends mysqli {
	
	/**
	 * 	Declaramos el Constructor de la Clase.
	 * 	@var Host, User, Pass, Nombre BD [Constantes] [Declaradas previamente en el core]
	 */
	public function __construct() {
		parent::__construct(DB_HOST,DB_USER,DB_PASS,DB_NAME);
		$this->connect_errno ? die("Error en la conexion a la base de datos") : null;
		$this->set_charset("utf8");
	}

	/**
	 * Obtiene el número de filas de un resultado
	 * @param  Query
	 * @return Devuelve el número de Filas
	 */
	public function obtenerFilas($query) {
		return mysqli_num_rows($query);
	}

	public function liberar($query) {
		return mysqli_free_result($query);
	}

	public function recorrer($query) {
		return mysqli_fetch_array($query);
	}
}

?>