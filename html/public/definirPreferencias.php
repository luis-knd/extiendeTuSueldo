<section class="mbr-section mbr-after-navbar">
    <div class="container mbr-section__container--isolated">
		<div id="accordion" role="tablist" class="col-md-12 centrado">
			<h4>Definamos tus preferencias</h4>
			<div>
	         	<form role="form">
	                        <?php $pais = new Pais;
	                        $pais->Listar(); ?>
	                        <select id="estado"><?php echo $opciones; ?></select>
					<div class="input-contact">
	                    <input type="text" id="pais" class="form-control" maxlength="80">
	                    <span><i class="glyphicon glyphicon-user"></i>
	                    	Pais
	                    </span>
	                </div>
	           		<div class="input-contact">
	                    <input type="text" id="interes" class="form-control" maxlength="150">
	                    <span><i class="glyphicon glyphicon-eye-open"></i>
	                        Moneda de Interes
	                    </span>
	                </div>
		           	<button type="button" class="btn btn-danger btn-block">
		           		<span class="glyphicon glyphicon-off"></span>Guardar Preferencias
		           	</button>
	        	</form>
	       	</div>
		</div>
	</div>
</section><br /><br /><br />