<?php
use PHPMailer\PHPMailer\PHPMailer;
require '../public/vendor/phpMail/autoload.php';

if (strlen(session_id()) < 1) 
	session_start();

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

require_once "../models/Cotizacion.php";
$cotizaciones=new Cotizacion();

$cotizacionID=isset($_POST["cotizacionID"])? limpiarCadena($_POST["cotizacionID"]):"";
$fisica_moral=isset($_POST["fisica_moral"])? limpiarCadena($_POST["fisica_moral"]):"";
$cliente_id=isset($_POST["cliente_id"])? limpiarCadena($_POST["cliente_id"]):"";
$cliente=isset($_POST["cliente"])? limpiarCadena($_POST["cliente"]):"";
$contacto=isset($_POST["contacto"])? limpiarCadena($_POST["contacto"]):"";
$email_contacto=isset($_POST["email_contacto"])? limpiarCadena($_POST["email_contacto"]):"";
$comentarios=isset($_POST["comentarios"])? limpiarCadena($_POST["comentarios"]):"";
$observaciones=isset($_POST["observaciones"])? limpiarCadena($_POST["observaciones"]):"";
$usuarioID=$_SESSION["usuarioID"];

//SECCIÓN DE VENTAS
$ventaID=isset($_POST["ventaID"])? limpiarCadena($_POST["ventaID"]):"";
$fecha=isset($_POST["fecha"])? limpiarCadena($_POST["fecha"]):"";
$usuarioID_ventas=isset($_POST["usuarioID_ventas"])? limpiarCadena($_POST["usuarioID_ventas"]):"";
$fuente=isset($_POST["fuente"])? limpiarCadena($_POST["fuente"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$apellido=isset($_POST["apellido"])? limpiarCadena($_POST["apellido"]):"";
$celular=isset($_POST["celular"])? limpiarCadena($_POST["celular"]):"";
$telefono=isset($_POST["telefono"])? limpiarCadena($_POST["telefono"]):"";
$email_ventas=isset($_POST["email_ventas"])? limpiarCadena($_POST["email_ventas"]):"";
$prospecto=isset($_POST["prospecto"])? limpiarCadena($_POST["prospecto"]):"";
$decision=isset($_POST["decision"])? limpiarCadena($_POST["decision"]):"";
$tipo_clase=isset($_POST["tipo_clase"])? limpiarCadena($_POST["tipo_clase"]):"";
$modalidad=isset($_POST["modalidad"])? limpiarCadena($_POST["modalidad"]):"";
$fecha_interes=isset($_POST["fecha_interes"])? limpiarCadena($_POST["fecha_interes"]):"";
$dia_sugerencia=isset($_POST["dia_sugerencia"])? limpiarCadena($_POST["dia_sugerencia"]):"";
$horario_sugerencia=isset($_POST["horario_sugerencia"])? limpiarCadena($_POST["horario_sugerencia"]):"";
$dia_sugerencia2=isset($_POST["dia_sugerencia2"])? limpiarCadena($_POST["dia_sugerencia2"]):"";
$horario_sugerencia2=isset($_POST["horario_sugerencia2"])? limpiarCadena($_POST["horario_sugerencia2"]):"";
$examen_aplicante=isset($_POST["examen_aplicante"])? limpiarCadena($_POST["examen_aplicante"]):"";
$resultado_examen=isset($_POST["resultado_examen"])? limpiarCadena($_POST["resultado_examen"]):"";
$comentarios_curso=isset($_POST["comentarios_curso"])? limpiarCadena($_POST["comentarios_curso"]):"";
$notas_seguimiento=isset($_POST["notas_seguimiento"])? limpiarCadena($_POST["notas_seguimiento"]):"";
$proxima_accion=isset($_POST["proxima_accion"])? limpiarCadena($_POST["proxima_accion"]):"";
$fecha_accion=isset($_POST["fecha_accion"])? limpiarCadena($_POST["fecha_accion"]):"";
$status_seguimiento=isset($_POST["status_seguimiento"])? limpiarCadena($_POST["status_seguimiento"]):"";
$cierre=isset($_POST["cierre"])? limpiarCadena($_POST["cierre"]):"";
$fecha_cierre=isset($_POST["fecha_cierre"])? limpiarCadena($_POST["fecha_cierre"]):"";
$empresa=isset($_POST["empresa"])? limpiarCadena($_POST["empresa"]):"";
$web_empresa=isset($_POST["web_empresa"])? limpiarCadena($_POST["web_empresa"]):"";
$empleados_empresa=isset($_POST["empleados_empresa"])? limpiarCadena($_POST["empleados_empresa"]):"";
$telefono_empresa=isset($_POST["telefono_empresa"])? limpiarCadena($_POST["telefono_empresa"]):"";
$notas_empresa=isset($_POST["notas_empresa"])? limpiarCadena($_POST["notas_empresa"]):"";

//Comentarios
$comentario=isset($_POST["comentario"])? limpiarCadena($_POST["comentario"]):"";

//POR HACER Status de Cotizaciones, Abierta, Aceptada, Cancelada, Pendiente, Seguimiento (Seguimiento debe de tener fecha en bootbox) Que al cumplirse el día se coloque en la ToDo List.
//POR HACER Se cambiará al agregar los cursos, habrá 2 opciones, Grupo o privado, y se podrá seleccionar el curso a posterior o no seleccionar ningún curso.
//POR HACER Agregar selección de persona Física y Persona moral al database

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($cotizacionID)){
			$rspta=$cotizaciones->insertar($fisica_moral,$cliente_id,$cliente,$contacto,$email_contacto,$comentarios,$observaciones,$usuarioID,$_POST["cantidad"],$_POST["curso_id"],$_POST["producto_id"],$_POST["personas"],$_POST["costo"]);
			echo $rspta ? "Cotización registrada" : "La Cotización no se pudo registrar";
		} else {
			$rspta=$cotizaciones->editar($cotizacionID,$fisica_moral,$cliente_id,$cliente,$contacto,$email_contacto,$comentarios,$observaciones,$usuarioID,$_POST["cantidad"],$_POST["curso_id"],$_POST["producto_id"],$_POST["personas"],$_POST["costo"]);
			echo $rspta ? "Cotización actualizada" : "La Cotización no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$cotizaciones->desactivar($cotizacionID,$usuarioID);
		echo $rspta ? "Cotización cancelar" : "La Cotización no se pudo cancelar";
	break;

	case 'activar':
		$rspta=$cotizaciones->activar($cotizacionID,$usuarioID);
		echo $rspta ? "Cotización activada" : "La Cotización no se pudo activar";
	break;

	case 'mostrar':
		$rspta=$cotizaciones->mostrar($cotizacionID);
		echo json_encode($rspta);
	break;

	case 'mostrarDetalles':
		$rspta=$cotizaciones->mostrarDetalles($cotizacionID);
		echo '<thead class="bg-info text-light">
			<th style="width:60px;"></th>
			<th style="width:140px;text-align:center;">Cantidad</th>
			<th>Producto</th>
			<th style="width:180px;text-align:center;">Curso</th>
			<th style="width:140px;text-align:center;">Personas</th>
			<th style="width:180px;text-align:center;">$Unitario</th>
			<th style="width:180px;text-align:center;">$Subtotal</th>
		</thead>';
		while ($reg = $rspta->fetch_object()){
			echo '<tr class="filas" id="fila'.$reg->id.'">
			<td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle('.$reg->id.')"><i class="fas fa-times"></i></button></td>

			//Cantidad
			<td style="width:140px;!important"><input type="number" min="1" step="1" style="text-align:right;" class="form-control" class="form-control" name="cantidad[]" id="cantidad'.$reg->id.'" placeholder="Número de Cantidad" value="'.$reg->cantidad.'" oninput="modificarTotales('.$reg->id.')" required></td>

			//Producto
			<td><input type="hidden" class="form-control" name="contador[]" id="contador'.$reg->id.'" value="'.$reg->id.'"><input type="hidden" name="producto_id[]" value="'.$reg->producto_id.'">'.$reg->nombreProducto.'</td>

			//Curso
			<td style="width:180px;!important"><select id="curso_id'.$reg->id.'" name="curso_id[]" class="form-control selectpicker" title="Selecciona el Curso" required></select></td>
			<script>
				cargaCursos('.$reg->id.',"'.$reg->curso_id.'");
			</script >


			//Personas
			<td style="width:140px;!important"><input type="number" min="0" step="1" style="text-align:right;" class="form-control" class="form-control" name="personas[]" id="personas'.$reg->id.'" placeholder="Número de Personas" value="'.$reg->personas.'" required></td>

			//Costo Unitario
			<td style="width:180px;!important;"><div class="input-group"><span class="input-group-text">$</span><input type="number" min="0" step="0.01" style="text-align:right;" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency" lang="en-US" class="form-control" name="costo[]" id="costo'.$reg->id.'" placeholder="Costo del Curso" value="'.$reg->costo.'" oninput="modificarTotales('.$reg->id.')"></div></td>

			//Subtotal
			<td style="width:140px;!important;text-align:right;"><span id="subtotal'.$reg->id.'">$0.00</span></td>

			</tr>
			<script>
				$("#tipo'.$reg->id.'").selectpicker("refresh");
				setTimeout(function(){
					$("#tipo'.$reg->id.'").val("'.$reg->tipo.'");
					$("#tipo'.$reg->id.'").selectpicker("refresh");
				}, 500);
				modificarTotales('.$reg->id.');
			</script>';
		}
		echo '<tfoot>
			<th style="width:60px;"></th>
			<th style="width:140px;text-align:center;"></th>
			<th></th>
			<th style="width:180px;text-align:center;"></th>
			<th style="width:140px;text-align:center;"></th>
			<th style="width:140px;text-align:right;">Total:</th>
			<th style="width:180px;text-align:right;"><strong id="grantotal">$0.00</strong></th>
		</tfoot>';
	break;

	case 'select_cotizacion':
		$rspta=$cotizaciones->select_cotizacion();
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->cotizacionID.'">'.$reg->nombre.'</option>';
		}
	break;

	case 'listar':
		$rspta=$cotizaciones->listar();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar cotización " onclick="mostrar('.$reg->cotizacionID.')"><i class="fas fa-fw fa-pencil-alt"></i></button> ';
			$linkMostrar = '<button class="btn btn-link" title="Mostrar cotización " onclick="mostrar('.$reg->cotizacionID.')">'.$reg->cotizacionID.'</button>';
			$botonActivar = ($reg->activo)? ' <button class="btn btn-danger btn-sm" title="Desactivar cotización " onclick="desactivar('.$reg->cotizacionID.')"><i class="fas fa-fw fa-times"></i></button> ' : ' <button class="btn btn-primary btn-sm" title="Activar cotización " onclick="activar('.$reg->cotizacionID.')"><i class="fa fa-check"></i></button> ';
			$botonEnviar = '<button class="btn btn-primary btn-sm" title="Enviar por email la cotización " onclick="mostrarEmailsCotizacion('.$reg->cotizacionID.')"><i class="fa-solid fa-envelope"></i></button> ';
			$botonImprimir = '<button class="btn btn-success btn-sm" title="Imprimir cotización " onclick="imprimir('.$reg->cotizacionID.')"><i class="fa-solid fa-print"></i></button> ';
			$data[]=array(
				"0"=>$linkMostrar,
				"1"=>date("Y-m-d", strtotime($reg->fecha)),
				"2"=>$reg->cliente,
				"3"=>$reg->cursos,
				"4"=>$reg->personas,
				"5"=>'$'.number_format($reg->costos,2,'.',','),
				"6"=>strtok($reg->vendedor," "),
				"7"=>($reg->activo)?'<span class="badge bg-success">Activado</span>':'<span class="badge bg-danger">Cancelada</span>',
				"8"=>$botonMostrar.' '.$botonEnviar.' '.$botonImprimir.' '.$botonActivar
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
		$rspta=$cotizaciones->listarProductos();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$botonAgregar = '<button class="btn btn-dark btn-sm" title="Agregar Producto" onclick="agregarProducto('.$reg->productoID.',\''.$reg->precioVenta.'\',\''.$reg->nombre.'\')"><i class="fa-solid fa-plus"></i></button>';
			$imagen = ($reg->imagen)?'<a href="../public/files/productos/'.$reg->imagen.'" data-featherlight="image"><img class="img-thumbnail" width="35" height="35" src="../public/files/productos/'.$reg->imagen.'"></a>' : '<img class="img-thumbnail" width="35" height="35" src="../public/images/placeholder.jpg">';
			$data[]=array(
				"0"=>$reg->nombre,
				"1"=>'$'.number_format($reg->precioVenta,2,'.',','),
				"2"=>$imagen,
				"3"=>$botonAgregar
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'imprimir':
		$rspta=$cotizaciones->imprimir_cotizacion($cotizacionID);
		$respuesta=false;
		while ($reg = $rspta->fetch_object()){
			$cotizacionID			= $reg->cotizacionID;
			$nombreVendedor			= $reg->nombreVendedor;
			$codeNumber				= encodedString(strtok($reg->nombreVendedor," "));
			$nombreCliente			= ($reg->nombreCliente)? strtoupper($reg->nombreCliente) : $reg->contacto;
			$nombreContacto			= ($reg->contacto && $reg->nombreCliente)? $reg->contacto : 'Cliente';
			$fecha					= fechaCompletaEspanol($reg->fecha);
			$fechaVigencia			= fechaCompletaEspanol($reg->fechaVigencia);
			$comentarios			= ($reg->comentarios!='.' || $reg->comentarios!='')? "<small id='comentarios'><b id='tituloObservaciones'>COMENTARIOS</b><br/>$reg->comentarios</small><br><br>" : "";
			$fondoCotizacion = ($reg->user_id=='10')? "../public/images/bg_cotizacion_caro.jpg" : "../public/images/bg_cotizacion.jpg";
		}
		$rspta ? $respuesta=true : $respuesta=false;
		$totalClases=0;
		if($rspta){
			$rspta2=$cotizaciones->imprimir_detalles($cotizacionID);
			while ($reg2 = $rspta2->fetch_object()){
				$Array_clases[]			= ($reg2->producto_id=='18')? $reg2->cantidad.' '.$reg2->nombreProducto.' '.$reg2->nombreCurso.' <strong>$'.number_format($reg2->costo*$reg2->cantidad,2,'.',',').'</strong> MXN</span> más IVA, menos retenciones' : (($reg2->producto_id=='2')? $reg2->cantidad.(($reg2->cantidad==1)? ' Grupo' : ' Grupos').' de horario fijo de hasta '.$reg2->personas.' colaboradores, impartido semanalmente por 3 horas en 2 sesiones, con un costo mensual de <span style="color:02506C;"><strong>$'.number_format($reg2->costo*$reg2->cantidad,2,'.',',').'</strong> MXN</span> más IVA, menos retenciones por grupo.' : $reg2->cantidad.(($reg2->cantidad==1)? ' Clase personalizada' : ' Clases personalizadas').' con horario flexible, impartida semanalmente por 3 horas en 2 sesiones o una sesión de 3 horas, según la agenda del usuario, con un costo mensual de <span style="color:02506C;"><strong>$'.number_format($reg2->costo*$reg2->cantidad,2,'.',',').'</strong> MXN</span> más IVA, menos retenciones por grupo.');
				$totalClases = $totalClases+($reg2->costo*$reg2->cantidad);
			}
			require_once "./imprimir_cotizacion_pdf.php";
		}
		$rspta2 ? $respuesta=true : $respuesta=false;
		echo $respuesta ? "La Cotización ha sido creada." : "No se pudo crear la Cotización.";
	break;

	case 'muestraHistorial':
		$cotizacionID=$_GET['hist'];
		$rspta = $cotizaciones->muestraHistorial($cotizacionID);

		$data= Array();
		while ($reg=$rspta->fetch_object()){
			$avatar = ($reg->avatar)? '<img src="../public/files/avatars/'.$reg->avatar.'" style="width:30px;height:30px;overflow:hidden;border-radius:50%;">' : '<img src="../public/images/default.jpg" class="img-rounded" style="max-width:30px;height:30px;border-radius:50%;">';
			$data[]=array(
				"0"=>$avatar,
				"1"=>date("Y-m-d H:i", strtotime($reg->fechaHistorial)),
				"2"=>$reg->registro
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'mostrarEmailsCotizacion':
		$rspta=$cotizaciones->mostrarEmailsCotizacion($cotizacionID);
		while ($reg = $rspta->fetch_object()){
			echo '<div class="checkbox mb-3"><label><input type="checkbox" name="emailsCotizacion[]" id="emailsCotizacion" value="'.$reg->email.'"> '.$reg->email.'</label></div>';
		}
		echo '<div class="checkbox mb-3"><label> <input type="checkbox" id="emailextra" onchange="activarEmailExtra()"> <input type="text" class="readonly-input" id="emailextrainput" name="emailsCotizacion[]" disabled></label></div>';
	break;

	case 'mostrarRutaCotizacion':
		$rspta=$cotizaciones->mostrarRutaCotizacion($cotizacionID);
		while ($reg = $rspta->fetch_object()){
			echo $cotizacionID.','.$reg->contacto.','.$reg->nombreCliente.','.$reg->tipoProyecto;
		}
	break;

	case 'enviaEmailCotizacion':
		$cotizacionID=1;
		if(file_exists(("../public/files/cotizaciones/Cotizacion_".$cotizacionID.".pdf"))){
		$emailsCotizacion=$_POST["emailsCotizacion"]; 
		$mail = new PHPMailer(true);
		try {
			require_once('../credenciales.php');
			$mail->FromName = mb_convert_encoding("Cotización Leap Works", 'ISO-8859-1', 'UTF-8');
			$mail->Subject = mb_convert_encoding("Cotización - ", 'ISO-8859-1', 'UTF-8').$cotizacionID." - ".mb_convert_encoding($nombreCliente, 'ISO-8859-1', 'UTF-8');
			$mail->AddEmbeddedImage("../public/images/Logo.jpg", "Logo", "Logo.jpg");

			if (isset($emailsCotizacion)){
				foreach ($emailsCotizacion as $email){
					echo $mail->AddAddress($email);
				}
			}
			if($contacto){
				$nombreContacto = $contacto;
			} else {
				$nombreContacto = 'Cliente';
			}
				$body  = "<img src=\"cid:Logo\" /><br><font size='4' face='Arial'><p><strong>Estimado Usuario</strong><br>";
				$body .= mb_convert_encoding("<font size='4' face='Arial'><p><b>Estimado ".$nombreContacto."</b><br>", 'ISO-8859-1', 'UTF-8');
				$body .= mb_convert_encoding("<p><b>Gracias por contactarnos.</b> <br> Con gusto anexamos la cotización No. <span style='color:blue;'>$cotizacionID </span> Solicitada. <br> Cualquier duda o comentario estamos a tus órdenes.<br><br>", 'ISO-8859-1', 'UTF-8');
				$body .= mb_convert_encoding("Saludos.</p></font>",'UTF-8','ISO-8859-1');

				$body .= "<font size='3' face='Arial'> <p>Agradecemos tu preferencia<br />";
				$body .= mb_convert_encoding("<b>Favor de confirmar de recibido</b></p><br />", 'ISO-8859-1', 'UTF-8');
				$body .= "<p>Atentamente</p>";
				$body .= mb_convert_encoding("<p><b>Leap Works</b><br />", 'ISO-8859-1', 'UTF-8');
				$body .= mb_convert_encoding("Atmósfera 2985,<br />", 'ISO-8859-1', 'UTF-8');
				$body .= mb_convert_encoding("44600 Guadalajara, Jal.<br />", 'ISO-8859-1', 'UTF-8');
				$body .= "Tel. (33) 3630 2808<br />";
				$body .= "noresponder@leap.works</p></font>";
				$mail->Body = $body;
				$mail->AddAttachment("../public/files/cotizaciones/Cotizacion_".$cotizacionID.".pdf");

				$mail->Send();
				echo "Se ha enviado con éxito la Cotización por email";
			} catch (Exception $e) {
				$mensajeRespuesta = ($e->getMessage()=='1')? '' : $e->getMessage();
				echo $mensajeRespuesta; //Captura cualquier otro mensaje que devuelva PHPMailer
				$rspta=$cotizaciones->emailEnviado($cotizacionID);
			}
		} else {
			echo "Es necesario crear el PDF de la cotización antes de enviarla.";
		}
	break;

	//SECCIÓN DE VENTAS
	case 'guardaryeditar_ventas':
		if (empty($ventaID)){
			$rspta=$cotizaciones->insertar_ventas($usuarioID,$fuente,$nombre,$apellido,$celular,$telefono,$email_ventas,$prospecto,$decision,$tipo_clase,$modalidad,$fecha_interes,$dia_sugerencia,$horario_sugerencia,$dia_sugerencia2,$horario_sugerencia2,$examen_aplicante,$resultado_examen,$comentarios_curso,$notas_seguimiento,$proxima_accion,$fecha_accion,$status_seguimiento,$cierre,$fecha_cierre,$empresa,$web_empresa,$empleados_empresa,$telefono_empresa,$notas_empresa);
			echo $rspta ? "Cotización registrada" : "La Cotización no se pudo registrar";
		} else {
			$rspta=$cotizaciones->editar_ventas($ventaID,$usuarioID,$fuente,$nombre,$apellido,$celular,$telefono,$email_ventas,$prospecto,$decision,$tipo_clase,$modalidad,$fecha_interes,$dia_sugerencia,$horario_sugerencia,$dia_sugerencia2,$horario_sugerencia2,$examen_aplicante,$resultado_examen,$comentarios_curso,$notas_seguimiento,$proxima_accion,$fecha_accion,$status_seguimiento,$cierre,$fecha_cierre,$empresa,$web_empresa,$empleados_empresa,$telefono_empresa,$notas_empresa);
			echo $rspta ? "Cotización actualizada" : "La Cotización no se pudo actualizar";
		}
	break;

	case 'listar_ventas':
		$rspta=$cotizaciones->listar_ventas();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar seguimiento " onclick="mostrar_ventas('.$reg->ventaID.')"><i class="fas fa-fw fa-pencil-alt"></i></button> ';
			$comentarios = (empty($reg->comentarios))? 'btn-primary' : 'btn-warning';
			$botonComentarios = '<button class="btn '.$comentarios.' btn-sm" title="Agregar Comentarios" onclick="mostrar_comentarios('.$reg->ventaID.')"><i class="fas fa-fw fa-comment"></i></button> ';
			$linkMostrar = '<button class="btn btn-link" title="Mostrar seguimiento " onclick="mostrar_ventas('.$reg->ventaID.')">'.$reg->ventaID.'</button>';
			$status_seguimiento = '<select id="status_seguimiento'.$reg->ventaID.'" data-width="180px" onchange="modificaStatusSeguimiento('.$reg->ventaID.',this)" class="form-control selectpicker" title="Status de seguimiento">
				<option value="0">Sin gestión</option>
				<option value="1">En proceso de contacto</option>
				<option value="2">Contactado</option>
				<option value="3">Propuesta presentada</option>
				<option value="4">Aceptó la propuesta</option>
				<option value="5">No aceptó la propuesta</option>
				<option value="6">Iniciará más adelante</option>
				<option value="7">Dejó de contestar (con propuesta)</option>
				<option value="8">No le interesa</option>
			</select>
			<script>
				$("#status_seguimiento'.$reg->ventaID.'").selectpicker("refresh");
				setTimeout(function(){
					$("#status_seguimiento'.$reg->ventaID.'").val("'.$reg->status_seguimiento.'");
					$("#status_seguimiento'.$reg->ventaID.'").selectpicker("refresh");
				}, 500);
			</script>';
			$txt_status_seguimiento = ($reg->status_seguimiento==0)? 'Sin gestión' : (($reg->status_seguimiento==1)? 'En proceso de contacto' : (($reg->status_seguimiento==2)? 'Contactado' : (($reg->status_seguimiento==3)? 'Propuesta presentada' : (($reg->status_seguimiento==4)? 'Aceptó la propuesta' : (($reg->status_seguimiento==5)? 'No aceptó la propuesta' : (($reg->status_seguimiento==6)? 'Iniciará más adelante' : (($reg->status_seguimiento==7)? 'Dejó de contestar (con propuesta)' : 'No le interesa')))))));

			$proxima_accion ='<select id="proxima_accion'.$reg->ventaID.'" data-width="180px" onchange="modificaProximaAccion('.$reg->ventaID.',this)" class="form-control selectpicker" title="Status de seguimiento">
				<option value="0">Llamada</option>
				<option value="1">WhatsApp</option>
				<option value="2">Correo</option>
				<option value="3">Cita</option>
				<option value="4">Otro (especificar en comentarios)</option>
			</select>
			<script>
				$("#proxima_accion'.$reg->ventaID.'").selectpicker("refresh");
				setTimeout(function(){
					$("#proxima_accion'.$reg->ventaID.'").val("'.$reg->proxima_accion.'");
					$("#proxima_accion'.$reg->ventaID.'").selectpicker("refresh");
				}, 500);
			</script>';
			$txt_proxima_accion = ($reg->proxima_accion==0)? 'Llamada' : (($reg->proxima_accion==1)? 'WhatsApp' : (($reg->proxima_accion==2)? 'Correo' : (($reg->proxima_accion==3)? 'Cita' : '<small>'.$reg->notas_seguimiento.'</small>')));
			$fecha_accion = ($reg->fecha_accion!='0000-00-00 00:00:00')? date('Y-m-d H:i',strtotime($reg->fecha_accion)) : '';
			$botonAgregarFecha = '<button class="btn btn-primary btn-sm float-end" title="Agregar o Modificar Fecha" onclick="modalFecha('.$reg->ventaID.',\''.$reg->fecha_accion.'\')"><i class="fas fa-fw fa-calendar"></i></button>';
			$fechaaccion = ($reg->fecha_accion!='0000-00-00 00:00:00')? '<span class="text-black">'.$fecha_accion.'</span> '.$botonAgregarFecha : $botonAgregarFecha;

			$data[]=array(
				"0"=>$linkMostrar,
				"1"=>date('Y-m-d',strtotime($reg->fecha)),
				"2"=>strtok($reg->vendedor," "),
				"3"=>empty($reg->empresa)? $reg->nombre.' '.$reg->apellido : $reg->empresa.'<br><small>'.$reg->nombre.' '.$reg->apellido.'</small>',
				"4"=>$reg->celular.' '.$reg->telefono.'<br><small>'.$reg->email.'</small>',
				"5"=>$proxima_accion.'<br>'.$fechaaccion,
				"6"=>$status_seguimiento,
				"7"=>($reg->cierre==0)?'<span class="badge bg-success">Activado</span>':'<span class="badge bg-primary">Cerrado</span>',
				"8"=>$botonMostrar.' '.$botonComentarios,
				"9"=>$txt_status_seguimiento,
				"10"=>$txt_proxima_accion
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'mostrar_ventas':
		$rspta=$cotizaciones->mostrar_ventas($ventaID);
		echo json_encode($rspta);
	break;

	case 'modificaStatusSeguimiento':
		$txtStatus = ($status_seguimiento==0)? 'Sin gestión' : (($status_seguimiento==1)? 'En proceso de contacto' : (($status_seguimiento==2)? 'Contactado' : (($status_seguimiento==3)? 'Propuesta presentada' : (($status_seguimiento==4)? 'Aceptó la propuesta' : (($status_seguimiento==5)? 'No aceptó la propuesta' : (($status_seguimiento==6)? 'Iniciará más adelante' : (($status_seguimiento==7)? 'Dejó de contestar (con propuesta)' : 'No le interesa')))))));
		$rspta=$cotizaciones->modificaStatusSeguimiento($ventaID,$status_seguimiento,$usuarioID,$txtStatus);
		echo $rspta ? "La acción ha sido modificada" : "No se ha modificado la acción";
	break;

	case 'modificaProximaAccion':
		$txtAccion = ($proxima_accion==0)? 'Llamada' : (($proxima_accion==1)? 'WhatsApp' : (($proxima_accion==2)? 'Correo' : (($proxima_accion==3)? 'Cita' : 'Especificado en comentarios')));
		$rspta=$cotizaciones->modificaProximaAccion($ventaID,$proxima_accion,$usuarioID,$txtAccion);
		echo $rspta ? "La acción ha sido modificada" : "No se ha modificado la acción";
	break;

	case 'guardar_fecha_accion':
		$rspta=$cotizaciones->guardar_fecha_accion($ventaID,$fecha_accion,$usuarioID);
		echo $rspta ? "Fecha registrada o modificada" : "La Fecha no se pudo registrar";
	break;

	case 'muestraHistorial_ventas':
		$ventaID=$_GET['hist'];
		$rspta = $cotizaciones->muestraHistorial_ventas($ventaID);

		$data= Array();
		while ($reg=$rspta->fetch_object()){
			$avatar = ($reg->avatar)? '<img src="../public/files/avatars/'.$reg->avatar.'" style="width:30px;height:30px;overflow:hidden;border-radius:50%;">' : '<img src="../public/images/default.jpg" class="img-rounded" style="max-width:30px;height:30px;border-radius:50%;">';
			$data[]=array(
				"0"=>$avatar,
				"1"=>date("Y-m-d H:i", strtotime($reg->fechaHistorial)),
				"2"=>$reg->registro
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	//SECCIÓN DE COMENTARIOS
	case 'guardar_comentario':
		$rspta=$cotizaciones->guardar_comentario($comentario,$usuarioID,$ventaID);
		echo $rspta ? "Comentario registrado" : "El Comentario no se pudo registrar";
	break;

	case 'listar_comentario':
		$id=$_GET['id'];
		$rspta=$cotizaciones->listar_comentario($id);
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$data[]=array(
				"0"=>$reg->comentariosID,
				"1"=>($reg->avatar)? '<img src="../public/files/avatars/'.$reg->avatar.'" class="img-thumbnail img-md img-fluid" style="max-width: 100%;height: auto;"> ' : '<img src="../public/files/avatars/default.jpg" class="img-thumbnail img-md img-fluid" style="max-width: 100%;height: auto;"> ',
				"2"=>'<b>'.$reg->nombre.'</b><br/>'.date("Y-m-d H:i", strtotime($reg->fechaComentarios)),
				"3"=>$reg->comentario.'<div style="width:250px;height:3px;"></div>'
			);
		}
		$results_comentarios = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results_comentarios);
	break;
}

function encodedString($your_string){
    $alpha_arr =  range("A", "Z");
    $your_string = strtoupper($your_string);
    $encoded = "";

    for($i=0; $i<strlen($your_string); $i++){
        $strOne = substr($your_string, $i, 1);
        if (in_array($strOne, $alpha_arr)){       
            $encoded .= array_search($strOne, $alpha_arr)+1;
        }   
    }
    return substr($encoded, 0, 6);
}
?>