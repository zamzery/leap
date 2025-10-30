<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Cargo {
	//Implementamos nuestro constructor
	public function __construct(){
		
	}

	public function insertar($nombre,$descripcion,$permisoID,$ver,$editar,$historial){
		$sw=true;
		$sql="INSERT INTO cargos (nombre,descripcion,activo,created_at)
		VALUES ('$nombre','$descripcion','1',NOW())";
		$cargoIDnew=ejecutarConsulta_retornarID($sql);

		if($cargoIDnew==''){
			$sw = false;
		} else {
			$num=0;
			if($permisoID){
				while ($num < count($permisoID)){
					$sql_detalle = "INSERT INTO permisos_cargo(cargoID,permisoID,ver,editar,historial) VALUES('$cargoIDnew', '$permisoID[$num]','$ver[$num]','$editar[$num]','$historial[$num]')";
					ejecutarConsulta($sql_detalle) or $sw = false;
					$num=$num + 1;
				}
			}
		}
		return $sw;
	}

	public function editar($cargoID,$nombre,$descripcion,$permisoID,$ver,$editar,$historial){
		$sw=true;
		$sql="UPDATE cargos SET nombre='$nombre',descripcion='$descripcion',updated_at=NOW() WHERE id='$cargoID'";
		ejecutarConsulta($sql) or $sw = false;

		if($sw){
			$num=0;
			if($permisoID){
				$sqldel="DELETE FROM permisos_cargo WHERE cargoID='$cargoID'";
				ejecutarConsulta($sqldel) or $sw=false;

				while ($num < count($permisoID)){
					$sql_detalle = "INSERT INTO permisos_cargo(cargoID,permisoID,ver,editar,historial) VALUES('$cargoID', '$permisoID[$num]','$ver[$num]','$editar[$num]','$historial[$num]')";
					ejecutarConsulta($sql_detalle) or $sw = false;
					$num=$num + 1;
				}
			}
		}
		return $sw;
	}

	public function desactivar($cargoID){
		$sql="UPDATE cargos SET activo='0',updated_at=NOW() WHERE id='$cargoID'";
		return ejecutarConsulta($sql);
	}

	public function activar($cargoID){
		$sql="UPDATE cargos SET activo='1',updated_at=NOW() WHERE id='$cargoID'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($cargoID){
		$sql="SELECT id AS cargoID,nombre,descripcion,activo FROM cargos WHERE id='$cargoID'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar(){
		$sql="SELECT id AS cargoID,nombre,descripcion,activo FROM cargos";
		return ejecutarConsulta($sql);		
	}

	public function select_cargo(){
		$sql="SELECT id AS cargoID,nombre,descripcion,activo FROM cargos WHERE activo='1' ORDER BY nombre ASC";
		return ejecutarConsulta($sql);
	}

	public function listar_permisoMarcado($cargoID){
		$sql="SELECT per.id AS permisoID,per.permiso,per.nombrePermiso,per.orden,per.principal,per.grupo,IFNULL(cont.contador,0) AS contador,marcado.ver,marcado.editar,marcado.historial,per.ver AS verPermiso,per.editar AS editarPermiso,per.historial AS historialPermiso FROM permisos per LEFT JOIN (SELECT COUNT(grupo) AS contador,id FROM permisos GROUP BY grupo ORDER BY principal) cont ON per.id=cont.id LEFT JOIN (SELECT cargoID,permisoID,ver,editar,historial FROM permisos_cargo WHERE cargoID='$cargoID') marcado ON per.id=marcado.permisoID ORDER BY per.orden ASC";
		return ejecutarConsulta($sql);
	}

	public function listarmarcados($cargoID){
		$sql="SELECT per.id,per.permiso,IFNULL(marcado.ver,0) AS ver,IFNULL(marcado.editar,0) AS editar,IFNULL(marcado.historial,0) AS historial FROM permisos per LEFT JOIN (SELECT COUNT(grupo) AS contador,id FROM permisos GROUP BY grupo ORDER BY orden) cont ON per.id=cont.id LEFT JOIN (SELECT cargoID,permisoID,ver,editar,historial FROM permisos_cargo WHERE cargoID='$cargoID') marcado ON per.id=marcado.permisoID ORDER BY per.id";
		return ejecutarConsulta($sql);
	}
}
?>