<?php
if (strlen(session_id()) < 1) 
	session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../models/Pedido.php";
$pedidos=new Pedido();

$usuarioID = (isset($_SESSION['usuarioID'])) ? $_SESSION['usuarioID'] : 0;
$pedidoID=isset($_POST["pedidoID"])? limpiarCadena($_POST["pedidoID"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$unidadmedida_id=isset($_POST["unidadmedida_id"])? limpiarCadena($_POST["unidadmedida_id"]):"";
$clavefacturacion_id=isset($_POST["clavefacturacion_id"])? limpiarCadena($_POST["clavefacturacion_id"]):"";
$observaciones=isset($_POST["observaciones"])? limpiarCadena($_POST["observaciones"]):"";

//Datos Cliente
$razonSocial=isset($_POST["razonSocial"])? limpiarCadena($_POST["razonSocial"]):"";
$rfcCliente=isset($_POST["rfcCliente"])? limpiarCadena($_POST["rfcCliente"]):"";
$email_cliente=isset($_POST["email_cliente"])? limpiarCadena($_POST["email_cliente"]):"";
$telefono=isset($_POST["telefono"])? limpiarCadena($_POST["telefono"]):"";
$calle=isset($_POST["calle"])? limpiarCadena($_POST["calle"]):"";
$num_ext=isset($_POST["num_ext"])? limpiarCadena($_POST["num_ext"]):"";
$num_int=isset($_POST["num_int"])? limpiarCadena($_POST["num_int"]):"";
$colonia=isset($_POST["colonia"])? limpiarCadena($_POST["colonia"]):"";
$poblacion=isset($_POST["poblacion"])? limpiarCadena($_POST["poblacion"]):"";
$edoPais=isset($_POST["edoPais"])? limpiarCadena($_POST["edoPais"]):"";
$cp=isset($_POST["cp"])? limpiarCadena($_POST["cp"]):"";
$regimenFiscal=isset($_POST["regimenFiscal"])? limpiarCadena($_POST["regimenFiscal"]):"";
$num_cuenta=isset($_POST["num_cuenta"])? limpiarCadena($_POST["num_cuenta"]):"";
$banco=isset($_POST["banco"])? limpiarCadena($_POST["banco"]):"";
$user_login=generarUserLogin($razonSocial);
$user_nicename = generarUserNicename($razonSocial);
$contrasenna=generarContrasenna(12);

//Datos Facturación
$facturaID=isset($_POST["facturaID"])? limpiarCadena($_POST["facturaID"]):"";
$serie=isset($_POST["serie"])? limpiarCadena($_POST["serie"]):"";
$metodoPago=isset($_POST["metodoPago"])? limpiarCadena($_POST["metodoPago"]):"";
$claveTipoComprobante=isset($_POST["claveTipoComprobante"])? limpiarCadena($_POST["claveTipoComprobante"]):"";
$usoCfdi=isset($_POST["usoCfdi"])? limpiarCadena($_POST["usoCfdi"]):"";
$formadePago=isset($_POST["formadePago"])? limpiarCadena($_POST["formadePago"]):"";
$descuento=isset($_POST["descuento"])? limpiarCadena($_POST["descuento"]):"0";
$moneda=isset($_POST["moneda"])? limpiarCadena($_POST["moneda"]):"";
$tipoCambio=isset($_POST["tipoCambio"])? limpiarCadena($_POST["tipoCambio"]):"1";
$cliente_id=isset($_POST["cliente_id"])? limpiarCadena($_POST["cliente_id"]):"0";
$clienteID=isset($_POST["clienteID"])? limpiarCadena($_POST["clienteID"]):"0";
$comentarios=isset($_POST["comentarios"])? limpiarCadena($_POST["comentarios"]):"";
$pedido_id=isset($_POST["pedido_id"])? limpiarCadena($_POST["pedido_id"]):"";
$tipo=isset($_POST["tipo"])? limpiarCadena($_POST["tipo"]):"";

//Detalles de la factura
$producto_id		= (isset($_POST["producto_id"]))? $_POST["producto_id"]: array();
$variante_id		= (isset($_POST["variante_id"]))? $_POST["variante_id"]: array();
$descripcion		= (isset($_POST["descripcion"]))? $_POST["descripcion"]: array();
$cantidad			= (isset($_POST["cantidad"]))? $_POST["cantidad"]: array();
$precioVenta		= (isset($_POST["precioVenta"]))? $_POST["precioVenta"]: array();
$subtotal			= (isset($_POST["subtotal"]))? $_POST["subtotal"]: array();

//Pedidos generados por el usuario
$factura_id=isset($_POST["factura_id"])? limpiarCadena($_POST["factura_id"]):"0";
$status=isset($_POST["status"])? limpiarCadena($_POST["status"]):"";
$nombreCliente=isset($_POST["nombreCliente"])? limpiarCadena($_POST["nombreCliente"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if(empty($pedidoID)){
			$rspta=$pedidos->insertar($clienteID,$nombreCliente,$observaciones,$producto_id,$variante_id,$descripcion,$cantidad,$precioVenta);
			echo $rspta ? "Pedido registrado" : "Pedido no se pudo registrar";
		} else{
			$rspta=$pedidos->editar($pedidoID,$clienteID,$nombreCliente,$observaciones,$producto_id,$variante_id,$descripcion,$cantidad,$precioVenta);
			echo $rspta ? "Pedido actualizado" : "El Pedido no se pudo actualizar";
		}
	break;

	case 'guardaryeditar_factura':
		if(empty($facturaID)){
			$rspta=$pedidos->insertar_factura($serie,$metodoPago,$claveTipoComprobante,$usoCfdi,$formadePago,$descuento,$moneda,$tipoCambio,$cliente_id,$razonSocial,$rfcCliente,$usuarioID,$comentarios,$pedido_id,$user_login,$user_nicename,$email_cliente,$contrasenna,$telefono,$calle,$num_ext,$num_int,$colonia,$poblacion,$edoPais,$cp,$regimenFiscal,$num_cuenta,$banco,$producto_id,$descripcion,$cantidad,$precioVenta,$subtotal);
			echo $rspta ? "Factura registrada" : "Factura no se pudo registrar";
		} else{
			$rspta=$pedidos->editar_factura($facturaID,$serie,$metodoPago,$claveTipoComprobante,$usoCfdi,$formadePago,$descuento,$moneda,$tipoCambio,$cliente_id,$razonSocial,$rfcCliente,$usuarioID,$comentarios,$pedido_id,$user_login,$user_nicename,$email_cliente,$contrasenna,$telefono,$calle,$num_ext,$num_int,$colonia,$poblacion,$edoPais,$cp,$regimenFiscal,$num_cuenta,$banco,$producto_id,$descripcion,$cantidad,$precioVenta,$subtotal);
			echo $rspta ? "Factura actualizada" : "El Factura no se pudo actualizar";
		}
	break;

	case 'mostrar':
		$rspta=$pedidos->mostrar($pedidoID);
		echo json_encode($rspta);
	break;

	case 'mostrar_pedido':
		$rspta=$pedidos->mostrar_pedido($pedidoID);
		echo json_encode($rspta);
	break;

	case 'mostrarDetallesFactura':
		if($tipo=='1'){
			$rspta=$pedidos->mostrarDetalles($pedidoID);
		} else {
			$rspta=$pedidos->mostrarDetalles_pedido($pedidoID);
		}
		echo '<thead class="bg-info text-light">
			<th>Cantidad</th>
			<th>Producto</th>
			<th>Variante</th>
			<th>IMG</th>
			<th>$Unitario</th>
			<th>$Subtotal</th>
		</thead>';
		$totales=0;
		while ($reg = $rspta->fetch_object()){
			$imagen = ($reg->imagen)?'<a href="'.$reg->imagen.'" data-featherlight="image"><img class="img-thumbnail" style="width:40px;height:auto;" src="'.$reg->imagen.'"></a>' : '<img class="img-thumbnail" style="width:40px;height:auto;" src="../public/images/placeholder.jpg">';

			echo '<tr class="filas" id="fila'.$reg->producto_id.'">

				//Cantidad
				<td style="width:140px;!important">
					<input type="hidden" name="cantidad[]" id="cantidad'.$reg->producto_id.'" placeholder="Número de Cantidad" value="'.$reg->cantidad.'" required>'.$reg->cantidad.'
				</td>

				//Producto
				<td>
					<input type="hidden" class="form-control" name="contador[]" id="contador'.$reg->producto_id.'" value="'.$reg->producto_id.'">
					<input type="hidden" name="producto_id[]" value="'.$reg->producto_id.'">'.$reg->nombre_producto.' <br><small><strong>SKU:</strong> '.$reg->sku.'</small>
				</td>

				//Descripcion
				<td>
					<textarea class="form-control" name="descripcion[]" id="descripcion'.$reg->producto_id.'" placeholder="Descripción" rows="3">'.$reg->descripcion.'</textarea>
				</td>

				//Imagen
				<td style="width:140px;!important;text-align:center;">
					'.$imagen.'
				</td>

				//Unitario
				<td style="width:140px;!important;text-align:right;">
					<input type="hidden" name="variante_id[]" value="'.$reg->variante_id.'">
					<input type="hidden" name="precioVenta[]" id="unitario'.$reg->producto_id.'" value="'.$reg->precioVenta.'" required>$'.number_format($reg->precioVenta, 2, '.', ',').'
				</td>

				//Subtotal
				<td style="width:140px;!important;text-align:right;">
					$<span id="subtotal_html'.$reg->producto_id.'">'.number_format($reg->precioVenta*$reg->cantidad, 2, '.', ',').'</span>
					<input type="hidden" class="form-control" name="subtotal[]" id="subtotal'.$reg->producto_id.'" value="'.number_format($reg->precioVenta, 2, '.', '').'">
				</td>
				</tr>';
				$precioVenta=floatval(str_replace(',','',$reg->precioVenta*$reg->cantidad));
				$totales=$totales+$precioVenta;
		}
		echo '<tfoot>
			<th  colspan="4"></th>
			<th style="width:140px;text-align:right;">Total:</th>
			<th style="width:180px;text-align:right;">$<strong id="grantotal_factura">'.number_format($totales, 2, '.', ',').'</strong></th>
		</tfoot>';
	break;

	case 'obtener_datos_factura':
		if($tipo=='1'){
			$rspta=$pedidos->obtener_datos_factura($pedidoID);
		} else {
			$rspta=$pedidos->ver_factura($facturaID);
		}
		echo json_encode($rspta);
	break;

	case 'ver_factura':
		$rspta=$pedidos->obtener_datos_factura($pedidoID);
		echo json_encode($rspta);
	break;

	case 'mostrarDetalles':
		$rspta=$pedidos->mostrarDetalles($pedidoID);
		echo '<thead class="bg-info text-light">
			<th></th>
			<th>Cantidad</th>
			<th>Producto</th>
			<th>Descripción</th>
			<th>IMG</th>
			<th>$Unitario</th>
			<th>$Subtotal</th>
		</thead>';
		while ($reg = $rspta->fetch_object()){
			$imagen = ($reg->imagen)?'<a href="'.$reg->imagen.'" data-featherlight="image"><img class="img-thumbnail" style="width:40px;height:auto;" src="'.$reg->imagen.'"></a>' : '<img class="img-thumbnail" style="width:40px;height:auto;" src="../public/images/placeholder.jpg">';

			echo '<tr class="filas" id="fila'.$reg->producto_id.'">
				<td>
					<input type="hidden" class="form-control" name="contador[]" id="contador'.$reg->producto_id.'" value="'.$reg->producto_id.'">
					<button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle('.$reg->producto_id.')"><i class="fas fa-times"></i></button>
				</td>

				//Cantidad
				<td style="width:140px;!important">
					<input type="number" min="1" step="1" style="text-align:right;" class="form-control" class="form-control" name="cantidad[]" id="cantidad'.$reg->producto_id.'" placeholder="Número de Cantidad" value="'.$reg->cantidad.'" oninput="modificarSubTotales('.$reg->producto_id.')" required>
				</td>

				//Producto
				<td>
					<input type="hidden" class="form-control" name="contador[]" id="contador'.$reg->producto_id.'" value="'.$reg->producto_id.'">
					<input type="hidden" name="variante_id[]" value="'.$reg->variante_id.'">
					<input type="hidden" name="producto_id[]" value="'.$reg->producto_id.'">'.$reg->nombre_producto.' <br><small><strong>SKU:</strong> '.$reg->sku.'</small>
				</td>

				//Descripción
				<td>
					<textarea class="form-control" name="descripcion[]" id="descripcion'.$reg->producto_id.'" placeholder="Descripción" rows="3">'.$reg->descripcion.'</textarea>
				</td>

				//Imagen
				<td style="width:140px;!important;text-align:center;">
					'.$imagen.'
				</td>

				//Unitario
				<td style="width:140px;!important;text-align:right;">
					<input type="number" min=".01" step=".01" style="text-align:right;" class="form-control" class="form-control" name="precioVenta[]" id="precioVenta'.$reg->producto_id.'" placeholder="Precio Unitario" value="'.$reg->precioVenta.'" oninput="modificarSubTotales('.$reg->producto_id.')" required>
				</td>

				//Subtotal
				<td style="width:140px;!important;text-align:right;">
					$<span id="subtotal_html'.$reg->producto_id.'">'.number_format($reg->precioVenta*$reg->cantidad, 2, '.', ',').'</span>
					<input type="hidden" class="form-control" name="subtotal[]" id="subtotal'.$reg->producto_id.'" value="'.number_format($reg->precioVenta*$reg->cantidad, 2, '.', '').'">
				</td>
				<script>
					setTimeout(function(){
						modificarTotales();
					},500);
				</script>
			</tr>';
		}
		echo '<tfoot>
			<th  colspan="5"></th>
			<th style="width:140px;text-align:right;">Total:</th>
			<th style="width:180px;text-align:right;">$<strong id="grantotal">0.00</strong></th>
		</tfoot>';
	break;

	case 'mostrarDetalles_pedido':
		$rspta=$pedidos->mostrarDetalles_pedido($pedidoID);
		echo '<thead class="bg-info text-light">
			<th></th>
			<th>Cantidad</th>
			<th>Producto</th>
			<th>Descripción</th>
			<th>IMG</th>
			<th>$Unitario</th>
			<th>$Subtotal</th>
		</thead>';
		while ($reg = $rspta->fetch_object()){
			$imagen = ($reg->imagen)?'<a href="'.$reg->imagen.'" data-featherlight="image"><img class="img-thumbnail" style="width:40px;height:auto;" src="'.$reg->imagen.'"></a>' : '<img class="img-thumbnail" style="width:40px;height:auto;" src="../public/images/placeholder.jpg">';

			echo '<tr class="filas" id="fila'.$reg->producto_id.'">
				<td>
					<input type="hidden" class="form-control" name="contador[]" id="contador'.$reg->producto_id.'" value="'.$reg->producto_id.'">
					<button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle('.$reg->producto_id.')"><i class="fas fa-times"></i></button>
				</td>

				//Cantidad
				<td style="width:140px;!important">
					<input type="number" min="1" step="1" style="text-align:right;" class="form-control" class="form-control" name="cantidad[]" id="cantidad'.$reg->producto_id.'" placeholder="Número de Cantidad" value="'.$reg->cantidad.'" oninput="modificarSubTotales('.$reg->producto_id.')" required>
				</td>

				//Producto
				<td>
					<input type="hidden" class="form-control" name="contador[]" id="contador'.$reg->producto_id.'" value="'.$reg->producto_id.'">
					<input type="hidden" name="variante_id[]" value="'.$reg->variante_id.'">
					<input type="hidden" name="producto_id[]" value="'.$reg->producto_id.'">'.$reg->nombre_producto.' <br><small><strong>SKU:</strong> '.$reg->sku.'</small>
				</td>

				//Descripción
				<td>
					<textarea class="form-control" name="descripcion[]" id="descripcion'.$reg->producto_id.'" placeholder="Descripción" rows="3">'.$reg->descripcion.'</textarea>
				</td>

				//Imagen
				<td style="width:140px;!important;text-align:center;">
					'.$imagen.'
				</td>

				//Unitario
				<td style="width:140px;!important;text-align:right;">
					<input type="number" min=".01" step=".01" style="text-align:right;" class="form-control" class="form-control" name="precioVenta[]" id="precioVenta'.$reg->producto_id.'" placeholder="Precio Unitario" value="'.$reg->precioVenta.'" oninput="modificarSubTotales('.$reg->producto_id.')" required>
				</td>

				//Subtotal
				<td style="width:140px;!important;text-align:right;">
					$<span id="subtotal_html'.$reg->producto_id.'">'.number_format($reg->precioVenta*$reg->cantidad, 2, '.', ',').'</span>
					<input type="hidden" class="form-control" name="subtotal[]" id="subtotal'.$reg->producto_id.'" value="'.number_format($reg->precioVenta*$reg->cantidad, 2, '.', '').'">
				</td>
				<script>
					setTimeout(function(){
						modificarTotales();
					},500);
				</script>
			</tr>';
		}
		echo '<tfoot>
			<th  colspan="5"></th>
			<th style="width:140px;text-align:right;">Total:</th>
			<th style="width:180px;text-align:right;">$<strong id="grantotal">0.00</strong></th>
		</tfoot>';
	break;

	case 'listar':
		$rspta=$pedidos->listar();
		//Vamos a declarar un array
		$data= Array();
//15406
		while ($reg=$rspta->fetch_object()){
			$botonMostrar = ($reg->tipo_pedido=='worpress')? '<button type="button" class="btn btn-dark btn-sm" title="Mostrar Pedido" href="#" onclick="mostrar('.$reg->pedidoID.')"><i class="fas fa-fw fa-pencil-alt"></i></button>' : '<button type="button" class="btn btn-dark btn-sm" title="Mostrar Pedido" href="#" onclick="mostrar_pedido('.$reg->pedidoID.')"><i class="fas fa-fw fa-pencil-alt"></i></button>';
			$pedidoWordpress = ($reg->tipo_pedido=='worpress')? '1' : '0';
			$botonFacturar = (isset($reg->facturaID) && empty($reg->folioFiscal))? ' <button type="button" class="btn btn-success btn-sm" title="Editar Factura" href="#" onclick="edita_factura('.$reg->pedidoID.','.$reg->facturaID.','.$pedidoWordpress.')"><i class="fas fa-fw fa-pencil-alt"></i></button>' : ((isset($reg->facturaID) && isset($reg->folioFiscal))? ' <button type="button" class="btn btn-success btn-sm" title="Ver Factura" href="#" onclick="ver_factura('.$reg->facturaID.','.$reg->pedidoID.')"><i class="fas fa-fw fa-eye"></i></button>' : ' <button type="button" class="btn btn-primary btn-sm" title="Facturar Pedido" href="#" onclick="facturar('.$reg->pedidoID.','.$pedidoWordpress.','.$reg->clienteID.')"><i class="fas fa-fw fa-file-invoice-dollar"></i></button>');
			$botonTimbrar = (isset($reg->facturaID) && $reg->facturaID!='0' && empty($reg->folioFiscal))? ' <button type="button" class="btn btn-info btn-sm" title="Timbrar Pedido" href="#" onclick="timbra('.$reg->facturaID.')"><i class="fas fa-fw fa-cog"></i></button>' : ((isset($reg->facturaID) && isset($reg->folioFiscal) && ($reg->status_factura=='Pagada' || $reg->status_factura=='Facturado'))? ' <button type="button" class="btn btn-danger btn-sm" title="Cancelar Factura" href="#" onclick="modalCancelaFactura('.$reg->facturaID.')"><i class="fa-solid fa-circle-xmark"></i></button>' : ' <button type="button" class="btn btn-muted btn-sm disabled" title="Timbrar Factura" href="#"><i class="fas fa-fw fa-cog"></i></button>');
			$linkMostrar = '<a class="link-underline-primary" title="Mostrar Pedido" onclick="mostrar('.$reg->pedidoID.')">'.$reg->pedidoID.'</a>';
			$statusLabel = ($reg->estado=='wc-pending') ? '<span class="badge rounded-pill text-bg-secondary">Pendiente</span>' :  ( ($reg->estado=='wc-processing') ? '<span class="badge rounded-pill text-bg-info">En Proceso</span>' : ( ($reg->estado=='wc-completed') ? '<span class="badge rounded-pill text-bg-success">Completado</span>' : ( ($reg->estado=='wc-cancelled') ? '<span class="badge rounded-pill text-bg-danger">Cancelado</span>' : '<span class="badge rounded-pill text-bg-secondary">'.htmlspecialchars($reg->estado).'</span>')));
			$pdf = (isset($reg->folioFiscal))? '<a href="'.$reg->nombrePDF.'" download="'.$reg->nombrePDF.'" target="_blank"><i class="fas fa-file-pdf text-danger" title="Descargar PDF"></i></a>' : '<i class="fas fa-file-pdf text-muted" title="Descargar PDF"></i>';
			$xml = (isset($reg->folioFiscal))? ' <a href="'.$reg->nombreXML.'" download="'.$reg->nombreXML.'" target="_blank"><i class="fas fa-file-code text-success" title="Descargar XML"></i></a>' : ' <i class="fas fa-file-code text-muted" title="Descargar XML"></i>';
			$factura = (isset($reg->facturaID))? '<small>'.$reg->facturaID.'-A / <span class="text-muted">'.$reg->metodoPago.'</span></small>' : '';
			$botonCopiar = ' <button type="button" class="btn btn-orange btn-sm" title="Copiar Pedido" href="#" onclick="copiar_pedido('.$reg->pedidoID.')"><i class="fas fa-fw fa-copy"></i></button>';
			$data[]=array(
				"0"=>$linkMostrar,
				"1"=>date("Y-m-d", strtotime($reg->fecha)),
				"2"=>$reg->cliente,
				"3"=>'$'.number_format($reg->total, 2, '.', ','),
				"4"=>$pdf.' '.$xml.'<br>'.$factura,
				"5"=>$statusLabel,
				"6"=>$botonMostrar.$botonFacturar.$botonTimbrar.$botonCopiar
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'ver_productos':
		$rspta=$pedidos->ver_productos();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$imagen = ($reg->imagen)?'<a href="'.$reg->imagen.'" data-featherlight="image"><img class="img-thumbnail" style="width:40px;height:auto;" src="'.$reg->imagen.'"></a>' : '<img class="img-thumbnail" style="width:40px;height:auto;" src="../public/images/placeholder.jpg">';
			$data[]=array(
				"0"=>$reg->nombreProducto.'<br><small><strong>SKU:</strong> '.$reg->sku.'</small>',
				"1"=>$reg->variante,
				"2"=>$imagen,
				"3"=>(isset($reg->precioVenta))? '$'.number_format($reg->precioVenta, 2, '.', ',') : 0,
				"4"=>'<button type="button" class="btn btn-warning btn-sm" onclick="agregarDetalle('.$reg->productoID.','.$reg->variante_id.',\''.$reg->nombreProducto.'\',\''.$reg->variante.'\',\''.$reg->imagen.'\','.$reg->precioVenta.')"><i class="fas fa-plus"></i></button>',
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'copiar_pedido':
		$rspta=$pedidos->copiar_pedido($pedidoID);
		if($rspta){
			echo "Pedido copiado exitosamente";
		} else {
			require_once "../config/Conexion.php";
			global $conexion;
			echo "El Pedido no se pudo copiar. Error MySQL: " . (isset($conexion) ? $conexion->error : "Conexión no disponible");
		}
	break;
}

function generarUserLogin($nombreCompleto) {
    // Convertir a minúsculas
    $login = strtolower($nombreCompleto);
    // Eliminar acentos y caracteres especiales
    $login = iconv('UTF-8', 'ASCII//TRANSLIT', $login);
    // Reemplazar espacios por guiones bajos
    $login = str_replace(' ', '_', $login);
    // Eliminar caracteres no permitidos (solo letras, números, guiones y guiones bajos)
    $login = preg_replace('/[^a-z0-9_-]/', '', $login);
    // Limitar a 60 caracteres (WordPress permite hasta 60)
    $login = substr($login, 0, 56);
    // Agregar 4 números aleatorios al final
    $login .= rand(1000, 9999);
    return $login;
}

function generarUserNicename($nombreCompleto) {
    // Convertir a minúsculas
    $nicename = strtolower($nombreCompleto);
    // Eliminar acentos y caracteres especiales
    $nicename = iconv('UTF-8', 'ASCII//TRANSLIT', $nicename);
    // Reemplazar espacios por guiones
    $nicename = str_replace(' ', '-', $nicename);
    // Eliminar caracteres no permitidos (solo letras, números y guiones)
    $nicename = preg_replace('/[^a-z0-9-]/', '', $nicename);
    // Eliminar guiones múltiples consecutivos
    $nicename = preg_replace('/-+/', '-', $nicename);
    // Eliminar guiones al inicio y final
    $nicename = trim($nicename, '-');
    // Limitar a 50 caracteres (WordPress recomienda nicenames cortos)
    $nicename = substr($nicename, 0, 50);
    // Si está vacío después de la limpieza, usar un valor por defecto
    if (empty($nicename)) {
        $nicename = 'usuario-' . rand(1000, 9999);
    }
    
    return $nicename;
}

function generarContrasenna($longitud = 12) {
    // Definir los caracteres permitidos
    $mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $minusculas = 'abcdefghijklmnopqrstuvwxyz';
    $numeros = '0123456789';
    $especiales = '!@#$%^&*()_-+=[]{}|:;<>?';
    // Asegurar que la contraseña tenga al menos uno de cada tipo
    $contrasenna = '';
    $contrasenna .= $mayusculas[rand(0, strlen($mayusculas) - 1)];
    $contrasenna .= $minusculas[rand(0, strlen($minusculas) - 1)];
    $contrasenna .= $numeros[rand(0, strlen($numeros) - 1)];
    $contrasenna .= $especiales[rand(0, strlen($especiales) - 1)];
    
    // Completar el resto de la longitud con caracteres aleatorios
    $todosCaracteres = $mayusculas . $minusculas . $numeros . $especiales;
    for ($i = 4; $i < $longitud; $i++) {
        $contrasenna .= $todosCaracteres[rand(0, strlen($todosCaracteres) - 1)];
    }
    
    // Mezclar los caracteres para que no estén en orden predecible
    $contrasenna = str_shuffle($contrasenna);
    
    return $contrasenna;
}

?>