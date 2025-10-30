<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Auth {
	//Implementamos nuestro constructor
	public function __construct(){

	}

	//Función para verificar el acceso al sistema
	public function verificar_email_token($email,$token){
		$sql="SELECT email FROM password_reset_tokens WHERE email='$email' AND token='$token' AND created_at >= NOW() - INTERVAL 1 DAY"; 
		return ejecutarConsulta($sql);
	}

	public function recuperar_contrasenna($email,$token){
		$sw=true;
		$sqldel="DELETE FROM password_reset_tokens WHERE email='$email'";
		ejecutarConsulta($sqldel) or $sw=false;

		if($sw=true){
			$sql="INSERT INTO password_reset_tokens (email,token,created_at)
			VALUES ('$email','$token',NOW())";
			return ejecutarConsulta($sql);
		}
	}

	public function email_existe($email){
		$sql="SELECT email FROM users WHERE email='$email' AND activo='1'"; 
		return ejecutarConsulta($sql);
	}

	public function editar_clave($email,$clavehash){
		$sw=true;
		$sql="UPDATE users SET clave='$clavehash' WHERE email='$email'";
		ejecutarConsulta($sql) or $sw=false;

		if($sw==true){
			$sqldel="DELETE FROM password_reset_tokens WHERE email='$email'";
			ejecutarConsulta($sqldel);
		}
		return $sw;
	}
}
?>