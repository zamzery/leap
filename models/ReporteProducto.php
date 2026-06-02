<?php
require "../config/Conexion.php";

Class ReporteProducto {
	public function __construct(){
		
	}

	private function filtrosFecha($fechaInicio, $fechaFin, $aliasFecha){
		$filtros = "";
		if (!empty($fechaInicio)){
			$filtros .= " AND DATE($aliasFecha) >= '$fechaInicio'";
		}
		if (!empty($fechaFin)){
			$filtros .= " AND DATE($aliasFecha) <= '$fechaFin'";
		}
		return $filtros;
	}

	private function filtrosProducto($productos, $campoProducto){
		if (empty($productos)){
			return "";
		}

		$ids = array();
		foreach ($productos as $producto){
			$producto = intval($producto);
			if ($producto > 0){
				$ids[] = $producto;
			}
		}

		if (empty($ids)){
			return "";
		}

		return " AND $campoProducto IN (".implode(",", $ids).")";
	}

	private function unionVentas($fechaInicio = "", $fechaFin = "", $productos = array()){
		$filtroManualFecha = $this->filtrosFecha($fechaInicio, $fechaFin, "ped.created_at");
		$filtroWooFecha = $this->filtrosFecha($fechaInicio, $fechaFin, "ord.post_date");

		$productoManual = "IF(det.variante_id IS NULL OR det.variante_id = 0, det.producto_id, det.variante_id)";
		$productoWoo = "IF(pm_variation.meta_value IS NULL OR pm_variation.meta_value = 0, product_id_meta.meta_value, pm_variation.meta_value)";

		$filtroManualProducto = $this->filtrosProducto($productos, $productoManual);
		$filtroWooProducto = $this->filtrosProducto($productos, $productoWoo);

		return "
			SELECT
				$productoManual AS productoID,
				IFNULL(NULLIF(CONCAT(IFNULL(prod.post_title, ''), IF(var.ID IS NOT NULL AND var.post_title != prod.post_title, CONCAT(' - ', var.post_title), '')), ''), det.descripcion) AS nombreProducto,
				DATE(ped.created_at) AS fecha,
				CAST(det.cantidad AS DECIMAL(18,2)) AS cantidad,
				CAST(det.cantidad * det.precioVenta AS DECIMAL(18,2)) AS importe,
				'Manual' AS origen
			FROM pedidosDetalles det
			INNER JOIN pedidos ped ON det.pedido_id = ped.id
			LEFT JOIN wp_posts prod ON det.producto_id = prod.ID
			LEFT JOIN wp_posts var ON det.variante_id = var.ID
			WHERE IFNULL(ped.status, '') NOT IN ('Cancelado', 'Cancelada', 'Cancelado SAT') $filtroManualFecha $filtroManualProducto

			UNION ALL

			SELECT
				$productoWoo AS productoID,
				IFNULL(NULLIF(CONCAT(IFNULL(prod.post_title, ''), IF(var.ID IS NOT NULL AND var.post_title != prod.post_title, CONCAT(' - ', var.post_title), '')), ''), oi.order_item_name) AS nombreProducto,
				DATE(ord.post_date) AS fecha,
				CAST(pm_qty.meta_value AS DECIMAL(18,2)) AS cantidad,
				CAST(pm_total.meta_value AS DECIMAL(18,2)) AS importe,
				'WooCommerce' AS origen
			FROM wp_woocommerce_order_items oi
			INNER JOIN wp_posts ord ON oi.order_id = ord.ID
			LEFT JOIN wp_woocommerce_order_itemmeta pm_qty ON oi.order_item_id = pm_qty.order_item_id AND pm_qty.meta_key = '_qty'
			LEFT JOIN wp_woocommerce_order_itemmeta product_id_meta ON oi.order_item_id = product_id_meta.order_item_id AND product_id_meta.meta_key = '_product_id'
			LEFT JOIN wp_woocommerce_order_itemmeta pm_variation ON oi.order_item_id = pm_variation.order_item_id AND pm_variation.meta_key = '_variation_id'
			LEFT JOIN wp_woocommerce_order_itemmeta pm_total ON oi.order_item_id = pm_total.order_item_id AND pm_total.meta_key = '_line_total'
			LEFT JOIN wp_posts prod ON product_id_meta.meta_value = prod.ID
			LEFT JOIN wp_posts var ON pm_variation.meta_value = var.ID
			WHERE ord.post_type = 'shop_order'
				AND oi.order_item_type = 'line_item'
				AND ord.post_status NOT IN ('wc-pending', 'wc-cancelled', 'trash')
				AND pm_qty.meta_value IS NOT NULL
				AND $productoWoo IS NOT NULL
				$filtroWooFecha $filtroWooProducto
		";
	}

	public function productosDisponibles(){
		$sql = "SELECT productoID, nombreProducto
			FROM (".$this->unionVentas().") ventas
			WHERE productoID IS NOT NULL
			GROUP BY productoID, nombreProducto
			ORDER BY nombreProducto ASC";
		return ejecutarConsulta($sql);
	}

	public function resumen($fechaInicio, $fechaFin, $productos){
		$sql = "SELECT
				IFNULL(SUM(cantidad), 0) AS unidades,
				IFNULL(SUM(importe), 0) AS importe,
				COUNT(DISTINCT productoID) AS productos,
				COUNT(DISTINCT fecha) AS dias
			FROM (".$this->unionVentas($fechaInicio, $fechaFin, $productos).") ventas";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function productosMasVendidos($fechaInicio, $fechaFin, $productos, $limite = 10){
		$limite = intval($limite);
		if ($limite < 1){
			$limite = 10;
		}
		$sql = "SELECT productoID, nombreProducto, SUM(cantidad) AS unidades, SUM(importe) AS importe
			FROM (".$this->unionVentas($fechaInicio, $fechaFin, $productos).") ventas
			WHERE productoID IS NOT NULL
			GROUP BY productoID, nombreProducto
			ORDER BY unidades DESC, importe DESC
			LIMIT $limite";
		return ejecutarConsulta($sql);
	}

	public function comparativoMensual($fechaInicio, $fechaFin, $productos){
		$sql = "SELECT productoID, nombreProducto, DATE_FORMAT(fecha, '%Y-%m') AS periodo, SUM(cantidad) AS unidades, SUM(importe) AS importe
			FROM (".$this->unionVentas($fechaInicio, $fechaFin, $productos).") ventas
			WHERE productoID IS NOT NULL
			GROUP BY productoID, nombreProducto, DATE_FORMAT(fecha, '%Y-%m')
			ORDER BY periodo ASC, nombreProducto ASC";
		return ejecutarConsulta($sql);
	}

	public function origenVentas($fechaInicio, $fechaFin, $productos){
		$sql = "SELECT origen, SUM(cantidad) AS unidades, SUM(importe) AS importe
			FROM (".$this->unionVentas($fechaInicio, $fechaFin, $productos).") ventas
			GROUP BY origen
			ORDER BY importe DESC";
		return ejecutarConsulta($sql);
	}
}
?>