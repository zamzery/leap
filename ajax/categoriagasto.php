<?php
if (strlen(session_id()) < 1) 
	session_start();

require_once "../models/Categoriagasto.php";
$categoriasgastos=new Categoriagasto();

$categoriagastoID=isset($_POST["categoriagastoID"])? limpiarCadena($_POST["categoriagastoID"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$descripcion=isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($categoriagastoID)){
			$rspta=$categoriasgastos->insertar($nombre,$descripcion);
			echo $rspta ? "Categoría de gasto registrada" : "La Categoría de gasto no se pudo registrar";
		} else {
			$rspta=$categoriasgastos->editar($categoriagastoID,$nombre,$descripcion);
			echo $rspta ? "Categoría de gasto actualizada" : "La Categoría de gasto no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$categoriasgastos->desactivar($categoriagastoID);
		echo $rspta ? "Categoría de gasto desactivada" : "La Categoría de gasto no se pudo desactivar";
	break;

	case 'activar':
		$rspta=$categoriasgastos->activar($categoriagastoID);
		echo $rspta ? "Categoría de gasto activada" : "La Categoría de gasto no se pudo activar";
	break;

	case 'mostrar':
		$rspta=$categoriasgastos->mostrar($categoriagastoID);
		echo json_encode($rspta);
	break;

	case 'select_categoriagasto':
		$rspta=$categoriasgastos->select_categoriagasto();
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->categoriagastoID.'">'.$reg->nombre.'</option>';
		}
	break;

	case 'listar':
		$rspta=$categoriasgastos->listar();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$linkMostrar = '<button class="btn btn-link" title="Mostrar categoría de gasto" onclick="mostrar('.$reg->categoriagastoID.')">'.$reg->nombre.'</button>';
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar categoría de gasto" onclick="mostrar('.$reg->categoriagastoID.')"><i class="fas fa-fw fa-pencil-alt"></i></button>';
			$botonActivar = ($reg->activo)? ' <button class="btn btn-danger btn-sm" title="Desactivar categoría de gasto" onclick="desactivar('.$reg->categoriagastoID.')"><i class="fas fa-fw fa-times"></i></button>' : ' <button class="btn btn-primary btn-sm" title="Activar categoría de gasto" onclick="activar('.$reg->categoriagastoID.')"><i class="fa fa-check"></i></button>';
			$data[]=array(
				"0"=>$linkMostrar,
				"1"=>$reg->descripcion,
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