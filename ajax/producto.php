<?php
if (strlen(session_id()) < 1) 
	session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../models/Producto.php";
$productos=new Producto();

$id=isset($_POST["id"])? limpiarCadena($_POST["id"]):"";
$productoID=isset($_POST["productoID"])? limpiarCadena($_POST["productoID"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$precioVenta=isset($_POST["precioVenta"])? limpiarCadena($_POST["precioVenta"]):"";
$medida_id=isset($_POST["medida_id"])? limpiarCadena($_POST["medida_id"]):"";
$clave_id=isset($_POST["clave_id"])? limpiarCadena($_POST["clave_id"]):"";
$observaciones=isset($_POST["observaciones"])? limpiarCadena($_POST["observaciones"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if($id==""){
			$rspta=$productos->guardar($productoID,$nombre,$precioVenta,$medida_id,$clave_id,$observaciones);
			echo $rspta ? "Producto registrado" : "El Producto no se pudo registrar";
		} else {
			$rspta=$productos->editar($id,$productoID,$nombre,$precioVenta,$medida_id,$clave_id,$observaciones);
			echo $rspta ? "Producto actualizado" : "El Producto no se pudo actualizar";
		}
	break;

	case 'mostrar':
		$rspta=$productos->mostrar($productoID);
		echo json_encode($rspta);
	break;

	case 'listar':
		$rspta=$productos->listar();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar Producto" href="#" onclick="mostrar('.$reg->productoID.')"><i class="fas fa-fw fa-pencil-alt"></i></button>';
			$botonVariante = ($reg->varianteID)? ' <button class="btn btn-info btn-sm" title="Mostrar Variante" href="#" onclick="modalVariantes('.$reg->productoID.')"><i class="fas fa-fw fa-eye espaciado-icn"></i> Variantes</button>' : '<button class="btn btn-transparent-dark btn-sm" title="Mostrar Variante" href="#" disabled><i class="fas fa-fw fa-eye espaciado-icn"></i> Variantes</button>';
			$linkMostrar = '<a class="link-underline-primary" title="Mostrar Producto" onclick="mostrar('.$reg->productoID.')">'.$reg->nombre.'</a>';
			$imagen = ($reg->imagen)?'<a href="'.$reg->imagen.'" data-featherlight="image"><img class="img-thumbnail" style="width:40px;height:auto;" src="'.$reg->imagen.'"></a>' : '<img class="img-thumbnail" style="width:40px;height:auto;" src="../public/images/placeholder.jpg">';
			$data[]=array(
				"0"=>$linkMostrar.'<br><small><strong>SKU:</strong> '.$reg->sku.'</small>',
				"1"=>$reg->nombreUnidad.' <small>('.$reg->unidad.')</small>',
				"2"=>$reg->nombreClave.' <small>('.$reg->clave.')</small>',
				"3"=>'$'.number_format($reg->precioVenta, 2, '.', ','),
				"4"=>$imagen,
				"5"=>$botonVariante,
				"6"=>$botonMostrar
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'listar_variante':
		$productoID = $_GET["prod"];
		$rspta=$productos->listar_variante($productoID);
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$imagen = ($reg->imagen)?'<a href="'.$reg->imagen.'" data-featherlight="image"><img class="img-thumbnail" style="width:40px;height:auto;" src="'.$reg->imagen.'"></a>' : '<img class="img-thumbnail" style="width:40px;height:auto;" src="../public/images/placeholder.jpg">';
			$data[]=array(
				"0"=>$reg->nombre.' - ID: '.$reg->productoID.'<br><small><strong>SKU:</strong> '.$reg->sku.'</small>',
				"1"=>$reg->nombreUnidad.' <small>('.$reg->unidad.')</small>',
				"2"=>'$'.number_format($reg->precioVenta, 2, '.', ','),
				"3"=>$imagen
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