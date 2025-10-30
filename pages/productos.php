<?php 
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])){
	header("Location: ../auth/login.html");
} else {
require '../components/header.php';
if ($_SESSION['productosver']==1){
?>
<title>Leap Works - Productos</title>
<!--Contenido-->
<header class="page-header page-header-dark pb-10 bg-img-repeat overlay-primary overlay-50 bg-primary">
	<div class="container-xl px-4">
		<div class="page-header-content pt-4">
			<div class="row align-items-center justify-content-between">
				<div class="col-auto mt-4 listadoregistros">
					<h1 class="page-header-title">
						<div class="page-header-icon"><i class="fa-solid fa-basket-shopping"></i></div>
						Productos/Servicios
					</h1>
					<div class="page-header-subtitle text-light">Administración y creación de productos y servicios.
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<!-- Main page content-->
<div class="container-xl px-4 mt-n10" id="container-body">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<!-- Comienza Contenedor de Productos -->
			<button class="btn text-light formularioregistros" style="font-size: 26px;" onclick="cancelarform()"
				type="button"><i class="fa-solid fa-arrow-left"></i></button>
			<div class="card">
				<div class="card-header listadoregistros">
					<h1 class="box-title titulos" style="font-size: 24px;"><button type="button" class="btn btn-success"
							id="btn-agregar" onclick="mostrarform(true)" title="Agregar"><i
								class="fa-solid fa-circle-plus espaciado-icn"> </i> Agregar</button></h1>
					<div class="box-tools pull-right">
					</div>
				</div><!-- /.box-header -->
				<!-- Comienza div de Productos -->
				<div class="card-body table-responsive listadoregistros">
					<table id="tbllistado" class="table table-striped table-bordered table-hover table-sm dt-responsive"
						style="width:100%;">
						<thead class="bg-dark text-light">
							<th>Producto/Servicio</th>
							<th>Unidad</th>
							<th>Clave</th>
							<th>$Unitario</th>
							<th>IMG</th>
							<th>Variantes</th>
							<th>Acciones</th>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<th>Producto/Servicio</th>
							<th>Unidad</th>
							<th>Clave</th>
							<th>$Unitario</th>
							<th>IMG</th>
							<th>Variantes</th>
							<th>Acciones</th>
						</tfoot>
					</table>
				</div>
				<div class="card-body formularioregistros" style="height: auto;">
					<form name="formulario" id="formulario" method="POST">
						<div class="row">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<h1 class="box-title" style="font-size: 24px;">PRODUCTO/SERVICIO</h1>
							</div>
							<div class="form-group col-lg-8 col-md-8 col-sm-12 mb-3">
								<label for="nombre"><span style="color:red;">*</span>Nombre:</label>
								<input type="hidden" name="productoID" id="productoID" value="">
								<input type="text" class="form-control" name="nombre" id="nombre"
									placeholder="Nombre de Producto o Servicio" required>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label for="precioVenta"><span style="color:red;">*</span>Precio Venta:</label>
								<div class="input-group">
									<span class="input-group-text">$</span>
									<input type="number" min="0" step="0.01" style="text-align:right;"
										data-number-to-fixed="2" data-number-stepfactor="100"
										class="form-control currency" lang="en-US" class="form-control"
										name="precioVenta" id="precioVenta" placeholder="Precio de Venta" required>
								</div>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label for="medida_id"><span style="color:red;">*</span>Unidad de Medida:</label>
								<select id="medida_id" name="medida_id" class="form-control selectpicker"
									placeholder="Selecciona la Unidad de Medida" data-live-search="true" required>
								</select>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label for="clave_id"><span style="color:red;">*</span>Clave de
									Facturación:</label>
								<select id="clave_id" name="clave_id" class="form-control selectpicker"
									placeholder="Selecciona la Clave de Facturación" data-live-search="true" required>
								</select>
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-3 mb-3">
								<label>Imagen Producto/Servicio:</label>
								<input type="hidden" name="imagenActual" id="imagenActual" value="">
								<input type="file" class="form-control" name="imagen" id="imagen"
									placeholder="Imagen Producto/Servicio">
								<small class="help-block">Sólo acepta archivos JPG, PNG, GIF y PDF.</small>
							</div>
							<div class="form-group col-lg-1 col-md-1 col-sm-1 mb-3">
								<label style="color: #f8f9fc;">.</label><br>
								<span id="imagenMuestra" style="height:85px;width:85px;">
							</div>
							<div class="form-group col-lg-6 col-md-6 col-sm-12 mb-3">
								<label for="sku">SKU:</label>
								<input type="text" class="form-control" name="sku" id="sku"
									placeholder="SKU de Producto o Servicio">
							</div>
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<label for="observaciones">Observaciones:</label>
								<textarea id="observaciones" name="observaciones" class="form-control"
									placeholder="Escribe alguna Observación" rows="3"></textarea>
							</div>
							<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<button class="btn btn-danger" onclick="cancelarform()" type="button"><i
										class="fa fa-arrow-circle-left espaciado-icn"></i> Cancelar/Volver</button>
								<button class="btn btn-primary float-end" type="submit" id="btnGuardar"><i
										class="fa fa-save espaciado-icn"></i> Guardar</button>
							</div>
						</div>
					</form>
				</div>
			</div><!-- /.termina card -->
		</div><!-- /.termina Contenedor de Productos -->
	</div><!-- /.row -->
</div><!-- /.container-fluid -->
<!--Fin-Contenido-->

<!-- Modal Variantes -->
<div class="modal fade" id="modalVariantes" tabindex="-1" role="dialog" aria-labelledby="modalVariantes"
	aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Variantes</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<table id="tblVariantes" class="table table-striped table-bordered table-condensed table-hover compact"
					style="width:100%;">
					<thead style="background-color:#5B5B5B;color:#FFFFFF;">
						<th style="width: 125px;">Nombre</th>
						<th>Unidad</th>
						<th>Precio</th>
						<th style="width: 85px;">IMG</th>
					</thead>
					<tbody>
					</tbody>
					<tfoot>
						<th style="width: 125px;">Nombre</th>
						<th>Unidad</th>
						<th>Precio</th>
						<th style="width: 85px;">IMG</th>
					</tfoot>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<!-- Fin modal Variantes -->
<?php
} else {
	require 'noacceso.php';
}
require '../components/footer.php';
?>
<script type="text/javascript" src="../services/producto.js"></script>
<?php 
}
ob_end_flush();
?>