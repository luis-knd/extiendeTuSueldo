<?php include(HTML_DIR . 'overall/header.php'); ?>
<body>
	<?php include(HTML_DIR . 'overall/topnav.php'); ?>
	<?php if (isset($_SESSION['app_id'])) { 
		if (!isset($_SESSION['app_id']['pais_residencia'])) { 
			include(HTML_DIR . 'public/definirPreferencias.php');
		} else { ?>
		<aside class="mbr-section mbr-after-navbar col-lg-12 right">
			<i class="btn btn-link btn-xs"> 
				<img src="views/app/images/banderas/usa.png" class="bandera"> 
				<?php echo number_format($dolarPorPeso,2,",","."); ?>  
			</i>
			<i class="btn btn-link btn-xs">
				<img src="views/app/images/banderas/euro.png" class="bandera"> 
				<?php echo number_format($euroPorPeso,2,",","."); ?>
			</i>
			<i class="btn btn-link btn-xs">
				<img src="views/app/images/banderas/brasil.png" class="bandera"> 
				<?php echo number_format($realPorPeso,2,",","."); ?> 
			</i>
			<i class="btn btn-link btn-xs">
				<img src="views/app/images/banderas/venezuela.png" class="bandera">
				<?php echo '<i title="'.$bolivarPorDolar .'">'. number_format($bolivarPorPeso,2,",","."). '</i>'; ?> 
			</i>
		</aside>
		<section class="mbr-section mbr-after-navbar">
	        <div class="container mbr-section__container--isolated left">

				<div id="accordion" role="tablist" class="col-md-3">
				    <div class="card">
					    <div class="card-header" role="tab" id="menuUno">
					      	<p class="mb-0">
						        <a data-toggle="collapse" data-parent="#accordion" href="#primero">
						          	<i class="fa fa-plus btn btn-danger anchoCompleto"> Ingresos</i>
						        </a>
					      	</p>
					    </div>
					    <div id="primero" class="collapse" role="tabpanel" aria-labelledby="menuUno">
					       	<ul class="list-group">
					          	<a href="#" class="list-group-item">
					            	<span class="tag tag-danger tag-pill float-xs-right">Nuevo</span>
					         	</a>
					         	<a href="#" class="list-group-item">
					            	<span class="tag tag-default tag-pill float-xs-right">Listar</span>
					          	</a>
					        </ul>     
					    </div>
					    
					    <div class="card">
						    <div class="card-header" role="tab" id="menuDos">
							    <p class="mb-0">
							        <a data-toggle="collapse" data-parent="#accordion" href="#segundo">
							          <i class="fa fa-minus btn btn-danger anchoCompleto"> Egresos</i>
							        </a>
							    </p>
						    </div>
						    <div id="segundo" class="collapse" role="tabpanel" aria-labelledby="menuDos">
						       	<ul class="list-group">
						          	<a href="#" class="list-group-item">
						            	<span class="tag tag-danger tag-pill float-xs-right">Nuevo</span>
						         	</a>
						         	<a href="#" class="list-group-item">
						            	<span class="tag tag-default tag-pill float-xs-right">Listar</span>
						          	</a>
						        </ul>     
						    </div>
					      
						    <div class="card">
							    <div class="card-header" role="tab" id="menuDos">
							      	<p class="mb-0">
							        	<a data-toggle="collapse" data-parent="#accordion" href="#tercero">
							        	<i class="fa fa-map-o btn btn-danger anchoCompleto"> Plan Mensual</i>
							        	</a>
							      	</p>
							    </div>
							    <div id="tercero" class="collapse" role="tabpanel" aria-labelledby="menuDos">
							       	<ul class="list-group">
								        <a href="#" class="list-group-item">
								            <span class="tag tag-default tag-pill float-xs-right">Estadisticas</span>
								        </a>
								        <a data-toggle="collapse" data-parent="#accordion" href="#terceroUno" class="list-group-item">
								            <span class="tag tag-default tag-pill float-xs-right">Productos <i class="fa fa-caret-down float-xs-right"></i></span>
								        </a>
									    <div id="terceroUno" class="collapse" role="tabpanel" aria-labelledby="menuDos">
										    <ul class="list-group">
											    <a href="#" class="list-group-item">
											        <span class="tag tag-default tag-pill float-xs-right">Nuevo</span>
											    </a>
											    <a href="#" class="list-group-item">
										         	<span class="tag tag-default tag-pill float-xs-right">Listar</span>
											    </a>
										    </ul>     
										</div>
										<a data-toggle="collapse" data-parent="#accordion" href="#terceroDos" class="list-group-item">
								            <span class="tag tag-default tag-pill float-xs-right">Supermercados<i class="fa fa-caret-down float-xs-right"></i></span>
								        </a>
									    <div id="terceroDos" class="collapse" role="tabpanel" aria-labelledby="menuDos">
										    <ul class="list-group">
											    <a href="#" class="list-group-item">
											        <span class="tag tag-default tag-pill float-xs-right">Nuevo</span>
											    </a>
											    <a href="#" class="list-group-item">
										         	<span class="tag tag-default tag-pill float-xs-right">Listar</span>
											    </a>
										    </ul>     
										</div>
							        </ul>     
							    </div>
					  		</div>

					  		<div class="card" role="tab" id="menuCuatro">
						      	<p class="mb-0">
							        <a data-toggle="collapse" data-parent="#accordion" href="#cuarto">
							          	<i class="fa fa-bank btn btn-danger anchoCompleto"> Ahorros</i>
							        </a>
						      	</p>
						    </div>
						    <div id="cuarto" class="collapse" role="tabpanel" aria-labelledby="menuCuatro">
						       	<ul class="list-group">
						          	<a href="#" class="list-group-item">
						            	<span class="tag tag-danger tag-pill float-xs-right">Ver Ahorros</span>
						         	</a>
						         	<a href="#" class="list-group-item">
						            	<span class="tag tag-default tag-pill float-xs-right">Comprar Divisas</span>
						          	</a>
						        </ul>     
						    </div>

					  		<div class="card" role="tab" id="menuCinco">
						      	<p class="mb-0">
							        <a data-toggle="collapse" data-parent="#accordion" href="#quinto">
							          	<i class="fa fa-file-text btn btn-danger anchoCompleto"> Reportes</i>
							        </a>
						      	</p>
						    </div>
						    <div id="quinto" class="collapse" role="tabpanel" aria-labelledby="menuCinco">
						       	<ul class="list-group">
						          	<a href="#" class="list-group-item">
						            	<span class="tag tag-danger tag-pill float-xs-right">Mensual</span>
						         	</a>
						         	<a href="#" class="list-group-item">
						            	<span class="tag tag-default tag-pill float-xs-right">Ingresos</span>
						          	</a>
						         	<a href="#" class="list-group-item">
						            	<span class="tag tag-default tag-pill float-xs-right">Egresos</span>
						          	</a>
						        </ul>     
						    </div>
						</div>
					</div>
				</div>
				<main class="col-md-9">
					<pre>
						<?php print_r($_SESSION['app_id']); ?>
					</pre>
			       	<h4 class="centrado">Resumen</h4>
					<table class="table">
						<thead>
							<th>
								<p class="centrado">Descripción</p>
							</th>
							<th>
								<p class="centrado">Pesos Arg</p>
							</th>
							<th>
								<p class="centrado">Dolares</p>
							</th>
						</thead>
						<tbody>
							<tr>
								<td>
									<p class="centrado">Efectivo</p>
								</td>
								<td>
									<p class="centrado">5000$</p>
								</td>
								<td>
									<p class="centrado"><?php echo number_format(5000/$dolarPorPeso,0,",",".").' U$D'; ?></p>
								</td>
							</tr>
							<tr>
								<td>
									<p class="centrado">Cuenta Galicia</p>
								</td>
								<td>
									<p class="centrado">8000$</p>
								</td>
								<td>
									<p class="centrado"><?php echo number_format(8000/$dolarPorPeso,0,",",".").' U$D'; ?></p>
								</td>
							</tr>
							<tr>
								<td>
									<p class="centrado">Cuenta HSBC</p>
								</td>
								<td>
									<p class="centrado">18000$</p>
								</td>
								<td>
									<p class="centrado"><?php echo number_format(18000/$dolarPorPeso,0,",",".").' U$D'; ?></p>
								</td>
							</tr>
							<tr>
								<td>
									<p class="centrado">Tarjeta de Credito</p>
								</td>
								<td>
									<p class="centrado">2000$</p>
								</td>
								<td>
									<p class="centrado"><?php echo number_format(2000/$dolarPorPeso,0,",",".").' U$D'; ?></p>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<th>
								<p class="centrado">Total</p>
							</th>
							<th>
								<p class="centrado">33000$</p>
							</th>
							<th>
								<p class="centrado"><?php echo number_format(33000/$dolarPorPeso,0,",",".").' U$D'; ?></p>
							</th>
						</tfoot>
					</table><br />

					<h4 class="centrado">Plan Mensual</h4>
					<table class="table">
						<tbody>
							<tr>
								<td>
									<p class="centrado">Estimado a Gastar</p>
								</td>
								<td>
									<p class="centrado">25000$</p>
								</td>
							</tr>
							<tr>
								<td>
									<p class="centrado">Gastos Actualmente</p>
								</td>
								<td>
									<p class="centrado">18000$</p>
								</td>
							</tr>
							<tr>
								<td>
									<p class="centrado">Situación Actual</p>
								</td>
								<td>
									<div class="progress progress-striped active centrado">
									  	<div class="progress-bar progress-bar-warning" style="width: 75%"></div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
			    </main>
			</div>
	    </section><br /><br />	
	<?php 
		} 
	} else { 
	 	include(HTML_DIR . 'public/sinpermiso.php');
	}
	include(HTML_DIR . 'overall/footer.php'); ?>
</body>
</html>