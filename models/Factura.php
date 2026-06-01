<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Factura {
	//Implementamos nuestro constructor
	public function __construct(){

	}

	// Implementamos un método para insertar Facturas
	public function insertar($serie,$metodoPago,$claveTipoComprobante,$usoCfdi,$formadePago,$descuento,$moneda,$tipoCambio,$cliente_id,$credito,$fechaCompromiso,$usuarioID,$comentarios,$facturaCfdiRelacionada,$tipoRelacion,$totalFacturaRelacionada,$producto_id,$descripcion,$cantidad,$precioVenta,$subtotal){
		$sw=true;
		$sql="INSERT INTO factura (serie,metodoPago,claveTipoComprobante,usoCfdi,formadePago,descuento,moneda,tipoCambio,cliente_id,credito,fechaCompromiso,user_id,comentarios,facturaCfdiRelacionada,tipoRelacion,totalFacturaRelacionada,created_at,fecha,status)
		VALUES ('$serie','$metodoPago','$claveTipoComprobante','$usoCfdi','$formadePago','$descuento','$moneda','$tipoCambio','$cliente_id','$credito','$fechaCompromiso','$usuarioID','$comentarios','$facturaCfdiRelacionada','$tipoRelacion','$totalFacturaRelacionada',NOW(),NOW(),'Espera')";
		$facturaIDnew=ejecutarConsulta_retornarID($sql) or $sw = false;

		if($producto_id){
			$num=0;
			while ($num < count($producto_id)){
				$sql_detalle = "INSERT INTO facturaDetalle (factura_id,producto_id,descripcion,cantidad,precioUnitario,subtotal,ivaUnitario) VALUES ('$facturaIDnew','$producto_id[$num]','$descripcion[$num]','$cantidad[$num]','$precioVenta[$num]','$subtotal[$num]','$subtotal[$num]'*0.16)";
				ejecutarConsulta($sql_detalle) or $sw = false;
				$num = $num + 1;
			}
		}
		return $sw;
	}

	public function editar($facturaID,$serie,$metodoPago,$claveTipoComprobante,$usoCfdi,$formadePago,$descuento,$moneda,$tipoCambio,$cliente_id,$credito,$fechaCompromiso,$usuarioID,$comentarios,$facturaCfdiRelacionada,$tipoRelacion,$totalFacturaRelacionada,$producto_id,$descripcion,$cantidad,$precioVenta,$subtotal){
		$sw=true;
		$sql="UPDATE factura SET serie='$serie',metodoPago='$metodoPago',claveTipoComprobante='$claveTipoComprobante',usoCfdi='$usoCfdi',formadePago='$formadePago',descuento='$descuento',moneda='$moneda',tipoCambio='$tipoCambio',cliente_id='$cliente_id',credito='$credito',fechaCompromiso='$fechaCompromiso',comentarios='$comentarios',facturaCfdiRelacionada='$facturaCfdiRelacionada',tipoRelacion='$tipoRelacion',totalFacturaRelacionada='$totalFacturaRelacionada',updated_at=NOW() WHERE id='$facturaID'";
		ejecutarConsulta($sql) or $sw=false;

		if($sw==true){
			if($producto_id){
				$sqldel="DELETE FROM facturaDetalle WHERE factura_id='$facturaID'";
				ejecutarConsulta($sqldel) or $sw=false;

				$num=0;
				while ($num < count($producto_id)){
					$sql_detalle = "INSERT INTO facturaDetalle (factura_id,producto_id,descripcion,cantidad,precioUnitario,subtotal,ivaUnitario) VALUES ('$facturaID','$producto_id[$num]','$descripcion[$num]','$cantidad[$num]','$precioVenta[$num]','$subtotal[$num]','$subtotal[$num]'*0.16)";
					ejecutarConsulta($sql_detalle) or $sw = false;

					$num = $num + 1;
				}
			}
		}
		return $sw;
	}

	//Implementamos un método para insertar los pagos
	public function guardar_pagos($facturaID_pago,$noPago,$pagado,$estePago,$porPagar,$banco,$numCuenta){
		$sql="INSERT INTO pago(facturaID,noPago,pagado,estePago,porPagar,banco,numCuenta)
		VALUES ('$facturaID_pago','$noPago','$pagado','$estePago','$porPagar','$banco','$numCuenta')";
		return ejecutarConsulta($sql);
	}

	public function guardaPago($pagoPDF,$pagoXML,$UUID){	
		$sql="UPDATE pago SET pagoPDF='$pagoPDF',pagoXML='$pagoXML',folioFiscal='$UUID' ORDER BY pagoID DESC LIMIT 1";
		return ejecutarConsulta($sql);
	}

	//UPDATE `tabla` SET datos = CONCAT(datos, "cadena_nueva") WHERE id =3;
	public function mostrar_pago($facturaID){
		$sql="SELECT pag.pagoID,fac.id AS facturaID,pag.fecha,pag.folioFiscal,pag.noPago,pag.pagado,pag.estePago,pag.porPagar,pag.banco,pag.numCuenta,pag.pagoPDF,pag.pagoXML,fac.total FROM pago pag RIGHT JOIN factura fac ON pag.facturaID=fac.id AS facturaID WHERE fac.id='$facturaID' ORDER BY noPago DESC";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementamos un método para marcar como Espera la orden
	public function espera($facturaID){
		$sql="UPDATE factura SET status='Espera' WHERE id='$facturaID' AND status!='Cancelado'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para cancelar la orden
	public function cancelar($facturaID){
		$sql="UPDATE factura SET status='Cancelado' WHERE id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para marcar como pago parcial
	public function parcial($facturaID){
		$sql="UPDATE factura SET status='Parcial' WHERE id='$facturaID' AND status!='Cancelado'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para marcar como pagada la orden
	public function pagada($facturaID,$fechaPago,$formaPagoAut){
		$sql="UPDATE factura SET status='Pagada',fechaPago='$fechaPago',formaPago='$formaPagoAut' WHERE id='$facturaID' AND status!='Cancelado'";
		return ejecutarConsulta($sql);
	}

	public function retimbrar($facturaID){
		$sql="UPDATE factura SET nombrePDF='',nombreXML='',status='Espera' WHERE id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($facturaID){
		$sql="SELECT fac.id AS facturaID,fac.pedido_id AS pedidoID,fac.serie,fac.metodoPago,fac.fecha,fac.folioFiscal,fac.claveTipoComprobante,fac.usoCfdi,fac.formadePago,fac.descuento,fac.moneda,fac.tipoCambio,fac.nombrePDF,fac.nombreXML,fac.cliente_id,fac.credito,fac.fechaCompromiso,fac.user_id,fac.conPago,fac.status,dat.razonSocial,IFNULL(dat.razonSocial,cli.display_name) AS nombreCliente,dat.rfcCliente,dat.regimenFiscal,dat.num_cuenta,dat.calle,dat.num_ext,dat.num_int,dat.colonia,dat.poblacion,dat.edoPais,dat.cp,usr.nombre AS nombreVendedor,fac.facturaCfdiRelacionada,fac.tipoRelacion,fac.totalFacturaRelacionada FROM factura fac LEFT JOIN datos_fiscales dat ON fac.cliente_id=dat.cliente_id LEFT JOIN wp_users cli ON fac.cliente_id=cli.ID LEFT JOIN users usr ON fac.user_id=usr.id WHERE fac.id='$facturaID'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarDetalle($facturaID){
		$sql="SELECT det.id,det.producto_id,det.descripcion,det.cantidad,det.precioUnitario,det.subtotal,IFNULL(prod.post_title,facprod.nombre) AS nombreProducto,IFNULL(med.nombre,'Pieza') AS nombreUnidad,IFNULL(med.unidad,'H87') AS unidad FROM facturaDetalle det LEFT JOIN wp_posts prod ON det.producto_id=prod.ID LEFT JOIN producto_facturacion facprod ON det.producto_id=facprod.producto_id LEFT JOIN unidadesmedida med ON facprod.medida_id=med.id WHERE det.factura_id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	public function mostrarPagos($facturaID){
		$sql="SELECT pag.id AS complementoID,'P' AS serie,pag.formadePago,pag.fechaPago,pag.folioFiscal,'P01' AS claveTipoComprobante,pag.usoCfdi,pag.formadePago,0 AS descuento,det.estePago,pag.pagoPDF,pag.pagoXML,pag.cliente_id,pag.user_id,pag.statusPago,pag.banco,pag.cliente_id,cli.razonSocial,cli.rfcCliente,cli.num_cuenta,cli.formadePago,cli.calle,cli.num_ext,cli.num_int,cli.colonia,cli.poblacion,cli.edoPais,cli.cp,u.nombre,det.facturas FROM complementos pag INNER JOIN (SELECT complemento_id,SUM(estePago) AS estePago,GROUP_CONCAT(DISTINCT factura_id) AS facturas FROM complementoDetalles GROUP BY complemento_id) det ON pag.id=det.complemento_id LEFT JOIN datos_fiscales cli ON pag.cliente_id=cli.cliente_id LEFT JOIN users u ON pag.user_id=u.id WHERE FIND_IN_SET('$facturaID', det.facturas) GROUP BY pag.id";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para cargar la orden en la factura
	public function cargar($pedido_id){
		// $sql="SELECT o.pedido_id,o.fechaOrden,o.cliente_id,u.nombre AS usuario,c.razonSocial AS razonSocial,c.cliente,c.contacto,o.asociadoID,asoc.nombreAsociado,o.contacto,o.precioEnvio,o.subtotal,o.descuento,o.iva,o.total,o.statusOrden FROM clases o INNER JOIN users u ON o.user_id=u.user_id INNER JOIN cliente c ON o.cliente_id=c.cliente_id INNER JOIN asociado asoc ON o.asociadoID=asoc.asociadoID WHERE o.pedido_id='$pedido_id'";
		// return ejecutarConsulta($sql);		
	}

	public function mostrarDetalles($facturaID){
		// $sql="SELECT pauno.pagoID,pauno.fecha,pauno.folioFiscal,det.total,pauno.banco,pauno.formadePago,pauno.fechaPago,pauno.usoCfdi,pauno.pagoPDF,pauno.pagoXML,pauno.statusPago,GROUP_CONCAT(DISTINCT det.facturaID SEPARATOR ', ') AS facturasRelacionadas,cli.cliente FROM pago pauno LEFT JOIN (SELECT facturaID,pagoID,SUM(estePago) AS total FROM detallePago GROUP BY pagoID) det ON det.pagoID=pauno.pagoID INNER JOIN cliente cli ON pauno.cliente_id_pago=cli.cliente_id WHERE det.id='$facturaID' GROUP BY pauno.pagoID";
		// return ejecutarConsulta($sql);
	}

	public function listarDetalle_pagos($facturaID){
		$sql="SELECT * FROM pago WHERE id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function listar(){
		$sql="SELECT fac.id AS facturaID,fac.serie,fac.metodoPago,fac.fecha,fac.folioFiscal,fac.claveTipoComprobante,fac.usoCfdi,fac.formadePago,fac.descuento,fac.moneda,fac.tipoCambio,fac.nombrePDF,fac.nombreXML,fac.cliente_id,fac.user_id,fac.status,IFNULL(dat.razonSocial,cli.display_name) AS nombreCliente,IFNULL(dat.razonSocial,cli.display_name) AS razonSocial,dat.regimenFiscal,usr.nombre,fac.emailEnviado,IFNULL(tot.subtotal,0) AS subtotal,IFNULL(tot.subtotal,0)*0.16 AS iva,IFNULL(tot.subtotal,0)*1.16 AS total,IFNULL(pagado.pagado,0) AS pagado,fac.nombrePDFCancelado,fac.nombreXMLCancelado FROM factura fac LEFT JOIN datos_fiscales dat ON fac.cliente_id=dat.cliente_id LEFT JOIN wp_users cli ON fac.cliente_id=cli.ID LEFT JOIN (SELECT factura_id,SUM(subtotal) AS subtotal FROM facturaDetalle GROUP BY factura_id) tot ON fac.id=tot.factura_id LEFT JOIN users usr ON fac.user_id=usr.id LEFT JOIN (SELECT detpag.factura_id,SUM(detpag.estePago) AS pagado FROM complementoDetalles detpag LEFT JOIN complementos comp ON detpag.complemento_id=comp.id WHERE comp.statusPago!='Cancelado' GROUP BY factura_id) pagado ON fac.id=pagado.factura_id GROUP BY fac.id";
		return ejecutarConsulta($sql);
	}

	public function listarProductos(){
		$sql="SELECT p.ID AS id,IFNULL(prod.post_title,p.post_title) AS nombreProducto,IFNULL(fac.observaciones,p.post_title) AS descripcion,COALESCE(fac.precioVenta,pm1.meta_value,0) AS precioVenta,IF(p.post_status='publish',1,0) AS activo,IFNULL(med.nombre,'Pieza') AS nombreUnidad,IFNULL(med.unidad,'H87') AS unidad,IFNULL(cla.nombre,'Componentes de vehículo') AS nombreClave,IFNULL(cla.clave,'25172100') AS clave FROM wp_posts p LEFT JOIN wp_posts prod ON p.post_parent=prod.ID AND prod.post_type='product' LEFT JOIN wp_postmeta pm1 ON p.ID=pm1.post_id AND pm1.meta_key='_price' LEFT JOIN producto_facturacion fac ON p.ID=fac.producto_id LEFT JOIN unidadesmedida med ON fac.medida_id=med.id LEFT JOIN clavesfactura cla ON fac.clave_id=cla.id WHERE p.post_type='product_variation' OR p.post_type='product' GROUP BY p.ID";
		return ejecutarConsulta($sql); 
	}

	public function timbra($facturaID){
		$sql="SELECT fac.id AS facturaID,fac.serie,fac.metodoPago,fac.fecha,fac.folioFiscal,fac.claveTipoComprobante,fac.usoCfdi,fac.credito,fac.fechaCompromiso,IFNULL(met.codigo,fac.formadePago) AS formadePago,fac.descuento,det.subtotal,fac.moneda,fac.tipoCambio,det.subtotal*0.16 AS iva,det.subtotal*1.16 AS total,fac.nombrePDF,fac.nombreXML,fac.cliente_id,fac.user_id,fac.conPago,fac.status,cli.razonSocial,cli.regimenFiscal,cli.razonSocial AS nombreCliente,cli.rfcCliente,cli.num_cuenta,cli.calle,cli.num_ext,cli.num_int,cli.colonia,cli.poblacion,cli.edoPais,cli.cp,u.nombre,fac.facturaFolioRelacionada,fac.facturaCfdiRelacionada,fac.tipoRelacion,fac.totalFacturaRelacionada,fac.comentarios AS comentarioAdicional FROM factura fac LEFT JOIN (SELECT factura_id,SUM(subtotal) AS subtotal FROM facturaDetalle GROUP BY factura_id) det ON fac.id=det.factura_id LEFT JOIN datos_fiscales cli ON fac.cliente_id=cli.cliente_id LEFT JOIN users u ON fac.user_id=u.id LEFT JOIN metodopagos met ON fac.formadePago=met.id WHERE fac.id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	public function timbra_detallesFacturaExtranjero($facturaID){
		$sql="SELECT dfi.factura_id,dfi.descripcion,IFNULL(prod.post_title,profac.nombre) AS nombreProducto,dfi.cantidad AS cantidad,dfi.producto_id,dfi.cantidad,dfi.precioUnitario AS precioVenta,IFNULL(dfi.claveIva,'002') AS claveIva,IFNULL(med.unidad,'H87') AS unidad,IFNULL(med.nombre,'Pieza') AS nombreMedida,IFNULL(dfi.factor,'Tasa') AS factor,IFNULL(dfi.tasaCuota,'0.160000') AS tasaCuota,dfi.ivaUnitario,fac.tipoCambio,IFNULL(cla.clave,'25172100') AS clave FROM facturaDetalle dfi INNER JOIN factura fac ON dfi.factura_id=fac.id LEFT JOIN producto_facturacion profac ON dfi.producto_id = profac.producto_id LEFT JOIN wp_posts prod ON dfi.producto_id = prod.ID LEFT JOIN unidadesmedida med ON profac.medida_id = med.id LEFT JOIN clavesfactura cla ON profac.clave_id = cla.id WHERE dfi.factura_id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	public function timbra_detallesFactura($facturaID){
		$sql="SELECT dfi.factura_id,dfi.descripcion,IFNULL(prod.post_title,profac.nombre) AS nombreProducto,dfi.cantidad AS cantidad,dfi.producto_id,dfi.cantidad,dfi.precioUnitario/1.16 AS precioVenta,IFNULL(dfi.claveIva,'002') AS claveIva,IFNULL(med.unidad,'H87') AS unidad,IFNULL(med.nombre,'Pieza') AS nombreMedida,IFNULL(dfi.factor,'Tasa') AS factor,IFNULL(dfi.tasaCuota,'0.160000') AS tasaCuota,dfi.ivaUnitario,fac.tipoCambio,IFNULL(cla.clave,'25172100') AS clave FROM facturaDetalle dfi INNER JOIN factura fac ON dfi.factura_id=fac.id LEFT JOIN producto_facturacion profac ON dfi.producto_id = profac.producto_id LEFT JOIN wp_posts prod ON dfi.producto_id = prod.ID LEFT JOIN unidadesmedida med ON profac.medida_id = med.id LEFT JOIN clavesfactura cla ON profac.clave_id = cla.id WHERE dfi.factura_id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	public function guardaFacturaXML($facturaID,$NomArchXML){	
		$sql="UPDATE factura SET status='Timbrada',nombreXML='$NomArchXML' WHERE id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	public function guardaFacturaPDF($facturaID,$NomArchPDF,$UUID){	
		$sql="UPDATE factura SET status='Facturado',nombrePDF='$NomArchPDF',folioFiscal='$UUID' WHERE id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	public function mostrarEmailsFactura($facturaID){
		$sql="SELECT fac.id AS facturaID,IFNULL(dat.email_cliente,cli.user_email) AS email,IFNULL(dat.razonSocial,cli.display_name) AS nombreCliente,fac.nombreXML,fac.nombrePDF FROM factura fac LEFT JOIN wp_users cli ON fac.cliente_id=cli.ID LEFT JOIN datos_fiscales dat ON fac.cliente_id=dat.cliente_id WHERE fac.id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	public function marcarFacturaEnviada($facturaID){
		$sql="UPDATE factura SET emailEnviado='1' WHERE id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	public function select_factura(){
		$sql="SELECT fac.id AS facturaID,fac.serie,fac.folioFiscal AS uuid,tot.subtotal,tot.subtotal*0.16 AS iva,tot.subtotal*1.16 AS total,fac.status,dat.razonSocial,IFNULL(dat.razonSocial,cli.display_name) AS nombreCliente FROM factura fac LEFT JOIN wp_users cli ON fac.cliente_id=cli.ID LEFT JOIN datos_fiscales dat ON fac.cliente_id=dat.cliente_id INNER JOIN (SELECT factura_id,SUM(subtotal) AS subtotal FROM facturaDetalle GROUP BY factura_id) tot ON fac.id=tot.factura_id WHERE fac.status!='Cancelado' AND fac.folioFiscal!='' ORDER BY facturaID DESC";
		return ejecutarConsulta($sql);
	}

	public function cancela_factura($facturaID){
		$sql="SELECT fac.id AS facturaID,fac.serie,fac.metodoPago,fac.fecha,fac.folioFiscal,fac.claveTipoComprobante,fac.usoCfdi,fac.credito,fac.fechaCompromiso,fac.formadePago,fac.descuento,tot.subtotal/1.16 AS subtotal,fac.moneda,fac.tipoCambio,fac.nombrePDF,fac.nombreXML,fac.cliente_id,fac.user_id,fac.conPago,fac.status,cli.razonSocial,cli.regimenFiscal,cli.razonSocial AS nombreCliente,cli.rfcCliente,cli.num_cuenta,cli.calle,cli.num_ext,cli.num_int,cli.colonia,cli.poblacion,cli.edoPais,cli.cp,u.nombre,fac.facturaFolioRelacionada,fac.facturaCfdiRelacionada,fac.tipoRelacion,fac.totalFacturaRelacionada,fac.comentarios AS comentarioAdicional FROM factura fac JOIN (SELECT factura_id,SUM(subtotal) AS subtotal FROM facturaDetalle GROUP BY factura_id) tot ON fac.id=tot.factura_id LEFT JOIN datos_fiscales cli ON fac.cliente_id=cli.cliente_id LEFT JOIN users u ON fac.user_id=u.id LEFT JOIN metodopagos met ON fac.formadePago=met.id WHERE fac.id='$facturaID' GROUP BY fac.id";
		return ejecutarConsulta($sql);
	}

	public function guardaFacturaCancelada($facturaID,$rutaPDF,$rutaXML,$motivo,$facturaIDRelacionada){
		$sql="UPDATE factura SET nombrePDFCancelado='$rutaPDF',nombreXMLCancelado='$rutaXML',motivo='$motivo',folioSustitucion='$facturaIDRelacionada',pedido='0' WHERE id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	public function actualiza_cancela_factura($facturaID){
		$sql="SELECT fac.id AS facturaID,fac.serie,fac.folioFiscal,fac.nombreXML,fac.nombrePDF,fac.status,IFNULL(dat.razonSocial,cli.display_name) AS nombreCliente FROM factura fac LEFT JOIN wp_users cli ON fac.cliente_id=cli.ID LEFT JOIN datos_fiscales dat ON fac.cliente_id=dat.cliente_id WHERE fac.id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	public function cancelar_factura($facturaID){
		$sql="UPDATE factura SET status='Cancelado',fechaCancelacion=NOW() WHERE id='$facturaID'";
		return ejecutarConsulta($sql);
	}

	public function obtener_facturas_cliente($cliente_id){
		$sql="SELECT fac.id AS facturaID,tot.subtotal,fac.serie,fac.folioFiscal,fac.status,IFNULL(dat.razonSocial,cli.display_name) AS nombreCliente,IFNULL(dat.razonSocial,cli.display_name) AS razonSocial FROM factura fac LEFT JOIN wp_users cli ON fac.cliente_id=cli.ID LEFT JOIN datos_fiscales dat ON fac.cliente_id=dat.cliente_id LEFT JOIN (SELECT factura_id,SUM(subtotal) AS subtotal FROM facturaDetalle GROUP BY factura_id) tot ON fac.id=tot.factura_id WHERE fac.cliente_id='$cliente_id' AND fac.status!='Cancelado' AND fac.folioFiscal!='' GROUP BY fac.id";
		return ejecutarConsulta($sql);
	}

	public function copiaFactura($facturaID,$cliente_id){
		$sw=true;
		$sql="INSERT INTO factura (serie,metodoPago,claveTipoComprobante,usoCfdi,formadePago,descuento,moneda,tipoCambio,cliente_id,user_id,comentarios,facturaCfdiRelacionada,tipoRelacion,totalFacturaRelacionada,created_at,fecha,status)
		SELECT fac.serie,fac.metodoPago,fac.claveTipoComprobante,fac.usoCfdi,fac.formadePago,fac.descuento,fac.moneda,fac.tipoCambio,'$cliente_id',fac.user_id,fac.comentarios,fac.facturaCfdiRelacionada,fac.tipoRelacion,fac.totalFacturaRelacionada,NOW(),NOW(),'Espera' FROM factura fac WHERE fac.id='$facturaID'";
		$facturaIDnew=ejecutarConsulta_retornarID($sql) or $sw = false;

		if($sw==true){
			$sql_detalle = "INSERT INTO facturaDetalle(factura_id,producto_id,descripcion,cantidad,precioUnitario,subtotal,ivaUnitario) SELECT '$facturaIDnew',det.producto_id,det.descripcion,det.cantidad,det.precioUnitario,det.subtotal,det.ivaUnitario FROM facturaDetalle det WHERE det.factura_id='$facturaID'";
			ejecutarConsulta($sql_detalle) or $sw = false;
		}
		return $sw;
	}
}
?>