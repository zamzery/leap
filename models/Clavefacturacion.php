<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Clavefacturacion {
	//Implementamos nuestro constructor
	public function __construct(){
		
	}

	public function insertar($nombre,$clave){
		$sql="INSERT INTO clavesfactura (nombre,clave,activo,created_at)
		VALUES ('$nombre','$clave','1',NOW())";
		return ejecutarConsulta($sql);
	}

	public function editar($claveID,$nombre,$clave){
		$sql="UPDATE clavesfactura SET nombre='$nombre',clave='$clave',updated_at=NOW() WHERE id='$claveID'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($claveID){
		$sql="UPDATE clavesfactura SET activo='0',updated_at=NOW() WHERE id='$claveID'";
		return ejecutarConsulta($sql);
	}

	public function activar($claveID){
		$sql="UPDATE clavesfactura SET activo='1',updated_at=NOW() WHERE id='$claveID'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($claveID){
		$sql="SELECT id AS claveID,nombre,clave,activo FROM clavesfactura WHERE id='$claveID'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar(){
		$sql="SELECT id AS claveID,nombre,clave,activo FROM clavesfactura";
		return ejecutarConsulta($sql);		
	}

	public function select_clavefacturacion(){
		$sql="SELECT id AS claveID,nombre,clave,activo FROM clavesfactura WHERE activo='1' ORDER BY nombre ASC";
		return ejecutarConsulta($sql);
	}
}
?>