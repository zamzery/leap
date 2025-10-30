<?php 
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])){
	header("Location: ../auth/login.html");
} else {
require '../components/header.php';
if ($_SESSION['cargosver']==1){
?>
<title>Leap Works - Cargos</title>
<!--Contenido-->
<header class="page-header page-header-dark pb-10 bg-img-repeat overlay-primary overlay-50 bg-primary">
	<div class="container-xl px-4">
		<div class="page-header-content pt-4">
			<div class="row align-items-center justify-content-between">
				<div class="col-auto mt-4 listadoregistros">
					<h1 class="page-header-title">
						<div class="page-header-icon"><i class="fa-solid fa-id-badge"></i></div>
						Cargo
					</h1>
					<div class="page-header-subtitle text-light">Administración y creación de cargos de los usuarios.
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
			<!-- Comienza Contenedor de Cargos -->
			<button class="btn text-light formularioregistros" style="font-size: 26px;" onclick="cancelarform()"
				type="button"><i class="fa-solid fa-arrow-left"></i></button>
			<div class="card">
				<div class="card-header listadoregistros">
					<h1 class="box-title titulos" style="font-size: 24px;"><button class="btn btn-success"
							id="btn-agregar" onclick="mostrarform(true)" title="Agregar"><i
								class="fa-solid fa-circle-plus espaciado-icn"> </i> Agregar</button></h1>
				</div><!-- /.box-header -->
				<!-- Comienza div de Cargos -->
				<div class="card-body table-responsive listadoregistros">
					<table id="tbllistado" class="table table-striped table-bordered table-hover table-sm dt-responsive"
						style="width:100%;">
						<thead class="bg-dark text-light">
							<th>Nombre</th>
							<th>Descripción</th>
							<th>Status</th>
							<th>Acciones</th>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<th>Nombre</th>
							<th>Descripción</th>
							<th>Status</th>
							<th>Acciones</th>
						</tfoot>
					</table>
				</div>
				<div class="card-body formularioregistros" style="height: auto;">
					<form name="formulario" id="formulario" method="POST">
						<div class="row">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<h1 class="box-title" style="font-size: 24px;">CARGO</h1>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label><span style="color:red;">*</span>Nombre del Cargo:</label>
								<input type="hidden" name="cargoID" id="cargoID">
								<input type="text" class="form-control" name="nombre" id="nombre"
									placeholder="Nombre del Cargo" required>
							</div>
							<div class="form-group col-lg-8 col-md-8 col-sm-12 mb-3">
								<label>Descripción:</label>
								<textarea class="form-control" name="descripcion" id="descripcion"
									placeholder="Descripción del Cargo"></textarea>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 mt-4">
								<h3 class="box-title" style="font-size: 20px;">PERMISOS</h3>
							</div>
							<div class="form-group col-lg-8 col-md-8 col-sm-12">
								<table id="tblPermiso"
									class="table table-striped table-bordered table-hover table-sm dt-responsive"
									style="width:100%;">
									<thead style="background-color:#A9D0F5">
										<th>Nombre Permiso</th>
										<th>Ver</th>
										<th>Editar</th>
										<th>Historial</th>
										<th></th>
										<th></th>
									</thead>
									<tbody>
									</tbody>
									<tfoot style="background-color:#A9D0F5">
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
									</tfoot>
								</table>
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
		</div><!-- /.termina Contenedor de Cargos -->
	</div><!-- /.row -->
</div><!-- /.container-fluid -->
<!--Fin-Contenido-->
<?php
} else {
	require 'noacceso.php';
}
require '../components/footer.php';
?>
<script type="text/javascript" src="../services/cargo.js"></script>
<?php 
}
ob_end_flush();
?>