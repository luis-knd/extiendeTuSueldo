<?php

if(!empty($_POST['user']) and !empty($_POST['pass'])) {
    $db     = new Conexion();
    $data   = $db->escape($_POST['user']);
    $pass   = Encrypt($_POST['pass']);
    /**
     * 	Hago el select de consulta si existe el usuario y la clave
     */
    $db->where ('email',$data);
    $db->where ('pass',$pass);
    $colum  = 'id, id_permisos, nombre, apellido';
    $existe = $db->get("users",1,$colum);
    if($existe) {
        $id         = $existe[0]['id'];
        $permiso    = intval($existe[0]['id_permisos']);
        $nombre		= $existe[0]['nombre'];
        $apellido	= $existe[0]['apellido'];
        if($permiso == 1 or $permiso == 2) {
			/**
			 * 	Definimos las Coockies por 30 días
			 */
            if($_POST['sesion']) { 
                ini_set('session.cookie_lifetime', time() + (60*60*24*30)); 
            }

            /**
             * 	Creamos la variable de sesión en un Array
             */
            $_SESSION['app_id'] = array('id' => $id, 'permisos' => $permiso, 'nombre' => $nombre, 'apellido' => $apellido);

            /**
             *  Verificamos si ya fueron definidas las preferencias
             */
            $db->where ('id_usuario',$_SESSION['app_id']['id']);
            $rows = 'id_pais_residencia, id_pais_interes, id_pais_interes_dos, id_pais_interes_tres, id_pais_interes_cuatro';
            $preferencias = $db->get('preferencias',1,$rows);
            /**
             *  Comprobamos que esten definidas y las agregamos a la variable de Sesion
             */
            if($preferencias) {
                $preference = array(
                    'id_pais_residencia'    => $preferencias[0]['id_pais_residencia'], 
                    'id_pais_interes'       => $preferencias[0]['id_pais_interes'],
                    'id_pais_interes_dos'   => $preferencias[0]['id_pais_interes_dos'],
                    'id_pais_interes_tres'  => $preferencias[0]['id_pais_interes_tres'],
                    'id_pais_interes_cuatro'=> $preferencias[0]['id_pais_interes_cuatro']);
                array_push($_SESSION['app_id'], $preference); 
            }
            echo 1;
        } else {
            echo 
            '<div class="alert alert-dismissible alert-danger justify">
                <button type="button" class="close" data-dismiss="alert">x</button>
                <strong>Error:</strong> No has activado tu usuario aún. Ingresa a tu mail y sigue las instrucciones para activarlo.
            </div>';
        }
    } else {
        echo 
        '<div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">x</button>
            <strong>ERROR:</strong> Las credenciales son incorrectas.
        </div>';
    }
    $db->disconnect();
} else {
    echo 
    '<div class="alert alert-dismissible alert-danger">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <strong>ERROR:</strong> Todos los datos deben estar llenos.
    </div>';
}

?>
