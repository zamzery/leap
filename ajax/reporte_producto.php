<?php
if (strlen(session_id()) < 1)
	session_start();

require_once "../models/ReporteProducto.php";
$reportesProductos = new ReporteProducto();

$fechaInicio = isset($_POST["fechaInicio"]) ? limpiarCadena($_POST["fechaInicio"]) : "";
$fechaFin = isset($_POST["fechaFin"]) ? limpiarCadena($_POST["fechaFin"]) : "";
$productos = isset($_POST["productos"]) ? $_POST["productos"] : array();

switch ($_GET["op"]){
	case 'select_productos':
		$rspta = $reportesProductos->productosDisponibles();
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->productoID.'">'.htmlspecialchars($reg->nombreProducto).'</option>';
		}
	break;

	case 'datos':
		$resumen = $reportesProductos->resumen($fechaInicio, $fechaFin, $productos);

		$top = array();
		$rsptaTop = $reportesProductos->productosMasVendidos($fechaInicio, $fechaFin, $productos, 10);
		while ($reg = $rsptaTop->fetch_object()){
			$top[] = array(
				"productoID" => $reg->productoID,
				"nombreProducto" => htmlspecialchars_decode($reg->nombreProducto),
				"unidades" => floatval($reg->unidades),
				"importe" => floatval($reg->importe)
			);
		}

		$comparativo = array();
		$rsptaComparativo = $reportesProductos->comparativoMensual($fechaInicio, $fechaFin, $productos);
		while ($reg = $rsptaComparativo->fetch_object()){
			$comparativo[] = array(
				"productoID" => $reg->productoID,
				"nombreProducto" => htmlspecialchars_decode($reg->nombreProducto),
				"periodo" => $reg->periodo,
				"unidades" => floatval($reg->unidades),
				"importe" => floatval($reg->importe)
			);
		}

		$origen = array();
		$rsptaOrigen = $reportesProductos->origenVentas($fechaInicio, $fechaFin, $productos);
		while ($reg = $rsptaOrigen->fetch_object()){
			$origen[] = array(
				"origen" => $reg->origen,
				"unidades" => floatval($reg->unidades),
				"importe" => floatval($reg->importe)
			);
		}

		echo json_encode(array(
			"resumen" => array(
				"unidades" => floatval($resumen["unidades"]),
				"importe" => floatval($resumen["importe"]),
				"productos" => intval($resumen["productos"]),
				"dias" => intval($resumen["dias"])
			),
			"top" => $top,
			"comparativo" => $comparativo,
			"origen" => $origen
		));
	break;
}
?>