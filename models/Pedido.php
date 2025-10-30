<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Pedido {
	//Implementamos nuestro constructor
	public function __construct(){
		
	}

	// Implementamos un método para insertar Facturas
	public function insertar($serie,$metodoPago,$claveTipoComprobante,$usoCfdi,$formadePago,$descuento,$moneda,$tipoCambio,$cliente_id,$razonSocial,$rfcCliente,$usuarioID,$comentarios,$pedido_id,$user_login,$user_nicename,$email_cliente,$contrasenna,$telefono,$calle,$num_ext,$num_int,$colonia,$poblacion,$edoPais,$cp,$regimenFiscal,$num_cuenta,$banco,$producto_id,$descripcion,$cantidad,$precioVenta,$subtotal){
		$sw=true;
		if($cliente_id==0){
			$sql_cliente="INSERT INTO wp_users (user_login,user_nicename,user_email,user_pass,role) VALUES ('$user_login','$user_nicename','$email_cliente',MD5('$contrasenna'),'customer')";
			$cliente_idnew=ejecutarConsulta_retornarID($sql_cliente) or $sw = false;

			$sql_datos_fiscales="INSERT INTO datos_fiscales (cliente_id,razonSocial,rfcCliente,telefono,calle,num_ext,num_int,colonia,poblacion,edoPais,cp,regimenFiscal,num_cuenta,banco) VALUES ('$cliente_idnew','$razonSocial','$rfcCliente','$telefono','$calle','$num_ext','$num_int','$colonia','$poblacion','$edoPais','$cp','$regimenFiscal','$num_cuenta','$banco')";
			ejecutarConsulta($sql_datos_fiscales) or $sw = false;

			$cliente_id=$cliente_idnew;
		} else {
			$sql_cliente="UPDATE wp_users SET user_email='$email_cliente' WHERE ID='$cliente_id'";
			ejecutarConsulta($sql_cliente) or $sw = false;

			$sql_borrar_datos_fiscales="DELETE FROM datos_fiscales WHERE cliente_id='$cliente_id'";
			ejecutarConsulta($sql_borrar_datos_fiscales) or $sw = false;
			if($sw==true){
				$sql_datos_fiscales="INSERT INTO datos_fiscales (cliente_id,razonSocial,rfcCliente,telefono,calle,num_ext,num_int,colonia,poblacion,edoPais,cp,regimenFiscal,num_cuenta,banco) VALUES ('$cliente_id','$razonSocial','$rfcCliente','$telefono','$calle','$num_ext','$num_int','$colonia','$poblacion','$edoPais','$cp','$regimenFiscal','$num_cuenta','$banco')";
				ejecutarConsulta($sql_datos_fiscales) or $sw = false;
			}
		}

		$sql_factura="INSERT INTO factura (serie,metodoPago,claveTipoComprobante,usoCfdi,formadePago,descuento,moneda,tipoCambio,cliente_id,user_id,comentarios,created_at,fecha,status)
		VALUES ('$serie','$metodoPago','$claveTipoComprobante','$usoCfdi','$formadePago','$descuento','$moneda','$tipoCambio','$cliente_id','$usuarioID','$comentarios',NOW(),NOW(),'Espera')";
		$facturaIDnew=ejecutarConsulta_retornarID($sql_factura) or $sw = false;

		if($producto_id){
			$num=0;
			while ($num < count($producto_id)){
				$sql_detalle = "INSERT INTO facturaDetalle (factura_id,producto_id,descripcion,cantidad,precioUnitario,subtotal,ivaUnitario) VALUES ('$facturaIDnew','$producto_id[$num]','$descripcion[$num]','$cantidad[$num]','$precioVenta[$num]','$subtotal[$num]','$subtotal[$num]'*0.16)";
				ejecutarConsulta($sql_detalle) or $sw = false;
				$num = $num + 1;
			}
		}
		return $sw;
	}

	public function editar($facturaID,$serie,$metodoPago,$claveTipoComprobante,$usoCfdi,$formadePago,$descuento,$moneda,$tipoCambio,$cliente_id,$razonSocial,$rfcCliente,$usuarioID,$comentarios,$pedido_id,$user_login,$user_nicename,$email_cliente,$contrasenna,$telefono,$calle,$num_ext,$num_int,$colonia,$poblacion,$edoPais,$cp,$regimenFiscal,$num_cuenta,$banco,$producto_id,$descripcion,$cantidad,$precioVenta,$subtotal){
		$sw=true;
		if($cliente_id==0){
			$sql_cliente="INSERT INTO wp_users (user_login,user_nicename,user_email,user_pass,role) VALUES ('$user_login','$user_nicename','$email_cliente',MD5('$contrasenna'),'customer')";
			$cliente_idnew=ejecutarConsulta_retornarID($sql_cliente) or $sw = false;

			$sql_datos_fiscales="INSERT INTO datos_fiscales (cliente_id,razonSocial,rfcCliente,telefono,calle,num_ext,num_int,colonia,poblacion,edoPais,cp,regimenFiscal,num_cuenta,banco) VALUES ('$cliente_idnew','$razonSocial','$rfcCliente','$telefono','$calle','$num_ext','$num_int','$colonia','$poblacion','$edoPais','$cp','$regimenFiscal','$num_cuenta','$banco')";
			ejecutarConsulta($sql_datos_fiscales) or $sw = false;

			$cliente_id=$cliente_idnew;
		} else {
			$sql_cliente="UPDATE wp_users SET user_email='$email_cliente' WHERE ID='$cliente_id'";
			ejecutarConsulta($sql_cliente) or $sw = false;

			$sql_borrar_datos_fiscales="DELETE FROM datos_fiscales WHERE cliente_id='$cliente_id'";
			ejecutarConsulta($sql_borrar_datos_fiscales) or $sw = false;
			if($sw==true){
				$sql_datos_fiscales="INSERT INTO datos_fiscales (cliente_id,razonSocial,rfcCliente,telefono,calle,num_ext,num_int,colonia,poblacion,edoPais,cp,regimenFiscal,num_cuenta,banco) VALUES ('$cliente_id','$razonSocial','$rfcCliente','$telefono','$calle','$num_ext','$num_int','$colonia','$poblacion','$edoPais','$cp','$regimenFiscal','$num_cuenta','$banco')";
				ejecutarConsulta($sql_datos_fiscales) or $sw = false;
			}
		}
		$sql_factura="UPDATE factura SET serie='$serie',metodoPago='$metodoPago',claveTipoComprobante='$claveTipoComprobante',usoCfdi='$usoCfdi',formadePago='$formadePago',descuento='$descuento',moneda='$moneda',tipoCambio='$tipoCambio',cliente_id='$cliente_id',comentarios='$comentarios',updated_at=NOW() WHERE id='$facturaID'";
		ejecutarConsulta($sql_factura) or $sw=false;

		if($sw==true){
			if($producto_id){
				$sqldel="DELETE FROM facturaDetalle WHERE factura_id='$facturaID'";
				ejecutarConsulta($sqldel) or $sw=false;

				$num=0;
				while ($num < count($producto_id)){
					$sql_detalle = "INSERT INTO facturaDetalle (factura_id,producto_id,descripcion,cantidad,precioUnitario,subtotal,ivaUnitario) VALUES ('$facturaID','$producto_id[$num]','$descripcion[$num]','$cantidad[$num]','$precioVenta[$num]','$subtotal[$num]','$subtotal[$num]'*0.16)";
					ejecutarConsulta($sql_detalle) or $sw = false;

					$num = $num + 1;
				}
			}
		}
		return $sw;
	}

	public function listar(){
		$sql="SELECT p.ID AS pedidoID, p.post_date_gmt AS fecha,p.post_status AS estado,CONCAT(pm_first.meta_value, ' ', pm_last.meta_value) AS cliente,pm_total.meta_value AS total FROM wp_posts p LEFT JOIN wp_postmeta pm_first ON p.ID = pm_first.post_id AND pm_first.meta_key = '_billing_first_name' LEFT JOIN wp_postmeta pm_last ON p.ID = pm_last.post_id AND pm_last.meta_key = '_billing_last_name' LEFT JOIN wp_postmeta pm_total ON p.ID = pm_total.post_id AND pm_total.meta_key = '_order_total' WHERE p.post_type = 'shop_order' AND p.post_date >= '2025-01-01' ORDER BY p.post_date DESC";
		return ejecutarConsulta($sql);		
	}

	public function mostrar($pedidoID){
		$sql="SELECT p.ID AS pedidoID, p.post_date_gmt AS fecha,p.post_status AS estado,CONCAT(pm_first.meta_value, ' ', pm_last.meta_value) AS nombreCliente,pm_total.meta_value AS total,IFNULL(user.ID,0) AS clienteID,customer.meta_id AS customer_user FROM wp_posts p LEFT JOIN wp_postmeta pm_first ON p.ID = pm_first.post_id AND pm_first.meta_key = '_billing_first_name' LEFT JOIN wp_postmeta pm_last ON p.ID = pm_last.post_id AND pm_last.meta_key = '_billing_last_name' LEFT JOIN wp_postmeta pm_total ON p.ID = pm_total.post_id AND pm_total.meta_key = '_order_total' LEFT JOIN wp_postmeta customer ON p.ID=customer.post_id AND customer.meta_key='_customer_user' LEFT JOIN wp_users user ON user.ID=customer.meta_value WHERE p.ID='$pedidoID' ORDER BY p.post_date DESC";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarDetalles($pedidoID){
		$sql="SELECT oi.order_item_id AS id,SELECT oi.order_item_id AS productoID,oi.order_item_name AS nombre_producto, pm_qty.meta_value AS cantidad, sku.meta_value AS sku, pm_total.meta_value AS precio_venta, v.post_title AS variante, IFNULL(img_variante.guid, img_producto.guid) AS imagen FROM wp_woocommerce_order_items oi LEFT JOIN wp_woocommerce_order_itemmeta pm_qty ON oi.order_item_id = pm_qty.order_item_id AND pm_qty.meta_key = '_qty' LEFT JOIN wp_woocommerce_order_itemmeta product_id_meta ON oi.order_item_id = product_id_meta.order_item_id AND product_id_meta.meta_key = '_product_id' LEFT JOIN wp_postmeta sku ON product_id_meta.meta_value = sku.post_id AND sku.meta_key = '_sku' LEFT JOIN wp_woocommerce_order_itemmeta pm_total ON oi.order_item_id = pm_total.order_item_id AND pm_total.meta_key = '_line_total' LEFT JOIN wp_woocommerce_order_itemmeta pm_variation ON oi.order_item_id = pm_variation.order_item_id AND pm_variation.meta_key = '_variation_id' LEFT JOIN wp_posts v ON pm_variation.meta_value = v.ID LEFT JOIN wp_postmeta thumb_variante ON v.ID = thumb_variante.post_id AND thumb_variante.meta_key = '_thumbnail_id' LEFT JOIN wp_posts img_variante ON thumb_variante.meta_value = img_variante.ID LEFT JOIN wp_postmeta thumb_producto ON product_id_meta.meta_value = thumb_producto.post_id AND thumb_producto.meta_key = '_thumbnail_id' LEFT JOIN wp_posts img_producto ON thumb_producto.meta_value = img_producto.ID WHERE oi.order_id = '$pedidoID' AND oi.order_item_type = 'line_item'";
		return ejecutarConsulta($sql);
	}

	public function obtener_datos_factura($pedidoID){
		$sql="SELECT p.ID AS pedidoID, p.post_date AS fecha,p.post_status AS estado,UPPER(CONCAT(pm_first.meta_value, ' ', pm_last.meta_value)) AS nombreCliente,IFNULL(user.ID,0) AS clienteID,pm_total.meta_value AS total,dat.telefono,dat.calle,dat.num_ext,dat.num_int,dat.colonia,dat.poblacion,dat.edoPais,dat.cp,dat.razonSocial,dat.rfcCliente,dat.regimenFiscal,dat.num_cuenta,dat.banco,dat.metodoPago,dat.formadePago,dat.usoCfdi,dat.comentarios,dat.constancia,dat.moneda FROM wp_posts p LEFT JOIN wp_postmeta pm_first ON p.ID = pm_first.post_id AND pm_first.meta_key = '_billing_first_name' LEFT JOIN wp_postmeta pm_last ON p.ID = pm_last.post_id AND pm_last.meta_key = '_billing_last_name' LEFT JOIN wp_postmeta pm_total ON p.ID = pm_total.post_id AND pm_total.meta_key = '_order_total' LEFT JOIN wp_postmeta customer ON p.ID=customer.post_id AND customer.meta_key='_customer_user' LEFT JOIN wp_users user ON user.ID=customer.meta_value LEFT JOIN datos_fiscales dat ON user.ID = dat.cliente_id WHERE p.post_type = 'shop_order' AND p.ID='$pedidoID'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function ver_productos($pedidoID){
		$sql="SELECT p.ID AS pedidoID FROM wp_posts p WHERE p.ID='$pedidoID'";
		return ejecutarConsultaSimpleFila($sql);
	}
}
?>