<?php 
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])){
	header("Location: ../auth/login.html");
} else {
require '../components/header.php';
if ($_SESSION['complementosver']==1){
?>
<title>Leap Works - Complementos</title>
<!--Contenido-->
<header class="page-header page-header-dark pb-10 bg-img-repeat overlay-50">
	<div class="container-xl px-4">
		<div class="page-header-content pt-4">
			<div class="row align-items-center justify-content-between">
				<div class="col-auto mt-4 listadoregistros">
					<h1 class="page-header-title">
						<div class="page-header-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
						Complementos de Pago
					</h1>
					<div class="page-header-subtitle text-light">Administración y creación de complementos de pago de la
						facturación.</div>
				</div>
			</div>
		</div>
	</div>
</header>
<!-- Main page content-->
<div class="container-xl px-4 mt-n10" id="container-body">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<!-- Comienza Contenedor de Complementos -->
			<button class="btn text-light formularioregistros" style="font-size: 26px;" onclick="cancelarform()"
				type="button"><i class="fa-solid fa-arrow-left"></i></button>
			<div class="card">
				<div class="card-header listadoregistros">
					<h1 class="box-title titulos" style="font-size: 24px;"><button class="btn btn-success"
							id="btn-agregar" onclick="mostrarform(true)" title="Agregar"><i
								class="fa-solid fa-circle-plus espaciado-icn"> </i> Agregar</button></h1>
				</div><!-- /.box-header -->
				<!-- Comienza div de Complementos -->
				<div class="row listadoregistros" style="margin-left:15px;">
					<div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12 pt-3 ">
						<label>Fecha Inicio</label>
						<div class="form-group">
							<div class="input-group date" id="fechaInicio" data-target-input="nearest">
								<input type="text" class="form-control datetimepicker-input" data-target="#fechaInicio"
									id="fechaInicioInput" />
								<div class="input-group-text" data-target="#fechaInicio" data-toggle="datetimepicker">
									<i class="fa fa-calendar"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12 pt-3 ">
						<label>Fecha Fin</label>
						<div class="form-group">
							<div class="input-group date" id="fechaFin" data-target-input="nearest">
								<input type="text" class="form-control datetimepicker-input" data-target="#fechaFin"
									id="fechaFinInput" />
								<div class="input-group-text" data-target="#fechaFin" data-toggle="datetimepicker">
									<i class="fa fa-calendar"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body table-responsive listadoregistros">
					<table id="tbllistado" class="table table-striped table-bordered table-hover table-sm dt-responsive"
						style="width:100%;">
						<thead class="bg-dark text-light">
							<th>Folio</th>
							<th>Fact. Rel.</th>
							<th>Fecha Pago</th>
							<th>Pago</th>
							<th>PDF/XML</th>
							<th>Status</th>
							<th>Acciones</th>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<th>Folio</th>
							<th>Fact. Rel.</th>
							<th>Fecha Pago</th>
							<th>Pago</th>
							<th>PDF/XML</th>
							<th>Status</th>
							<th>Acciones</th>
						</tfoot>
					</table>
				</div>
				<div class="card-body formularioregistros" style="height: auto;">
					<form name="formulario" id="formulario" method="POST">
						<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
							<h1 class="box-title" style="font-size: 24px;">PAGO</h1>
						</div>
						<div class="row form-group mb-3 pb-3">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<label style="padding-top:10px;">
									<h4>Información General de Complemento</h4>
								</label>
							</div>
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<label>Folio de Complemento:</label><br />
								<input type="hidden" class="form-control" id="folioFiscal">
								<input type="text" class="form-control" name="complementoID" id="complementoID"
									readonly="readonly">
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label><text style="color:red;">*</text>Cliente:</label>
								<select id="cliente_id" name="cliente_id" class="form-control selectpicker"
									data-live-search="true" style="cursor: default;" title="Selecciona el Cliente"
									required>
								</select>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label><text style="color:red;">*</text>Entidad Bancaria: <small>En caso de no aplicar:
										NA</small></label>
								<input type="text" class="form-control" name="banco" id="banco"
									title="Entidad Bancaria">
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label>No. de Cuenta: <small>Cuenta 10/Tarjeta 16/CLABE 18</small></label>
								<input type="text" class="form-control" name="numCuenta" id="numCuenta" min="10"
									max="50" title="Número de Cuenta" required>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label><text style="color:red;">*</text>Forma de Pago:</label>
								<select type="text" class="form-control selectpicker" name="formadePago"
									id="formadePago" title="Forma de Pago" required></select>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label><text style="color:red;">*</text>Uso de CFDI:</label>
								<select id="usoCfdi" name="usoCfdi" class="form-control selectpicker" required>
									<option value="CP01" selected>Pagos</option>
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
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<label>Comentario Adicional:</label>
								<textarea rows="3" type="text" class="form-control" name="comentarioAdicional"
									id="comentarioAdicional"></textarea>
							</div>
						</div>
						<div class="row form-group">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<button id="btnAgregarArt" type="button" class="btn btn-primary"
									onclick="muestraModalFacturas()"><i class="fa-solid fa-plus espaciado-icn"></i>
									Agregar Detalles</button>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 mb-3">
								<table id="detalles"
									class="table table-striped table-bordered table-hover table-sm dt-responsive"
									style="width:100%;">
									<thead class="bg-primary text-light">
										<th></th>
										<th>Factura</th>
										<th>Fecha</th>
										<th>Cliente</th>
										<th>Parcialidad</th>
										<th>Saldo Anterior</th>
										<th>Este Pago</th>
										<th>Saldo Restante</th>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th style="font-weight:bold;"><span class="float-end">Este Pago:</span></th>
										<th><strong class="float-end">$<span id="totalPago">0.00</span></strong></th>
										<th></th>
									</tfoot>
								</table>
							</div>
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<button class="btn btn-danger" onclick="cancelarform()" type="button"><i
										class="fa fa-arrow-circle-left espaciado-icn"></i> Cancelar/Volver</button>
								<button class="btn btn-primary float-end" type="submit" id="btnGuardar"><i
										class="fa fa-save espaciado-icn"></i> Guardar</button>
							</div>
						</div>
					</form>
				</div>
			</div><!-- /.termina card -->
		</div><!-- /.termina Contenedor de Complementos -->
	</div><!-- /.row -->
</div><!-- /.container-fluid -->
<!--Fin-Contenido-->

<!-- Forma Complemento Pagada -->
<div class="modal fade" id="modalFacturas" tabindex="-1" role="dialog" aria-labelledby="modalFacturas"
	aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Selecciona la Factura:</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="form-group col-lg-12 col-md-12 col-sm-12 col-sm-12 mb-3">
						<table id="tblFacturas"
							class="table table-striped table-bordered table-hover table-sm dt-responsive"
							style="width:100%;">
							<thead class="bg-dark text-light">
								<th>Factura</th>
								<th>Fecha</th>
								<th>Cliente</th>
								<th>$Total</th>
								<th>Parcialidad</th>
								<th>Restante</th>
								<th>Status</th>
								<th>Acciones</th>
							</thead>
							<tbody>
								<tr>
									<td colspan="8" id="tablaVacia">Se debe seleccionar un cliente para agregar
										facturas.</td>
								</tr>
							</tbody>
							<tfoot>
								<th>Folio</th>
								<th>Fact. Rel.</th>
								<th>Fecha Pago</th>
								<th>Pago</th>
								<th>PDF/XML</th>
								<th>Status</th>
								<th>Acciones</th>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- Fin Forma Complemento Pagada -->

<!-- Modal Enviar Complemento -->
<div class="modal fade" id="enviaComplemento" tabindex="-1" role="dialog" aria-labelledby="enviaComplemento"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Enviar Complemento <strong id="numeroPagoTxt"> </strong> al Cliente(s):</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form name="formulario_enviaComplemento" id="formulario_enviaComplemento" method="POST">
					<div class="form-group" id="emailsEnvioComplemento"></div>
					<input type="hidden" name="nombrePDF" id="nombrePDF">
					<input type="hidden" name="nombreXML" id="nombreXML">
					<input type="hidden" name="complementoID" id="numeroPago">
					<input type="hidden" name="complementoID2" id="numeroPago2">
					<input type="hidden" name="cliente2" id="cliente2">
					<br />
					<button class="btn btn-primary" type="submit" id="btnEnviarComplemento"><i
							class="fa-solid fa-envelope espaciado-icn"></i> Enviar Complemento</button>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- Fin Modal Enviar Complemento -->

<!-- Modal Cancela Complemento -->
<div class="modal fade" id="cancelaComplemento" tabindex="-1" role="dialog" aria-labelledby="cancelaComplemento"
	aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Cancelar Complemento <strong id="facturaCancela"> </strong></h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="form-group col-lg-6 col-md6 col-sm-12 mb-3">
						<label>Motivo de Cancelación</label>
						<div class="form-check">
							<label style="font-weight: normal!important;">
								<input class="form-check-input" type="radio" name="motivo" id="motivo01" value="01"
									onclick="cambiaValorRadio(this.value)" checked>
								01 Comprobante emitido con errores con relación.
							</label>
							<label style="font-weight: normal!important;">
								<input class="form-check-input" type="radio" name="motivo" id="motivo02" value="02"
									onclick="cambiaValorRadio(this.value)">
								02 Comprobante emitido con errores sin relación.
							</label>
							<label style="font-weight: normal!important;">
								<input class="form-check-input" type="radio" name="motivo" id="motivo03" value="03"
									onclick="cambiaValorRadio(this.value)">
								03 No se llevó a cabo la operación.
							</label>
							<label style="font-weight: normal!important;">
								<input class="form-check-input" disabled type="radio" name="motivo" id="motivo04"
									value="04" onclick="cambiaValorRadio(this.value)">
								04 Operación nominativa relacionada en la factura global.
							</label>
						</div>
					</div>
					<div class="form-group col-lg-6 col-md6 col-sm-12 mb-3">
						<label>Complemento Relacionada</label>
						<input type="hidden" name="complementoID_cancela" id="complementoID_cancela">
						<select id="folioSustitucion" name="folioSustitucion" class="form-control selectpicker"
							data-live-search="true" title="Selecciona la Complemento Relacionada"></select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
				<button class="btn btn-danger" type="button" id="btnCancelarComplemento" onclick="cancelar_factura()"><i
						class="fa-solid fa-xmark espaciado-icn"></i> Cancelar Complemento</button>
			</div>
		</div>
	</div>
</div>
<!-- Fin modal Cancela Complemento -->
<?php
} else {
	require 'noacceso.php';
}
require '../components/footer.php';
?>
<script type="text/javascript" src="../services/complemento.js"></script>
<?php 
}
ob_end_flush();
?>