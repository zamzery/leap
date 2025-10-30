<?php 
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])){
	header("Location: ../auth/login.html");
} else {
require '../components/header.php';
if ($_SESSION['usuariosver']==1){
?>
<style type="text/css" media="screen">
#permisos {
	height: 450px;
	/*your fixed height*/
	-webkit-column-count: 2;
	-moz-column-count: 2;
	column-count: 2;
	/*3 in those rules is just placeholder -- can be anything*/
}
</style>
<title>Leap Works - Usuarios</title>
<!--Contenido-->
<header class="page-header page-header-dark pb-10 bg-img-repeat overlay-primary overlay-50 bg-primary">
	<div class="container-xl px-4">
		<div class="page-header-content pt-4">
			<div class="row align-items-center justify-content-between">
				<div class="col-auto mt-4 listadoregistros">
					<h1 class="page-header-title">
						<div class="page-header-icon"><i class="fa-solid fa-user"></i></div>
						Usuarios
					</h1>
					<div class="page-header-subtitle text-light">Administración y creación de usuarios.</div>
				</div>
			</div>
		</div>
	</div>
</header>
<!-- Main page content-->
<div class="container-xl px-4 mt-n10" id="container-body">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<!-- Comienza Contenedor de Usuarios -->
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
				<!-- Comienza div de Usuarios -->
				<div class="card-body table-responsive listadoregistros">
					<table id="tbllistado" class="table table-striped table-bordered table-hover table-sm dt-responsive"
						style="width:100%;">
						<thead class="bg-dark text-light listadoregistros">
							<th>Nombre</th>
							<th>Email</th>
							<th>Teléfono</th>
							<th>Cargo</th>
							<th>Color</th>
							<th>Texto</th>
							<th>Ávatar</th>
							<th>Status</th>
							<th>Acciones</th>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<th>Nombre</th>
							<th>Email</th>
							<th>Teléfono</th>
							<th>Cargo</th>
							<th>Color</th>
							<th>Texto</th>
							<th>Ávatar</th>
							<th>Status</th>
							<th>Acciones</th>
						</tfoot>
					</table>
				</div>
				<div class="card-body formularioregistros" style="height: auto;">
					<form name="formulario" id="formulario" method="POST">
						<div class="row">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<h1 class="box-title" style="font-size: 24px;">USUARIO</h1>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label><span style="color:red;">*</span>Nombre Completo:</label>
								<input type="hidden" name="usuarioID" id="usuarioID">
								<input type="text" class="form-control" name="nombre" id="nombre"
									placeholder="Nombre del Usuario" required>
							</div>

							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label>Teléfono:</label>
								<input type="text" class="form-control" name="telefono" id="telefono"
									placeholder="Teléfono">
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label><span style="color:red;">*</span>Cargo:</label><br />
								<select id="cargo_id" name="cargo_id" class="form-control selectpicker"
									placeholder="Selecciona el cargo" required>
								</select>
							</div>
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<label>Domicilio:</label>
								<textarea class="form-control" name="direccion" id="direccion"
									placeholder="Domicilio del Usuario"></textarea>
							</div>
						</div>
						<div class="row bg-orange-soft mb-2">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mt-4">
								<h3 class="box-title" style="font-size: 20px;">ACCESO</h3>
							</div>
							<div class="form-group col-lg-6 col-md-6 col-sm-12  mb-3">
								<label><span style="color:red;">*</span>Email:</label>
								<input type="text" autocomplete="username" class="form-control" name="email" id="email"
									placeholder="Email" required>
							</div>
							<div class="form-group col-lg-6 col-md-6 col-sm-12  mb-3">
								<label>Contraseña:</label>
								<input type="password" class="form-control" minlength="8" name="clave" id="clave"
									placeholder="Contraseña" autocomplete="new-password" value="">
							</div>
						</div>
						<div class="row bg-purple-soft">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mt-4">
								<h3 class="box-title" style="font-size: 20px;">SISTEMA</h3>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label><span style="color:red;">*</span>Redirección:</label><br />
								<select id="redireccion" name="redireccion" class="form-control selectpicker"
									placeholder="Selecciona la redirección al ingresar" required>
									<option value="escritorio">Escritorio</option>
									<option value="pedidos">Pedidos</option>
									<option value="pagos">Pagos</option>
									<option value="gastos">Gastos</option>
									<option value="facturas">Facturación</option>
								</select>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12  mb-3">
								<label><span style="color:red;">*</span>Color:</label>
								<div class="input-group mb-3">
									<span class="input-group-text addon-color"><input type="color"
											class="form-control form-control-color casillaColor" id="muestracolor"
											onchange="cambiaColor(this)" value="#000000"></span>
									<input type="text" class="form-control" name="color" id="color" value="#000000"
										required>
								</div>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12  mb-3">
								<label><span style="color:red;">*</span>Color del Texto:</label>
								<div class="input-group mb-3">
									<span class="input-group-text addon-color"><input type="color"
											class="form-control form-control-color casillaColor" id="muestracolorText"
											onchange="cambiaColor(this)" value="#FFFFFF"></span>
									<input type="text" class="form-control" name="colorText" id="colorText"
										value="#FFFFFF" required>
								</div>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12  mb-3">
								<label>Subir/Cambiar Ávatar:</label>
								<input type="hidden" name="avataractual" id="avataractual">
								<input type="file" class="form-control" name="avatar" id="avatar">
								<p class="help-block">Sólo acepta archivos JPG, PNG y GIF.</p>
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-12  mb-3">
								<label>Ávatar Actual:</label><br>
								<img src="" width="100" height="100" id="avatarmuestra">
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
		</div><!-- /.termina Contenedor de Usuarios -->
	</div><!-- /.row -->
</div><!-- /.container-fluid -->
<!--Fin-Contenido-->
<?php
} else {
	require 'noacceso.php';
}
require '../components/footer.php';
?>
<script type="text/javascript" src="../services/user.js"></script>
<?php 
}
ob_end_flush();
?>