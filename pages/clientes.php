<?php 
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])){
	header("Location: ../auth/login.html");
} else {
require '../components/header.php';
if ($_SESSION['clientesver']==1){
?>
<title>Leap Works - Clientes</title>
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
						<div class="page-header-icon"><i class="fa-solid fa-user-plus"></i></div>
						Clientes
					</h1>
					<div class="page-header-subtitle text-light">Administración y creación de Clientes.</div>
				</div>
			</div>
		</div>
	</div>
</header>
<!-- Main page content-->
<div class="container-xl px-4 mt-n10" id="container-body">
	<div class="row">
		<div class="col-sm-12 mb-3 col-lg-12">
			<!-- Comienza Contenedor de Clientes -->
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
				<!-- Comienza div de Clientes -->
				<div class="card-body table-responsive listadoregistros">
					<table id="tblListado" class="table table-striped table-bordered table-hover table-sm dt-responsive"
						style="width:100%;">
						<thead class="bg-dark text-light">
							<th>Cliente</th>
							<th>Datos Factura</th>
							<th>Email</th>
							<th>Teléfono</th>
							<th>Constancia</th>
							<th>Status</th>
							<th>Acciones</th>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<th>Cliente</th>
							<th>Datos Factura</th>
							<th>Email</th>
							<th>Teléfono</th>
							<th>Constancia</th>
							<th>Status</th>
							<th>Acciones</th>
						</tfoot>
					</table>
				</div>
				<div class="card-body formularioregistros" style="height: auto;">
					<form name="formulario" id="formulario" method="POST">
						<div class="row">
							<div class="form-group col-lg-8 col-md-8 col-sm-8 mb-4">
								<h1 class="box-title" style="font-size: 24px;">CLIENTE</h1>
							</div>
							<?php 
							if ($_SESSION['clienteshistorial']==1){
								echo '<div class="form-group col-lg-4 col-md-4 col-sm-4 mb-4">
									<button type="button" class="form-group btn btn-primary float-end" id="botonHistorial" onclick="muestraHistorial()"><i class="fa-solid fa-clock-rotate-left espaciado-icn"></i> Historial</button>
								</div>';
							}
						?>
							<div class="form-group col-lg-6 col-md-6 col-sm-12 mb-4">
								<input type="hidden" name="clienteID" id="clienteID">
								<input type="hidden" id="user_nicename" name="user_nicename" value="">
								<label><text style="color:red;">*</text>Nombre Comercial:</label>
								<input type="text" class="form-control" name="display_name" id="display_name"
									placeholder="Nombre Comercial o Referencia del Cliente"
									oninput="make_slug(this.value)" required>
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-6">
								<label>Teléfono:</label>
								<input type="text" class="form-control" name="telefono" id="telefono"
									placeholder="Teléfono">
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-6">
								<label>Email:</label>
								<input type="text" class="form-control" name="user_email" id="user_email"
									placeholder="Email">
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-4">
								<label><text style="color:red;">*</text>Razón Social: <small>(De no tener: nombre
										comercial)</small></label>
								<input type="text" class="form-control" name="razonSocial" id="razonSocial"
									placeholder="Razón Social del Cliente" required>
							</div>
							<div class="form-group col-lg-2 col-md-2 col-sm-6">
								<label><text style="color:red;">*</text>RFC:</label>
								<input type="text" class="form-control" name="rfcCliente" id="rfcCliente" minlength="12"
									maxlength="13" placeholder="RFC Cliente" value="XEXX010101000" required="">
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-4">
								<label><text style="color:red;">*</text>Régimen Fiscal:</label>
								<select id="regimenFiscal" name="regimenFiscal" class="form-control selectpicker"
									title="Selecciona el Régimen Fiscal" data-size="5" data-container="body"
									data-live-search="true" required>
									<option value="601">601 - Persona Moral</option>
									<option value="603">603 - Persona Moral con Fines no Lucrativos</option>
									<option value="605">605 - Asalariados</option>
									<option value="606">606 - Arrendamiento</option>
									<option value="607">607 - Régimen de Enajenación o Adquisición de Bienes</option>
									<option value="608">608 - Demás ingresos</option>
									<option value="609">609 - Consolidación</option>
									<option value="610">610 - Extranjeros sin Establecimiento Permanente en México
									</option>
									<option value="611">611 - Ingresos por Dividendos</option>
									<option value="612">612 - Personas Físicas</option>
									<option value="614">614 - Ingresos por intereses</option>
									<option value="615">615 - Régimen de los ingresos por obtención de premios</option>
									<option value="616" selected>616 - Sin obligaciones fiscales</option>
									<option value="620">620 - Sociedades Cooperativas de Producción que optan por
										diferir sus ingresos</option>
									<option value="621">621 - Incorporación Fiscal</option>
									<option value="622">622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras
									</option>
									<option value="623">623 - Opcional para Grupos de Sociedades</option>
									<option value="624">624 - Coordinados</option>
									<option value="625">625 - Régimen de las Actividades Empresariales con ingresos a
										través de Plataformas Tecnológicas</option>
									<option value="626">626 - Régimen Simplificado de Confianza</option>
									<option value="628">628 - Hidrocarburos</option>
									<option value="629">629 - De los Regímenes Fiscales Preferentes y de las Empresas
										Multinacionales</option>
									<option value="630">630 - Enajenación de acciones en bolsa de valores</option>
								</select>
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-4">
								<label><text style="color:red;">*</text>Uso de CFDI:</label>
								<select id="usoCfdi" name="usoCfdi" class="form-control"
									title="El uso de CFDI preferente del cliente">
									<option value="D10"> Pagos por servicios educativos (colegiaturas)</option>
									<option value="G01">Adquisición de mercancías</option>
									<option value="G03">Gastos en general</option>
									<option value="S01" selected>Sin Efectos Fiscales</option>
									<option value="CP01 ">Pagos</option>
									<option value="P01">Por definir</option>
								</select>
							</div>
							<div class="form-group col-lg-9 col-md-9 col-sm-9 mb-4">
								<label>Subir/Cambiar Constancia:</label>
								<input type="hidden" name="constanciaactual" id="constanciaactual">
								<input type="file" class="form-control" name="constancia" id="constancia"
									accept=".jpg,.png,.pdf">
								<small class="help-block">Sólo acepta archivos JPG, PNG y PDF.</small>
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-3 mb-4">
								<br>
								<span id="botonConstancia" class="pt-2"></span>
							</div>
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-4">
								<label>Observaciones:</label>
								<textarea class="form-control" rows="2" name="comentarios" id="comentarios"
									placeholder="Observaciones"></textarea>
							</div>
						</div>
						<!-- <div class="row" style="background-color:#ebdbf1;">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3 col-xs-12 mt-4">
								<h3 class="box-title" style="font-size: 20px;">LOGIN WORDPRESS</h3>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-4">
								<label><text style="color:red;">*</text>Usuario:</label>
								<input type="text" class="form-control" title="Usuario login" name="user_login"
									id="user_login" title="Usuario" autocomplete="username" required>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-4">
								<label>Contraseña:</label>
								<input type="text" class="form-control" name="contrasenna" id="contrasenna" min="8"
									placeholder="Contraseña" title="Contraseña" autocomplete="new-password">
							</div>
						</div> -->
						<div class="row pb-4" style="background-color:#f0f5ff;">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3 col-xs-12 mt-4">
								<h3 class="box-title" style="font-size: 20px;">DIRECCIÓN</h3>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-4">
								<label>Calle:</label>
								<input type="text" class="form-control" name="calle" id="calle" placeholder="Calle">
							</div>
							<div class="form-group col-lg-2 col-md-2 col-sm-6">
								<label>Número Exterior:</label>
								<input type="text" class="form-control" name="num_ext" id="num_ext"
									placeholder="Número Exterior">
							</div>
							<div class="form-group col-lg-2 col-md-2 col-sm-6">
								<label>Número Interior:</label>
								<input type="text" class="form-control" name="num_int" id="num_int"
									placeholder="Número Interior">
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-6">
								<label>Colonia:</label>
								<input type="text" class="form-control" name="colonia" id="colonia"
									placeholder="Colonia">
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-6">
								<label>Municipio:</label>
								<input type="text" class="form-control" name="poblacion" id="poblacion"
									placeholder="Municipio, Ciudad o Delegación">
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-6">
								<label>Estado:</label>
								<input type="text" class="form-control" name="edoPais" id="edoPais"
									placeholder="Estado">
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-6">
								<label>País:</label>
								<input type="text" class="form-control" name="pais" id="pais" placeholder="País">
							</div>
							<div class="form-group col-lg-3 col-md-3 col-sm-6">
								<label><text style="color:red;">*</text>C.P.: <small>Extranjeros poner:
										44520</small></label>
								<input type="text" class="form-control" name="cp" id="cp" minlength="5" maxlength="5"
									placeholder="Código Postal" required>
							</div>
						</div>
						<div class="row" style="background-color:#ebfef1;">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3 col-xs-12 mt-4">
								<h3 class="box-title" style="font-size: 20px;">DATOS DE PAGO Y BANCARIOS</h3>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-4">
								<label><text style="color:red;">*</text>Forma de Pago:</label>
								<select type="text" class="form-control" title="Selecciona la Forma de Pago"
									name="formadePago" id="formadePago" title="Forma de Pago" required></select>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-4">
								<label><text style="color:red;">*</text>Método de Pago:</label>
								<select id="metodoPago" name="metodoPago" class="form-control"
									title="El método de pago preferente del cliente" required>
									<option value="PUE" selected>Pago en Una Sola Exhibición</option>
									<option value="PPD">Pago en Parcialidades o Diferido</option>
								</select>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-4">
								<label><text style="color:red;">*</text>Moneda:</label>
								<select id="moneda" name="moneda" class="form-control"
									title="La moneda en la que se realizará la factura" required>
									<option value="USD" selected>Dólares Americanos (USD)</option>
									<option value="MXN">Pesos Mexicanos (MXN)</option>
								</select>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-4">
								<label>Nombre del Banco:</label>
								<input type="text" class="form-control" name="banco" id="banco"
									placeholder="Nombre del Banco">
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-4">
								<label>No. Cuenta/CLABE:</label>
								<input type="text" class="form-control" name="num_cuenta" id="num_cuenta" maxlength="50"
									placeholder="Número de Cuenta">
							</div>
						</div>
						<div class="row mt-4">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3 col-xs-12 mb-4">
								<button class="btn btn-primary float-end" type="submit" id="btnGuardar"><i
										class="fa-solid fa-floppy-disk espaciado-icn"></i> Guardar</button>
								<button class="btn btn-danger" onclick="cancelarform()" type="button"><i
										class="fa-solid fa-circle-arrow-left espaciado-icn"></i> Cancelar</button>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-4">
								<label>Fecha de Ultimo Cambio:</label>
								<input type="text" class="form-control" id="fechaCliente"
									placeholder="Fecha de Ultimo Cambio"
									style="font-weight: bold; cursor: default; background:#eaecf4;" readonly>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3 usuarioasignado">
								<label>Vendedor Asignado:</label>
								<input type="text" class="form-control" id="usuarioNombre_general"
									placeholder="Usuario Asignado"
									style="font-weight: bold; cursor: default; background:#eaecf4;" readonly>
							</div>
							<?php 
							if ($_SESSION['clienteseditar']==1){
								echo '<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3 usuarioasignado" id="cambiausuario">
									<label>Cambiar Usuario:</label><br>
									<select class="form-control" id="usuarioNombre" data-live-search="true" data-size="5" onchange="cambia_usuario(this)" title="Selecciona el nuevo usuario asignado">
										
									</select>
								</div>';
							}
						?>
						</div>
					</form>
				</div>
			</div><!-- /.termina card -->
		</div><!-- /.termina Contenedor de Clientes -->
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
					<thead style="background-color:#A9D0F5">
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
<script src="../public/vendor/qrcode/qrcode.min.js"></script>
<script type="text/javascript" src="../services/cliente.js"></script>
<?php 
}
ob_end_flush();
?>