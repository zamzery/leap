<?php
if (strlen(session_id()) < 1) 
	session_start();

require_once "../models/Metodopago.php";
$metodospagos=new Metodopago();

$metodopagoID=isset($_POST["metodopagoID"])? limpiarCadena($_POST["metodopagoID"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$descripcion=isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";
$codigo=isset($_POST["codigo"])? limpiarCadena($_POST["codigo"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($metodopagoID)){
			$rspta=$metodospagos->insertar($nombre,$codigo,$descripcion,$codigo);
			echo $rspta ? "Método de pago registrado" : "El Método de pago no se pudo registrar";
		} else {
			$rspta=$metodospagos->editar($metodopagoID,$nombre,$codigo,$descripcion,$codigo);
			echo $rspta ? "Método de pago actualizado" : "El Método de pago no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$metodospagos->desactivar($metodopagoID);
		echo $rspta ? "Método de pago desactivado" : "El Método de pago no se pudo desactivar";
	break;

	case 'activar':
		$rspta=$metodospagos->activar($metodopagoID);
		echo $rspta ? "Método de pago activado" : "El Método de pago no se pudo activar";
	break;

	case 'mostrar':
		$rspta=$metodospagos->mostrar($metodopagoID);
		echo json_encode($rspta);
	break;

	case 'select_metodopago':
		$rspta=$metodospagos->select_metodopago();
		while ($reg = $rspta->fetch_object()){
			$selected = ($reg->codigo == '03') ? 'selected' : '';
			echo '<option value="'.$reg->metodopagoID.'" data-codigo="'.$reg->codigo.'" '.$selected.'>'.$reg->nombre.'</option>';
		}
	break;

	case 'listar':
		$rspta=$metodospagos->listar();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$linkMostrar = '<button class="btn btn-link" title="Mostrar Método de pago" onclick="mostrar('.$reg->metodopagoID.')">'.$reg->nombre.'</button>';
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar Método de pago" onclick="mostrar('.$reg->metodopagoID.')"><i class="fas fa-fw fa-pencil-alt"></i></button>';
			$botonActivar = ($reg->activo)? ' <button class="btn btn-danger btn-sm" title="Desactivar Método de pago" onclick="desactivar('.$reg->metodopagoID.')"><i class="fas fa-fw fa-times"></i></button>' : ' <button class="btn btn-primary btn-sm" title="Activar Método de pago" onclick="activar('.$reg->metodopagoID.')"><i class="fa fa-check"></i></button>';
			$data[]=array(
				"0"=>$linkMostrar,
				"1"=>$reg->codigo,
				"2"=>$reg->descripcion,
				"3"=>($reg->activo)?'<span class="badge bg-success">Activado</span>':'<span class="badge bg-danger">Desactivado</span>',
				"4"=>$botonMostrar.$botonActivar
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