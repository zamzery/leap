<?php 
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])){
	header("Location: ../auth/login.html");
} else {
require '../components/header.php';
if ($_SESSION['pedidosver']==1){
?>
<title>Pedidos</title>
<style>
.text-bg-secondary {
	background-color: #6c757d !important;
}
</style>
<!--Contenido-->
<header class="page-header page-header-dark pb-10 bg-img-repeat overlay-primary overlay-50 bg-primary">
	<div class="container-xl px-4">
		<div class="page-header-content pt-4">
			<div class="row align-items-center justify-content-between">
				<div class="col-auto mt-4 listadoregistros">
					<h1 class="page-header-title">
						<div class="page-header-icon"><i class="fa-solid fa-basket-shopping"></i></div>
						Pedidos
					</h1>
					<div class="page-header-subtitle text-light">Administración y creación de pedidos.
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
			<!-- Comienza Contenedor de Pedidos -->
			<button class="btn text-light formularioregistros" style="font-size: 26px;" onclick="cancelarform()"
				type="button"><i class="fa-solid fa-arrow-left"></i></button>
			<div class="card">
				<div class="card-header listadoregistros">
					<h1 class="box-title titulos" style="font-size: 24px;"><button type="button" class="btn btn-success"
							id="btn-agregar" onclick="mostrarform(true)" title="Agregar"><i
								class="fa-solid fa-circle-plus espaciado-icn"> </i> Agregar</button></h1>
				</div><!-- /.box-header -->
				<!-- Comienza div de Pedidos -->
				<div class="card-body table-responsive listadoregistros">
					<table id="tbllistado" class="table table-striped table-bordered table-hover table-sm dt-responsive"
						style="width:100%;">
						<thead class="bg-dark text-light">
							<th>#</th>
							<th>Fecha</th>
							<th>Cliente</th>
							<th>$Total</th>
							<th>Productos</th>
							<th>Status</th>
							<th>Acciones</th>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<th>#</th>
							<th>Fecha</th>
							<th>Cliente</th>
							<th>$Total</th>
							<th>Productos</th>
							<th>Status</th>
							<th>Acciones</th>
						</tfoot>
					</table>
				</div>
				<div class="card-body formularioregistros" style="height: auto;">
					<form name="formulario" id="formulario" method="POST">
						<div class="row">
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<h1 class="box-title" style="font-size: 24px;">PEDIDOS</h1>
							</div>
							<div class="form-group col-lg-6 col-md-6 col-sm-12 mb-3">
								<label for="pedidoID">Pedido:</label>
								<input type="text" class="form-control" name="pedidoID" id="pedidoID" placeholder="#"
									readonly style="background-color:#e9ecef;">
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label for="fecha">Fecha:</label>
								<input type="date" class="form-control" id="fecha" placeholder="Fecha de Pedido"
									readonly style="background-color:#e9ecef;">
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label for="clienteID"><span style="color:red;">*</span>Cliente:</label>
								<select id="clienteID" name="clienteID" class="form-control selectpicker"
									placeholder="Selecciona el Cliente" data-live-search="true" data-size="10"></select>
							</div>
							<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
								<label for="nombreCliente">Nombre Cliente:</label>
								<input type="text" id="nombreCliente" name="nombreCliente" class="form-control"
									placeholder="Nombre del Cliente">
							</div>
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<label for="observaciones">Observaciones:</label>
								<textarea id="observaciones" name="observaciones" class="form-control"
									placeholder="Escribe alguna Observación" rows="3"></textarea>
							</div>
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
								<button id="btnAgregarArt" type="button" class="btn btn-primary"
									onclick="verModalProductos()"><i class="fa-solid fa-plus espaciado-icn"></i> Agregar
									Detalles</button>
							</div>
							<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3 table-responsive">
								<table id="detalles"
									class="table table-striped table-bordered table-hover table-sm dt-responsive"
									style="width:100%;">
									<thead class="bg-dark text-light">
										<th></th>
										<th>Cantidad</th>
										<th>Producto</th>
										<th>Descripción</th>
										<th>IMG</th>
										<th>$Unitario</th>
										<th>$Subtotal</th>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<th colspan="5"></th>
										<th style="width:140px;text-align:right;">Total:</th>
										<th style="width:180px;text-align:right;">$<strong id="grantotal">0.00</strong>
										</th>
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
		</div><!-- /.termina Contenedor de Pedidos -->
	</div><!-- /.row -->
</div><!-- /.container-fluid -->
<!--Fin-Contenido-->

<!-- Modal Cancela Factura -->
<div class="modal fade" id="cancelaFactura" tabindex="-1" role="dialog" aria-labelledby="cancelaFactura"
	aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Cancelar Factura <strong id="facturaCancela"> </strong></h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="form-group col-lg-6 col-md6 col-sm-12 col-xs-12">
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
					<div class="form-group col-lg-6 col-md6 col-sm-12 col-xs-12">
						<label>Factura Relacionada</label>
						<input type="hidden" name="facturaID_cancela" id="facturaID_cancela">
						<select id="folioSustitucion" name="folioSustitucion" class="form-control selectpicker"
							data-live-search="true" title="Selecciona la Factura Relacionada"></select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
				<button class="btn btn-danger" type="button" id="btnCancelarFactura" onclick="cancelar_factura()"><i
						class="fa-solid fa-xmark espaciado-icn"></i> Cancelar Factura</button>
			</div>
		</div>
	</div>
</div>
<!-- Fin modal Cancela Factura -->

<!-- Modal Productos del Pedido -->
<div class="modal fade" id="modalProductos" tabindex="-1" role="dialog" aria-labelledby="modalProductos"
	aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Productos del Pedido</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body row">
				<div class="col-lg-12 col-md-12 col-sm-12 mb-3">
					<table id="tblProductos" class="table table-striped table-bordered table-condensed table-hover">
						<thead style="background-color:#A9D0F5">
							<th>Producto</th>
							<th>Variante</th>
							<th></th>
							<th>Precio Venta</th>
							<th></th>
						</thead>
						<tbody></tbody>
						<tfoot>
							<th>Producto</th>
							<th>Variante</th>
							<th></th>
							<th>Precio Venta</th>
							<th></th>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<!-- Fin modal Productos del Pedido -->

<!-- Modal Facturas -->
<div class="modal fade" id="modalFacturas" tabindex="-1" role="dialog" aria-labelledby="modalFacturas"
	aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Facturas</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form name="formFactura" id="formFactura" method="POST">
					<div class="row form-group mb-3 pb-3" style="background-color:#f5eee6;">
						<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
							<label style="padding-top:10px;">
								<h4>Información General de Factura</h4>
							</label>
						</div>
						<div class="form-group col-lg-9 col-md-9 col-sm-12 mb-3">
							<label>Folio de Factura:</label><br />
							<input type="hidden" class="form-control" id="status">
							<input type="hidden" class="form-control" id="pedido_id_factura" name="pedido_id">
							<input type="hidden" class="form-control" id="folioFiscal">
							<input type="text" class="form-control" name="facturaID" id="facturaID" readonly="readonly"
								style="background-color:#e9ecef;">
						</div>
						<!-- <div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
						<label>Fecha Compromiso Pago:</label>
						<div class="input-group date" id="fechaCompromiso" data-target-input="nearest">
							<input type="text" class="form-control datetimepicker-input"
								data-target="#fechaCompromiso" id="fechaCompromisoInput" />
							<div class="input-group-text" data-target="#fechaCompromiso"
								data-toggle="datetimepicker">
								<i class="fa fa-calendar"></i>
							</div>
						</div>
					</div> -->
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3" style="display:none;">
							<label><text style="color:red;">*</text>Serie:</label><br />
							<select id="serie" name="serie" class="form-control selectpicker" required>
								<option value="A" selected>A</option>
							</select>
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label><text style="color:red;">*</text>Moneda:</label>
							<select id="moneda" name="moneda" class="form-control selectpicker"
								onchange="obtenerTipoCambio(this.value)" required>
								<option value="USD">Dólares</option>
								<option value="MXN">Pesos Mexicanos</option>
							</select>
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>Tipo de Cambio:</label><br />
							<input type="text" class="form-control" name="tipoCambio" id="tipoCambio" value="1">
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-6">
							<label><text style="color:red;">*</text>Tipo de Comprobante:</label>
							<select id="claveTipoComprobante" name="claveTipoComprobante"
								class="form-control selectpicker" readonly>
								<option value="I" selected>Ingreso</option>
								<option value="E" disabled>Egreso</option>
								<option value="T" disabled>Traslado</option>
							</select>
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label><text style="color:red;">*</text>Impuesto:</label><br />
							<select id="iva_muestra" class="form-control selectpicker" required>
								<option value="002" selected>IVA</option>
								<option value="001">IVA 0%</option>
							</select>
						</div>
						<input type="hidden" id="impuesto" name="impuesto">
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label><text style="color:red;">*</text>Método de Pago:</label>
							<select id="metodoPago" name="metodoPago" class="form-control selectpicker"
								title="Selecciona el Método de Pago" required>
								<option value="PUE" selected>Pago en una sola exhibición</option>
								<option value="PPD">Pago en parcialidades o diferido</option>
							</select>
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label><text style="color:red;">*</text>Uso de CFDI:</label>
							<select id="usoCfdi" name="usoCfdi" class="form-control selectpicker" required>
								<option value="G01">Adquisición de mercancías</option>
								<option value="G03">Gastos en general</option>
								<option value="S01">Sin Efectos Fiscales</option>
								<option value="P01">Por definir</option>
							</select>
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>Descuento:</label>
							<div class="input-group">
								<span class="input-group-text">$</span>
								<input type="number" min="0" step="0.01" style="text-align:right;"
									data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency"
									lang="en-US" class="form-control" name="descuento" id="descuento"
									placeholder="Descuento de Factura">
							</div>
						</div>
						<!-- <div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
						<label for="credito"><text style="color:red;">*</text>Crédito:</label>
						<select id="credito" name="credito" class="form-control" title="El crédito del cliente"
							required>
							<option value="0" selected>Inmediato</option>
							<option value="7">7 días</option>
							<option value="15">15 días</option>
							<option value="30">30 días</option>
						</select>
					</div> -->
						<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
							<label>Comentarios adicionales para la factura:</label>
							<textarea id="comentarios" name="comentarios" class="form-control"
								title="Comentario adicional" rows="3"></textarea>
						</div>
					</div>
					<div class="row form-group mb-3 pb-3" style="display:none;background-color:#e6f5ff;">
						<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
							<label style="padding-top:10px;">
								<h4>Información Factura Relacionada</h4>
							</label>
						</div>
						<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
							<label>Factura Relacionada</label>
							<input type="hidden" class="form-control" name="facturaCfdiRelacionada"
								id="facturaCfdiRelacionada">
							<select id="facturaRelacionada" name="facturaRelacionada" class="form-control selectpicker"
								data-live-search="true" title="Selecciona la Factura Relacionada">
							</select>
						</div>
						<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
							<label>Tipo de Relación:</label>
							<select id="tipoRelacion" name="tipoRelacion" class="form-control selectpicker"
								title="Selecciona el tipo de Relación de Factura">
								<option value="01">01 Nota de crédito de los documentos relacionados</option>
								<option value="02">02 Nota de débito de los documentos relacionados</option>
								<option value="03">03 Devolución de mercancía sobre facturas o traslados previos
								</option>
								<option value="04">04 Sustitución de los CFDI previos</option>
								<option value="05">05 Traslados de mercancías facturados previamente</option>
								<option value="06">06 Factura generada por los traslados previos</option>
								<option value="07">07 CFDI por aplicación de anticipo</option>
							</select>
						</div>
						<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
							<label>Total Factura Relacionada:</label>
							<div class="input-group">
								<span class="input-group-text">$</span>
								<input type="number" min="0" step="0.01" style="text-align:right;"
									data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency"
									lang="en-US" class="form-control" name="totalFacturaRelacionada"
									id="totalFacturaRelacionada" placeholder="Total de Factura Relacionada">
							</div>
						</div>
					</div>
					<div class="row form-group mb-3 pb-3" style="background-color:#eeebf5;">
						<div class="form-group col-lg-12 col-md-12 col-sm-12 mb-3">
							<label style="padding-top:10px;">
								<h4>Información del Cliente</h4>
							</label>
						</div>
						<div class="form-group col-lg-8 col-md-8 col-sm-12 mb-3">
							<label><text style="color:red;">*</text>Cliente:</label>
							<select id="clienteID_factura" name="cliente_id" class="form-control selectpicker"
								data-live-search="true" title="Selecciona al Cliente" required></select>
						</div>
						<div class="form-group col-lg-4 col-md-4 col-sm-12 mb-3">
							<label><text style="color:red;">*</text>Razón Social:</label>
							<input type="text" class="form-control" name="razonSocial" id="razonSocial" required>
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label><text style="color:red;">*</text>RFC Cliente:</label>
							<input type="text" class="form-control" name="rfcCliente" id="rfcCliente" required>
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label><text style="color:red;">*</text>Régimen Fiscal:</label>
							<select id="regimenFiscal" name="regimenFiscal" class="form-control selectpicker"
								title="Selecciona el Régimen Fiscal" data-size="5" required>
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
								<option value="616">616 - Sin obligaciones fiscales</option>
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
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label><text style="color:red;">*</text>Forma de Pago:</label>
							<select type="text" class="form-control selectpicker" name="formadePago" id="formadePago"
								title="Forma de Pago" required>
								<option value="01">Efectivo</option>
								<option value="02">Cheque nominativo</option>
								<option value="03" selected>Transferencia electrónica de fondos</option>
								<option value="06">Dinero electrónico</option>
								<option value="30">Aplicación de anticipos</option>
								<option value="99">Por definir</option>
							</select>
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>Calle:</label>
							<input type="text" class="form-control" id="calle" name="calle">
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>No. Exterior:</label>
							<input type="text" class="form-control" id="num_ext" name="num_ext">
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>No. Interior:</label>
							<input type="text" class="form-control" id="num_int" name="num_int">
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>Colonia:</label>
							<input type="text" class="form-control" id="colonia" name="colonia">
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>Municipio/Delegación:</label>
							<input type="text" class="form-control" id="poblacion" name="poblacion">
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>Estado / País:</label>
							<input type="text" class="form-control" id="edoPais" name="edoPais">
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>Código Postal:</label>
							<input type="text" class="form-control" id="cp" name="cp">
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>Email del Cliente:</label>
							<input type="text" class="form-control" id="email_cliente" name="email_cliente">
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>Teléfono del Cliente:</label>
							<input type="text" class="form-control" id="telefono" name="telefono">
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>Número de Cuenta:</label>
							<input type="text" class="form-control" id="num_cuenta" name="num_cuenta">
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 mb-3">
							<label>Banco:</label>
							<input type="text" class="form-control" id="banco" name="banco">
						</div>
					</div>
					<div class="row form-group">
						<div class="col-lg-12 col-md-12 col-sm-12 mb-3">
							<table id="detallesFactura"
								class="table table-striped table-bordered table-condensed table-hover">
								<thead style="background-color:#A9D0F5">
									<th>Cantidad</th>
									<th>Producto</th>
									<th>Variante</th>
									<th>IMG</th>
									<th>$IVA</th>
									<th>$Subtotal</th>
								</thead>
								<tbody></tbody>
								<tfoot>
									<th colspan="5"></th>
									<th style="width:140px;text-align:right;">Total:</th>
									<th style="width:180px;text-align:right;">$<strong
											id="grantotal_factura">0.00</strong></th>
								</tfoot>
							</table>
						</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cerrar</button>
				<button class="btn btn-primary float-end" type="submit" id="btnGuardarFactura" form="formFactura"><i
						class="fa fa-save espaciado-icn"></i> Guardar Factura</button>
			</div>
		</div>
	</div>
</div>
<!-- Fin modal Facturas -->

<?php
} else {
	require 'noacceso.php';
}
require '../components/footer.php';
?>
<script type="text/javascript" src="../services/pedido.js"></script>
<?php 
}
ob_end_flush();
?>