<?php
if (strlen(session_id()) < 1) 
	session_start();

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

require_once "../config/PasswordHash.php";
require_once "../models/Cliente.php";
$clientes=new Cliente();

$usuarioID=(isset($_SESSION["usuarioID"]))? $_SESSION["usuarioID"]:"";

//INFORMACIÓN DE LOGIN WP
$display_name=isset($_POST["display_name"])? limpiarCadena($_POST["display_name"]):"";
// $user_login=isset($_POST["user_login"])? limpiarCadena($_POST["user_login"]):"";
// $user_nicename=isset($_POST["user_nicename"])? limpiarCadena($_POST["user_nicename"]):"";
// $user_email=isset($_POST["user_email"])? limpiarCadena($_POST["user_email"]):"";

$ok = 0;
$t_hasher = new PasswordHash(8, FALSE);
$contrasenna = (isset($_POST["contrasenna"])) ? $t_hasher->HashPassword($_POST["contrasenna"]) : "";

//VALORES FISCALES
$clienteID=isset($_POST["clienteID"])? limpiarCadena($_POST["clienteID"]):"";
$telefono=isset($_POST["telefono"])? limpiarCadena($_POST["telefono"]):"";
$calle=isset($_POST["calle"])? limpiarCadena($_POST["calle"]):"";
$num_ext=isset($_POST["num_ext"])? limpiarCadena($_POST["num_ext"]):"";
$num_int=isset($_POST["num_int"])? limpiarCadena($_POST["num_int"]):"";
$colonia=isset($_POST["colonia"])? limpiarCadena($_POST["colonia"]):"";
$poblacion=isset($_POST["poblacion"])? limpiarCadena($_POST["poblacion"]):"";
$edoPais=isset($_POST["edoPais"])? limpiarCadena($_POST["edoPais"]):"";
$cp=isset($_POST["cp"])? limpiarCadena($_POST["cp"]):"";
$razonSocial=isset($_POST["razonSocial"])? limpiarCadena($_POST["razonSocial"]):"";
$rfcCliente=isset($_POST["rfcCliente"])? limpiarCadena($_POST["rfcCliente"]):"";
$regimenFiscal=isset($_POST["regimenFiscal"])? limpiarCadena($_POST["regimenFiscal"]):"";
$num_cuenta=isset($_POST["num_cuenta"])? limpiarCadena($_POST["num_cuenta"]):"";
$banco=isset($_POST["banco"])? limpiarCadena($_POST["banco"]):"";
$metodoPago=isset($_POST["metodoPago"])? limpiarCadena($_POST["metodoPago"]):"";
$formadePago=isset($_POST["formadePago"])? limpiarCadena($_POST["formadePago"]):"";
$usoCfdi=isset($_POST["usoCfdi"])? limpiarCadena($_POST["usoCfdi"]):"";
$comentarios=isset($_POST["comentarios"])? limpiarCadena($_POST["comentarios"]):"";
$moneda=isset($_POST["moneda"])? limpiarCadena($_POST["moneda"]):"";
$constancia=isset($_POST["constancia"])? limpiarCadena($_POST["constancia"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (!file_exists($_FILES['constancia']['tmp_name']) || !is_uploaded_file($_FILES['constancia']['tmp_name'])){
			$constancia=$_POST["constanciaactual"];
		} else {
			$ext = explode(".", $_FILES["constancia"]["name"]);
			if ($_FILES['constancia']['type'] == "image/jpg" || $_FILES['constancia']['type'] == "image/jpeg" || $_FILES['constancia']['type'] == "image/png" || $_FILES['constancia']['type'] == "application/pdf"){
				$constancia = round(microtime(true)) . '.' . end($ext);
				move_uploaded_file($_FILES["constancia"]["tmp_name"], "../public/files/constancia/" . $constancia);
			}
		}
		if (empty($clienteID)){
			$rspta=$clientes->insertar($display_name,$telefono,$calle,$num_ext,$num_int,$colonia,$poblacion,$edoPais,$cp,$razonSocial,$rfcCliente,$regimenFiscal,$num_cuenta,$banco,$metodoPago,$formadePago,$usoCfdi,$comentarios,$constancia,$moneda,$usuarioID);
			echo $rspta ? "Cliente registrado" : "El Cliente no se pudo registrar";
		} else {
			$rspta=$clientes->editar($clienteID,$display_name,$telefono,$calle,$num_ext,$num_int,$colonia,$poblacion,$edoPais,$cp,$razonSocial,$rfcCliente,$regimenFiscal,$num_cuenta,$banco,$metodoPago,$formadePago,$usoCfdi,$comentarios,$constancia,$moneda,$usuarioID);
			echo $rspta ? "Cliente actualizado" : "El Cliente no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$clientes->desactivar($clienteID,$usuarioID);
		echo $rspta ? "Cliente Desactivado" : "El Cliente no se puede desactivar";
	break;

	case 'activar':
		$rspta=$clientes->activar($clienteID,$usuarioID);
		echo $rspta ? "Cliente activado" : "El Cliente no se puede activar";
	break;

	case 'eliminarConstancia':
		unlink("../public/files/constancia/".$constancia);
		$rspta=$clientes->eliminarConstancia($clienteID);
		echo $rspta ? "Constancia eliminada" : "La Constancia no se pudo eliminar";
	break;

	case 'mostrar':
		$rspta=$clientes->mostrar($clienteID,$usuarioID);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);
	break;

	case 'cambia_usuario':
		$rspta=$clientes->cambia_usuario($clienteID,$nuevoUsuario,$usuarioID);
		echo $rspta ? "El Usuario ha cambiado" : "El usuario no se ha podido cambiar";
	break;

	case 'select_usuario_cuenta':
		require_once "../models/User.php";
		$usuarios=new User();
		$rspta=$usuarios->select_usuario();
		while ($reg = $rspta->fetch_object()){
			echo '<option value='.$reg->usuarioID.'>'.$reg->nombreCliente.'</option>';
		}
	break;

	case 'listar':
		$rspta=$clientes->listar($usuarioID);
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$botonConstancia = ($reg->constancia)? "<a class='btn btn-primary btn-sm' target='_blank' href='../public/files/constancia/$reg->constancia'><i class='fa-solid fa-eye espaciado-icn'></i> Ver</a>" : "<a class='btn btn-primary btn-sm disabled' href='#'><i class='fa-solid fa-eye espaciado-icn'></i> Ver</a>" ;
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar Cliente" onclick="mostrar('.$reg->clienteID.')"><i class="fas fa-fw fa-pencil-alt"></i></button>';
			$linkMostrar = '<a href="#" title="Mostrar Cliente" onclick="mostrar('.$reg->clienteID.')">'.$reg->nombreCliente.'</a>';
			$botonActivar = ($reg->activo==1)? '<button class="btn btn-danger btn-sm" title="Desactivar Cliente" onclick="desactivar('.$reg->clienteID.')"><i class="fas fa-fw fa-times"></i></button>' : '<button class="btn btn-primary btn-sm" title="Activar Cliente" onclick="activar('.$reg->clienteID.')"><i class="fa fa-check"></i></button>';
			$moneda = ($reg->moneda=='USD' || empty($reg->moneda))? 'Dólares (USD) ' : 'Pesos (MXN) ';
			$razon = ($reg->razonSocial)? $reg->razonSocial.' - ' : '';
			$rfc = ($reg->rfcCliente)? 'RFC: '.$reg->rfcCliente.' - ' : '';
			$calle = ($reg->calle)? $reg->calle : '';
			$num_ext = ($reg->num_ext)? $reg->num_ext.', ' : '';
			$num_int = ($reg->num_int)? ' Int. '.$reg->num_int.', ' : '';
			$colonia = ($reg->colonia)? 'Col. '.$reg->colonia.', ' : '';
			$poblacion = ($reg->poblacion)? $reg->poblacion : '';
			$edoPais = ($reg->edoPais)? $reg->edoPais : '';
			$cp = ($reg->cp)? 'C.P. '.$reg->cp : '';
			$direccion = ($reg->cp)? $calle.$num_ext.$num_int.$colonia.$poblacion.$edoPais.$cp : '- Sin registro -';
			$datosFacturacion = '<small>'.$moneda.$razon.$rfc.$direccion.'</small>';
			$data[]=array(
				"0"=>$linkMostrar,
				"1"=>$datosFacturacion,
				"2"=>($reg->user_email)? $reg->user_email : 'Sin registro' ,
				"3"=>($reg->telefono)? $reg->telefono : 'Sin registro',
				"4"=>$botonConstancia,
				"5"=>($reg->activo=='1')?'<span class="badge bg-success">Activado</span>':'<span class="badge bg-danger">Desactivado</span>',
				"6"=>$botonMostrar.' '.$botonActivar,
				"7"=>($reg->user_registered)? date("Y-m-d", strtotime($reg->user_registered)) : date("Y-m-d", strtotime($reg->fechaCliente)) ,
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'select_cliente':
		$rspta=$clientes->select_cliente();
		echo '<option data-contacto="" data-nombre="" data-clienteid="0" value="0" selected>--- SIN USER ---</option>';
		while ($reg = $rspta->fetch_object()){
			echo '<option data-subtext="'.$reg->razonSocial.'" data-formapago="'.$reg->formadePago.'" data-nocuenta="'.$reg->num_cuenta.'" data-nombre="'.$reg->nombreCliente.'" data-clienteid="'.$reg->clienteID.'" data-banco="'.$reg->banco.'" value="'.$reg->clienteID.'">'.htmlspecialchars_decode($reg->nombreCliente).'</option>';
		}
	break;

	case 'select_cliente_todos':
		$rspta=$clientes->select_cliente();
		echo '<option data-contacto="" data-nombre="" data-clienteid="0" value="0" selected>---- TODOS ----</option>';
		while ($reg = $rspta->fetch_object()){
			echo '<option data-subtext="'.$reg->razonSocial.'" data-formapago="'.$reg->formadePago.'" data-nocuenta="'.$reg->num_cuenta.'" data-nombre="'.$reg->nombreCliente.'" data-clienteid="'.$reg->clienteID.'" data-banco="'.$reg->banco.'" value="'.$reg->clienteID.'">'.htmlspecialchars_decode($reg->nombreCliente).'</option>';
		}
	break;

	case 'select_cliente_factura':
		$rspta=$clientes->select_cliente_factura();
		while ($reg = $rspta->fetch_object()){
			echo '<option style="color:#222;" data-subtext="'.$reg->razonSocial.'" data-formapago="'.$reg->formadePago.'" data-banco="'.$reg->banco.'" data-nocuenta="'.$reg->num_cuenta.'" data-cliente="'.$reg->cliente.'" data-clienteid="'.$reg->clienteID.'" data-calle="'.$reg->calle.'" data-numext="'.$reg->num_ext.'" data-numint="'.$reg->num_int.'" data-colonia="'.$reg->colonia.'" data-poblacion="'.$reg->poblacion.'" data-edopais="'.$reg->edoPais.'" data-cp="'.$reg->cp.'" data-rfccliente="'.$reg->rfcCliente.'" data-regimenfiscal="'.$reg->regimenFiscal.'" data-usocfdi="'.$reg->usoCfdi.'" data-metodopago="'.$reg->metodoPago.'" data-credito="'.$reg->credito.'" data-constancia="'.htmlspecialchars_decode($reg->constancia).'" value="'.$reg->clienteID.'">'.htmlspecialchars_decode($reg->nombreCliente).'</option>';
		}
	break;

	case 'muestraHistorial':
		$clienteID=$_GET['hist'];
		$rspta = $clientes->muestraHistorial($clienteID);

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
}
?>