<section class="mbr-section mbr-after-navbar">
    <div class="container mbr-section__container--isolated">
		<div id="accordion" role="tablist" class="col-md-12 centrado">
			<h2>No tienes permisos para visualizar esta página</h2>
			<p class="centrado">
				Debes iniciar sesión o registrarte en nuestro sitio para poder disfrutar de todo los beneficios que tenemos para usted.
			</p>
			<a href="?view=index">
				<div class="btn btn-primary texto-chico">
					<i class="fa fa-user"></i> Ingresar
				</div>
			</a>
			<a data-toggle="modal" data-target="#Registro">
				<div class="btn btn-primary texto-chico">
					<i class="fa fa-user-plus"></i> Registrate
				</div>
			</a>
			<?php 
				if(!isset($_SESSION['app_id'])) { //Si no esta definida la variable de sesion mostramos  
				    include(HTML_DIR . 'public/reg.html');  
				}
			?>
		</div>
	</div>
</section><br /><br /><br />