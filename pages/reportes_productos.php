<?php
ob_start();
session_start();

if (!isset($_SESSION["nombre"])){
	header("Location: ../auth/login.html");
} else {
require '../components/header.php';
if ($_SESSION['reportesver']==1 && $_SESSION['reporteproductosver']==1){
?>
<title>Leap Works - Reporte de Productos</title>
<style>
.report-kpi {
	border-left: 4px solid #0d6efd;
	min-height: 118px;
}

.report-kpi .kpi-label {
	color: #6c757d;
	font-size: .85rem;
	text-transform: uppercase;
	font-weight: 700;
}

.report-kpi .kpi-value {
	font-size: 1.75rem;
	font-weight: 700;
	color: #1f2d3d;
}

.report-chart {
	position: relative;
	height: 340px;
}

.report-empty {
	min-height: 160px;
	display: flex;
	align-items: center;
	justify-content: center;
	color: #6c757d;
	border: 1px dashed #ced4da;
	border-radius: 6px;
}
</style>
<!--Contenido-->
<header class="page-header page-header-dark pb-10 bg-img-repeat overlay-primary overlay-50 bg-primary">
	<div class="container-xl px-4">
		<div class="page-header-content pt-4">
			<div class="row align-items-center justify-content-between">
				<div class="col-auto mt-4">
					<h1 class="page-header-title">
						<div class="page-header-icon"><i class="fa-solid fa-chart-pie"></i></div>
						Reporte de Productos
					</h1>
					<div class="page-header-subtitle text-light">Productos mas vendidos y comparativas por periodo.
					</div>
				</div>
			</div>
		</div>
	</div>
</header>

<div class="container-xl px-4 mt-n10" id="container-body">
	<div class="card mb-4">
		<div class="card-header">
			<i class="fa-solid fa-filter espaciado-icn"></i> Filtros
		</div>
		<div class="card-body">
			<form id="formFiltros" class="row align-items-end">
				<div class="form-group col-lg-3 col-md-6 col-sm-12 mb-3">
					<label for="fechaInicio">Fecha inicial:</label>
					<input type="date" class="form-control" id="fechaInicio" name="fechaInicio">
				</div>
				<div class="form-group col-lg-3 col-md-6 col-sm-12 mb-3">
					<label for="fechaFin">Fecha final:</label>
					<input type="date" class="form-control" id="fechaFin" name="fechaFin">
				</div>
				<div class="form-group col-lg-4 col-md-8 col-sm-12 mb-3">
					<label for="productos">Productos:</label>
					<div class="input-group">
						<select id="productos" name="productos[]" class="form-control selectpicker"
							data-live-search="true" multiple data-actions-box="true"
							data-select-all-text="Seleccionar todos" data-deselect-all-text="Deseleccionar todos"
							title="Todos los productos"></select>
						<button class="btn btn-outline-secondary" type="button" id="btnLimpiarProductos"
							title="Deseleccionar productos"><i class="fa-solid fa-xmark"></i></button>
					</div>
				</div>
				<div class="form-group col-lg-2 col-md-4 col-sm-12 mb-3">
					<button type="submit" class="btn btn-primary w-100"><i
							class="fa-solid fa-magnifying-glass espaciado-icn"></i> Consultar</button>
				</div>
			</form>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-3 col-md-6 mb-4">
			<div class="card report-kpi">
				<div class="card-body">
					<div class="kpi-label">Unidades vendidas</div>
					<div class="kpi-value" id="kpiUnidades">0</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 mb-4">
			<div class="card report-kpi">
				<div class="card-body">
					<div class="kpi-label">Importe vendido</div>
					<div class="kpi-value" id="kpiImporte">$0.00</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 mb-4">
			<div class="card report-kpi">
				<div class="card-body">
					<div class="kpi-label">Productos vendidos</div>
					<div class="kpi-value" id="kpiProductos">0</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 mb-4">
			<div class="card report-kpi">
				<div class="card-body">
					<div class="kpi-label">Dias con venta</div>
					<div class="kpi-value" id="kpiDias">0</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xl-8 mb-4">
			<div class="card">
				<div class="card-header"><i class="fa-solid fa-ranking-star espaciado-icn"></i> Productos mas vendidos
				</div>
				<div class="card-body">
					<div class="report-chart"><canvas id="chartTopProductos"></canvas></div>
					<div id="emptyTopProductos" class="report-empty d-none">Sin datos para los filtros seleccionados.
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-4 mb-4">
			<div class="card">
				<div class="card-header"><i class="fa-solid fa-store espaciado-icn"></i> Origen de ventas</div>
				<div class="card-body">
					<div class="report-chart"><canvas id="chartOrigen"></canvas></div>
					<div id="emptyOrigen" class="report-empty d-none">Sin datos para los filtros seleccionados.</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card mb-4">
		<div class="card-header"><i class="fa-solid fa-chart-line espaciado-icn"></i> Comparativo mensual</div>
		<div class="card-body">
			<div class="report-chart"><canvas id="chartComparativo"></canvas></div>
			<div id="emptyComparativo" class="report-empty d-none">Selecciona uno o mas productos para comparar por mes.
			</div>
		</div>
	</div>

	<div class="card mb-4">
		<div class="card-header"><i class="fa-solid fa-table espaciado-icn"></i> Detalle comparativo</div>
		<div class="card-body table-responsive">
			<table id="tablaComparativo" class="table table-striped table-bordered table-hover table-sm"
				style="width:100%;">
				<thead class="bg-dark text-light">
					<th>Periodo</th>
					<th>Producto</th>
					<th>Unidades</th>
					<th>Importe</th>
				</thead>
				<tbody></tbody>
				<tfoot>
					<th>Periodo</th>
					<th>Producto</th>
					<th>Unidades</th>
					<th>Importe</th>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<!--Fin-Contenido-->
<?php
} else {
	require 'noacceso.php';
}
require '../components/footer.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script type="text/javascript" src="../services/reporte_producto.js"></script>
<?php
}
ob_end_flush();
?>