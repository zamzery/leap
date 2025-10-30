<?php
if (strlen(session_id()) < 1) 
	session_start();

require_once "../models/Pago_efectuado.php";
$pagos=new Pago_efectuado();

$pagoID=isset($_POST["pagoID"])? limpiarCadena($_POST["pagoID"]):"";
$cliente_id=isset($_POST["cliente_id"])? limpiarCadena($_POST["cliente_id"]):"";
$fechaPago=isset($_POST["fechaPago"])? limpiarCadena($_POST["fechaPago"]):"";
$metodopago_id=isset($_POST["metodopago_id"])? limpiarCadena($_POST["metodopago_id"]):"";
$banco=isset($_POST["banco"])? limpiarCadena($_POST["banco"]):"";
$numero_cuenta=isset($_POST["numero_cuenta"])? limpiarCadena($_POST["numero_cuenta"]):"";
$parcialidad=isset($_POST["parcialidad"])? limpiarCadena($_POST["parcialidad"]):"";
$saldoAnterior=isset($_POST["saldoAnterior"])? limpiarCadena($_POST["saldoAnterior"]):"";
$pago=isset($_POST["pago"])? limpiarCadena($_POST["pago"]):"";
$saldoRestante=isset($_POST["saldoRestante"])? limpiarCadena($_POST["saldoRestante"]):"";
$usuarioID=$_SESSION['usuarioID'];

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (!file_exists($_FILES['comprobantePago']['tmp_name']) || !is_uploaded_file($_FILES['comprobantePago']['tmp_name'])){
			$comprobantePago=$_POST["comprobantePagoActual"];
		} else {
			if ($_FILES['comprobantePago']['type'] == "image/jpg" || $_FILES['comprobantePago']['type'] == "image/jpeg" || $_FILES['comprobantePago']['type'] == "image/png" || $_FILES['comprobantePago']['type'] == "image/gif" || $_FILES['comprobantePago']['type'] == "application/pdf"){
				$ext = explode(".", $_FILES["comprobantePago"]["name"]);
				$comprobantePago = round(microtime(true)). '.' . end($ext);
				move_uploaded_file($_FILES["comprobantePago"]["tmp_name"], "../public/files/pagos/" . $comprobantePago);
			}
		}
		if (empty($pagoID)){
			$rspta=$pagos->insertar($cliente_id,$fechaPago,$metodopago_id,$banco,$numero_cuenta,$parcialidad,$saldoAnterior,$pago,$comprobantePago,$saldoRestante,$usuarioID);
			echo $rspta ? "Pago registrado" : "El pago no se pudo registrar";
		} else {
			$rspta=$pagos->editar($pagoID,$cliente_id,$fechaPago,$metodopago_id,$banco,$numero_cuenta,$parcialidad,$saldoAnterior,$pago,$comprobantePago,$saldoRestante,$usuarioID);
			echo $rspta ? "Pago actualizado" : "El pago no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$pagos->desactivar($pagoID,$usuarioID);
		echo $rspta ? "Pago desactivado" : "El pago no se pudo desactivar";
	break;

	case 'activar':
		$rspta=$pagos->activar($pagoID,$usuarioID);
		echo $rspta ? "Pago activado" : "El pago no se pudo activar";
	break;

	case 'mostrar':
		$rspta=$pagos->mostrar($pagoID);
		echo json_encode($rspta);
	break;

	case 'select_pago':
		$rspta=$pagos->select_pago();
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->pagoID.'">'.$reg->pago.' - '.$reg->fecha.'</option>';
		}
	break;

	case 'listar':
		$rspta=$pagos->listar();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar Pago" onclick="mostrar('.$reg->pagoID.')"><i class="fas fa-fw fa-pencil-alt"></i></button>';
			$linkMostrar = '<button class="btn btn-link" title="Mostrar Pago" onclick="mostrar('.$reg->pagoID.')">'.$reg->pagoID.'</button>';
			$botonActivar = ($reg->activo)? ' <button class="btn btn-danger btn-sm" title="Desactivar pago" onclick="desactivar('.$reg->pagoID.')"><i class="fas fa-fw fa-times"></i></button>' : ' <button class="btn btn-primary btn-sm" title="Activar pago" onclick="activar('.$reg->pagoID.')"><i class="fa fa-check"></i></button>';

			$tipoArchivo = substr($reg->comprobantePago, -3);
			if($tipoArchivo=='pdf' || $tipoArchivo=='PDF'){
				$imagenPago = '<span style="display:none;">PDF</span><a href="../public/files/pagos/'.$reg->comprobantePago.'" target="_blank"><img class="img-thumbnail" width="35" height="35" src="../public/images/pdf-file.png"></a>';
			} else {
				$imagenPago = ($reg->comprobantePago)?'<span style="display:none;">JPG</span><a href="../public/files/pagos/'.$reg->comprobantePago.'" data-featherlight="image"><img class="img-thumbnail" width="35" height="35" src="../public/files/pagos/'.$reg->comprobantePago.'"></a>' : '<span style="display:none;">ZZZ</span><img class="img-thumbnail" width="35" height="35" src="../public/images/placeholder.jpg">';
			}
			$data[]=array(
				"0"=>$linkMostrar,
				"1"=>$reg->fechaPago,
				"2"=>$reg->nombreCliente,
				"3"=>$reg->nombreMetodo,
				"4"=>'$'.number_format($reg->pago,2,'.',','),
				"5"=>$imagenPago,
				"6"=>($reg->activo)?'<span class="badge bg-success">Activado</span>':'<span class="badge bg-danger">Desactivado</span>',
				"7"=>$botonMostrar.$botonActivar
			);
		}

		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'muestraHistorial':
		$pagoID=$_GET['hist'];
		$rspta = $pagos->muestraHistorial($pagoID);

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