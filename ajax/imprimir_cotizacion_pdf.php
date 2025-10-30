<?php
if (strlen(session_id()) < 1) 
session_start();
require_once '../public/mpdf/vendor/autoload.php';

	$mpdfConfig = array(
		'mode' => 'utf-8', 
		'format' => array(210,297),
		'margin_header' => 0,     // 30mm not pixel
		'margin_footer' => 0,     // 10mm
		'margin_left' => 0,   	// 15 margin_left
		'margin_right' => 0,
		'orientation' => 'P',
		'default_font' => 'dejavusans',
		'setAutoTopMargin' => 'stretch',
		'setAutoBottomMargin' => 'stretch',
		'autoMarginPadding' => 1
	);
	$estilos = '
	body{
		font-weight: lighter;
	}
	#cuerpoCotizacion {
		width:16.5cm;
		padding:42.9mm 24.8mm 0 30.6mm;
	}
	#textoCotizacion{
		
	}
	#quedo{
		text-align:right;
	}';
	$mpdf = new \Mpdf\Mpdf($mpdfConfig);
	$mpdf->showWatermarkImage = true;
	$mpdf->watermarkImgBehind = true;
	$mpdf->SetWatermarkImage(
		$fondoCotizacion,
		1, array(210,297), 'P'
	);

	$stylesheet = file_get_contents('../public/mpdf/style.css');
	$stylesheet .= $estilos;

//================ CLASES ================
	$CuentaArray = count($Array_clases);
	if($CuentaArray>0){
		$arregloClases = "";
		for ($i=0; $i<$CuentaArray; $i++){
			$arregloClases .= "<tr>
				<td style='padding:4mm 0;'>$Array_clases[$i]</td>
			</tr>";
		}
	} else {
		$arregloClases = "";
	}

$htmlCotizacion = " 
	<div class='imagenFondo' id='cuerpoCotizacion'>
		<div id='contenedorDatos' style='display: grid;'>
			<table style='width:100%;font-size:14px;'>
				<tr>
					<td style='text-align:right;'>
						$nombreVendedor<br>
						Av. Enrique Ladrón de Guevara 1454, Zapopan, Jalisco.
					</td>
				</tr>
				<tr>
					<td style='text-align:center;padding:5mm 0 8mm 0;'>
						Cotización para $nombreCliente
					</td>
				</tr>
				<tr>
					<td>
						Guadalajara Jalisco a $fecha
					</td>
				</tr>
				<tr>
					<td style='padding:10mm 0 0 0;'>
						Estimado $nombreContacto<br>
						Te comparto la cotización de inglés:
					</td>
				</tr>
				$arregloClases
			</table>
			$comentarios
		</div>
		<div id='quedo'>
			Quedo al pendiente para aclarar cualquier duda que pudiera surgir.<br><br>
			Muchas gracias.<br><br>
			<small><strong>Cotización válida por 3 meses.</strong></small>
		</div>
	</div>";

$mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($htmlCotizacion,\Mpdf\HTMLParserMode::HTML_BODY);

$mpdf->Output('../public/files/cotizaciones/Cotizacion_'.$cotizacionID.'.pdf', 'F');