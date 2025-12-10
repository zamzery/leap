<?php 
require "../config/Conexion.php";

Class Cliente {
	//Implementamos nuestro constructor
	public function __construct(){

	}

	//Implementamos un método para insertar registros
	public function insertar($display_name,$telefono,$calle,$num_ext,$num_int,$colonia,$poblacion,$edoPais,$cp,$razonSocial,$rfcCliente,$regimenFiscal,$num_cuenta,$banco,$metodoPago,$formadePago,$usoCfdi,$comentarios,$constancia,$moneda,$usuarioID){
		$sw=true;
		$sql="INSERT INTO wp_users (display_name,user_registered)
		VALUES ('$display_name',NOW())";
		$clienteIDnew = ejecutarConsulta_retornarID($sql) or $sw = false;

		if($sw==true){
			$sql_fiscales = "INSERT INTO datos_fiscales (cliente_id,telefono,calle,num_ext,num_int,colonia,poblacion,edoPais,cp,razonSocial,rfcCliente,regimenFiscal,num_cuenta,banco,metodoPago,formadePago,usoCfdi,comentarios,constancia,moneda,user_id,created_at,activo) VALUES ('$clienteIDnew','$telefono','$calle','$num_ext','$num_int','$colonia','$poblacion','$edoPais','$cp','$razonSocial','$rfcCliente','$regimenFiscal','$num_cuenta','$banco','$metodoPago','$formadePago','$usoCfdi','$comentarios','$constancia','$moneda','$usuarioID',NOW(),'1')";
			ejecutarConsulta($sql_fiscales) or $sw = false;
			

			$sql_historial = "INSERT INTO historial (usuarioID,created_at,registro,clienteID) SELECT '$usuarioID',NOW(),CONCAT(usr.nombre,' ha agregado al cliente: ', '$display_name'),'$clienteIDnew' FROM users usr WHERE usr.id='$usuarioID'";
			ejecutarConsulta($sql_historial);
		}
		return $sw;
	}

	//Implementamos un método para editar registros
	public function editar($clienteID,$display_name,$telefono,$calle,$num_ext,$num_int,$colonia,$poblacion,$edoPais,$cp,$razonSocial,$rfcCliente,$regimenFiscal,$num_cuenta,$banco,$metodoPago,$formadePago,$usoCfdi,$comentarios,$constancia,$moneda,$usuarioID){
		$sw=true;
		$sql="UPDATE wp_users SET display_name='$display_name' WHERE ID='$clienteID'";
		ejecutarConsulta($sql) or $sw = false;

		if($sw==true){
			$sql_del_fiscales = "DELETE FROM datos_fiscales WHERE cliente_id='$clienteID'";
			ejecutarConsulta($sql_del_fiscales) or $sw = false;

			if($sw==true){
				$sql_fiscales = "INSERT INTO datos_fiscales (cliente_id,telefono,calle,num_ext,num_int,colonia,poblacion,edoPais,cp,razonSocial,rfcCliente,regimenFiscal,num_cuenta,banco,metodoPago,formadePago,usoCfdi,comentarios,constancia,moneda,user_id,created_at,activo) VALUES ('$clienteID','$telefono','$calle','$num_ext','$num_int','$colonia','$poblacion','$edoPais','$cp','$razonSocial','$rfcCliente','$regimenFiscal','$num_cuenta','$banco','$metodoPago','$formadePago','$usoCfdi','$comentarios','$constancia','$moneda','$usuarioID',NOW(),'1')";
				ejecutarConsulta($sql_fiscales) or $sw = false;

				$sql_historial = "INSERT INTO historial (usuarioID,created_at,registro,clienteID) SELECT '$usuarioID',NOW(),CONCAT(usr.nombre,' ha editado al cliente: ', '$display_name'),'$clienteID' FROM users usr INNER JOIN wp_users cli ON cli.ID='$clienteID' WHERE usr.id='$usuarioID'";
				ejecutarConsulta($sql_historial);
			}
		}
		return $sw;
	}

	//Implementamos un método para desactivar clientes
	public function desactivar($clienteID,$usuarioID){
		$sql_historial = "INSERT INTO historial (usuarioID,created_at,registro,clienteID) SELECT '$usuarioID',NOW(),CONCAT(usr.nombre,' ha desactivado a: ', cli.display_name),'$clienteID' FROM users usr INNER JOIN wp_users cli ON cli.ID='$clienteID' WHERE usr.id='$usuarioID'";
		ejecutarConsulta($sql_historial);

		$sql="UPDATE wp_users SET activo='0' WHERE id='$clienteID'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar clientes
	public function activar($clienteID,$usuarioID){
		$sql_historial = "INSERT INTO historial (usuarioID,created_at,registro,clienteID) SELECT '$usuarioID',NOW(),CONCAT(usr.nombre,' ha activado a: ', cli.display_name),'$clienteID' FROM users usr INNER JOIN wp_users cli ON cli.ID='$clienteID' WHERE usr.id='$usuarioID'";
		ejecutarConsulta($sql_historial);

		$sql="UPDATE wp_users SET activo='1' WHERE id='$clienteID'";
		return ejecutarConsulta($sql);
	}

	public function eliminarConstancia($clienteID){
		$sql="UPDATE wp_users SET constancia='' WHERE id='$clienteID'";
		return ejecutarConsulta($sql);
	}

	public function cambia_usuario($clienteID,$nuevoUsuario,$usuarioID){
		$sql_historial = "INSERT INTO historial (usuarioID,created_at,registro,clienteID) SELECT '$usuarioID',NOW(),CONCAT(usr.nombre,' ha cambiado al vendedor asignado de: ', ant.nombre, ' a: ',nue.nombre),'$clienteID' FROM users usr INNER JOIN datos_fiscales fisc ON fisc.cliente_id='$clienteID' INNER JOIN users ant ON ant.id=fisc.user_id INNER JOIN users nue ON nue.id='$nuevoUsuario' WHERE usr.id='$usuarioID'";
		ejecutarConsulta($sql_historial);

		$sql="UPDATE datos_fiscales SET user_id='$nuevoUsuario',updated_at=NOW() WHERE cliente_id='$clienteID'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($clienteID){
		$sql="SELECT cli.ID AS clienteID,cli.display_name,cli.user_login,cli.user_nicename,cli.user_email,fisc.telefono,fisc.calle,fisc.num_ext,fisc.num_int,fisc.colonia,fisc.poblacion,fisc.edoPais,fisc.cp,fisc.razonSocial,fisc.rfcCliente,fisc.regimenFiscal,fisc.num_cuenta,fisc.banco,fisc.metodoPago,fisc.formadePago,fisc.usoCfdi,fisc.comentarios,fisc.constancia,fisc.moneda,IFNULL(fisc.updated_at,fisc.created_at) AS fechaCliente,fisc.user_id,usr.nombre AS nombreVendedor FROM wp_users cli LEFT JOIN datos_fiscales fisc ON cli.ID=fisc.cliente_id LEFT JOIN users usr ON fisc.user_id=usr.id WHERE cli.ID='$clienteID'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar(){
		$sql="SELECT a.ID AS clienteID,a.display_name AS nombreCliente,a.user_login,a.user_nicename,fisc.telefono,a.user_email,a.user_registered,fisc.razonSocial,fisc.num_cuenta,fisc.formadePago,fisc.banco,fisc.calle,fisc.num_ext,fisc.num_int,fisc.colonia,fisc.poblacion,fisc.edoPais,fisc.cp,fisc.rfcCliente,fisc.regimenFiscal,a.activo,IFNULL(fisc.updated_at,fisc.created_at) AS fechaCliente,fisc.constancia,fisc.moneda FROM wp_users a LEFT JOIN datos_fiscales fisc ON a.ID=fisc.cliente_id WHERE a.user_login!='1' AND a.user_login!='11' GROUP BY a.ID";
		return ejecutarConsulta($sql);		
	}

	public function select_cliente(){
		$sql="SELECT a.ID AS clienteID,a.display_name AS nombreCliente,a.user_email AS email_cliente,fisc.razonSocial,fisc.num_cuenta,fisc.formadePago,fisc.banco,fisc.calle,fisc.num_ext,fisc.num_int,fisc.colonia,fisc.poblacion,fisc.edoPais,fisc.cp,fisc.rfcCliente,fisc.regimenFiscal FROM wp_users a LEFT JOIN datos_fiscales fisc ON a.ID=fisc.cliente_id WHERE a.activo='1' AND a.user_login!='1' AND a.user_login!='11' AND a.ID!='0' AND a.display_name!='' GROUP BY a.ID ORDER BY a.display_name ASC";
		return ejecutarConsulta($sql);
	}

	public function select_cliente_factura(){
		$sql="SELECT a.ID AS clienteID,a.display_name AS nombreCliente,a.user_email AS email_cliente,fisc.cliente_id AS clienteID,fisc.telefono,fisc.calle,fisc.num_ext,fisc.num_int,fisc.colonia,fisc.poblacion,fisc.edoPais,fisc.cp,fisc.razonSocial,fisc.rfcCliente,fisc.regimenFiscal,fisc.num_cuenta,fisc.banco,fisc.metodoPago,fisc.formadePago,fisc.usoCfdi,fisc.constancia,fisc.moneda FROM wp_users a LEFT JOIN datos_fiscales fisc ON a.ID=fisc.cliente_id WHERE a.activo='1' AND a.user_login!='1' AND a.user_login!='11' GROUP BY a.display_name ORDER BY a.display_name ASC";
		return ejecutarConsulta($sql);
	}

	public function borrarToken($tokenInvitadoID){
		$sql="UPDATE sessionInvitado SET tokenInvitadoID='VOID',fechaLogout=DATE_SUB(NOW(),INTERVAL +1 hour) WHERE tokenInvitadoID='$tokenInvitadoID'";
		return ejecutarConsulta($sql);
	}

	public function muestraHistorial($clienteID){
		$sql="SELECT his.created_at AS fechaHistorial,usr.nombre,usr.avatar,his.registro,his.clienteID FROM historial his INNER JOIN users usr ON his.usuarioID=usr.id WHERE his.clienteID='$clienteID' AND his.clienteID!=0 ORDER BY his.created_at DESC";
		return ejecutarConsulta($sql);		
	}
}
?>