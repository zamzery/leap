<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (strlen(session_id()) < 1) 
	session_start();

	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL & ~E_DEPRECATED);

require_once "../models/Complemento.php";
$pagos=new Complemento();

$usuarioID=$_SESSION["usuarioID"];
$complementoID=isset($_POST["complementoID"])? limpiarCadena($_POST["complementoID"]):"";

$totalPago=isset($_POST["totalPago"])? limpiarCadena($_POST["totalPago"]):"";
$cliente_id=isset($_POST["cliente_id"])? limpiarCadena($_POST["cliente_id"]):"";
$banco=isset($_POST["banco"])? limpiarCadena($_POST["banco"]):"";
$numCuenta=isset($_POST["numCuenta"])? limpiarCadena($_POST["numCuenta"]):"";
$formadePago=isset($_POST["formadePago"])? limpiarCadena($_POST["formadePago"]):"";
$fechaPago=isset($_POST["fechaPago"])? limpiarCadena($_POST["fechaPago"]):"";
$usoCfdi=isset($_POST["usoCfdi"])? limpiarCadena($_POST["usoCfdi"]):"";
$comentarioAdicional=isset($_POST["comentarioAdicional"])? limpiarCadena($_POST["comentarioAdicional"]):"";

$NomArchPDF=isset($_POST["NomArchPDF"])? limpiarCadena($_POST["NomArchPDF"]):"";
$NomArchXML=isset($_POST["NomArchXML"])? limpiarCadena($_POST["NomArchXML"]):"";
$UUID=isset($_POST["UUID"])? limpiarCadena($_POST["UUID"]):"";

$nombrePDF=isset($_POST["nombrePDF"])? limpiarCadena($_POST["nombrePDF"]):"";
$nombreXML=isset($_POST["nombreXML"])? limpiarCadena($_POST["nombreXML"]):"";
$Fact_NoFact=isset($_POST["Fact_NoFact"])? limpiarCadena($_POST["Fact_NoFact"]):"";
$razonSocial=isset($_POST["razonSocial"])? limpiarCadena($_POST["razonSocial"]):"";
$folioFiscal=isset($_POST["folioFiscal"])? limpiarCadena($_POST["folioFiscal"]):"";
$vendedor2=isset($_POST["vendedor2"])? limpiarCadena($_POST["vendedor2"]):"";
$cliente2=isset($_POST["cliente2"])? limpiarCadena($_POST["cliente2"]):"";

//Cancela Complementos
$folioSustitucion=isset($_POST["folioSustitucion"])? limpiarCadena($_POST["folioSustitucion"]):"";
$complementoIDRelacionado=isset($_POST["complementoIDRelacionado"])? limpiarCadena($_POST["complementoIDRelacionado"]):"";
$motivo=isset($_POST["motivo"])? limpiarCadena($_POST["motivo"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if(empty($complementoID)){
			$rspta=$pagos->insertar($cliente_id,$banco,$numCuenta,$formadePago,$fechaPago,$usoCfdi,$comentarioAdicional,$usuarioID,$_POST["factura"],$_POST["folioFiscalFac"],$_POST["fechaFactura"],$_POST["parcialidad"],$_POST["saldoAnterior"],$_POST["estePago"],$_POST["saldoRestante"]);
			echo $rspta ? "Pago creado" : "No se pudo crear el pago";
		} else {
			$rspta=$pagos->editar($complementoID,$cliente_id,$banco,$numCuenta,$formadePago,$fechaPago,$usoCfdi,$comentarioAdicional,$usuarioID,$_POST["factura"],$_POST["folioFiscalFac"],$_POST["fechaFactura"],$_POST["parcialidad"],$_POST["saldoAnterior"],$_POST["estePago"],$_POST["saldoRestante"]);
			echo $rspta ? "Pago editado" : "No se pudo editar el pago";
		}
	break;

	case 'timbra':
		$respuestaServer = false;
		$respuesta = true;
		$rspta=$pagos->timbra($complementoID);
		$datosPagoEncontrados = false;
		while ($reg = $rspta->fetch_object()){
			$datosPagoEncontrados = true;
			/// 4. DATOS GENERALES DE LA FACTURA //////////////////////////////////////////////
			$fact_serie         = 'P';                                 // 4.1 Número de serie
			$fact_folio         = $reg->complementoID;               // 4.2 Número de folio
			$NoFac              = $reg->complementoID.'-P';         // 4.3 Serie de la factura concatenado con el número de folio
			$NumCtaPago         = $reg->numCuenta;                      // 4.5 Número de cuenta (sólo últimos 4 dígitos, opcional)
			$LugarExpedicion    = '44460';                              // 4.6 Lugar de expedición (código postal de emisor)
			$razonSocial        = $reg->razonSocial;
			$formaPago          = $reg->formadePago;
			$formaPagoFac       = $reg->formadePago;
			$monedaPago         = 'MXN';
			$montoPago          = $reg->total;
			$bancoPago          = $reg->banco;
			$cuentaPago         = $reg->numCuenta;
			$usoCfdi            = $reg->usoCfdi;
			$fechaPago          = $reg->fechaPago;
			$metodoDePago		= "";
			$descuento			= "";
			$SumaEstePago       = "";

			/// 8. DATOS GENERALES DEL RECEPTOR (CLIENTE) /////////////////////////////////////
			$RFC_Recep = $reg->rfcCliente;   // 8.1 RFC (Al momento de timbrar el PAC verifica que el RFC se encuentre activo en el SAT).
			if (strlen($RFC_Recep)==12){$RFC_Recep = " ".$RFC_Recep; }else{$RFC_Recep = $RFC_Recep;} // 8.2 Al RFC de personas morales se le antecede un espacio en blanco para que su longitud sea de 13 caracteres ya que estos son de longitud 12.
			$receptor_rfc       = $RFC_Recep;    // 8.3 RFC.
			// $receptor_rfc       = 'XAXX010101000';
			$receptor_rs        = decodificar_utf8($reg->razonSocial); // 8.4 Nombre o razón social
			$cliente 		  	= $reg->nombreCliente; // 8.5 Nombre o razón social
			$regimenFiscalReceptor  = $reg->regimenFiscal;
			$domicilioFiscalReceptor = $reg->cp;
			
			$direccion_recep    = $reg->calle.' No. '.$reg->num_ext.''.$reg->num_int.', '.$reg->colonia.', '.$reg->cp.' '.$reg->poblacion.', '.$reg->edoPais; // 8.5 Dirección para colocar en el PDF
		}
		if (!$datosPagoEncontrados) {
			echo "No se encontraron datos fiscales para timbrar el pago";
			break;
		}
		$rspta ? $respuesta : $respuesta = false;

		 /// 4. DATOS GENERALES DE LA FACTURA //////////////////////////////////////////////
		//Estos datos no se modifican pues son necesarios para los requesitos del SAT al efectuar el pago
		$moneda            = "XXX";	// 4.12 Moneda
		$fact_tipcompr     = "P";	// 4.4 Tipo de comprobante
		$tasa_iva          = 16;	// 4.5 Tasa del impuesto IVA
		$subTotal          = 0;		// 4.6 Subtotal (suma de los importes antes de descuentos e impuestos)
		$IVA               = number_format(0,2,'.','');	// 4.7 IVA (suma de los impuestos)
		$total             = 0;		// 4.8 Total (Subtotal - Descuentos + Impuestos)
		$mensaje		    = "";
		$ImporteTotalIVA	= 0;

		/// 5. VARIABLES QUE CONTIENEN EL CONCEPTO DE VENTA ////////////////////////////
		$ConcepVenta_ClaveProdServ  = '84111506'; // 5.1 El Valor debe ser 84111506 en la estructura XML de recepción de pagos
		$ConcepVenta_Cantidad       = '1';        // 5.2 El Valor debe ser 1 en la estructura XML de recepción de pagos
		$ConcepVenta_ClaveUnidad    = 'ACT';      // 5.3 El Valor debe ser ACT en la estructura XML de recepción de pagos
		$ConcepVenta_Descripcion    = 'Pago';     // 5.4 El Valor debe ser Pago en la estructura XML de recepción de pagos
		$ConcepVenta_ValorUnitario  = '0';        // 5.5 El Valor unitario debe ser 0 en la estructura XML de recepción de pagos
		$ConcepVenta_Importe        = '0';        // 5.6 El Valor unitario debe ser 0 en la estructura XML de recepción de pagos
		$MontoTotalPagos 			= number_format(0,2,'.','');
		$TotalTrasladosBaseIVA16    = number_format(0,2,'.','');
		$MontoTotalIVA              = number_format(0,2,'.','');

		$rspta2=$pagos->timbra_detallesPago($complementoID);
		while ($reg2 = $rspta2->fetch_object()){
			/// 6. ARRAYS QUE CONTIENEN LAS RECEPCIONES DE PAGOS ///////////////////////////
			$ArrayDocRel_IdDocumento[] 			= ($reg2->folioFiscalFac)? $reg2->folioFiscalFac : $reg2->folioFiscal; // 6.1 El UUID relacionado al presente pago
			$ArrayDocRel_Serie[] 				= $reg2->serie;                 // 6.2 Número de serie de la factura relacionada
			$ArrayDocRel_Folio[] 				= $reg2->facturaID;   			// 6.3 Número de folio de la factura relacionada
			$ArrayDocRel_MonedaDR[] 			= $reg2->moneda;                // 6.4 Moneda en la que se efectúa el pago, será la misma que la de la factura
			$ArrayDocRel_TipoCambioP[] 			= $reg2->tipoCambio;            // 6.5 Tipo de cambio de la factura
			$monedaPago         				= $reg2->moneda;
			$regimenFiscalRec         			= $reg2->regimenFiscal;
			$TipoCambioP						= $reg2->tipoCambio;
			$ArrayDocRel_MetodoDePagoDR[] 		= $reg2->metodoPago;            // 6.6 Método de pago de la factura
			$ArrayDocRel_NumParcialidad[] 		= $reg2->parcialidad;           // 6.7 Número de pago actual
			$ArrayDocRel_ImpSaldoAnt[] 			= ($regimenFiscalRec=='616')? round($reg2->saldoAnterior*$TipoCambioP,2) : ($reg2->saldoAnterior*$TipoCambioP)*1.16;         // 6.8 Saldo anterior al pago
			$estePago							= ($regimenFiscalRec=='616')? round(($reg2->estePago*$TipoCambioP)*1.16, 2) : round(($reg2->estePago*$TipoCambioP), 2);
			$saldoRestante						= round($reg2->saldoRestante*$TipoCambioP, 2);
			$ArrayDocRel_ImpPagado[] 			= ($estePago);              // 6.9 Importe pagado que ampara ésta factura
			$ArrayDocRel_ImpMonto[]				= ($regimenFiscalRec!='616')? ($estePago)*1.16 : $estePago;
			$ArrayDocRel_ImpSaldoInsoluto[]		= ($regimenFiscalRec!='616')? ($saldoRestante)*1.16 : $saldoRestante;
			$ArrayDocRel_Fecha[] 				= $reg2->fechaFactura;			// 6.11 fecha de factura
			$ArrayDocRel_IvaImpPagado[]			= ($regimenFiscalRec!='616')? round(($estePago)*0.16, 2) : round(($estePago), 2);
			$MontoTotalPagos					+= ($regimenFiscalRec!='616')? ($estePago)*1.16 : ($estePago);
			$TotalTrasladosBaseIVA16			+= ($estePago);
			$MontoTotalIVA						+= ($regimenFiscalRec!='616')? round(($estePago)*0.16, 2) : round(($estePago), 2);
		}
		$rspta2 ? $respuesta : $respuesta = false;

		require_once "../facturar/CFDI_recepcionDePagos.php";
		if($respuestaServer==true){
			$NomArchXML=$_POST['NomArchXML'];
			$NomArchPDF=$_POST['NomArchPDF'];
			$UUID=$_POST['UUID'];
			require_once "../models/Complemento.php";
			$guarda_pagos=new Complemento();
			$rspta3=$guarda_pagos->guardaArchivoPago($complementoID,$NomArchPDF,$NomArchXML,$UUID);
			$rspta3 ? $respuesta : $respuesta = false;
		} else {
			$respuesta = false;
		}
		echo $respuesta ? "El pago ha sido timbrado" : "No se pudo timbrar el pago";
	break;

	case 'mostrar':
		$rspta=$pagos->mostrar($complementoID);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);
	break;

	case 'obtener_complementos_cliente':
		$rspta = $pagos->obtener_complementos_cliente($cliente_id);
		while ($reg = $rspta->fetch_object()){
			$total = number_format(floatval($reg->subtotal),2,'.',',');
			echo '<option data-total="'.$total.'" data-foliofiscal="'.$reg->folioFiscal.'" data-folio="'.$reg->complementoID.' '.$reg->serie.'" value="'.$reg->folioFiscal.'" data-subtext="'.$reg->razonSocial.'">'.$reg->complementoID.'-'.$reg->serie.' | $'.$total.' | '.$reg->nombreCliente.' </option>';
		}
	break;

	case 'cancelar':
		// 4. DATOS GENERALES DE LA FACTURA //////////////////////////////////////////////
		$respuesta = false;
		$rspta=$pagos->cancela_pago($complementoID);
		while ($reg = $rspta->fetch_object()){
			$fact_folio			= $reg->complementoID;
			$NoFac				= $complementoID.' P';
			$UIIDcancelar		= $reg->folioFiscal;
			$PDFcancelar 		= $reg->pagoPDF;
			$XMLcancelar 		= $reg->pagoXML;
			$cliente			= $reg->nombreCliente;
			$pago				= 1;
		}
		require_once "../facturar/CFDI_cancelarFactura.php";
		if($respuestaServer==true){
			$rutaXML=$_POST['rutaXML'];
			$rutaPDF=$_POST['rutaPDF'];
			$guarda_cancelar=new Complemento();
			$rspta2=$guarda_cancelar->guardaPagoCancelado($complementoID,$rutaPDF,$rutaXML,$motivo,$complementoIDRelacionado);
			$respuesta = $rspta2 ? true : false;
		}
		echo $respuestaServer ? "Pago Cancelado" : "El pago no se pudo cancelar";
	break;

	case 'mostrarEmails':
		$rspta=$pagos->mostrarEmails($complementoID);
		while ($reg = $rspta->fetch_object()){
			if (empty($reg->nombrePDF)){
				echo "<strong>Se debe generar la factura antes de enviarla por email.</strong>";
			} else {
					if (empty($reg->email)){
					echo "No hay emails registrados para el cliente de esta factura.<br>";
				} else {
					echo '<div class="checkbox"><label><input type="checkbox" name="emails[]" value="'.$reg->email.'"> '.$reg->email.'</label></div>';

				}
			}
		}
		echo '<div class="checkbox"><label> <input type="checkbox" id="emailextra" onchange="activarEmailExtra()"> <input type="text" class="readonly-input" id="emailextrainput" name="emails[]" disabled></label></div>';
	break;

	case 'mostrarRuta':
		$rspta=$pagos->mostrarEmails($complementoID);
		while ($reg = $rspta->fetch_object()){
			echo $reg->nombreXML.','.$reg->nombrePDF.','.$reg->Fact_NoFact.','.$reg->complementoID.','.$reg->razonSocial;
		}
	break;

	case 'enviaEmail':
		require('../public/vendor/phpMail/phpmailer/phpmailer/src/PHPMailer.php');
		require('../public/vendor/phpMail/phpmailer/phpmailer/src/SMTP.php');
		require('../public/vendor/phpMail/phpmailer/phpmailer/src/Exception.php');
		$emails=$_POST["emails"]; 
		$mail = new PHPMailer(true);
		try {
			require_once('../credenciales.php');
			$mail->FromName = mb_convert_encoding("Facturación Leap Works", 'ISO-8859-1', 'UTF-8');
			$mail->Subject = "Complemento de Pago - ".$Fact_NoFact." - ".mb_convert_encoding($cliente2, 'ISO-8859-1', 'UTF-8');
			$mail->AddReplyTo ("facturacion@leap.works",mb_convert_encoding("Leap Works", 'ISO-8859-1', 'UTF-8'));
		if (isset($emails)){
			foreach ($emails as $email){
				echo $mail->AddAddress($email);
			}
		}

			$body = "<font size='4' face='Arial'><p><strong>Estimado Cliente</strong><br>";
			$body .= "<p> == FAVOR DE VERIFICAR QUE TUS DATOS FISCALES SEAN CORRECTOS ==<p/>";
			$body .= mb_convert_encoding("<p>Te estamos enviando por este medio la factura electrónica No. <span style='color:blue;'>$Fact_NoFact </span>", 'ISO-8859-1', 'UTF-8');
			$body .= mb_convert_encoding("para su trámite de pago, al mismo tiempo nos reiteramos a tus ", 'ISO-8859-1', 'UTF-8');
			$body .= mb_convert_encoding("órdenes para cualquier aclaración o duda al respecto.</p>", 'ISO-8859-1', 'UTF-8');

			$body .= mb_convert_encoding("Para una rápida identificación de tu pago, no olvides realizar tu depósito o transferencia anexando el folio de la factura que estas pagando.", 'ISO-8859-1', 'UTF-8');

			$body .= "<font size='3' face='Arial'> <p>Agradecemos tu preferencia<br />";
			$body .= mb_convert_encoding("<b>Favor de confirmar de recibido</b></p><br />", 'ISO-8859-1', 'UTF-8');
			$body .= "<p>Atentamente</p>";
			$body .= mb_convert_encoding("<p><b>Leap Works</b><br />", 'ISO-8859-1', 'UTF-8');
			// $body .= mb_convert_encoding("Av Moctezuma 5709, Moctezuma Pte.,<br />");
			// $body .= mb_convert_encoding("Moctezuma Poniente, 45059 Zapopan, Jal<br />");
			$body .= "Tel. (33) 2906 0057, WhatsApp (33) 0000 0000<br />";
			$body .= "facturacion@leap.works</p></font>";
			$mail->Body = $body;
			$mail->AddAttachment($nombrePDF);
			$mail->AddAttachment($nombreXML);
			$mail->Send();
			echo "Se ha enviado con éxito el PDF y XML del Complemento por email";
		} catch (Exception $e) {
			echo $e->errorMessage();
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	break;

	case 'marcarEmailEnviado':
		$rspta=$pagos->marcarEmailEnviado($complementoID);
	break;

	case 'listarDetalle':
		//Recibimos el número de la factura
		$id=$_GET['comp'];
		$total=0;
		$rspta = $pagos->listarDetalle($id);
		echo '<thead style="background-color:#A9D0F5">
			<th></th>
			<th>Factura</th>
			<th>Fecha</th>
			<th>Cliente</th>
			<th>Parcialidad</th>
			<th>Saldo Anterior</th>
			<th>Este Pago</th>
			<th>Saldo Restante</th>
		</thead>';
		while ($reg = $rspta->fetch_object()){   
			echo '<tr class="filas" id="fila'.$reg->id.'">

				<td style="text-align:center;"><button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle('.$reg->id.')"><i class="fa fa-close"></i></button><input type="hidden" name="contador[]" value="'.$reg->id.'"></td>

				<td><input type="hidden" name="folioFiscalFac[]" id="folioFiscalFac'.$reg->id.'" value="'.$reg->folioFiscalFac.'"> <input type="hidden" name="factura[]" id="factura'.$reg->id.'" value="'.$reg->factura_id.'">'.$reg->factura_id.'-A</td>

				<td><input type="hidden" name="fechaFactura[]" id="fechaFactura'.$reg->id.'" value="'.$reg->fecha.'">'.$reg->fecha.'</td>

				<td>'.$reg->nombreCliente.'</td>

				<td style="width:100px;"><input class="form-control" type="number" maxlength="2" name="parcialidad[]" id="parcialidad'.$reg->id.'" value="'.$reg->parcialidad.'" style="width:100px;"></td>

				<td style="text-align:right;">$ <span id="saldoAnterior'.$reg->id.'">'.number_format($reg->saldoAnterior,2,'.',',').'</span> <input type="hidden" name="saldoAnterior[]" id="saldoAnteriorInput'.$reg->id.'" value="'.$reg->saldoAnterior.'"></td>

				<td style="width:220px;"><div class="form-group"><div class="input-group date" id="fechaPago" data-target-input="nearest"><div class="input-group-text">$</div><input type="number" class="form-control" name="estePago[]" id="estePago'.$reg->id.'" value="'.$reg->estePago.'" onkeyup="modificarSubtotales()"/></div></div> <input type="hidden" name="estePagoOriginal[]" id="estePagoOriginal'.$reg->id.'" value="'.$reg->estePago.'"></td>

				<td style="text-align:right;">$ <span id="saldoRestante2'.$reg->id.'">'.number_format($reg->saldoRestante,2,'.',',').'</span> <input type="hidden" name="saldoRestante[]" id="saldoRestante'.$reg->id.'" value="'.$reg->saldoRestante.'"></td>
			</tr><script>modificarSubtotales()</script>';
			$total+=$reg->estePago;
		}
		echo '<tfoot>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th style="font-weight:bold;"><span class="float-end">Este Pago:</span></th>
			<th><strong class="float-end">$<span id="totalPago">'.number_format($total,2,'.',',').'</span></strong></th>
			<th></th>
		</tfoot>';
	break;

	case 'listar_facturas':
		$rspta=$pagos->listar_facturas();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$factura=$reg->facturaID.'-'.$reg->serie;
			$total = ($reg->usoCfdi!='S01')? number_format((floatval($reg->subtotal)-floatval($reg->descuento))*1.16,2,'.',''): number_format((floatval($reg->subtotal)-floatval($reg->descuento)),2,'.','');
			$estePago = number_format($total-floatval($reg->estePago),2,'.','');
			$data[]=array(
				"0"=>$factura,
				"1"=>date("Y-m-d", strtotime($reg->fecha)),
				"2"=>$reg->nombreCliente.' <small>'.$reg->razonSocial.'</small>',
				"3"=>'$'.number_format($total,2,'.',',').'<br><small>('.$reg->moneda.')</small>',
				"4"=>$reg->parcialidad,
				"5"=>'$'.number_format($estePago,2,'.',','),
				"6"=>($reg->status=='Pagado')? '<span class="badge bg-success">Pagada</span>' : (($reg->status=='Parcial')?'<span class="badge bg-yellow">Parcial</span>':'<span class="badge bg-black">Sin Pago</span>'),
				"7"=>'<button id="factura'.$reg->facturaID.'-A" class="btn btn-warning btn-sm" onclick="agregarDetalle(\''.$reg->facturaID.'\',\''.$reg->fecha.'\',\''.$reg->saldoAnterior.'\',\''.$total.'\',\''.$estePago.'\',\''.$reg->folioFiscal.'\',\''.$reg->cliente_id.'\',\''.str_replace('"', "", $reg->nombreCliente).'\',\''.$reg->parcialidad.'\',\''.$reg->moneda.'\')"><span class="fa fa-plus"></span></button>',
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'listar':
		$rspta=$pagos->listar();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			if ($reg->emailEnviado=='1') {
				$botonemail='<a data-toggle="modal" href="#enviaPago"><button title="Enviar complemento al cliente" class="btn btn-success btn-sm" onclick="mostrarEmails('.$reg->complementoID.')"><i class="fa fa-envelope" aria-hidden="true"></i></button></a> '; 				
			} else {
				$botonemail='<a data-toggle="modal" href="#enviaPago"><button title="Enviar complemento al cliente" class="btn btn-primary btn-sm" onclick="mostrarEmails('.$reg->complementoID.')"><i class="fa fa-envelope" aria-hidden="true"></i></button></a> ';
			}
			$icono = ($reg->statusPago=='En Espera')? 'fa-solid fa-pencil' : 'fa-solid fa-eye';
			$botonMostrar = '<button title="Ver Pago" class="btn btn-warning btn-sm" onclick="mostrar('.$reg->complementoID.')"><i class="'.$icono.'"></i></button>';
			$botonTimbrar = (empty($reg->pagoPDF))? '<button class="btn btn-primary btn-sm" onclick="timbra('.$reg->complementoID.')"><i class="fa-solid fa-gear"></i></button>' : '<button class="btn btn-muted btn-sm" style="color:DarkGray;" href="#"><i class="fa-solid fa-gear"></i></button>';
			$botonCancelar = (empty($reg->pagoPDF) || $reg->statusPago=='Cancelado')? '<button class="btn btn-muted btn-sm disabled"><i class="fa-solid fa-xmark"></i></button>' : '<button class="btn btn-danger btn-sm" onclick="modalCancelaComplemento('.$reg->complementoID.',\''.$reg->cliente_id.'\')"><i class="fa-solid fa-xmark"></i></button>';
			$xml = (empty($reg->folioFiscal) && empty($reg->pagoXML))? ' <i class="fas fa-file-code text-muted" title="Descargar XML"></i>' : ((isset($reg->folioFiscal) && empty($reg->pagoXMLCancelado))? ' <a href="'.$reg->pagoXML.'" target="_blank"><i class="fas fa-file-code text-success" title="Descargar XML"></i></a>' : ' <a href="'.$reg->pagoXMLCancelado.'" target="_blank"><i class="fas fa-file-code text-success" title="Descargar XML"></i></a>');
			$pdf = (empty($reg->folioFiscal) && empty($reg->pagoPDF))? ' <i class="fas fa-file-pdf text-muted" title="Descargar PDF"></i>' : ((isset($reg->folioFiscal) && empty($reg->pagoPDFCancelado))? ' <a href="'.$reg->pagoPDF.'" target="_blank"><i class="fas fa-file-pdf text-danger" title="Descargar PDF"></i></a>' : ' <a href="'.$reg->pagoPDFCancelado.'" target="_blank"><i class="fas fa-file-pdf text-danger" title="Descargar PDF"></i></a>');
			$data[]=array(
				"0"=>$reg->complementoID,
				"1"=>$reg->facturasRelacionadas,
				"2"=>date("Y-m-d", strtotime($reg->fechaPago)),
				"3"=>'$ '.number_format($reg->total,2,'.',','),
				"4"=>$pdf.' '.$xml,
				"5"=>($reg->statusPago=='En Espera')?'<span class="badge bg-black">En Espera</span>':(($reg->statusPago=='Timbrada')?'<span class="badge bg-success">Timbrada</span>':'<span class="badge bg-danger">Cancelada</span>'),
				"6"=>$botonMostrar.' '.$botonTimbrar.' '.$botonCancelar,
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;
}
?>