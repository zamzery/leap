<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Pedido {
	//Implementamos nuestro constructor
	public function __construct(){
		
	}

	public function insertar($cliente_id,$nombreCliente,$observaciones,$producto_id,$variante_id,$descripcion,$cantidad,$precioVenta){
		$sw=true;
		$sql_pedido="INSERT INTO pedidos (cliente_id,nombreCliente,observaciones,created_at,status) VALUES ('$cliente_id','$nombreCliente','$observaciones',NOW(),'Espera')";
		$pedidoIDnew=ejecutarConsulta_retornarID($sql_pedido) or $sw = false;

		if($producto_id){
			$num=0;
			while ($num < count($producto_id)){
				$sql_detalle = "INSERT INTO pedidosDetalles (pedido_id,producto_id,variante_id,descripcion,cantidad,precioVenta) VALUES ('$pedidoIDnew','$producto_id[$num]','$variante_id[$num]','$descripcion[$num]','$cantidad[$num]','$precioVenta[$num]')";
				ejecutarConsulta($sql_detalle) or $sw = false;
				$num = $num + 1;
			}
		}
		return $sw;
	}

	public function editar($pedidoID,$cliente_id,$nombreCliente,$observaciones,$producto_id,$variante_id,$descripcion,$cantidad,$precioVenta){
		$sw=true;
		$sql_pedido="UPDATE pedidos SET cliente_id='$cliente_id',nombreCliente='$nombreCliente',observaciones='$observaciones',updated_at=NOW() WHERE id='$pedidoID'";
		ejecutarConsulta($sql_pedido) or $sw = false;

		if($producto_id){
			$num=0;
			$sqldel="DELETE FROM pedidosDetalles WHERE pedido_id='$pedidoID'";
			ejecutarConsulta($sqldel) or $sw=false;

			while ($num < count($producto_id)){
				$sql_detalle = "INSERT INTO pedidosDetalles (pedido_id,producto_id,variante_id,descripcion,cantidad,precioVenta) VALUES ('$pedidoID','$producto_id[$num]','$variante_id[$num]','$descripcion[$num]','$cantidad[$num]','$precioVenta[$num]')";
				ejecutarConsulta($sql_detalle) or $sw = false;
				$num = $num + 1;
			}
		}
		return $sw;
	}

	// Implementamos un método para insertar Facturas
	public function insertar_factura($serie,$metodoPago,$claveTipoComprobante,$usoCfdi,$formadePago,$descuento,$moneda,$tipoCambio,$cliente_id,$razonSocial,$rfcCliente,$usuarioID,$comentarios,$pedido_id,$user_login,$user_nicename,$email_cliente,$contrasenna,$telefono,$calle,$num_ext,$num_int,$colonia,$poblacion,$edoPais,$cp,$regimenFiscal,$num_cuenta,$banco,$producto_id,$descripcion,$cantidad,$precioVenta,$subtotal){
		$sw=true;
		if($cliente_id==0){
			$sql_cliente="INSERT INTO wp_users (user_login,user_nicename,user_email,user_pass) VALUES ('$user_login','$user_nicename','$email_cliente',MD5('$contrasenna'))";
			$cliente_idnew=ejecutarConsulta_retornarID($sql_cliente) or $sw = false;

			$sql_datos_fiscales="INSERT INTO datos_fiscales (cliente_id,razonSocial,rfcCliente,telefono,calle,num_ext,num_int,colonia,poblacion,edoPais,cp,regimenFiscal,num_cuenta,banco) VALUES ('$cliente_idnew','$razonSocial','$rfcCliente','$telefono','$calle','$num_ext','$num_int','$colonia','$poblacion','$edoPais','$cp','$regimenFiscal','$num_cuenta','$banco')";
			ejecutarConsulta($sql_datos_fiscales) or $sw = false;

			$cliente_id=$cliente_idnew;
		} else {
			$sql_borrar_datos_fiscales="DELETE FROM datos_fiscales WHERE cliente_id='$cliente_id'";
			ejecutarConsulta($sql_borrar_datos_fiscales) or $sw = false;

			if($sw==true){
				$sql_datos_fiscales="INSERT INTO datos_fiscales (cliente_id,razonSocial,rfcCliente,telefono,email_cliente,calle,num_ext,num_int,colonia,poblacion,edoPais,cp,regimenFiscal,num_cuenta,banco) VALUES ('$cliente_id','$razonSocial','$rfcCliente','$telefono','$email_cliente','$calle','$num_ext','$num_int','$colonia','$poblacion','$edoPais','$cp','$regimenFiscal','$num_cuenta','$banco')";
				ejecutarConsulta($sql_datos_fiscales) or $sw = false;
			}
		}

		$sql_factura="INSERT INTO factura (serie,metodoPago,claveTipoComprobante,usoCfdi,formadePago,descuento,moneda,tipoCambio,cliente_id,user_id,comentarios,pedido_id,created_at,fecha,status)
		VALUES ('$serie','$metodoPago','$claveTipoComprobante','$usoCfdi','$formadePago','$descuento','$moneda','$tipoCambio','$cliente_id','$usuarioID','$comentarios','$pedido_id',NOW(),NOW(),'Espera')";
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

	public function editar_factura($facturaID,$serie,$metodoPago,$claveTipoComprobante,$usoCfdi,$formadePago,$descuento,$moneda,$tipoCambio,$cliente_id,$razonSocial,$rfcCliente,$usuarioID,$comentarios,$pedido_id,$user_login,$user_nicename,$email_cliente,$contrasenna,$telefono,$calle,$num_ext,$num_int,$colonia,$poblacion,$edoPais,$cp,$regimenFiscal,$num_cuenta,$banco,$producto_id,$descripcion,$cantidad,$precioVenta,$subtotal){
		$sw=true;
		if($cliente_id==0){
			$sql_cliente="INSERT INTO wp_users (user_login,user_nicename,user_email,user_pass) VALUES ('$user_login','$user_nicename','$email_cliente',MD5('$contrasenna'))";
			$cliente_idnew=ejecutarConsulta_retornarID($sql_cliente) or $sw = false;

			$sql_datos_fiscales="INSERT INTO datos_fiscales (cliente_id,razonSocial,rfcCliente,telefono,calle,num_ext,num_int,colonia,poblacion,edoPais,cp,regimenFiscal,num_cuenta,banco) VALUES ('$cliente_idnew','$razonSocial','$rfcCliente','$telefono','$calle','$num_ext','$num_int','$colonia','$poblacion','$edoPais','$cp','$regimenFiscal','$num_cuenta','$banco')";
			ejecutarConsulta($sql_datos_fiscales) or $sw = false;

			$cliente_id=$cliente_idnew;
		} else {
			$sql_borrar_datos_fiscales="DELETE FROM datos_fiscales WHERE cliente_id='$cliente_id'";
			ejecutarConsulta($sql_borrar_datos_fiscales) or $sw = false;

			if($sw==true){
				$sql_datos_fiscales="INSERT INTO datos_fiscales (cliente_id,razonSocial,rfcCliente,telefono,email_cliente,calle,num_ext,num_int,colonia,poblacion,edoPais,cp,regimenFiscal,num_cuenta,banco) VALUES ('$cliente_id','$razonSocial','$rfcCliente','$telefono','$email_cliente','$calle','$num_ext','$num_int','$colonia','$poblacion','$edoPais','$cp','$regimenFiscal','$num_cuenta','$banco')";
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
		$sql="SELECT 'worpress' AS tipo_pedido,p.ID AS pedidoID,p.post_date_gmt AS fecha,p.post_status AS estado,CONCAT(pm_first.meta_value, ' ', pm_last.meta_value) AS cliente,pm_total.meta_value AS total,fac.id AS facturaID,fac.nombrePDF,fac.nombreXML,fac.folioFiscal FROM wp_posts p LEFT JOIN wp_postmeta pm_first ON p.ID = pm_first.post_id AND pm_first.meta_key = '_billing_first_name' LEFT JOIN wp_postmeta pm_last ON p.ID = pm_last.post_id AND pm_last.meta_key = '_billing_last_name' LEFT JOIN wp_postmeta pm_total ON p.ID = pm_total.post_id AND pm_total.meta_key = '_order_total' LEFT JOIN factura fac ON p.ID = fac.pedido_id WHERE p.post_type = 'shop_order' AND p.post_date >= '2025-01-01' GROUP BY p.ID
		UNION ALL
		SELECT 'manual' AS tipo_pedido,ped.id AS pedidoID,ped.created_at AS fecha,ped.status AS estado,IFNULL(dat.razonSocial,ped.nombreCliente) AS cliente,det.totales AS total,fac.id AS facturaID,fac.nombrePDF,fac.nombreXML,fac.folioFiscal FROM pedidos ped INNER JOIN (SELECT pedido_id,SUM(cantidad*precioVenta) AS totales FROM pedidosDetalles GROUP BY pedido_id) det ON ped.id=det.pedido_id LEFT JOIN datos_fiscales dat ON ped.cliente_id = dat.cliente_id LEFT JOIN factura fac ON ped.id = fac.pedido_id";
		return ejecutarConsulta($sql);
	}

	public function mostrar($pedidoID){
		$sql="SELECT p.ID AS pedidoID, p.post_date_gmt AS fecha,p.post_status AS estado,CONCAT(pm_first.meta_value, ' ', pm_last.meta_value) AS nombreCliente,pm_total.meta_value AS total,IFNULL(user.ID,0) AS clienteID,customer.meta_id AS customer_user FROM wp_posts p LEFT JOIN wp_postmeta pm_first ON p.ID = pm_first.post_id AND pm_first.meta_key = '_billing_first_name' LEFT JOIN wp_postmeta pm_last ON p.ID = pm_last.post_id AND pm_last.meta_key = '_billing_last_name' LEFT JOIN wp_postmeta pm_total ON p.ID = pm_total.post_id AND pm_total.meta_key = '_order_total' LEFT JOIN wp_postmeta customer ON p.ID=customer.post_id AND customer.meta_key='_customer_user' LEFT JOIN wp_users user ON user.ID=customer.meta_value WHERE p.ID='$pedidoID' ORDER BY p.post_date DESC";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrar_pedido($pedidoID){
		$sql="SELECT ped.id AS pedidoID,ped.created_at AS fecha,ped.status AS estado,IFNULL(dat.razonSocial,ped.nombreCliente) AS nombreCliente,det.totales AS total,ped.cliente_id AS clienteID,fac.id AS facturaID,fac.nombrePDF,fac.nombreXML,fac.folioFiscal FROM pedidos ped INNER JOIN (SELECT pedido_id,SUM(cantidad*precioVenta) AS totales FROM pedidosDetalles GROUP BY pedido_id) det ON ped.id=det.pedido_id LEFT JOIN datos_fiscales dat ON ped.cliente_id=dat.cliente_id LEFT JOIN factura fac ON ped.id = fac.pedido_id WHERE ped.id='$pedidoID' GROUP BY ped.id";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function ver_factura($facturaID){
		$sql="SELECT fac.id AS facturaID,fac.cliente_id AS clienteID,fac.serie,fac.metodoPago,fac.claveTipoComprobante,fac.usoCfdi,fac.formadePago,fac.descuento,fac.moneda,fac.tipoCambio,fac.cliente_id,fac.user_id,fac.comentarios,fac.created_at,fac.fecha,fac.status,dat.telefono,dat.email_cliente,dat.calle,dat.num_ext,dat.num_int,dat.colonia,dat.poblacion,dat.edoPais,dat.cp,dat.razonSocial,dat.razonSocial AS nombreCliente,dat.rfcCliente,dat.regimenFiscal,dat.num_cuenta,dat.banco FROM factura fac LEFT JOIN datos_fiscales dat ON fac.cliente_id=dat.cliente_id WHERE fac.id='$facturaID'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarDetalles($pedidoID){
		$sql="SELECT pm_variation.meta_value AS producto_id, oi.order_item_name AS nombre_producto, pm_qty.meta_value AS cantidad, sku.meta_value AS sku, pm_total.meta_value AS precioVenta, v.post_title AS variante, IFNULL(img_variante.guid, img_producto.guid) AS imagen,IFNULL(fac.medida_id,1) AS medida_id,IFNULL(med.nombre,'Pieza') AS nombreUnidad,IFNULL(med.unidad,'H87') AS unidad,IFNULL(cla.clave,'25172100') AS clave,IFNULL(cla.nombre,'Componentes de vehículo') AS nombreClave FROM wp_woocommerce_order_items oi LEFT JOIN wp_woocommerce_order_itemmeta pm_qty ON oi.order_item_id = pm_qty.order_item_id AND pm_qty.meta_key = '_qty' LEFT JOIN wp_woocommerce_order_itemmeta product_id_meta ON oi.order_item_id = product_id_meta.order_item_id AND product_id_meta.meta_key = '_product_id' LEFT JOIN wp_postmeta sku ON product_id_meta.meta_value = sku.post_id AND sku.meta_key = '_sku' LEFT JOIN wp_woocommerce_order_itemmeta pm_total ON oi.order_item_id = pm_total.order_item_id AND pm_total.meta_key = '_line_total' LEFT JOIN wp_woocommerce_order_itemmeta pm_variation ON oi.order_item_id = pm_variation.order_item_id AND pm_variation.meta_key = '_variation_id' LEFT JOIN wp_posts v ON pm_variation.meta_value = v.ID LEFT JOIN wp_postmeta thumb_variante ON v.ID = thumb_variante.post_id AND thumb_variante.meta_key = '_thumbnail_id' LEFT JOIN wp_posts img_variante ON thumb_variante.meta_value = img_variante.ID LEFT JOIN wp_postmeta thumb_producto ON product_id_meta.meta_value = thumb_producto.post_id AND thumb_producto.meta_key = '_thumbnail_id' LEFT JOIN wp_posts img_producto ON thumb_producto.meta_value = img_producto.ID LEFT JOIN producto_facturacion fac ON pm_variation.meta_value = fac.producto_id LEFT JOIN unidadesmedida med ON fac.medida_id = med.id LEFT JOIN clavesfactura cla ON fac.clave_id = cla.id WHERE oi.order_id = '$pedidoID' AND oi.order_item_type = 'line_item'";
		return ejecutarConsulta($sql);
	}

	public function mostrarDetalles_pedido($pedidoID){
		$sql="SELECT det.pedido_id,det.producto_id,det.variante_id,det.cantidad,det.precioVenta,det.descripcion,prod.post_title AS nombre_producto,var.post_title AS nombre_variante,img.guid AS imagen,IFNULL(med.nombre,'Pieza') AS nombreUnidad,IFNULL(med.unidad,'H87') AS unidad,IFNULL(cla.clave,'25172100') AS clave,IFNULL(cla.nombre,'Componentes de vehículo') AS nombreClave,pm2.meta_value AS sku FROM pedidosDetalles det INNER JOIN wp_posts prod ON det.producto_id = prod.ID INNER JOIN wp_posts var ON det.variante_id=var.ID LEFT JOIN wp_posts img ON det.producto_id = img.post_parent AND img.post_type = 'attachment' AND img.post_mime_type LIKE 'image/%' LEFT JOIN producto_facturacion fac ON det.producto_id = fac.producto_id LEFT JOIN unidadesmedida med ON fac.medida_id = med.id LEFT JOIN clavesfactura cla ON fac.clave_id = cla.id LEFT JOIN wp_postmeta pm2 ON det.producto_id = pm2.post_id AND pm2.meta_key = '_sku' WHERE det.pedido_id = '$pedidoID' GROUP BY det.id";
		return ejecutarConsulta($sql);
	}

	public function obtener_datos_factura($pedidoID){
		$sql="SELECT p.ID AS pedidoID, p.post_date AS fecha,p.post_status AS estado,IFNULL(dat.razonSocial,UPPER(CONCAT(pm_first.meta_value, ' ', pm_last.meta_value))) AS razonSocial,IFNULL(dat.razonSocial,UPPER(CONCAT(pm_first.meta_value, ' ', pm_last.meta_value))) AS nombreCliente,IFNULL(user.ID,0) AS clienteID,pm_total.meta_value AS total,IFNULL(dat.telefono,pm_phone.meta_value) AS telefono,IFNULL(dat.calle,pm_address.meta_value) AS calle,dat.num_ext,dat.num_int,dat.colonia,IFNULL(dat.poblacion,pm_city.meta_value) AS poblacion,IFNULL(dat.edoPais,pm_state.meta_value) AS edoPais,IFNULL(dat.cp,pm_postcode.meta_value) AS cp,dat.rfcCliente,dat.regimenFiscal,dat.num_cuenta,dat.banco,IFNULL(fac.metodoPago,dat.metodoPago) AS metodoPago,IFNULL(fac.formadePago,dat.formadePago) AS formadePago,IFNULL(fac.usoCfdi,dat.usoCfdi) AS usoCfdi,IFNULL(fac.comentarios,dat.comentarios) AS comentarios,IFNULL(fac.moneda,dat.moneda) AS moneda,IFNULL(fac.tipoCambio,1) AS tipoCambio,IFNULL(dat.email_cliente,user.user_email) AS email_cliente,pm_country.meta_value AS pais FROM wp_posts p LEFT JOIN wp_postmeta pm_first ON p.ID = pm_first.post_id AND pm_first.meta_key = '_billing_first_name' LEFT JOIN wp_postmeta pm_last ON p.ID = pm_last.post_id AND pm_last.meta_key = '_billing_last_name' LEFT JOIN wp_postmeta pm_total ON p.ID = pm_total.post_id AND pm_total.meta_key = '_order_total' LEFT JOIN wp_postmeta pm_phone ON p.ID = pm_phone.post_id AND pm_phone.meta_key = '_billing_phone' LEFT JOIN wp_postmeta pm_address ON p.ID = pm_address.post_id AND pm_address.meta_key = '_billing_address_1' LEFT JOIN wp_postmeta pm_city ON p.ID = pm_city.post_id AND pm_city.meta_key = '_billing_city' LEFT JOIN wp_postmeta pm_state ON p.ID = pm_state.post_id AND pm_state.meta_key = '_billing_state' LEFT JOIN wp_postmeta pm_postcode ON p.ID = pm_postcode.post_id AND pm_postcode.meta_key = '_billing_postcode' LEFT JOIN wp_postmeta pm_email ON p.ID = pm_email.post_id AND pm_email.meta_key = '_billing_email' LEFT JOIN wp_postmeta pm_country ON p.ID = pm_country.post_id AND pm_country.meta_key = '_billing_country' LEFT JOIN wp_postmeta customer ON p.ID=customer.post_id AND customer.meta_key='_customer_user' LEFT JOIN wp_users user ON user.ID=customer.meta_value LEFT JOIN datos_fiscales dat ON user.ID = dat.cliente_id LEFT JOIN factura fac ON p.ID = fac.pedido_id WHERE p.post_type = 'shop_order' AND p.ID='$pedidoID'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function ver_productos(){
		$sql="SELECT prod.ID AS productoID,p.ID AS variante_id,IF(p.post_status= 'publish',1,0) AS activo,p.post_title AS variante,prod.post_title AS nombreProducto,pm1.meta_value AS precioVenta,pm2.meta_value AS sku,prod.ID,img.guid AS imagen,IFNULL(fac.clave_id,1) AS clave_id,IFNULL(fac.medida_id,1) AS medida_id,IFNULL(med.nombre,'Pieza') AS nombreUnidad,IFNULL(med.unidad,'H87') AS unidad,IFNULL(cla.clave,'25172100') AS clave,IFNULL(cla.nombre,'Componentes de vehículo') AS nombreClave
			FROM wp_posts p
			LEFT JOIN wp_posts prod ON p.post_parent = prod.ID AND prod.post_type = 'product'
			LEFT JOIN wp_posts img ON prod.ID = img.post_parent AND img.post_type = 'attachment' AND img.post_mime_type LIKE 'image/%'
			LEFT JOIN wp_postmeta pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_price'
			LEFT JOIN wp_postmeta pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_sku'
			LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
			LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'product_cat'
			LEFT JOIN wp_terms t ON tt.term_id = t.term_id
			LEFT JOIN producto_facturacion fac ON p.ID = fac.producto_id
			LEFT JOIN unidadesmedida med ON fac.medida_id = med.id
			LEFT JOIN clavesfactura cla ON fac.clave_id = cla.id
			LEFT JOIN wp_posts var ON p.ID = var.post_parent AND var.post_type = 'product_variation'
			WHERE p.post_type='product_variation' GROUP BY p.ID";
		return ejecutarConsulta($sql);
	}
}
?>