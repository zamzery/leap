<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class User {
	//Implementamos nuestro constructor
	public function __construct(){

	}

	//Implementamos un método para insertar Usuarios
	public function insertar($nombre,$email,$clavehash,$cargo_id,$maestro,$telefono,$direccion,$color,$colorText,$redireccion,$avatar,$permisoID,$ver,$editar,$historial){
		$sw=true;
		$sql="INSERT INTO users (nombre,email,clave,cargo_id,maestro,telefono,direccion,color,colorText,redireccion,avatar,activo,created_at)
		VALUES ('$nombre','$email','$clavehash','$cargo_id','$maestro','$telefono','$direccion','$color','$colorText','$redireccion','$avatar','1',NOW())";
		$usuarioIDnew=ejecutarConsulta_retornarID($sql) or $sw = false;

		if($sw){
			$num=0;
			if($permisoID){
				while ($num < count($permisoID)){
					$sql_detalle = "INSERT INTO permisos_usuario(usuarioID,permisoID,ver,editar,historial) VALUES('$usuarioIDnew', '$permisoID[$num]','$ver[$num]','$editar[$num]','$historial[$num]')";
					ejecutarConsulta($sql_detalle);
					$num=$num + 1;
				}
			}
		}
		return $sw;
	}

	//Implementamos un método para editar Usuarios
	public function editar($usuarioID,$nombre,$email,$clavehash,$cargo_id,$maestro,$telefono,$direccion,$color,$colorText,$redireccion,$avatar,$permisoID,$ver,$editar,$historial){
		$sw=true;
		$sql="UPDATE users SET nombre='$nombre',email='$email',clave='$clavehash',cargo_id='$cargo_id',maestro='$maestro',telefono='$telefono',direccion='$direccion',color='$color',colorText='$colorText',redireccion='$redireccion',avatar='$avatar' WHERE id='$usuarioID'";
		ejecutarConsulta($sql) or $sw=false;

		if($permisoID){
			$sqldel="DELETE FROM permisos_usuario WHERE usuarioID='$usuarioID'";
			ejecutarConsulta($sqldel);

			$num=0;
			while ($num < count($permisoID)){
				$sql_detalle = "INSERT INTO permisos_usuario(usuarioID,permisoID,ver,editar,historial) VALUES('$usuarioID', '$permisoID[$num]','$ver[$num]','$editar[$num]','$historial[$num]')";
				ejecutarConsulta($sql_detalle);
				$num=$num + 1;
			}
		}
		return $sw;
	}
	
	public function editar_sinpass($usuarioID,$nombre,$email,$cargo_id,$maestro,$telefono,$direccion,$color,$colorText,$redireccion,$avatar,$permisoID,$ver,$editar,$historial){
		$sw=true;
		$sql="UPDATE users SET nombre='$nombre',email='$email',cargo_id='$cargo_id',maestro='$maestro',telefono='$telefono',direccion='$direccion',color='$color',colorText='$colorText',redireccion='$redireccion',avatar='$avatar',updated_at=NOW() WHERE id='$usuarioID'";
		ejecutarConsulta($sql) or $sw = false;

		if($sw==true){
			$sqldel="DELETE FROM permisos_usuario WHERE usuarioID='$usuarioID'";
			ejecutarConsulta($sqldel);
		}
		if($sw==true){
			$num=0;
			while ($num < count($permisoID)){
				$sql_detalle = "INSERT INTO permisos_usuario(usuarioID,permisoID,ver,editar,historial) VALUES('$usuarioID','$permisoID[$num]','$ver[$num]','$editar[$num]','$historial[$num]')";
				ejecutarConsulta($sql_detalle);
				$num=$num + 1;
			}
		}
		return $sw;
	}

	public function editar_sinpermisos($usuarioID,$nombre,$telefono,$direccion,$clavehash,$avatar,$color,$colorText){
		$sql="UPDATE users SET nombre='$nombre',telefono='$telefono',direccion='$direccion',color='$color',colorText='$colorText',clave='$clavehash',avatar='$avatar',updated_at=NOW() WHERE id='$usuarioID'";
		return ejecutarConsulta($sql);
	}

	public function editar_sinpermisos_sinpass($usuarioID,$nombre,$telefono,$direccion,$avatar,$color,$colorText){
		$sql="UPDATE users SET nombre='$nombre',telefono='$telefono',direccion='$direccion',color='$color',colorText='$colorText',avatar='$avatar',updated_at=NOW() WHERE id='$usuarioID'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para desactivar los Usuarios
	public function desactivar($usuarioID){
		$sql="UPDATE users SET activo='0',updated_at=NOW() WHERE id='$usuarioID'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar los Usuarios
	public function activar($usuarioID){
		$sql="UPDATE users SET activo='1',updated_at=NOW() WHERE id='$usuarioID'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($usuarioID){
		$sql="SELECT * FROM users WHERE id='$usuarioID'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar(){
		$sql="SELECT usr.id,usr.nombre,usr.email,usr.telefono,usr.color,usr.colorText,usr.avatar,usr.activo,crg.nombre AS nombreCargo FROM users usr INNER JOIN cargos crg ON usr.cargo_id=crg.id ORDER BY usr.id DESC";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro en el select
	public function select_usuario(){
		$sql="SELECT id,nombre FROM users WHERE activo='1' AND id!='1'";
		return ejecutarConsulta($sql);
	}

	public function select_maestro(){
		$sql="SELECT id,nombre FROM users WHERE activo='1' AND maestro='1' AND id!='1'";
		return ejecutarConsulta($sql);
	}

	public function select_vendedor(){
		$sql="SELECT id,nombre FROM users WHERE activo='1' AND maestro!='1' AND id!='1'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los permisos marcados
	public function listar_permisoMarcado($usuarioID){
		$sql="SELECT per.id AS permisoID,per.permiso,per.nombrePermiso,per.orden,per.principal,per.grupo,IFNULL(cont.contador,0) AS contador,IFNULL(marcado.ver,0) AS ver,IFNULL(marcado.editar,0) AS editar,IFNULL(marcado.historial,0) AS historial,per.ver AS verPermiso,per.editar AS editarPermiso,per.historial AS historialPermiso FROM permisos per LEFT JOIN (SELECT COUNT(grupo) AS contador,id FROM permisos GROUP BY grupo ORDER BY principal) cont ON per.id=cont.id LEFT JOIN (SELECT usuarioID,permisoID,ver,editar,historial FROM permisos_usuario WHERE usuarioID='$usuarioID') marcado ON per.id=marcado.permisoID GROUP BY per.id ORDER BY per.orden ASC";
		return ejecutarConsulta($sql);
	}

	public function listarmarcados($usuarioID){
		$sql="SELECT per.id,per.permiso,IFNULL(marcado.ver,0) AS ver,IFNULL(marcado.editar,0) AS editar,IFNULL(marcado.historial,0) AS historial FROM permisos per LEFT JOIN (SELECT COUNT(grupo) AS contador,id FROM permisos GROUP BY grupo ORDER BY orden) cont ON per.id=cont.id LEFT JOIN (SELECT usuarioID,permisoID,ver,editar,historial FROM permisos_usuario WHERE usuarioID='$usuarioID') marcado ON per.id=marcado.permisoID GROUP BY per.id ORDER BY per.id";
		return ejecutarConsulta($sql);
	}

	//Función para verificar el acceso al sistema
	public function verificar($login,$clave){
		$sql="SELECT * FROM users WHERE email='$login' AND clave='$clave' AND activo='1'"; 
		return ejecutarConsulta($sql);
	}

	public function email_existe($email){
		$sql="SELECT email FROM users WHERE email='$email' AND activo='1'"; 
		return ejecutarConsulta($sql);
	}
}
?>