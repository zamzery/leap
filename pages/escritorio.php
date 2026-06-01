<?php 
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])){
	header("Location: ../auth/login.html");
} else {
require '../components/header.php';
if ($_SESSION['escritoriover']==1){
?>
<title>Leap Works - Escritorio</title>
<style>
#tooltip {
	padding: 8px;
	border-radius: 10px;
	border: 2px solid #ccc;
	width: 300px;
	height: auto;
	background: #fff;
	color: #000;
	position: absolute;
	z-index: 10001;
	-webkit-box-shadow: 0 4px 4px rgba(0, 0, 0, 0.3), 0 0 10px;
	-moz-box-shadow: 0 4px 4px rgba(0, 0, 0, 0.3), 0 0 10px;
	box-shadow: 0 4px 4px rgba(0, 0, 0, 0.3), 0 0 10px;
}

.fc-toolbar-title::first-letter {
	text-transform: uppercase
}

.tooltipClass {
	position: relative;
	display: inline-block;
	border-bottom: 1px dotted black;
	/* If you want dots under the hoverable text */
}

/* Tooltip text */
.tooltipClass .tooltiptext {
	visibility: hidden;
	min-width: 210px;
	background-color: black;
	color: #fff;
	padding: 5px 10px;
	border-radius: 6px;
	font-size: 12px;
	font-weight: normal;

	/* Position the tooltip text - see examples below! */
	position: absolute;
	z-index: 1;
}

.tooltipClass:hover .tooltiptext {
	visibility: visible;
}
</style>
<!-- Full Calendar-->
<link href="../public/css/fullcalendar.min.css" rel="stylesheet" type="text/css">

<!--Contenido-->
<header class="page-header page-header-dark pb-10 bg-img-repeat">
	<div class="container-xl px-4">
		<div class="page-header-content pt-4">
			<div class="row align-items-center justify-content-between">
				<div class="col-auto mt-4 listadoregistros">
					<h1 class="page-header-title">
						<div class="page-header-icon"><i class="fa-solid fa-chart-simple"></i></div>
						Escritorio
					</h1>
					<div class="page-header-subtitle text-light">Visualización de reportes y registros.</div>
				</div>
			</div>
		</div>
	</div>
</header>
<!-- Main page content-->
<div class="container-xl px-4 mt-n10" id="container-body">
	<button class="btn text-light formularioregistros" style="font-size: 26px;" onclick="cancelarformAlumno()"
		type="button"><i class="fa-solid fa-arrow-left"></i></button>
	<div class="row">
		<!-- <div class="col-xxl-12 col-xl-12 mb-4 formularioregistros" style="margin-top:50px;">
			<label for="clienteID"></label>
			<select id="clienteID" name="clienteID" class="form-control selectpicker" data-style="btn-dark"
				data-live-search="true" title="Selecciona al Cliente" onchange="mostrar(this)" value="0"></select>
		</div> -->
		<div class="col-lg-6 col-xl-3 mb-4 formulariogrupos" style="height:130px;">
			<a href="#tblFacturas" style="text-decoration: none;">
				<div class="card bg-primary text-white h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div class="me-3">
								<div class="text-white small">Ventas del Mes a Hoy</div>
								<div class="text-xl fw-bold text-white">$<span id="numeroVentas">0.00</span></div>
							</div>
							<i class="fa-solid fa-cart-shopping text-white-50 fa-2xl"></i>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="col-lg-6 col-xl-3 mb-4 formulariogrupos" style="height:130px;">
			<div class="card bg-warning text-white h-100">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div class="me-3">
							<div class="text-white small">Facturas Efectuadas a Hoy</div>
							<div class="text-xl fw-bold text-white">$<span id="numeroFacturas">0.00</span></div>
						</div>
						<span class="fa-stack">
							<i class="fa-solid fa-dollar-sign text-white-50 fa-stack-2x"
								style="width:25px!important;"></i>
							<i class="fa-solid fa-circle-minus fa-stack-1x text-white-75"
								style="margin-top:-5px!important;margin-right:-8px!important;"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-6 col-xl-3 mb-4 formulariogrupos" style="height:130px;">
			<div class="card bg-success text-white h-100">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div class="me-3">
							<div class="text-white small">Pagos Realizados a Hoy</div>
							<div class="text-xl fw-bold text-white">$<span id="numeroRegistrados">0.00</span></div>
						</div>
						<span class="fa-stack">
							<i class="fa-solid fa-dollar-sign text-white-50 fa-stack-2x"
								style="width:25px!important;"></i>
							<i class="fa-solid fa-circle-plus fa-stack-1x text-white-75"
								style="margin-top:-5px!important;margin-right:-8px!important;"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-6 col-xl-3 mb-4 formulariogrupos" style="height:130px;">
			<div class="card bg-black text-white h-100">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div class="me-3">
							<div class="text-white small">Balance Pendiente</div>
							<div class="text-xl fw-bold text-white"><span id="balance">Pendiente</span></div>
						</div>
						<i class="fa-solid fa-scale-balanced text-white-50 fa-2xl"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="card formulariogrupos mb-4">
		<div class="card-header">
			Lista de Facturas
		</div>
		<div class="card-body" style="height: auto;">
			<div class="row">
				<div class="form-group col-lg-12 col-md-12 col-sm-12 mt-4 mb-3 datosClase">
					<table id="tblFacturas"
						class="table table-striped table-bordered table-hover table-sm dt-responsive"
						style="width:100%;">
						<thead class="bg-dark text-light">
							<th>Factura</th>
							<th>Fecha</th>
							<th>Cliente/Referencia</th>
							<th>Total</th>
							<th>Pagado</th>
							<th>Saldo</th>
							<th>PDF/XML</th>
							<th>Status</th>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<th>Factura</th>
							<th>Fecha</th>
							<th>Cliente/Referencia</th>
							<th>Total</th>
							<th>Pagado</th>
							<th>Saldo</th>
							<th>PDF/XML</th>
							<th>Status</th>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="row mt-4 formularioregistros">
				<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3 col-xs-12 mb-4">
					<button class="btn btn-danger" onclick="cancelarformAlumno()" type="button"><i
							class="fa-solid fa-circle-arrow-left espaciado-icn"></i> Volver</button>
				</div>
			</div>
		</div>
	</div>

	<div class="card formulariogrupos mb-4">
		<div class="card-header">
			Lista de Pagos Recibidos
		</div>
		<div class="card-body" style="height: auto;">
			<div class="row">
				<div class="form-group col-lg-12 col-md-12 col-sm-12 mt-4 mb-3 datosClase">
					<table id="tblRecibidos"
						class="table table-striped table-bordered table-hover table-sm dt-responsive"
						style="width:100%;">
						<thead class="bg-dark text-light">
							<th>#</th>
							<th>Facturas</th>
							<th>Fecha</th>
							<th>Cliente</th>
							<th>Pago</th>
							<th>PDF/XML</th>
							<th>Status</th>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<th>#</th>
							<th>Facturas</th>
							<th>Fecha</th>
							<th>Cliente</th>
							<th>Pago</th>
							<th>PDF/XML</th>
							<th>Status</th>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="row mt-4 formularioregistros">
				<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3 col-xs-12 mb-4">
					<button class="btn btn-danger" onclick="cancelarformAlumno()" type="button"><i
							class="fa-solid fa-circle-arrow-left espaciado-icn"></i> Volver</button>
				</div>
			</div>
		</div>
	</div>

	<!-- <div class="card formulariogrupos mb-4">
		<div class="card-header">
			Lista de Pagos Efectuados
		</div>
		<div class="card-body" style="height: auto;">
			<div class="row">
				<div class="form-group col-lg-12 col-md-12 col-sm-12 mt-4 mb-3 datosClase">
					<table id="tblEfectuados"
						class="table table-striped table-bordered table-hover table-sm dt-responsive"
						style="width:100%;">
						<thead class="bg-dark text-light">
							<th>#</th>
							<th>Fecha</th>
							<th>Cliente</th>
							<th>Metodo Pago</th>
							<th>Pago</th>
							<th>Comprobante</th>
							<th>Status</th>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<th>#</th>
							<th>Fecha</th>
							<th>Cliente</th>
							<th>Metodo Pago</th>
							<th>Pago</th>
							<th>Comprobante</th>
							<th>Status</th>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="row mt-4 formularioregistros">
				<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3 col-xs-12 mb-4">
					<button class="btn btn-danger" onclick="cancelarformAlumno()" type="button"><i
							class="fa-solid fa-circle-arrow-left espaciado-icn"></i> Volver</button>
				</div>
			</div>
		</div>
	</div> -->
</div><!-- /.container-fluid -->
<!--Fin-Contenido-->

<script src="https://unpkg.com/@popperjs/core@2"></script>
<!-- Fin modal Asistencia -->
<?php
} else {
	require 'noacceso.php';
}
require '../components/footer.php';
?>
<script src="../public/scripts/fullcalendar.min.js" type="text/javascript" charset="UTF-8"></script>
<script src="../public/scripts/locale/es.js" type="text/javascript" charset="UTF-8"></script>
<script src="../public/scripts/chartist.js" type="text/javascript" charset="UTF-8"></script>
<script src="../services/escritorio.js" type="text/javascript" charset="UTF-8"></script>
<?php 
}
ob_end_flush();
?>
