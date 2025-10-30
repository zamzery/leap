<?php
if (strlen(session_id()) < 1) 
	session_start();

require_once "../models/Cargo.php";
$cargos=new Cargo();

$cargoID=isset($_POST["cargoID"])? limpiarCadena($_POST["cargoID"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$descripcion=isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($cargoID)){
			$rspta=$cargos->insertar($nombre,$descripcion,$_POST['permiso'],$_POST['ver'],$_POST['editar'],$_POST['historial']);
			echo $rspta ? "Cargo registrado" : "El Cargo no se pudo registrar";
		} else {
			$rspta=$cargos->editar($cargoID,$nombre,$descripcion,$_POST['permiso'],$_POST['ver'],$_POST['editar'],$_POST['historial']);
			echo $rspta ? "Cargo actualizado" : "El Cargo no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$cargos->desactivar($cargoID);
		echo $rspta ? "Cargo desactivado" : "El Cargo no se pudo desactivar";
	break;

	case 'activar':
		$rspta=$cargos->activar($cargoID);
		echo $rspta ? "Cargo activado" : "El Cargo no se pudo activar";
	break;

	case 'mostrar':
		$rspta=$cargos->mostrar($cargoID);
		echo json_encode($rspta);
	break;

	case 'select_cargo':
		$rspta=$cargos->select_cargo();
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->cargoID.'">'.$reg->nombre.'</option>';
		}
	break;

	case 'listar':
		$rspta=$cargos->listar();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$linkMostrar = '<button class="btn btn-link" title="Mostrar cargo" onclick="mostrar('.$reg->cargoID.')">'.$reg->nombre.'</button>';
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar cargo" onclick="mostrar('.$reg->cargoID.')"><i class="fas fa-fw fa-pencil-alt"></i></button>';
			$botonActivar = ($reg->activo)? ' <button class="btn btn-danger btn-sm" title="Desactivar cargo" onclick="desactivar('.$reg->cargoID.')"><i class="fas fa-fw fa-times"></i></button>' : ' <button class="btn btn-primary btn-sm" title="Activar cargo" onclick="activar('.$reg->cargoID.')"><i class="fa fa-check"></i></button>';
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

	case 'listar_permiso':
		$id=$_GET['id'];
		$rspta = $cargos->listar_permisoMarcado($id);
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$nombrePermiso = ($reg->principal==0)? $reg->nombrePermiso : '<strong>'.$reg->nombrePermiso.'</strong>';
			$marcado=($reg->ver)?'checked':'';
			$disabled = ($reg->verPermiso)? '':'disabled';
			$ver=($reg->ver)?'checked':'';
			$editar=($reg->editar)?'checked':'';
			$historial=($reg->historial)?'checked':'';
			$checkVer = ($reg->verPermiso)? '<input type="checkbox" class="permisos" id="'.$reg->permiso.'verCheck" '.$ver.' onchange="cambiaValorPermiso(\''.$reg->permiso.'\',\'ver\')"><input id="'.$reg->permiso.'ver" type="hidden" name="ver[]" value="'.$reg->ver.'">': '<input type="hidden" name="ver[]" value="'.$reg->ver.'">';
			$checkEditar = ($reg->editarPermiso)? '<input type="checkbox" class="permisos" '.$disabled.' id="'.$reg->permiso.'editarCheck" '.$editar.' onchange="cambiaValorPermiso(\''.$reg->permiso.'\',\'editar\')"><input id="'.$reg->permiso.'editar" type="hidden" name="editar[]" value="'.$reg->editar.'">' : '<input type="hidden" name="editar[]" value="'.$reg->editar.'">';
			$checkHistorial = ($reg->historialPermiso)? '<input type="checkbox" class="permisos" '.$disabled.' id="'.$reg->permiso.'historialCheck" '.$historial.' onchange="cambiaValorPermiso(\''.$reg->permiso.'\',\'historial\')"><input id="'.$reg->permiso.'historial" type="hidden" name="historial[]" value="'.$reg->historial.'">' : '<input type="hidden" name="historial[]" value="'.$reg->historial.'">';

			$data[]=array(
				"0"=>$nombrePermiso.'<input type="hidden" name="permiso[]" value="'.$reg->permisoID.'">',
				"1"=>$checkVer,
				"2"=>$checkEditar,
				"3"=>$checkHistorial,
				"4"=>$reg->orden,
				"5"=>$reg->grupo,
			);
		}
		$results_cargos = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results_cargos);
	break;
}
?>