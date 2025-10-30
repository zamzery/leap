<?php 
if (strlen(session_id()) < 1) 
	session_start();

	use PHPMailer\PHPMailer\PHPMailer;
	require '../public/vendor/phpMail/autoload.php';

require_once "../models/User.php";
$usuarios=new User();

$usuarioID=isset($_POST["usuarioID"])? limpiarCadena($_POST["usuarioID"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$email=isset($_POST["email"])? limpiarCadena($_POST["email"]):"";
$clave=isset($_POST["clave"])? limpiarCadena($_POST["clave"]):"";
$cargo_id=isset($_POST["cargo_id"])? limpiarCadena($_POST["cargo_id"]):"";
$maestro=isset($_POST["maestro"])? limpiarCadena($_POST["maestro"]):"";
$telefono=isset($_POST["telefono"])? limpiarCadena($_POST["telefono"]):"";
$direccion=isset($_POST["direccion"])? limpiarCadena($_POST["direccion"]):"";
$color=isset($_POST["color"])? limpiarCadena($_POST["color"]):"";
$colorText=isset($_POST["colorText"])? limpiarCadena($_POST["colorText"]):"";
$redireccion=isset($_POST["redireccion"])? limpiarCadena($_POST["redireccion"]):"";
$avatar=isset($_POST["avatar"])? limpiarCadena($_POST["avatar"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (!file_exists($_FILES['avatar']['tmp_name']) || !is_uploaded_file($_FILES['avatar']['tmp_name'])){
			$avatar=$_POST["avataractual"];
		} else {
			$ext = explode(".", $_FILES["avatar"]["name"]);
			if ($_FILES['avatar']['type'] == "image/jpg" || $_FILES['avatar']['type'] == "image/jpeg" || $_FILES['avatar']['type'] == "image/png" || $_FILES['avatar']['type'] == "image/gif"){
				$avatar = round(microtime(true)) . '.' . end($ext);
				move_uploaded_file($_FILES["avatar"]["tmp_name"], "../public/files/avatars/" . $avatar);
			}
		}
		//Hash SHA256 en la contraseña
		$clavehash=hash("SHA256",$clave);
		if (empty($usuarioID)){
			$rspta=$usuarios->insertar($nombre,$email,$clavehash,$cargo_id,$maestro,$telefono,$direccion,$color,$colorText,$redireccion,$avatar,$_POST['permiso'],$_POST['ver'],$_POST['editar'],$_POST['historial']);
			echo $rspta ? "Usuario registrado" : "No se pudieron registrar todos los datos del usuario";
		} else {
			if(empty($clave)){
				$rspta=$usuarios->editar_sinpass($usuarioID,$nombre,$email,$cargo_id,$maestro,$telefono,$direccion,$color,$colorText,$redireccion,$avatar,$_POST['permiso'],$_POST['ver'],$_POST['editar'],$_POST['historial']);
				echo $rspta ? "Usuario actualizado" : "El Usuario no se pudo actualizar";
			} else {
				$rspta=$usuarios->editar($usuarioID,$nombre,$email,$clavehash,$cargo_id,$maestro,$telefono,$direccion,$color,$colorText,$redireccion,$avatar,$_POST['permiso'],$_POST['ver'],$_POST['editar'],$_POST['historial']);
				echo $rspta ? "Usuario actualizado" : "El Usuario no se pudo actualizar";
			}
		}
	break;

	case 'editar_sinpermisos':
		if (!file_exists($_FILES['avatar']['tmp_name']) || !is_uploaded_file($_FILES['avatar']['tmp_name'])){
			$avatar=$_POST["avataractual"];
		} else {
			$ext = explode(".", $_FILES["avatar"]["name"]);
			if ($_FILES['avatar']['type'] == "image/jpg" || $_FILES['avatar']['type'] == "image/jpeg" || $_FILES['avatar']['type'] == "image/png" || $_FILES['avatar']['type'] == "image/gif"){
				$avatar = round(microtime(true)) . '.' . end($ext);
				move_uploaded_file($_FILES["avatar"]["tmp_name"], "../public/files/avatars/" . $avatar);
			}
		}
		if(empty($clave)){
			$rspta=$usuarios->editar_sinpermisos_sinpass($usuarioID,$nombre,$telefono,$direccion,$avatar,$color,$colorText);
			echo $rspta ? "Usuario actualizado" : "El Usuario no se pudo actualizar";
		} else {
			$clavehash=hash("SHA256",$clave);
			$rspta=$usuarios->editar_sinpermisos($usuarioID,$nombre,$telefono,$direccion,$clavehash,$avatar,$color,$colorText);
			echo $rspta ? "Usuario actualizado" : "El Usuario no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$usuarios->desactivar($usuarioID);
		echo $rspta ? "Usuario Desactivado" : "El Usuario no se puede desactivar";
	break;

	case 'activar':
		$rspta=$usuarios->activar($usuarioID);
		echo $rspta ? "Usuario activado" : "El Usuario no se puede activar";
	break;

	case 'mostrar':
		$rspta=$usuarios->mostrar($usuarioID);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);
	break;

	case 'select_vendedor':
		$rspta=$usuarios->select_vendedor();
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->id.'">'.$reg->nombre.'</option>';
		}
	break;

	case 'select_usuario':
		$rspta=$usuarios->select_usuario();
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->id.'">'.$reg->nombre.'</option>';
		}
	break;

	case 'select_usuario_todo':
		$rspta=$usuarios->select_usuario();
		echo '<option value="0" selected>NINGUNO</option>';
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->id.'">'.$reg->nombre.'</option>';
		}
	break;

	case 'select_maestro':
		$rspta=$usuarios->select_maestro();
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->id.'">'.$reg->nombre.'</option>';
		}
	break;

	case 'select_maestro_todos':
		$rspta=$usuarios->select_maestro();
		echo '<option value="0" selected>TODOS</option>';
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->id.'">'.$reg->nombre.'</option>';
		}
	break;

	case 'recuperar_contrasenna':
		$emailrec = $_POST['emailrec'];
		require_once "../models/Auth.php";
		$autorizacion=new Auth();

		$rspta=$usuarios->email_existe($emailrec);
		if($rspta){
			while ($reg=$rspta->fetch_object()){
				if($reg->email!=''){
					$token = bin2hex(random_bytes(32));
					$rspta2=$autorizacion->recuperar_contrasenna($reg->email,$token);
					if($rspta2){
						$mail = new PHPMailer;
						try {
							require_once "../credenciales.php";
							$mail->FromName = mb_convert_encoding("Leap Works", 'ISO-8859-1', 'UTF-8');
							$mail->AddReplyTo ("facturacion@leap.works",mb_convert_encoding("Leap Works", 'ISO-8859-1', 'UTF-8'));
							$mail->Subject = mb_convert_encoding("Recuperación de contraseña", 'ISO-8859-1', 'UTF-8');
							$mail->AddAddress($emailrec);
							$mail->AddBCC("zamzery@gmail.com");
							$mail->AddEmbeddedImage("../public/images/Logo.jpg", "Logo", "Logo.jpg");

							$body  = "<img src=\"cid:Logo\" /><br><font size='4' face='Arial'><p><strong>Estimado Usuario</strong><br>";
							$body .= "<p> == SE HA SOLICITADO EL CAMBIO DE CONTRASEÑA ==<p/>";
							$body .= mb_convert_encoding("<p>Si usted no realizó esta acción, por favor ignore este mensaje.</p><br><br>", 'ISO-8859-1', 'UTF-8');
							$body .= mb_convert_encoding("Por favor siga este link para cambiar su contraseña:<br>", 'ISO-8859-1', 'UTF-8');
							$body .= mb_convert_encoding("<a href='https://eos.leap.works/auth/recuperar_contrasenna.php?op=recuperar_contrasenna&email=$emailrec&tokenkey=$token'>https://eos.leap.works/auth/recuperar_contrasenna.php?op=recuperar_contrasenna&email=$emailrec&tokenkey=$token</a><br><br>", 'ISO-8859-1', 'UTF-8');
							$body .= mb_convert_encoding("<p>Si tiene problemas con el link, por favor copie y pegue el link en su navegador.<br>", 'ISO-8859-1', 'UTF-8');
							$body .= mb_convert_encoding("<small>El link expirará en un día.</small>", 'ISO-8859-1', 'UTF-8');

							$mail->Body = $body;
							$mail->Send();
							echo "Si el email existe en el sistema, se enviará un correo.";
						} catch (Exception $e) {
							echo $e->getMessage(); //Captura cualquier otro mensaje que devuelva PHPMailer
						}
					}
				} else {
					echo "Si el email existe en el sistema, se enviará un correo.";
				}
			}
		} else {
			echo "Si el email existe en el sistema, se enviará un correo.";
		}
	break;

	case 'listar':
		$rspta=$usuarios->listar();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$bordeTexto = ($reg->colorText=='#FFFFFF')? 'border:1px solid #AAA;':'';
			$borde = ($reg->color=='#FFFFFF')? 'border:1px solid #AAA;':'';
			$bordeSistema = ($reg->color=='#FFFFFF')? 'border:1px solid #AAA;':'';
			$botonMostrar = '<button class="btn btn-warning btn-sm" title="Mostrar usuario" onclick="mostrar('.$reg->id.')"><i class="fas fa-pencil-alt"></i></button>';
			$botonActivaDesactiva = ($reg->activo)? ' <button class="btn btn-danger btn-sm" title="Desactivar Usuario" onclick="desactivar('.$reg->id.')"><i class="fas fa-times"></i></button>' : ' <button class="btn btn-primary btn-sm" title="Activar Usuario" onclick="activar('.$reg->id.')"><i class="fa fa-check"></i></button>';
			$linkMostrar = '<button class="btn btn-link" title="Mostrar usuario" onclick="mostrar('.$reg->id.')">'.$reg->nombre.'</button>';
			$data[]=array(
				"0"=>$linkMostrar,
				"1"=>$reg->email,
				"2"=>$reg->telefono,
				"3"=>$reg->nombreCargo,
				"4"=>($reg->color)?'<div style="border-radius: 5px;height:32px;width:32px;background-color:'.$reg->color.';margin: 0 auto 0 auto;'.$bordeSistema.'"></div>':'<div style="height:32px;width:32px;margin: 8px auto 0 auto;color:#c4c4c4"><i class="fas fa-tint-slash"></i></div>',
				"5"=>($reg->colorText)?'<div style="border-radius: 5px;height:32px;width:32px;background-color:'.$reg->colorText.';margin: 0 auto 0 auto;'.$bordeTexto.'"></div>':'<div style="height:32px;width:32px;margin: 8px auto 0 auto;colorText:#c4c4c4"><i class="fas fa-tint-slash"></i></div>',
				"6"=>($reg->avatar=='default.jpg' || $reg->avatar=='')? '<img class="img-thumbnail" width="40" height="40" src="../public/images/default.jpg">' : '<a href="../public/files/avatars/'.$reg->avatar.'" data-featherlight="image"><img class="img-thumbnail" width="40" height="40" src="../public/files/avatars/'.$reg->avatar.'"></a>',
				"7"=>($reg->activo)?'<span class="badge bg-success">Activado</span>':'<span class="badge bg-danger">Desactivado</span>',
				"8"=>$botonMostrar . $botonActivaDesactiva
			);
		}
		$resultss = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($resultss);
	break;

	case 'listar_permiso':
		$id=$_GET['id'];
		$rspta = $usuarios->listar_permisoMarcado($id);
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$nombrePermiso = ($reg->principal==0)? $reg->nombrePermiso : '<strong>'.$reg->nombrePermiso.'</strong>';
			$marcado=($reg->ver)?'checked':'';
			$disabled = ($reg->verPermiso)? '':'disabled';
			$ver=($reg->ver)?'checked':'';
			$editar=($reg->editar)?'checked':'';
			$historial=($reg->historial)?'checked':'';
			$checkVer = ($reg->verPermiso)? '<input type="checkbox" class="permisos" id="'.$reg->permiso.'verCheck" '.$ver.' onchange="cambiaValorPermiso(\''.$reg->permiso.'\',\'ver\')"><input id="'.$reg->permiso.'ver" type="hidden" name="ver[]" value="'.$reg->ver.'">': '<input type="hidden" name="ver[]" value="'.$reg->ver.'">';
			$checkEditar = ($reg->editarPermiso)? '<input type="checkbox" class="permisos" '.$disabled.' id="'.$reg->permiso.'editarCheck" '.$editar.' onchange="cambiaValorPermiso(\''.$reg->permiso.'\',\'editar\')"><input id="'.$reg->permiso.'editar" type="hidden" name="editar[]" value="'.$reg->editar.'">' : '<input type="hidden" name="editar[]" value="'.$reg->editar.'">';
			$checkHistorial = ($reg->historialPermiso)? '<input type="checkbox" class="permisos" '.$disabled.' id="'.$reg->permiso.'historialCheck" '.$historial.' onchange="cambiaValorPermiso(\''.$reg->permiso.'\',\'historial\')"><input id="'.$reg->permiso.'historial" type="hidden" name="historial[]" value="'.$reg->historial.'">' : '<input type="hidden" name="historial[]" value="'.$reg->historial.'">';

			$data[]=array(
				"0"=>$nombrePermiso.'<input type="hidden" name="permiso[]" value="'.$reg->permisoID.'">',
				"1"=>$checkVer,
				"2"=>$checkEditar,
				"3"=>$checkHistorial,
				"4"=>$reg->orden,
				"5"=>$reg->grupo,
			);
		}
		$results_usuarios = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results_usuarios);
	break;

	case 'verificar':
		$emaila=$_POST['emaila'];
		$clavea=$_POST['clavea'];

		//Hash SHA256 en la contraseña
		$clavehash=hash("SHA256",$clavea);
		$rspta=$usuarios->verificar($emaila, $clavehash);
		$fetch=$rspta->fetch_object();

		if (isset($fetch)){
			//Declaramos las variables de sesión
			$_SESSION['usuarioID']=$fetch->id;
			$_SESSION['nombre']=$fetch->nombre;
			$_SESSION['avatar']=$fetch->avatar;
			$_SESSION['email']=$fetch->email;

			//Obtenemos los permisos del usuario
			$marcados = $usuarios->listarmarcados($fetch->id);

			//Declaramos el array para almacenar todos los permisos marcados
			$valores=array();

			//Almacenamos los permisos marcados en el array
			while ($per = $marcados->fetch_object()){
				$_SESSION[$per->permiso.'ver']=$per->ver;
				$_SESSION[$per->permiso.'editar']=$per->editar;
				$_SESSION[$per->permiso.'historial']=$per->historial;
			}
		}
		echo json_encode($fetch);
	break;

	case 'salir':
		//Limpiamos las variables de sesión   
		session_unset();
		//Destruìmos la sesión
		session_destroy();
		//Redireccionamos al login
		header("Location: ../index.php");
	break;
}

function isMobile() {
	if(preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"])){
		return 1;
	} else {
		return 0;
	}
}
?>