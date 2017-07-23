<?php

if(!empty($_POST['user']) and !empty($_POST['pass'])) {
    $db     = new Conexion();
    $data   = $db->escape($_POST['user']);
    $pass   = Encrypt($_POST['pass']);
    $db->where ('email',$data);
    $db->where ('pass',$pass);
    $colum  = 'id, id_permisos';
    $existe = $db->get("users",1,$colum);
    if($existe) {
        $id         = $existe[0]['id'];
        $permiso    = intval($existe[0]['id_permisos']);
        if($permiso == 1 or $permiso == 2) {
            if($_POST['sesion']) { 
                ini_set('session.cookie_lifetime', time() + (60*60*24*30)); 
            }
            $_SESSION['app_id'] = $id;
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
