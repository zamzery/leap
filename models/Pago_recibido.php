<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Pago_recibido {
	//Implementamos nuestro constructor
	public function __construct(){
		
	}

	public function insertar($cliente_id,$fechaPago,$metodopago_id,$banco,$numero_cuenta,$parcialidad,$saldoAnterior,$pago,$comprobantePago,$saldoRestante,$usuarioID){
		$sw = true;
		$sql="INSERT INTO pagos (cliente_id,fechaPago,metodopago_id,banco,numero_cuenta,parcialidad,saldoAnterior,pago,comprobantePago,saldoRestante,user_id,activo,created_at)
		VALUES ('$cliente_id','$fechaPago','$metodopago_id','$banco','$numero_cuenta','$parcialidad','$saldoAnterior','$pago','$comprobantePago','$saldoRestante','$usuarioID','1',NOW())";
		$pagoIDnew = ejecutarConsulta_retornarID($sql) or $sw = false;
		if($sw){
			$sql_historial = "INSERT INTO historial (usuarioID,created_at,registro,pagoID) SELECT '$usuarioID',NOW(),CONCAT(usr.nombre,' ha agregado el pago: #', '$pagoIDnew',', con $','$pago'),'$pagoIDnew' FROM users usr WHERE usr.id='$usuarioID'";
			ejecutarConsulta($sql_historial) or $sw = false;
		}
		return $sw;
	}

	public function editar($pagoID,$cliente_id,$fechaPago,$metodopago_id,$banco,$numero_cuenta,$parcialidad,$saldoAnterior,$pago,$comprobantePago,$saldoRestante,$usuarioID){
		$sw = true;
		$sql="UPDATE pagos SET cliente_id='$cliente_id',fechaPago='$fechaPago',metodopago_id='$metodopago_id',banco='$banco',numero_cuenta='$numero_cuenta',parcialidad='$parcialidad',saldoAnterior='$saldoAnterior',pago='$pago',comprobantePago='$comprobantePago',saldoRestante='$saldoRestante',user_id='$usuarioID',updated_at=NOW() WHERE id='$pagoID'";
		ejecutarConsulta($sql) or $sw = false;
		if($sw){
			$sql_historial = "INSERT INTO historial (usuarioID,created_at,registro,pagoID) SELECT '$usuarioID',NOW(),CONCAT(usr.nombre,' ha editado el pago: #', '$pagoID',', con $','$pago'),'$pagoID' FROM users usr WHERE usr.id='$usuarioID'";
			ejecutarConsulta($sql_historial) or $sw = false;
		}
		return $sw;
	}

	public function desactivar($pagoID,$usuarioID){
		$sql_historial = "INSERT INTO historial (usuarioID,created_at,registro,pagoID) SELECT '$usuarioID',NOW(),CONCAT(usr.nombre,' ha desactivado el pago: #', '$pagoID',', con $',pag.pago),'$pagoID' FROM users usr INNER JOIN pagos pag ON pag.id='$pagoID' WHERE usr.id='$usuarioID'";
		ejecutarConsulta($sql_historial);

		$sql="UPDATE pagos SET activo='0',updated_at=NOW() WHERE id='$pagoID'";
		return ejecutarConsulta($sql);
	}

	public function activar($pagoID,$usuarioID){
		$sql_historial = "INSERT INTO historial (usuarioID,created_at,registro,pagoID) SELECT '$usuarioID',NOW(),CONCAT(usr.nombre,' ha activado el pago: #', '$pagoID',', con $',pag.pago),'$pagoID' FROM users usr INNER JOIN pagos pag ON pag.id='$pagoID' WHERE usr.id='$usuarioID'";
		ejecutarConsulta($sql_historial);

		$sql="UPDATE pagos SET activo='1',updated_at=NOW() WHERE id='$pagoID'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($pagoID){
		$sql="SELECT id AS pagoID,cliente_id,fechaPago,metodopago_id,banco,numero_cuenta,parcialidad,saldoAnterior,pago,comprobantePago,saldoRestante,activo FROM pagos WHERE id='$pagoID'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar(){
		$sql="SELECT pag.id AS pagoID,pag.cliente_id,pag.fechaPago,pag.metodopago_id,pag.banco,pag.numero_cuenta,pag.parcialidad,pag.saldoAnterior,pag.pago,pag.comprobantePago,pag.saldoRestante,pag.activo,pag.created_at,cli.display_name AS nombreCliente,met.nombre AS nombreMetodo FROM pagos pag INNER JOIN wp_users cli ON cli.id=pag.cliente_id INNER JOIN metodopagos met ON met.id=pag.metodopago_id ORDER BY pag.id DESC";
		return ejecutarConsulta($sql);		
	}

	public function select_pago(){
		$sql="SELECT id AS pagoID,cliente_id,fechaPago,metodopago_id,banco,numero_cuenta,parcialidad,saldoAnterior,pago,saldoRestante,activo,created_at FROM pagos WHERE activo='1' ORDER BY nombre ASC";
		return ejecutarConsulta($sql);
	}

	public function muestraHistorial($pagoID){
		$sql="SELECT his.created_at AS fechaHistorial,usr.nombre,usr.avatar,his.registro,his.pagoID FROM historial his INNER JOIN users usr ON his.usuarioID=usr.id WHERE his.pagoID='$pagoID' AND his.pagoID!=0 ORDER BY his.created_at DESC";
		return ejecutarConsulta($sql);		
	}
}
?>