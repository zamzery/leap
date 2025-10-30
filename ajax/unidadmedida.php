<?php
if (strlen(session_id()) < 1) 
	session_start();

require_once "../models/Unidadmedida.php";
$unidadesdemedidas=new Unidadmedida();

$unidadmedidaID=isset($_POST["unidadmedidaID"])? limpiarCadena($_POST["unidadmedidaID"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$unidad=isset($_POST["unidad"])? limpiarCadena($_POST["unidad"]):"";
$abreviatura=isset($_POST["abreviatura"])? limpiarCadena($_POST["abreviatura"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($unidadmedidaID)){
			$rspta=$unidadesdemedidas->insertar($nombre,$unidad,$abreviatura);
			echo $rspta ? "Unidad de medida registrada" : "La Unidad de medida no se pudo registrar";
		} else {
			$rspta=$unidadesdemedidas->editar($unidadmedidaID,$nombre,$unidad,$abreviatura);
			echo $rspta ? "Unidad de medida actualizada" : "La Unidad de medida no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$unidadesdemedidas->desactivar($unidadmedidaID);
		echo $rspta ? "Unidad de medida desactivada" : "La Unidad de medida no se pudo desactivar";
	break;

	case 'activar':
		$rspta=$unidadesdemedidas->activar($unidadmedidaID);
		echo $rspta ? "Unidad de medida activada" : "La Unidad de medida no se pudo activar";
	break;

	case 'mostrar':
		$rspta=$unidadesdemedidas->mostrar($unidadmedidaID);
		echo json_encode($rspta);
	break;

	case 'select_unidadmedida':
		$rspta=$unidadesdemedidas->select_unidadmedida();
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->unidadmedidaID.'" data-subtext="'.$reg->unidad.'">'.$reg->nombre.'</option>';
		}
	break;

	case 'listar':
		$rspta=$unidadesdemedidas->listar();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$linkMostrar = '<button class="btn btn-link" title="Mostrar unidad de medida" onclick="mostrar('.$reg->unidadmedidaID.')">'.$reg->nombre.'</button>';
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar unidad de medida" onclick="mostrar('.$reg->unidadmedidaID.')"><i class="fas fa-fw fa-pencil-alt"></i></button>';
			$botonActivar = ($reg->activo)? ' <button class="btn btn-danger btn-sm" title="Desactivar unidad de medida" onclick="desactivar('.$reg->unidadmedidaID.')"><i class="fas fa-fw fa-times"></i></button>' : ' <button class="btn btn-primary btn-sm" title="Activar unidad de medida" onclick="activar('.$reg->unidadmedidaID.')"><i class="fa fa-check"></i></button>';
			$data[]=array(
				"0"=>$linkMostrar,
				"1"=>$reg->unidad,
				"2"=>$reg->abreviatura,
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