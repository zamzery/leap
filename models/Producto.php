<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Producto {
	//Implementamos nuestro constructor
	public function __construct(){
		
	}

	public function editar($productoID,$nombre,$precioVenta,$unidadmedida_id,$clavefacturacion_id,$observaciones,$imagen){
		$sql="UPDATE wp_post SET post_title='$nombre',precioVenta='$precioVenta',unidadmedida_id='$unidadmedida_id',clavefacturacion_id='$clavefacturacion_id',post_content='$observaciones',imagen='$imagen',updated_at=NOW() WHERE product_id='$productoID'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($productoID){
		$sql="SELECT p.ID AS productoID,p.post_type,p.post_status,p.post_title AS nombre,pm1.meta_value AS precioVenta,pm2.meta_value AS sku,p.ID,t.name AS categoria,img.guid AS imagen,p.post_content AS observaciones,IFNULL(fac.clave_id,1) AS clave_id,IFNULL(fac.medida_id,1) AS medida_id,IFNULL(med.nombre,'Pieza') AS nombreUnidad,IFNULL(med.unidad,'H87') AS unidad,IFNULL(cla.clave,'25172100') AS nombreClave
			FROM wp_posts p
			LEFT JOIN wp_posts img ON p.ID = img.post_parent AND img.post_type = 'attachment' AND img.post_mime_type LIKE 'image/%'
			LEFT JOIN wp_postmeta pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_price'
			LEFT JOIN wp_postmeta pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_sku'
			LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
			LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'product_cat'
			LEFT JOIN wp_terms t ON tt.term_id = t.term_id
			LEFT JOIN producto_facturacion fac ON p.ID = fac.producto_id
			LEFT JOIN unidadesmedida med ON fac.medida_id = med.id
			LEFT JOIN clavesfactura cla ON fac.clave_id = cla.id
			WHERE p.ID='$productoID' GROUP BY p.ID";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar_variante($productoID){
		$sql="SELECT p.ID AS productoID,p.post_type,IF(p.post_status= 'publish',1,0) AS activo,p.post_title AS nombre,pm1.meta_value AS precioVenta,pm2.meta_value AS sku,p.ID,t.name AS categoria,img.guid AS imagen,IFNULL(fac.clave_id,1) AS clave_id,IFNULL(fac.medida_id,1) AS medida_id,IFNULL(med.nombre,'Pieza') AS nombreUnidad,IFNULL(med.unidad,'H87') AS unidad,IFNULL(cla.clave,'25172100') AS nombreClave
			FROM wp_posts p
			LEFT JOIN wp_posts img ON p.ID = img.post_parent AND img.post_type = 'attachment' AND img.post_mime_type LIKE 'image/%'
			LEFT JOIN wp_postmeta pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_price'
			LEFT JOIN wp_postmeta pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_sku'
			LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
			LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'product_cat'
			LEFT JOIN wp_terms t ON tt.term_id = t.term_id
			LEFT JOIN producto_facturacion fac ON p.ID = fac.producto_id
			LEFT JOIN unidadesmedida med ON fac.medida_id = med.id
			LEFT JOIN clavesfactura cla ON fac.clave_id = cla.id
			WHERE p.post_parent='$productoID' AND p.post_type='product_variation' GROUP BY p.ID";
		return ejecutarConsulta($sql);
	}

	public function listar(){
		$sql="SELECT p.ID AS productoID,p.post_type,IF(p.post_status= 'publish',1,0) AS activo,p.post_title AS nombre,pm1.meta_value AS precioVenta,pm2.meta_value AS sku,p.ID,t.name AS categoria,img.guid AS imagen,IFNULL(fac.clave_id,1) AS clave_id,IFNULL(fac.medida_id,1) AS medida_id,IFNULL(med.nombre,'Pieza') AS nombreUnidad,IFNULL(med.unidad,'H87') AS unidad,IFNULL(cla.clave,'25172100') AS clave,IFNULL(cla.nombre,'Componentes de vehículo') AS nombreClave,IFNULL(var.ID,0) AS varianteID
			FROM wp_posts p
			LEFT JOIN wp_posts img ON p.ID = img.post_parent AND img.post_type = 'attachment' AND img.post_mime_type LIKE 'image/%'
			LEFT JOIN wp_postmeta pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_price'
			LEFT JOIN wp_postmeta pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_sku'
			LEFT JOIN wp_term_relationships tr ON p.ID = tr.object_id
			LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'product_cat'
			LEFT JOIN wp_terms t ON tt.term_id = t.term_id
			LEFT JOIN producto_facturacion fac ON p.ID = fac.producto_id
			LEFT JOIN unidadesmedida med ON fac.medida_id = med.id
			LEFT JOIN clavesfactura cla ON fac.clave_id = cla.id
			LEFT JOIN wp_posts var ON p.ID = var.post_parent AND var.post_type = 'product_variation'
			WHERE p.post_type = 'product' AND p.post_status = 'publish' GROUP BY p.ID";
		return ejecutarConsulta($sql);		
	}

	public function select_producto(){
		$sql="SELECT id AS productoID,post_title AS nombre,activo FROM wp_post WHERE activo='1' ORDER BY post_title ASC";
		return ejecutarConsulta($sql);
	}
}
?>