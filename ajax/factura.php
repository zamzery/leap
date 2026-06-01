<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (strlen(session_id()) < 1) 
	session_start();

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

//https://www.snice.gob.mx/cs/avi/snice/nico.ligie.html

require_once "../models/Factura.php";
$facturas=new Factura();

$usuarioID=$_SESSION["usuarioID"];
$facturaID=isset($_POST["facturaID"])? limpiarCadena($_POST["facturaID"]):"";
$serie=isset($_POST["serie"])? limpiarCadena($_POST["serie"]):"";
$metodoPago=isset($_POST["metodoPago"])? limpiarCadena($_POST["metodoPago"]):"";
$claveTipoComprobante=isset($_POST["claveTipoComprobante"])? limpiarCadena($_POST["claveTipoComprobante"]):"";
$usoCfdi=isset($_POST["usoCfdi"])? limpiarCadena($_POST["usoCfdi"]):"";
$formadePago=isset($_POST["formadePago"])? limpiarCadena($_POST["formadePago"]):"";
$descuento=isset($_POST["descuento"])? limpiarCadena($_POST["descuento"]):"";
$moneda=isset($_POST["moneda"])? limpiarCadena($_POST["moneda"]):"";
$tipoCambio=isset($_POST["tipoCambio"])? limpiarCadena($_POST["tipoCambio"]):"";
$cliente_id=isset($_POST["cliente_id"])? limpiarCadena($_POST["cliente_id"]):"";
$comentarios=isset($_POST["comentarios"])? limpiarCadena($_POST["comentarios"]):"";
$facturaCfdiRelacionada=isset($_POST["facturaCfdiRelacionada"])? limpiarCadena($_POST["facturaCfdiRelacionada"]):"";
$tipoRelacion=isset($_POST["tipoRelacion"])? limpiarCadena($_POST["tipoRelacion"]):"";
$totalFacturaRelacionada=isset($_POST["totalFacturaRelacionada"])? limpiarCadena($_POST["totalFacturaRelacionada"]):"";
$credito=isset($_POST["credito"])? limpiarCadena($_POST["credito"]):"";
$fechaCompromiso=isset($_POST["fechaCompromiso"])? limpiarCadena($_POST["fechaCompromiso"]):"";

$nombrePDF=isset($_POST["nombrePDF"])? limpiarCadena($_POST["nombrePDF"]):"";
$nombreXML=isset($_POST["nombreXML"])? limpiarCadena($_POST["nombreXML"]):"";
$Fact_NoFact=isset($_POST["Fact_NoFact"])? limpiarCadena($_POST["Fact_NoFact"]):"";
$cliente2=isset($_POST["cliente2"])? limpiarCadena($_POST["cliente2"]):"";
$folioFiscal=isset($_POST["folioFiscal"])? limpiarCadena($_POST["folioFiscal"]):"";
$observaciones=isset($_POST["observaciones"])? limpiarCadena($_POST["observaciones"]):"";
$fechaPago=isset($_POST["fechaPago"])? limpiarCadena($_POST["fechaPago"]):"";
$formaPagoAut=isset($_POST["formaPagoAut"])? limpiarCadena($_POST["formaPagoAut"]):"";

//Cancela Facturas
$folioSustitucion=isset($_POST["folioSustitucion"])? limpiarCadena($_POST["folioSustitucion"]):"";
$facturaIDRelacionada=isset($_POST["facturaIDRelacionada"])? limpiarCadena($_POST["facturaIDRelacionada"]):"";
$motivo=isset($_POST["motivo"])? limpiarCadena($_POST["motivo"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($facturaID)){	
			$rspta=$facturas->insertar($serie,$metodoPago,$claveTipoComprobante,$usoCfdi,$formadePago,$descuento,$moneda,$tipoCambio,$cliente_id,$credito,$fechaCompromiso,$usuarioID,$comentarios,$facturaCfdiRelacionada,$tipoRelacion,$totalFacturaRelacionada,$_POST["producto_id"],$_POST["descripcion"],$_POST["cantidad"],$_POST["precioVenta"],$_POST["subtotal"]);
			echo $rspta ? "Factura creada" : "No se pudieron registrar todos los datos de la factura";
		} else {
			$rspta=$facturas->editar($facturaID,$serie,$metodoPago,$claveTipoComprobante,$usoCfdi,$formadePago,$descuento,$moneda,$tipoCambio,$cliente_id,$credito,$fechaCompromiso,$usuarioID,$comentarios,$facturaCfdiRelacionada,$tipoRelacion,$totalFacturaRelacionada,$_POST["producto_id"],$_POST["descripcion"],$_POST["cantidad"],$_POST["precioVenta"],$_POST["subtotal"]);
			echo $rspta ? "La Factura se ha editado" : "No se pudo editar la factura";
		}
	break;

	case 'espera':
		$rspta=$facturas->espera($facturaID);
		echo $rspta ? "Factura marcada como Espera" : "La factura no se pudo marcar como Espera";
	break;

	case 'parcial':
		$rspta=$facturas->parcial($facturaID);
		echo $rspta ? "Factura marcada como Pago Parcial" : "La factura no se pudo marcar como Pago Parcial";
	break;

	case 'pagada':
		$rspta=$facturas->pagada($facturaID,$fechaPago,$formaPagoAut);
		echo $rspta ? "Factura marcada como Pagada" : "La factura no se pudo marcar como Pagada";
	break;

	case 'cancelar':
		$rspta=$facturas->cancelar($facturaID);
		echo $rspta ? "Factura Cancelada" : "La factura no se pudo cancelar";
	break;

	case 'retimbrar':
		$rspta=$facturas->retimbrar($facturaID);
		echo $rspta ? "Factura reiniciada para timbrado" : "La factura no se pudo reiniciar";
	break;

	case 'copiaFactura':
		$rspta=$facturas->copiaFactura($facturaID,$cliente_id);
		echo $rspta ? "Factura Duplicada" : "La factura no se pudo duplicar";
	break;

	case 'select_factura':
		$rspta = $facturas->select_factura();
		while ($reg = $rspta->fetch_object()){
			echo '<option data-total="'.$reg->total.'" data-uuid="'.$reg->uuid.'" value="'.$reg->uuid.'">'.$reg->facturaID.' '.$reg->serie.' - '.$reg->nombreCliente.' - '.$reg->razonSocial.'</option>';
		}
	break;

	case 'mostrarEmailsFactura':
		$rspta=$facturas->mostrarEmailsFactura($facturaID);
		while ($reg = $rspta->fetch_object()){
			if (empty($reg->nombrePDF)){
				echo "<strong>Se debe generar la factura antes de enviarla por email.</strong>";
			} else {
					if (empty($reg->email)){
					echo "No hay emails registrados para el cliente de esta factura";
					echo '<div class="checkbox"><label> <input type="checkbox" id="emailextra" onchange="activarEmailExtra()"> <input type="text" class="readonly-input" id="emailextrainput" name="emailsFactura[]" disabled></label></div>';
				} else {
					echo '<div class="checkbox"><label><input type="checkbox" name="emailsFactura[]" value="'.$reg->email.'"> '.$reg->email.'</label></div>';
				}
			}
		}
		echo '<div class="checkbox"><label> <input type="checkbox" id="emailextra" onchange="activarEmailExtra()"> <input type="text" class="readonly-input" id="emailextrainput" name="emailsFactura[]" disabled></label></div>';
	break;

	case 'mostrarRutaFactura':
		$rspta=$facturas->mostrarEmailsFactura($facturaID);
		while ($reg = $rspta->fetch_object()){
			echo $reg->nombreXML.','.$reg->nombrePDF.','.$reg->facturaID.','.$reg->facturaID.','.$reg->nombreCliente;
		}
	break;

	case 'enviaEmailFactura':
	require('../public/vendor/phpMail/phpmailer/phpmailer/src/PHPMailer.php');
	require('../public/vendor/phpMail/phpmailer/phpmailer/src/SMTP.php');
	require('../public/vendor/phpMail/phpmailer/phpmailer/src/Exception.php');
		$emailsFactura=$_POST["emailsFactura"]; 
		$mail = new PHPMailer(true);
		try {
			require_once('../credenciales.php');
			$mail->FromName = mb_convert_encoding("Facturación Leap Works", 'ISO-8859-1', 'UTF-8');
			$mail->Subject = "Factura electronica - ".$Fact_NoFact." - ".mb_convert_encoding($cliente2, 'ISO-8859-1', 'UTF-8');
			$mail->AddReplyTo ("facturacion@leap.works",mb_convert_encoding("Leap Works", 'ISO-8859-1', 'UTF-8'));
		if (isset($emailsFactura)){
			foreach ($emailsFactura as $email){
				echo $mail->AddAddress($email);
			}
		}
			$body = "<font size='4' face='Arial'><p><strong>Estimado Cliente</strong><br>";
			$body .= "<p> == FAVOR DE VERIFICAR QUE TUS DATOS FISCALES SEAN CORRECTOS ==<p/>";
			$body .= mb_convert_encoding("<p>Te estamos enviando por este medio la factura electrónica No. <span style='color:blue;'>$Fact_NoFact </span>", 'ISO-8859-1', 'UTF-8');
			$body .= mb_convert_encoding("para su trámite de pago, al mismo tiempo nos reiteramos a tus ", 'ISO-8859-1', 'UTF-8');
			$body .= mb_convert_encoding("órdenes para cualquier aclaración o duda al respecto.</p>", 'ISO-8859-1', 'UTF-8');

			$body .= mb_convert_encoding("Para una rápida identificación de tu pago, no olvides realizar tu depósito o transferencia anexando el folio de la factura que estas pagando.", 'ISO-8859-1', 'UTF-8');

			$body .= mb_convert_encoding("Cuentas con un plaza de 3 días a partir de la fecha de recepción, para efecturar algún comentario o duda al tel: 33 1806 9873 o al correo facturacion@leap.works ", 'ISO-8859-1', 'UTF-8');

			$body .= "<font size='3' face='Arial'> <p>Agradecemos tu preferencia<br />";
			$body .= mb_convert_encoding("<b>Favor de confirmar de recibido</b></p><br />", 'ISO-8859-1', 'UTF-8');
			$body .= "<p>Atentamente</p>";
			$body .= mb_convert_encoding("<p><b>Leap Works</b><br />", 'ISO-8859-1', 'UTF-8');
			// $body .= mb_convert_encoding("Av Moctezuma 5709, Moctezuma Pte.,<br />");
			// $body .= mb_convert_encoding("Moctezuma Poniente, 45059 Zapopan, Jal<br />");
			$body .= "Tel. (33) 2906 0057";
			$body .= "facturacion@leap.works</p></font>";
			$mail->Body = $body;
			$mail->AddAttachment($nombrePDF);
			$mail->AddAttachment($nombreXML);
			$mail->Send();
			echo "Se ha enviado con éxito el PDF y XML de la Factura por email";
		} catch (Exception $e) {
			echo $e->errorMessage(); //Captura los mensajes de error de PHPMailer
		}
	break;

	case 'obtener_precio_dolar':
		// Se consulta el precio del dólar desde free.currencyconverterapi.com, es un servicio gratuito, si caduca hay que cambiar de API
		$precioDolar = file_get_contents("https://free.currconv.com/api/v7/convert?apiKey=bc00eebaf3746a531da4&q=USD_MXN&compact=y");
		// Se procesa la respuesta
		$precioDolar = preg_replace('/[^0-9\.]/', '', $precioDolar);
		$precioDolar = number_format($precioDolar,5,'.',',');
		// Se presenta la respuesta con el formato deseado. $precioDolar contiene el valor de conversion final
		echo $precioDolar;
	break;

	case 'mostrar':
		$rspta=$facturas->mostrar($facturaID);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);
	break;

	case 'marcarFacturaEnviada':
		$rspta=$facturas->marcarFacturaEnviada($facturaID);
	break;

	case 'listarDetalle':
		//Recibimos el noCotizacion
		$id=$_GET['id'];
		$rspta = $facturas->listarDetalle($id);
		$total=0;
		echo '<thead style="background-color:#A9D0F5">
			<th></th>
			<th>Producto</th>
			<th>Descripción</th>
			<th>Cantidad</th>
			<th>$Unitario</th>
			<th>$Subtotal</th>
		</thead><tbody>';
		$subtotal = 0;
		while ($reg = $rspta->fetch_object()){
			echo '<tr class="filas" id="fila'.$reg->id.'">
			//Botones
				<td style="text-align:center;"><button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle('.$reg->id.')"><i class="fa fa-close"></i></button><input type="hidden" value="'.$reg->id.'" id="contador'.$reg->id.'" name="contador_actual[]"></td>

			//Nombre Producto
				<td><input type="hidden" name="producto_id[]" id="producto_id'.$reg->id.'" value="'.$reg->producto_id.'">'.$reg->nombreProducto.' <small class="text-muted">'.$reg->nombreUnidad.'</small></td>
	
			//Descripción
				<td style="width:210px;"><textarea style="width:210px;" name="descripcion[]" id="descripcion'.$reg->id.'">'.$reg->descripcion.'</textarea></td>
	
			//Cantidad
				<td style="text-align:right;width:90px;"><input type="number" style="width:80px;text-align:right;" onchange="modificarSubtotales()" min="0" step="1" name="cantidad[]" id="cantidad'.$reg->id.'" value="'.$reg->cantidad.'"></td>
	
			//Precio Unitario
				<td style="text-align:right;"><input type="hidden" name="precioVenta[]" id="precioVenta'.$reg->id.'" value="'.$reg->precioUnitario.'" onchange="modificarSubtotales()">$ '.$reg->precioUnitario.'</td>
	
			//Subtotal
				<td style="text-align:right;">$ <span name="subtotal" id="subtotal'.$reg->id.'">'.$reg->subtotal.'</span><input type="hidden" name="subtotal[]" id="subtotal'.$reg->id.'" value="'.$reg->subtotal.'"></td>
	
				</tr><script>modificarSubtotales();</script>';
			$subtotal += $reg->subtotal;
		}
		echo '</tbody><tfoot>
			<th colspan="4"></th>
			<th style="text-align:right;width:220px;">
				Subtotal: $<span id="subtotal_fac_imp">'.number_format($subtotal,2,'.',',').'</span><br>
				<span id="iva"></span>
			</th>
			<th style="text-align:right;width:220px;">
				<span id="isrRetencion"></span>
				<span id="ivaRetencion"></span>
				<span style="font-weight:bold;">Total: $<span id="total_fac_imp">'.number_format($subtotal*1.16,2,'.',',').'</span></span>
			</th>
		</tfoot>';
	break;

	case 'listar':
		$rspta=$facturas->listar();

		$data= Array();
		while ($reg=$rspta->fetch_object()){
			if($reg->regimenFiscal=='601'){
				$subtotal = $reg->total/1.16;
				$isr2 = $subtotal * 0.0125;
				$iva2 = $subtotal * 0.106700;
				$isr = number_format($isr2,2,'.',',');
				$iva = number_format($iva2,2,'.',',');
				$total = ( $reg->total ) - ( $isr2 + $iva2 );
			} else {
				$total = ( $reg->total );
			}
			$pagado = ($reg->status=='Pagada')? $total : $reg->pagado;
			$saldo = (($total-$pagado)=='0')? '<span style="color: #239b56;font-weight:bold;">$0.00</span>' : '$'.number_format($total-$pagado,2,'.',',');
			$metodoPago = ($reg->metodoPago=='PUE')? '<small style="font-weight:bold;color:#4287f5;font-size:0.7em;">PUE</small>' : '<small style="font-weight:bold;color:purple;font-size:0.7em;">PPD</small>';
			$icono = (empty($reg->nombrePDF))? '<i class="fa-solid fa-pencil fa-fw"></i>':'<i class="fa-solid fa-eye fa-fw"></i>';
			$botonMostrarFactura = '<button title="Ver Factura" class="btn btn-warning btn-sm" onclick="mostrar('.$reg->facturaID.')">'.$icono.'</button>';
			$botonEmail=($reg->emailEnviado=='1')? '<a data-bs-toggle="modal" data-bs-target="#enviaFactura"><button title="Enviar factura al cliente" class="btn btn-success btn-sm" onclick="mostrarEmailsFactura('.$reg->facturaID.')"><i class="fa fa-envelope" aria-hidden="true"></i></button></a> ' : '<a data-bs-toggle="modal" data-bs-target="#enviaFactura"><button title="Enviar factura al cliente" class="btn btn-dark btn-sm" onclick="mostrarEmailsFactura('.$reg->facturaID.')"><i class="fa fa-envelope" aria-hidden="true"></i></button></a> ';
			$pagada = (empty($reg->nombrePDF) || $reg->status=='Pagada' || $reg->status=='Cancelado' || $reg->metodoPago=='PPD')? '<a class="dropdown-item" style="color:DarkGray;" href="#" disabled><i class="fa-solid fa-check espaciado-icn"></i> Pagada</a>' : '<a class="dropdown-item text-success" href="#" onclick="pagada('.$reg->facturaID.')"><i class="fa-solid fa-check espaciado-icn"></i> Pagada</a>';
			$copiar = (empty($reg->nombrePDF))? '<li><a class="dropdown-item" style="color:DarkGray;" href="#"><i class="fa-solid fa-copy"></i> Duplicar</a></li>' : '<li><a class="dropdown-item text-primary" href="#" onclick="modalCopiarFactura('.$reg->facturaID.','.$reg->cliente_id.')"><i class="fa-solid fa-copy"></i> Duplicar</a></li>';
			$cancelar = (empty($reg->nombrePDF) || $reg->status=='Cancelado' || date("Ym", strtotime($reg->fecha)) < date('Ym'))? '<li><a class="dropdown-item" style="color:DarkGray;" href="#"><i class="fa-solid fa-xmark espaciado-icn"></i> Cancelar</a></li>' : '<li><a class="dropdown-item text-danger" href="#" onclick="modalCancelaFactura('.$reg->facturaID.','.$reg->cliente_id.')"><i class="fa-solid fa-xmark espaciado-icn"></i> Cancelar</a></li>';
			$timbrarFactura = (empty($reg->nombrePDF))? '<li><a class="dropdown-item" href="#" onclick="timbra('.$reg->facturaID.')"><i class="fa-solid fa-gear espaciado-icn"></i> Timbrar Factura</a></li>' : '<li><a class="dropdown-item" style="color:DarkGray;" href="#"><i class="fa-solid fa-gear espaciado-icn"></i> Timbrar Factura</a></li>';
			$linkMostrar = '<button class="btn btn-link" title="Mostrar Curso" onclick="mostrar('.$reg->facturaID.')">'.$reg->facturaID.'-'.$reg->serie.' '.$metodoPago.'</button>';

			$dropdown = '<div class="btn-group">
				<button type="button" title="Acciones de Facturación" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
					<i class="fa-solid fa-screwdriver-wrench"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-end shadow-sm">
					'.$timbrarFactura.'
					'.$pagada.'
					'.$copiar.'
					<div class="dropdown-divider"></div>
					'.$cancelar.'
				</div>
			</div>';
			$timbrarFactura = (isset($reg->nombrePDF))? '<button title="Timbrar Factura" class="btn btn-info btn-sm muted"><i class="fa fa-cog"></i></button> ' : '<button title="Timbrar Factura" class="btn btn-info btn-sm" onclick="timbra('.$reg->facturaID.')"><i class="fa fa-cog"></i></button> ';
			
			$data[]=array(
				"0"=>$linkMostrar,
				"1"=>date("Y-m-d", strtotime($reg->fecha)),
				"2"=>$reg->nombreCliente,
				"3"=>'$'.number_format($total,2,'.',','),
				"4"=>'$'.number_format($pagado,2,'.',','),
				"5"=>$saldo,
				"6"=>(isset($reg->nombrePDFCancelado) AND $reg->nombrePDFCancelado!='')? '<a href="'.$reg->nombrePDFCancelado.'" target="_blank"><img class="img-thumbnail" width="30" height="30" src="../public/images/pdf-file.png"></a> <a href="'.$reg->nombreXMLCancelado.'" download target="_blank"><img class="img-thumbnail" width="30" height="30" src="../public/images/xml-file.png"></a>' : ((empty($reg->nombrePDF))? '<span style="display:none;">0</span><img class="img-thumbnail" style="filter: grayscale(100%);opacity: 0.5;" width="30" height="30" src="../public/images/pdf-file.png"> <img class="img-thumbnail" style="filter: grayscale(100%);opacity: 0.5;" width="30" height="30" src="../public/images/xml-file.png">':'<span style="display:none;">1</span><a href="'.$reg->nombrePDF.'" target="_blank"><img class="img-thumbnail" width="30" height="30" src="../public/images/pdf-file.png"></a> <a href="'.$reg->nombreXML.'" download target="_blank"><img class="img-thumbnail" width="30" height="30" src="../public/images/xml-file.png"></a>'),
				"7"=>'<button type="button" class="btn btn-warning btn-sm" onclick="mostrarPagos('.$reg->facturaID.')"><i class="fas fa-search"></i> Pagos</button>',
				"8"=>(($total-$pagado)==$total && $reg->status!='Cancelado' && $reg->status!='Facturado')? '<span class="badge bg-dark">Espera</span>' : ((($total-$pagado)==$total && $reg->status!='Cancelado')? '<span class="badge bg-primary">Timbrada</span>' : (((($total-$pagado)=='0' && $reg->status!='Cancelado')? '<span class="badge bg-success">Pagada</span>' : ((($total-$pagado)>0 && $reg->status!='Cancelado')? '<span class="badge bg-warning">Parcial</span>' : '<span class="badge bg-danger">Cancelada</span>')))),
				"9"=>$botonMostrarFactura.' '.$botonEmail.' '.$dropdown
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'listarProductos':
		$rspta=$facturas->listarProductos();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$nombreProducto=htmlspecialchars(addslashes($reg->nombreProducto), ENT_QUOTES, 'UTF-8');
			$data[]=array(
				"0"=>$reg->id,
				"1"=>$reg->nombreProducto,
				"2"=>$reg->descripcion,
				"3"=>'$'.number_format($reg->precioVenta,2,'.',','),
				"4"=>'<button type="button" class="btn btn-warning btn-sm" onclick="agregarDetalle(\''.$reg->id.'\',\''.$nombreProducto.'\',\''.$reg->precioVenta.'\')"><span class="fa fa-plus"></span></button>',
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'mostrarPagos':
		$factura=$_GET['fac'];
		$rspta=$facturas->mostrarPagos($factura);
		$data= Array();
		if($rspta){
			while ($reg=$rspta->fetch_object()){
				$data[]=array(
					"0"=>$reg->complementoID,
					"1"=>fechaEspanol($reg->fechaPago),
					"2"=>($reg->formadePago=='01')? 'Efectivo' : (($reg->formadePago=='02')? 'Cheque' : (($reg->formadePago=='03')? 'Transferencia': (($reg->formadePago=='04')? 'Tarjeta Crédito':(($reg->formadePago=='28')? 'Tarjeta Débito':(($reg->formadePago=='30')?'Anticipo':'Por Definir'))))),
					"3"=>($reg->banco)? $reg->banco : 'N/A',
					"4"=>(empty($reg->pagoPDF) OR ($reg->pagoPDF=='#'))?'<img class="img-thumbnail" style="filter: grayscale(100%);opacity: 0.5;" width="30" height="30" src="../public/images/pdf-file.png"> <img class="img-thumbnail" style="filter: grayscale(100%);opacity: 0.5;" width="30" height="30" src="../public/images/xml-file.png">':'<a href="'.$reg->pagoPDF.'" target="_blank"><img class="img-thumbnail" width="30" height="30" src="../public/images/pdf-file.png"></a> <a href="'.$reg->pagoXML.'" download target="_blank"><img class="img-thumbnail" width="30" height="30" src="../public/images/xml-file.png"></a>',
					"5"=>'$'.number_format($reg->estePago,2,'.',','),
					"6"=>($reg->statusPago=='En Espera')? '<span class="badge bg-secondary">Espera</span>' : (($reg->statusPago=='Timbrada')? '<span class="badge bg-success">Timbrada</span>' : '<span class="badge bg-danger">Cancelada</span>')
				);
			}
			$results = array(
				"sEcho"=>1, //Información para el datatables
				"iTotalRecords"=>count($data), //enviamos el total registros al datatable
				"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
				"aaData"=>$data);
			echo json_encode($results);
		} else {
			echo '<tr style="text-align:center;"><td colspan="7">No hay pagos registrados.</td></tr>';
		}
	break;

	case 'timbra':
		/*
		Facturación electrónica Versión CFDI 4.0, programado por Eduardo Martínez, cualquier duda a zamzery@gmail.com
		=============================

		NOTA:
		ESTAR AL PENDIENTE DE LAS ACTUALIZACIONES DEL SAT SI CAMBIAN LAS ESPECIFICACIONES DE LAS FACTURAS
		*/

		// 4. DATOS GENERALES DE LA FACTURA //////////////////////////////////////////////
		$rspta=$facturas->timbra($facturaID);
		$respuestaServer = false;
		$respuesta=false;
		while ($reg = $rspta->fetch_object()){
			$fact_serie 			= $reg->serie;							// Número de serie
			$fact_folio 			= $reg->facturaID;						// Número de folio
			$NoFac 					= $facturaID.' '.$fact_serie;			// Serie de la factura concatenado con el número de folio
			$fact_tipcompr 			= $reg->claveTipoComprobante; 		// Tipo de comprobante
			$fecha_fact 			= $reg->fecha;					 		// Fecha y hora de facturación
			$NumCtaPago 			= $reg->num_cuenta; 	// Número de cuenta (sólo últimos 4 dígitos, opcional)
			$formaDePago 			= $reg->formadePago; 	// Forma de pago
			$metodoDePago 			= $reg->metodoPago; 	// Clave del método de pago. Consultar catálogos de métodos de pago del SAT
			$TipoCambio 			= $reg->tipoCambio*1; 	// Tipo de cambio de la moneda
			$LugarExpedicion 		= "44460"; 				// Lugar de expedición (código postal) Prueba: 44960
			$usoCfdi		 		= $reg->usoCfdi;		// Uso del CFDI que le dará el cliente
			$moneda 				= $reg->moneda; 		// Moneda
			$RFC_Recep 		 		= $reg->rfcCliente; // 9.1 RFC del Receptor
			$cliente 				= ($RFC_Recep=='XEXX010101000' || $RFC_Recep=='XAXX010101000')? ' - '.$reg->nombreCliente : '';
			$numeroInterior			= ($reg->num_int)? ', Int. '.$reg->num_int : '';
			$direccion_recep 		= $reg->calle.' No. '.$reg->num_ext.''.$numeroInterior.', '.$reg->colonia.', '.$reg->cp.' '.$reg->poblacion.', '.$reg->edoPais;		 // 9.5 Dirección a mostrar en el PDF como referencia 
			$receptor_rs 	 		= ($RFC_Recep=='XEXX010101000' || $RFC_Recep=='XAXX010101000')? decodificar_utf8('PÚBLICO EN GENERAL') : $reg->razonSocial;		// 9.4 Nombre o razón social
			$descuento 				= floatval($reg->descuento);	// Descuento 
			$facturaCfdiRelacionada = $reg->facturaCfdiRelacionada;
			$facturaFolioRelacionada= $reg->facturaFolioRelacionada;
			$tipoRelacion			= $reg->tipoRelacion;
			$totalFacturaRelacionada = $reg->totalFacturaRelacionada;
			$preporcentaje			= $reg->descuento / $reg->subtotal * 100;
			$porcentaje 			= 100 - $preporcentaje;
			$receptor_domicilioFiscal 	= ($RFC_Recep=='XEXX010101000' || $RFC_Recep=='XAXX010101000')? '44460' : $reg->cp; //Prueba: 44960
			$receptor_regimenFiscal 	= ($RFC_Recep=='XEXX010101000' || $RFC_Recep=='XAXX010101000')? '616' : $reg->regimenFiscal;
			$comentarioAdicional	= $reg->comentarioAdicional;
			$credito				= $reg->credito;
			$fechaCompromiso		= $reg->fechaCompromiso;
			$subtotal				= $reg->subtotal;
		}
		$rspta ? $respuesta=true : $respuesta=false;
		$informacionGlobal			= '';
		$tasa_iva 					= 16; 		// Tasa del impuesto IVA.
		$subTotal 					= number_format(0,2,'.',''); // Subtotal, suma de los importes antes de descuentos e impuestos (se calculan mas adelante). 
		$IVA 						= number_format(0,2,'.',''); // IVA, suma de los impuestos (se calculan mas adelante).
		$total 						= number_format(0,2,'.',''); // Total, Subtotal - Descuentos + Impuestos (se calculan mas adelante).
		$totalImpuestosTrasladados	= number_format(0,2,'.','');
		$totalImpuestosRetenidos	= number_format(0,2,'.','');			// Total de impuestos retenidos (se calculan mas adelante)
		$impuestosRetenidosISR		= number_format(0,2,'.','');
		$impuestosRetenidosIVA		= number_format(0,2,'.','');

		// 5. ARRAYS QUE CONTIENEN LOS ARTICULOS QUE FORMAN LA VENTA /////////////////
		if($RFC_Recep=='XEXX010101000'){
			$rspta2=$facturas->timbra_detallesFacturaExtranjero($facturaID);
		} else {
			$rspta2=$facturas->timbra_detallesFactura($facturaID);
		}
		while ($reg2 = $rspta2->fetch_object()){
			$Array_ClaveProdServ[] 		= isset($reg2->clave)? $reg2->clave : '25172100'; //Clave del producto o servicio
			$Array_NoIdentificacion 	= ''; //Clave asignada al artículo o servicio (opcional)
			$Array_Cantidad[] 			= $reg2->cantidad;
			$Array_ClaveUnidad[] 		= isset($reg2->unidad)? $reg2->unidad : 'H87';
			$Array_Unidad[] 			= isset($reg2->nombreMedida)? $reg2->nombreMedida : 'Pieza';
			$Array_Descripcion[] 		= ($reg2->descripcion)? mb_convert_encoding($reg2->descripcion, 'ISO-8859-1', 'UTF-8') : mb_convert_encoding($reg2->nombreProducto, 'ISO-8859-1', 'UTF-8');
			$Array_ValorUnitario[] 		= number_format($reg2->precioVenta,2,'.','');
			$Array_Importe[] 			= number_format($reg2->precioVenta*$reg2->cantidad,2,'.','');
			//$Array_Descuento = $reg->descuento; 	 //Descuento aplicado al artículo o servicio. Actualmente sin incluirse
			
		// 6. ARRAYS QUE CONTIENEN LOS IMPUESTOS TRASLADADOS Y RETENIDOS POR CONCEPTO ///////////
			// Trasladados
			$ArrayTraslado_Base[]			= number_format($reg2->precioVenta*$reg2->cantidad,2,'.','');			//Atributo base para el cálculo del impuesto. No se permiten valores negativos
			$ArrayTraslado_Impuesto[]		= ($RFC_Recep=='XEXX010101000')? '002' : $reg2->claveIva;			//Atributo requerido para señalar la clave de impuesto trasladado aplicable
			$ArrayTraslado_TipoFactor[]		= $reg2->factor;				//Atributo requerido para señalar la clave de factor que se aplica a la base del impuesto
			$ArrayTraslado_TasaOCuota[] 	= ($RFC_Recep=='XEXX010101000')? '0.000000' : $reg2->tasaCuota;			//Atributo condicional para señalar el valor de la tasa o cuota del impuesto que se traslada
			$ArrayTraslado_Importe[] 		= ($RFC_Recep=='XEXX010101000')? '0.00' : number_format($reg2->precioVenta*0.16,2,'.','');		//Atributo condicional para señalar el importe del impuesto trasladado que aplica al concepto. No se permiten valores negativos.

		}
		$rspta2 ? $respuesta=true : $respuesta=false;

		// 9. DATOS GENERALES DEL RECEPTOR (CLIENTE) //////////////////////////////////
		// 9.1 Al RFC de personas morales se le antecede un espacio en blanco para que su longitud sea de 13 caracteres ya que estos son de longitud 12.
		// if (strlen($RFC_Recep)==12){ $RFC_Recep = " ".$RFC_Recep; } else { $RFC_Recep = $RFC_Recep; } 
		$receptor_rfc = $RFC_Recep;

		require_once "../facturar/CFDI_generaXML.php";
		$respuesta = $respuestaServer ? true : false;
		if($respuestaServer){
			$NomArchPDF=$_POST['NomArchPDF'];
			$UUID=$_POST['UUID'];

			$rspta3=$facturas->guardaFacturaPDF($facturaID,$NomArchPDF,$UUID);
			$rspta3 ? $respuesta=true : $respuesta=false;
		}
		echo $respuesta ? "La Factura creada" : "No se pudo crear la factura";
	break;

	case 'obtener_facturas_cliente':
		$rspta = $facturas->obtener_facturas_cliente($cliente_id);
		while ($reg = $rspta->fetch_object()){
			$total = number_format(floatval($reg->subtotal*1.16),2,'.',',');
			echo '<option data-total="'.$total.'" data-foliofiscal="'.$reg->folioFiscal.'" data-folio="'.$reg->facturaID.' '.$reg->serie.'" value="'.$reg->folioFiscal.'" data-subtext="'.$reg->razonSocial.'">'.$reg->facturaID.'-'.$reg->serie.' | $'.$total.' | '.$reg->nombreCliente.' </option>';
		}
	break;

	case 'cancela_factura':
		// 4. DATOS GENERALES DE LA FACTURA //////////////////////////////////////////////
		$respuestaServer = false;
		$respuesta = false;
		$rspta=$facturas->cancela_factura($facturaID);
		while ($reg = $rspta->fetch_object()){
			$fact_folio			= $reg->facturaID;
			$fact_serie			= $reg->serie;
			$NoFac				= $facturaID.' '.$fact_serie;
			$UIIDcancelar		= $reg->folioFiscal;
			$XMLcancelar 		= $reg->nombreXML;
			$PDFcancelar 		= $reg->nombrePDF;
			$cliente			= $reg->nombreCliente;
			$pago				= 0;
		}
		require_once "../facturar/CFDI_cancelarFactura.php";
		$respuestaServer = $rspta ? true : false;
		if($respuestaServer==true){
			$rutaXML=$_POST['rutaXML'];
			$rutaPDF=$_POST['rutaPDF'];
			$rspta2=$facturas->guardaFacturaCancelada($facturaID,$rutaPDF,$rutaXML,$motivo,$facturaIDRelacionada);
			$respuesta = $rspta2 ? true : false;
		}
		echo $respuesta ? "La Factura cancelada" : "No se pudo cancelar la factura";
	break;
}
?>