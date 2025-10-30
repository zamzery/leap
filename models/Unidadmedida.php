<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Unidadmedida {
	//Implementamos nuestro constructor
	public function __construct(){
		
	}

	public function insertar($nombre,$unidad,$abreviatura){
		$sql="INSERT INTO unidadesmedida (nombre,unidad,abreviatura,activo,created_at)
		VALUES ('$nombre','$unidad','$abreviatura','1',NOW())";
		return ejecutarConsulta($sql);
	}

	public function editar($unidadmedidaID,$nombre,$unidad,$abreviatura){
		$sql="UPDATE unidadesmedida SET nombre='$nombre',unidad='$unidad',abreviatura='$abreviatura',updated_at=NOW() WHERE id='$unidadmedidaID'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($unidadmedidaID){
		$sql="UPDATE unidadesmedida SET activo='0',updated_at=NOW() WHERE id='$unidadmedidaID'";
		return ejecutarConsulta($sql);
	}

	public function activar($unidadmedidaID){
		$sql="UPDATE unidadesmedida SET activo='1',updated_at=NOW() WHERE id='$unidadmedidaID'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($unidadmedidaID){
		$sql="SELECT id AS unidadmedidaID,nombre,unidad,abreviatura,activo FROM unidadesmedida WHERE id='$unidadmedidaID'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar(){
		$sql="SELECT id AS unidadmedidaID,nombre,unidad,abreviatura,activo FROM unidadesmedida";
		return ejecutarConsulta($sql);		
	}

	public function select_unidadmedida(){
		$sql="SELECT id AS unidadmedidaID,nombre,unidad,abreviatura,activo FROM unidadesmedida WHERE activo='1' ORDER BY nombre ASC";
		return ejecutarConsulta($sql);
	}
}
?>