<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Complemento {
	//Implementamos nuestro constructor
	public function __construct(){
	}

	public function insertar($cliente_id,$banco,$numCuenta,$formadePago,$fechaPago,$usoCfdi,$comentarioAdicional,$usuarioID,$factura,$folioFiscalFac,$fechaFactura,$parcialidad,$saldoAnterior,$estePago,$saldoRestante){
		$sw=true;
		$sql="INSERT INTO complementos(cliente_id,banco,numCuenta,formadePago,fechaPago,usoCfdi,comentarioAdicional,user_id,created_at,statusPago)
		VALUES ('$cliente_id','$banco','$numCuenta','$formadePago','$fechaPago','$usoCfdi','$comentarioAdicional','$usuarioID',NOW(),'En Espera')";
		$complementoIDnew=ejecutarConsulta_retornarID($sql) or $sw = false;

		if($sw==true){
			$num=0;
			while ($num < count($factura)){
				if($saldoRestante[$num]=='0.00' OR $saldoRestante[$num]==0){
					$sql_pagada = "UPDATE factura SET status='Pagada',fechaPago='$fechaPago',conPago='1' WHERE id='$factura[$num]'";
					ejecutarConsulta($sql_pagada);
				}
				$sql_detalle = "INSERT INTO complementoDetalles(complemento_id,factura_id,folioFiscalFac,fechaFactura,parcialidad,saldoAnterior,estePago,saldoRestante,created_at) VALUES ('$complementoIDnew', '$factura[$num]', '$folioFiscalFac[$num]', '$fechaFactura[$num]','$parcialidad[$num]','$saldoAnterior[$num]','$estePago[$num]','$saldoRestante[$num]',NOW())";
				ejecutarConsulta($sql_detalle) or $sw = false;
				$num=$num + 1;
			}
		}
		return $sw;
	}

	public function editar($complementoID,$cliente_id,$banco,$numCuenta,$formadePago,$fechaPago,$usoCfdi,$comentarioAdicional,$usuarioID,$factura,$folioFiscalFac,$fechaFactura,$parcialidad,$saldoAnterior,$estePago,$saldoRestante){
		$sw=true;
		$sql="UPDATE complementos SET cliente_id='$cliente_id',banco='$banco',numCuenta='$numCuenta',formadePago='$formadePago',fechaPago='$fechaPago',usoCfdi='$usoCfdi',comentarioAdicional='$comentarioAdicional',updated_at=NOW() WHERE id='$complementoID'";
		ejecutarConsulta($sql) or $sw = false;

		if($sw==true){
			$sqldel="DELETE FROM complementoDetalles WHERE complemento_id='$complementoID'";
			ejecutarConsulta($sqldel) or $sw=false;

			$num=0;
			while ($num < count($factura)){
				if($saldoRestante[$num]=='0.00' OR $saldoRestante[$num]==0){
					$sql_pagada = "UPDATE factura SET status='Pagada',fechaPago='$fechaPago',conPago='1' WHERE id='$factura[$num]'";
					ejecutarConsulta($sql_pagada);
				}
				$sql_detalle = "INSERT INTO complementoDetalles(complemento_id,factura_id,folioFiscalFac,fechaFactura,parcialidad,saldoAnterior,estePago,saldoRestante,created_at) VALUES ('$complementoID', '$factura[$num]', '$folioFiscalFac[$num]', '$fechaFactura[$num]','$parcialidad[$num]','$saldoAnterior[$num]','$estePago[$num]','$saldoRestante[$num]',NOW())";
				ejecutarConsulta($sql_detalle) or $sw = false;
				$num=$num + 1;
			}
		}
		return $sw;
	}

	public function mostrar($complementoID){
		$sql="SELECT comp.id AS complementoID,comp.folioFiscal,comp.banco,comp.numCuenta,comp.formadePago,comp.usoCfdi,comp.comentarioAdicional,comp.cliente_id,comp.fechaPago,comp.statusPago FROM complementos comp WHERE comp.id='$complementoID'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementamos un método para cancelar el pago
	public function cancelar_pago($complementoID){
		$sql="UPDATE complementos SET statusPago='Cancelado' WHERE complementoID='$complementoID'";
		return ejecutarConsulta($sql);
	}

	public function retimbrar_pago($complementoID){
		$sql="UPDATE complementos SET pagoPDF='',pagoXML='', statusPago='En Espera' WHERE complementoID='$complementoID'";
		return ejecutarConsulta($sql);
	}

	public function cambia_numCuenta($complementoID,$numCuenta){
		$sql="UPDATE complementos SET numCuenta='$numCuenta' WHERE complementoID='$complementoID'";
		return ejecutarConsulta($sql);
	}

	public function listarDetalle($complementoID){
		$sql="SELECT det.id,det.complemento_id AS complementoID,det.factura_id,det.folioFiscalFac,det.serie,fac.fecha,det.parcialidad,det.saldoAnterior,det.estePago,det.saldoRestante,cli.nombre AS nombreCliente FROM complementoDetalles det INNER JOIN factura fac ON det.factura_id=fac.id INNER JOIN clientes cli ON fac.cliente_id=cli.id WHERE det.complemento_id='$complementoID'";
		return ejecutarConsulta($sql);
	}

	public function listar(){
		$sql="SELECT pag.id AS complementoID,pag.fechaPago,pag.folioFiscal,det.total,pag.banco,pag.formadePago,pag.fechaPago,pag.usoCfdi,pag.pagoPDF,pag.pagoXML,pag.statusPago,det.facturasRelacionadas,pag.emailEnviado,pag.cliente_id FROM complementos pag INNER JOIN (SELECT complemento_id,GROUP_CONCAT(DISTINCT factura_id SEPARATOR ', ') AS facturasRelacionadas,SUM(estePago) AS total FROM complementoDetalles GROUP BY complemento_id) det ON det.complemento_id=pag.id GROUP BY pag.id";
		return ejecutarConsulta($sql);		
	}

	public function listar_facturas($cliente_id){
		$sql="SELECT fac.id AS facturaID,fac.cliente_id,fac.serie,fac.fecha,fac.folioFiscal,det.subtotal,fac.descuento,cli.nombre AS nombreCliente,cli.razonSocial,usr.nombre AS vendedor,fac.status,IFNULL(pago.parcialidad,0) AS parcialidad,pago.saldoAnterior,IFNULL(pago.estePago,0) AS estePago,pago.statusPago FROM factura fac LEFT JOIN clientes cli ON fac.cliente_id=cli.id INNER JOIN (SELECT factura_id,SUM(subtotal) AS subtotal FROM facturaDetalle GROUP BY factura_id) det ON fac.id=det.factura_id INNER JOIN users usr ON fac.user_id=usr.id LEFT JOIN (SELECT IFNULL(MIN(detpag.saldoRestante),'0.00') AS saldoRestante,IFNULL(MAX(detpag.saldoAnterior),'0.00') AS saldoAnterior,SUM(estePago) AS estePago,detpag.factura_id,MAX(detpag.parcialidad) AS parcialidad,pag.statusPago FROM complementoDetalles detpag LEFT JOIN complementos pag ON detpag.complemento_id=pag.id WHERE pag.statusPago!='Cancelado' GROUP BY detpag.factura_id) pago ON fac.id=pago.factura_id WHERE fac.cliente_id='$cliente_id' AND fac.status='Facturado' GROUP BY fac.id";
		return ejecutarConsulta($sql);
	}

	public function timbra($complementoID){
		$sql="SELECT pago.id AS complementoID,det.total,pago.banco,pago.numCuenta,fop.codigo AS formadePago,pago.fechaPago,pago.usoCfdi,pago.comentarioAdicional,cli.nombre AS nombreCliente,cli.razonSocial,cli.rfcCliente,cli.calle,cli.num_ext,cli.num_int,cli.colonia,cli.cp,cli.poblacion,cli.edoPais,cli.regimenFiscal FROM complementos pago INNER JOIN (SELECT complemento_id,SUM(estePago) AS total FROM complementoDetalles GROUP BY complemento_id) det ON pago.id=det.complemento_id INNER JOIN clientes cli ON pago.cliente_id=cli.id INNER JOIN metodopagos fop ON pago.formadePago=fop.id WHERE pago.id='$complementoID'";
		return ejecutarConsulta($sql);
	}

	public function timbra_detallesPago($complementoID){
		$sql="SELECT pago.id,pago.complemento_id AS complementoID,pago.factura_id,pago.folioFiscalFac,pago.fechaFactura,pago.parcialidad,pago.saldoAnterior,pago.estePago/1.16 AS estePago,pago.saldoRestante/1.16 AS saldoRestante,fac.metodoPago FROM complementoDetalles pago INNER JOIN factura fac ON fac.id=pago.factura_id WHERE pago.complemento_id='$complementoID'";
		return ejecutarConsulta($sql);
	}

	public function guardaArchivoPago($complementoID,$NomArchPDF,$NomArchXML,$UUID){
		$sql="UPDATE complementos SET folioFiscal='$UUID', pagoPDF='$NomArchPDF', pagoXML='$NomArchXML',statusPago='Timbrada' WHERE id='$complementoID'";
		return ejecutarConsulta($sql);
	}

	public function mostrarEmails($complementoID){
		$sql="SELECT fac.complementoID,CONCAT_WS(',',cli.email,cli.email2,cli.email3,cli.email4,cli.email5,cli.email6) AS email,fac.pagoXML AS nombreXML,fac.pagoPDF AS nombrePDF,CONCAT_WS(' - ',fac.complementoID,'P') AS Fact_NoFact,'P' AS serie,cli.razonSocial FROM complementos fac INNER JOIN clientes cli ON fac.cliente_id=cli.id WHERE fac.complementoID='$complementoID'";
		return ejecutarConsulta($sql);
	}

	public function marcarEmailEnviado($complementoID){
		$sql="UPDATE complementos SET emailEnviado='1' WHERE complementoID='$complementoID'";
		return ejecutarConsulta($sql);
	}
}
?>