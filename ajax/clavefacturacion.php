<?php
if (strlen(session_id()) < 1) 
	session_start();

require_once "../models/Clavefacturacion.php";
$clavesfacturacion=new Clavefacturacion();

$claveID=isset($_POST["claveID"])? limpiarCadena($_POST["claveID"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$clave=isset($_POST["clave"])? limpiarCadena($_POST["clave"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($claveID)){
			$rspta=$clavesfacturacion->insertar($nombre,$clave);
			echo $rspta ? "Clave de Facturación registrada" : "La Clave de Facturación no se pudo registrar";
		} else {
			$rspta=$clavesfacturacion->editar($claveID,$nombre,$clave);
			echo $rspta ? "Clave de Facturación actualizada" : "La Clave de Facturación no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$clavesfacturacion->desactivar($claveID);
		echo $rspta ? "Clave de Facturación desactivada" : "El Clave de Facturación no se pudo desactivar";
	break;

	case 'activar':
		$rspta=$clavesfacturacion->activar($claveID);
		echo $rspta ? "Clave de Facturación activada" : "El Clave de Facturación no se pudo activar";
	break;

	case 'mostrar':
		$rspta=$clavesfacturacion->mostrar($claveID);
		echo json_encode($rspta);
	break;

	case 'select_clavefacturacion':
		$rspta=$clavesfacturacion->select_clavefacturacion();
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->claveID.'" data-subtext="'.$reg->clave.'">'.$reg->nombre.'</option>';
		}
	break;

	case 'listar':
		$rspta=$clavesfacturacion->listar();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$linkMostrar = '<button class="btn btn-link" title="Mostrar Clave de Facturación" onclick="mostrar('.$reg->claveID.')">'.$reg->nombre.'</button>';
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar Clave de Facturación" onclick="mostrar('.$reg->claveID.')"><i class="fas fa-fw fa-pencil-alt"></i></button>';
			$botonActivar = ($reg->activo)? ' <button class="btn btn-danger btn-sm" title="Desactivar Clave de Facturación" onclick="desactivar('.$reg->claveID.')"><i class="fas fa-fw fa-times"></i></button>' : ' <button class="btn btn-primary btn-sm" title="Activar Clave de Facturación" onclick="activar('.$reg->claveID.')"><i class="fa fa-check"></i></button>';
			$data[]=array(
				"0"=>$linkMostrar,
				"1"=>$reg->clave,
				"2"=>($reg->activo)?'<span class="badge bg-success">Activado</span>':'<span class="badge bg-danger">Desactivado</span>',
				"3"=>$botonMostrar.$botonActivar
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