<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Metodopago {
	//Implementamos nuestro constructor
	public function __construct(){
		
	}

	public function insertar($nombre,$codigo,$descripcion){
		$sql="INSERT INTO metodopagos (nombre,codigo,descripcion,activo,created_at)
		VALUES ('$nombre','$codigo','$descripcion','1',NOW())";
		return ejecutarConsulta($sql);
	}

	public function editar($metodopagoID,$nombre,$codigo,$descripcion){
		$sql="UPDATE metodopagos SET nombre='$nombre',codigo='$codigo',descripcion='$descripcion',updated_at=NOW() WHERE id='$metodopagoID'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($metodopagoID){
		$sql="UPDATE metodopagos SET activo='0',updated_at=NOW() WHERE id='$metodopagoID'";
		return ejecutarConsulta($sql);
	}

	public function activar($metodopagoID){
		$sql="UPDATE metodopagos SET activo='1',updated_at=NOW() WHERE id='$metodopagoID'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($metodopagoID){
		$sql="SELECT id AS metodopagoID,nombre,codigo,descripcion,activo FROM metodopagos WHERE id='$metodopagoID'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar(){
		$sql="SELECT id AS metodopagoID,nombre,codigo,descripcion,activo FROM metodopagos";
		return ejecutarConsulta($sql);		
	}

	public function select_metodopago(){
		$sql="SELECT id AS metodopagoID,nombre,codigo,descripcion,activo FROM metodopagos WHERE activo='1' ORDER BY nombre ASC";
		return ejecutarConsulta($sql);
	}
}
?>