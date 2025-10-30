<?php 
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])){
	header("Location: ../auth/login.html");
} else {
require '../components/header.php';
if ($_SESSION['pagosver']==1){
?>
<title>Leap Works - Pagos Recibidos</title>
<style>
.bootstrap-select .disabled {
	background-color: #c1cbd9 !important;
}

.bootstrap-select {
	background-color: #FFFFFF !important;
}
</style>
<!--Contenido-->
<header class="page-header page-header-dark pb-10 bg-img-repeat overlay-primary overlay-50 bg-primary">
	<div class="container-xl px-4">
		<div class="page-header-content pt-4">
			<div class="row align-items-center justify-content-between">
				<div class="col-auto mt-4 listadoregistros">
					<h1 class="page-header-title">
						<div class="page-header-icon"><i class="fa-solid fa-dollar-sign"></i></div>
						Pagos Recibidos
					</h1>
					<div class="page-header-subtitle text-light">Administración y creación de pagos recibidos.</div>
				</div>
			</div>
		</div>
	</div>
</header>
<!-- Main page content-->
<div class="container-xl px-4 mt-n10" id="container-body">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<!-- Comienza Contenedor de Pagos -->
			<button class="btn text-light formularioregistros" style="font-size: 26px;" onclick="cancelarform()"
				type="button"><i class="fa-solid fa-arrow-left"></i></button>
			<div class="card">
				<div class="card-header listadoregistros">
					<h1 class="box-title titulos" style="font-size: 24px;"><button class="btn btn-success"
							id="btn-agregar" onclick="mostrarform(true)" title="Agregar"><i
								class="fa-solid fa-circle-plus espaciado-icn"> </i> Agregar</button></h1>
					<div class="box-tools pull-right">
					</div>
				</div><!-- /.box-header -->
				<!-- Comienza div de Pagos -->
				<div class="card-body table-responsive listadoregistros">
					<table id="tbllistado" class="table table-striped table-bordered table-hover table-sm dt-responsive"
						style="width:100%;">
						<thead class="bg-dark text-light">
							<th>#</th>
							<th>Fecha</th>
							<th>Nombre Cliente</th>
							<th>Metodo Pago</th>
							<th>Pago</th>
							<th></th>
							<th>Status</th>
							<th>Acciones</th>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<th>#</th>
							<th>Fecha</th>
							<th>Nombre Cliente</th>
							<th>Metodo Pago</th>
							<th>Pago</th>
							<th></th>
							<th>Status</th>
							<th>Acciones</th>
						</tfoot>
					</table>
				</div>
				<div class="card-body formularioregistros" style="height: auto;">
					<form name="formulario" id="formulario" method="POST">
						<div class="row">
							<div class="form-group col-lg-8 col-md-8 col-sm-8 mb-3">
								<h1 class="box-title" style="font-size: 24px;">PAGO</h1>
							</div>
							<?php 
							if ($_SESSION['pagoshistorial']==1){
								echo '<div class="form-group col-lg-4 col-md-4 col-sm-4 mb-3">
									<button type="button" class="form-group btn btn-primary float-end" id="botonHistorial" onclick="muestraHistorial()"><i class="fa fa-history" aria-hidden="true"></i> Historial</button>
								</div>';
							}
						?>
							<div class="form-group col-lg-6 col-md-6 col-sm-12 mb-3">
								<label># Pago Efectuado:</label>
								<input type="text" class="form-control" name="pagoID" id="pagoID" readonly
									style="background-color:#e0e5ec;">
							</div>
							<div class="form-group col-lg-6 col-md-6 col-sm-12 mb-3">
							</div>
							<div class="form-group col-lg-8 col-md-8 col-sm-12 mb-3">
								<label><span style="color:red;">*</span>Cliente:</label>
								<select id="cliente_id" name="cliente_id" class="form-control selectpicker"
									placeholder="Selecciona el Cliente" data-size="10" data-live-search="true" required>
								</select>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label><span style="color:red;">*</span>Fecha de Pago:</label>
								<div class="form-group">
									<div class="input-group date" id="fechaPago" data-target-input="nearest">
										<input type="text" class="form-control datetimepicker-input"
											data-target="#fechaPago" name="fechaPago" id="fechaPagoInput" required />
										<div class="input-group-text" data-target="#fechaPago"
											data-toggle="datetimepicker">
											<i class="fa fa-calendar"></i>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label><span style="color:red;">*</span>Método de Pago:</label>
								<select id="metodopago_id" name="metodopago_id" class="form-control selectpicker"
									placeholder="Selecciona el Método de Pago" required>
								</select>
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
								<label>Banco:</label>
								<input type="text" class="form-control" name="banco" id="banco"
									placeholder="Nombre del Banco">
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
								<label>Número de Cuenta o Tarjeta:</label>
								<input type="text" class="form-control" name="numero_cuenta" id="numero_cuenta"
									placeholder="Número de Cuenta">
							</div>
							<div class="form-group col-lg-2 col-md-2 col-sm-12 mb-3">
								<label>Parcialidad:</label>
								<input type="number" class="form-control" name="parcialidad" id="parcialidad" readonly
									style="background-color:#e0e5ec;">
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label>Saldo Anterior:</label>
								<div class="input-group">
									<span class="input-group-text">$</span>
									<input type="number" min="0" step="0.01"
										style="text-align:right;background-color:#e0e5ec;" data-number-to-fixed="2"
										data-number-stepfactor="100" class="form-control currency" lang="en-US"
										class="form-control" name="saldoAnterior" id="saldoAnterior"
										placeholder="Costo del Curso" readonly>
								</div>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label><span style="color:red;">*</span>Este Pago:</label>
								<div class="input-group">
									<span class="input-group-text">$</span>
									<input type="number" min="0" step="0.01" style="text-align:right;"
										data-number-to-fixed="2" data-number-stepfactor="100"
										class="form-control currency" lang="en-US" class="form-control" name="pago"
										id="pago" placeholder="Este Pago" oninput="calculaRestante(this.value)"
										required>
								</div>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label>Saldo Restante:</label>
								<div class="input-group">
									<span class="input-group-text">$</span>
									<input type="number" min="0" step="0.01"
										style="text-align:right;background-color:#e0e5ec;" data-number-to-fixed="2"
										data-number-stepfactor="100" class="form-control currency" lang="en-US"
										class="form-control" name="saldoRestante" id="saldoRestante"
										placeholder="Saldo Restante" readonly>
								</div>
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
								<label>Documento de Pago:</label>
								<input type="hidden" name="comprobantePagoActual" id="comprobantePagoActual">
								<input type="file" class="form-control" name="comprobantePago" id="comprobantePago"
									placeholder="Comprobante de Pago">
								<small class="help-block">Sólo acepta archivos JPG, PNG, GIF y PDF.</small>

							</div>
							<div class="form-group col-lg-1 col-md-1 col-sm-12 mb-3">
								<label style="color: #f8f9fc;">.</label><br>
								<a class="btn btn-primary" href="" id="comprobantePagoMuestra"
									target="_blank">Documento</a>
							</div>
						</div>
						<div class="row mt-4">
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
		</div><!-- /.termina Contenedor de Pagos -->
	</div><!-- /.row -->
</div><!-- /.container-fluid -->
<!--Fin-Contenido-->

<!-- Modal Historial -->
<div class="modal fade" id="modalHistorial" tabindex="-1" role="dialog" aria-labelledby="modalHistorial"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Historial</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<table id="tblHistorial" class="table table-striped table-bordered table-condensed table-hover compact">
					<thead style="background-color:#5B5B5B;color:#FFFFFF;">
						<th style="width:50px;"></th>
						<th style="width:125px;">Fecha</th>
						<th>Acción</th>
					</thead>
					<tbody>
					</tbody>
					<tfoot>
						<th style="width:50px;"></th>
						<th style="width:125px;">Fecha</th>
						<th>Acción</th>
					</tfoot>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<!-- Fin modal Historial -->
<?php
} else {
	require 'noacceso.php';
}
require '../components/footer.php';
?>
<script type="text/javascript" src="../services/pago_recibido.js"></script>
<?php 
}
ob_end_flush();
?>