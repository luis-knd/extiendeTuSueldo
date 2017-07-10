<?php include(HTML_DIR . 'overall/header.php'); ?>
<body>
	<?php include(HTML_DIR . 'overall/topnav.php'); ?>
	<section class="mbr-section mbr-after-navbar">
        <div class="container mbr-section__container--isolated">
            <div class="centrado-porcentual col-md-4">
            	<div id="_AJAX_LOGIN_"></div>
		       	<div>
		         	<form role="form">
		         		<h4>
		         			<span class="glyphicon glyphicon-lock"></span> 
		         			Iniciar Sesión
		         		</h4>

						<div class="input-contact">
		                    <input type="text" id="usuario" class="form-control" maxlength="80">
		                    <span><i class="glyphicon glyphicon-user"></i>
		                        Usuario o Email
		                    </span>
		                </div>
		           		<div class="input-contact">
		                    <input type="password" id="clave" class="form-control" maxlength="150">
		                    <span><i class="glyphicon glyphicon-eye-open"></i>
		                        Contraseña
		                    </span>
		                </div>
			           	<div class="checkbox">
			             	<label><input type="checkbox" value="1" id="sesion" checked>Recordarme</label>
			           	</div>
			           	<button type="button" class="btn btn-danger btn-block">
			           		<span class="glyphicon glyphicon-off"></span>Iniciar Sesión
			           	</button>
		        	</form>
		       	</div><br />
		       	<div>
		         	<p>¿No estás registrado? <a data-toggle="modal" data-target="#Registro">Registrate!</a></p>
		         	<p><a data-toggle="modal" data-target="#Lostpass">Recuperar contraseña</a></p>
		       	</div>
            </div>
            <?php 
				if(!isset($_SESSION['app_id'])) { //Si no esta definida la variable de sesion mostramos  
				    include(HTML_DIR . 'public/reg.html'); 
				    include(HTML_DIR . 'public/lostpass.html'); 
				}

			?>
        </div>
    </section>
    
	<script src=views/app/js/login.js></script>
	<?php include(HTML_DIR . 'overall/footer.php'); ?>
</body>
</html>