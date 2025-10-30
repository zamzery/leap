<?php 
require_once "../models/Auth.php";
$autorizacion=new Auth();

$emailrec=isset($_POST["emailrec"])? limpiarCadena($_POST["emailrec"]):"";
$tokenrec=isset($_POST["tokenrec"])? limpiarCadena($_POST["tokenrec"]):"";
$claverec=isset($_POST["claverec"])? limpiarCadena($_POST["claverec"]):"";

switch ($_GET["op"]){
	case 'verificar':
		$respuesta='';
        //Hash SHA256 en la contraseña
        $clavehash=hash("SHA256",$claverec);
		$rspta=$autorizacion->verificar_email_token($emailrec,$tokenrec);
		if($rspta){
			while ($reg=$rspta->fetch_object()){
				if($reg->email!=''){
					$rspta2=$autorizacion->editar_clave($reg->email,$clavehash);
					$respuesta= ($rspta2)? "Contraseña actualizada" : "No se pudo actualizar la contraseña";
				} else {
					$respuesta = "El token ha expirado";
				}
			}
		} 
		echo $respuesta;
	break;
}
?>