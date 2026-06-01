<?php
if (strlen(session_id()) < 1) 
	session_start();

	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);

require_once "../models/Escritorio.php";
$escritorios=new Escritorio();

$clienteID=isset($_POST["clienteID"])? limpiarCadena($_POST["clienteID"]):"";
$asistenciaID=isset($_POST["asistenciaID"])? limpiarCadena($_POST["asistenciaID"]):"";
$claseID=isset($_POST["claseID"])? limpiarCadena($_POST["claseID"]):"";
$alumnoID=isset($_POST["alumnoID"])? limpiarCadena($_POST["alumnoID"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$costo=isset($_POST["costo"])? limpiarCadena($_POST["costo"]):"";
$comentarios=isset($_POST["comentarios"])? limpiarCadena($_POST["comentarios"]):"";
$activo=isset($_POST["activo"])? limpiarCadena($_POST["activo"]):"";

switch ($_GET["op"]){
	case 'resumen_financiero':
		$rspta=$escritorios->resumenFinanciero();
		echo json_encode($rspta);
	break;

	case 'listar_facturas':
		$rspta=$escritorios->listarFacturas();
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			if($reg->regimenFiscal=='601'){
				$subtotal = $reg->total/1.16;
				$isr2 = $subtotal * 0.0125;
				$iva2 = $subtotal * 0.106700;
				$total = $reg->total - ($isr2 + $iva2);
			} else {
				$total = $reg->total;
			}

			$pagado = ($reg->status=='Pagada')? $total : $reg->pagado;
			$saldo = ($total-$pagado==0)? '<span class="text-success fw-bold">$0.00</span>' : '$'.number_format($total-$pagado,2,'.',',');
			$metodoPago = ($reg->metodoPago=='PUE')? '<small class="fw-bold text-primary">PUE</small>' : '<small class="fw-bold text-purple">PPD</small>';
			$pdfXml = (isset($reg->nombrePDFCancelado) && $reg->nombrePDFCancelado!='')
				? '<a href="'.$reg->nombrePDFCancelado.'" target="_blank"><img class="img-thumbnail" width="30" height="30" src="../public/images/pdf-file.png"></a> <a href="'.$reg->nombreXMLCancelado.'" download target="_blank"><img class="img-thumbnail" width="30" height="30" src="../public/images/xml-file.png"></a>'
				: ((empty($reg->nombrePDF))
					? '<span style="display:none;">0</span><img class="img-thumbnail" style="filter: grayscale(100%);opacity: 0.5;" width="30" height="30" src="../public/images/pdf-file.png"> <img class="img-thumbnail" style="filter: grayscale(100%);opacity: 0.5;" width="30" height="30" src="../public/images/xml-file.png">'
					: '<span style="display:none;">1</span><a href="'.$reg->nombrePDF.'" target="_blank"><img class="img-thumbnail" width="30" height="30" src="../public/images/pdf-file.png"></a> <a href="'.$reg->nombreXML.'" download target="_blank"><img class="img-thumbnail" width="30" height="30" src="../public/images/xml-file.png"></a>');
			$status = ($reg->status=='Cancelado')
				? '<span class="badge bg-danger">Cancelada</span>'
				: ((($total-$pagado)==0 && $reg->status!='Espera') ? '<span class="badge bg-success">Pagada</span>' : ((empty($reg->nombrePDF)) ? '<span class="badge bg-dark">Espera</span>' : '<span class="badge bg-primary">Timbrada</span>'));

			$data[]=array(
				"0"=>$reg->facturaID.'-'.$reg->serie.' '.$metodoPago,
				"1"=>date("Y-m-d", strtotime($reg->fecha)),
				"2"=>$reg->nombreCliente,
				"3"=>'$ '.number_format($total,2,'.',','),
				"4"=>'$ '.number_format($pagado,2,'.',','),
				"5"=>$saldo,
				"6"=>$pdfXml,
				"7"=>$status
			);
		}
		$results = array(
			"sEcho"=>1,
			"iTotalRecords"=>count($data),
			"iTotalDisplayRecords"=>count($data),
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'listar_pagos':
		$rspta=$escritorios->listarPagos();
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$tipoArchivo = substr($reg->comprobantePago, -3);
			if($tipoArchivo=='pdf' || $tipoArchivo=='PDF'){
				$imagenPago = '<span style="display:none;">PDF</span><a href="../public/files/pagos/'.$reg->comprobantePago.'" target="_blank"><img class="img-thumbnail" width="35" height="35" src="../public/images/pdf-file.png"></a>';
			} else {
				$imagenPago = ($reg->comprobantePago)?'<span style="display:none;">JPG</span><a href="../public/files/pagos/'.$reg->comprobantePago.'" data-featherlight="image"><img class="img-thumbnail" width="35" height="35" src="../public/files/pagos/'.$reg->comprobantePago.'"></a>' : '<span style="display:none;">ZZZ</span><img class="img-thumbnail" width="35" height="35" src="../public/images/placeholder.jpg">';
			}
			$data[]=array(
				"0"=>$reg->pagoID,
				"1"=>$reg->fechaPago,
				"2"=>$reg->nombreCliente,
				"3"=>$reg->nombreMetodo,
				"4"=>'$'.number_format($reg->pago,2,'.',','),
				"5"=>$imagenPago,
				"6"=>($reg->activo)?'<span class="badge bg-success">Activado</span>':'<span class="badge bg-danger">Desactivado</span>'
			);
		}
		$results = array(
			"sEcho"=>1,
			"iTotalRecords"=>count($data),
			"iTotalDisplayRecords"=>count($data),
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'mostrar':
		$rspta=$escritorios->mostrar($clienteID);
		echo json_encode($rspta);
	break;

	case 'mostrar_todos':
		$rspta=$escritorios->mostrar_todos();
		echo json_encode($rspta);
	break;

	case 'mostrarAlumno':
		$flag = true;
		$rspta=$escritorios->mostrarAlumno($alumnoID);
		while ($reg = $rspta->fetch_object()){
			$botonMostrarLink = '<button class="btn btn-link" title="Mostrar Grupo" onclick="mostrarClase('.$reg->claseID.')"></button>';
			$asistencia = ($reg->asistenciasAlumnos==0)? '0' : number_format((($reg->asistenciasAlumnos)*100)/$reg->asistenciasTotales,2,'.','');
			$avance = ($reg->paginas==0)? '0' : number_format(($reg->paginas*100)/$reg->paginasCurso,2,'.','');
			$color = ($flag)? 'success':'primary';
			echo '<div class="mb-2 datosCLase">
				<div class="d-flex flex-row justify-content-between flex-wrap">
					<span class="text-start"><strong>'.$reg->nombreAlumno.'</strong> '.$reg->nombreCurso.'</span>  <span class="text-center">'.$reg->nombreClase.'</span>
					<span class="text-center">Teacher: '.$reg->nombreMaestro.'</span>
				</div>
				<div class="d-flex flex-row justify-content-between flex-wrap">
					
				</div>
				<div class="row">
					<div class="col-lg-6 col-xl-6 mb-4 text-body">
						<span class="float-start"><strong>Avance:</strong> '.$reg->paginas.'/'.$reg->paginasCurso.'</span> <span class="float-end">'.$avance.'%</span><br>
						<div class="progress"> 
							<div class="progress-bar bg-primary" role="progressbar" style="width: '.$avance.'%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="'.$reg->paginasCurso.'"></div>
						</div>
					</div>
					<div class="col-lg-6 col-xl-6 mb-4 text-body">
						<span class="float-start"><strong>Asistencia:</strong></span> <span class="float-end">'.$asistencia.'%</span><br>
						<div class="progress">
							<div class="progress-bar bg-primary" role="progressbar" style="width: '.$asistencia.'%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="'.$reg->paginasCurso.'"></div>
						</div>
					</div>
				</div>
			</div>';
		}
	break;

	case 'mostrarClase':
		$rspta=$escritorios->mostrarClase($claseID);
		echo json_encode($rspta);
	break;

	case 'progresoClases':
		$rspta = $escritorios->progresoClases($clienteID);
		$flag = true;
		while ($reg = $rspta->fetch_object()){
			$botonMostrarLink = '<div class="tooltipClass"><button class="btn btn-link" onclick="mostrarClase('.$reg->claseID.')">'.$reg->nombreClase.'</button><span class="tooltiptext"><b>Alumnos:</b><br>'.$reg->nombreAlumnos.'</span></div>';
			$resultadoPorcentaje= ($reg->asistenciasTotales)? number_format((($reg->asistenciasAlumnos+1.25)*100)/$reg->asistenciasTotales,2,'.','') : 0;
			$porcentaje = ($reg->asistenciasAlumnos==0)? '0' : (($resultadoPorcentaje>=100)? 100 : $resultadoPorcentaje);
			$avance = ($reg->paginaCurso==0)? '0' : number_format(($reg->paginaCurso*100)/$reg->paginas_totales,2,'.','');
			$color = ($flag)? 'success':'primary';
			echo '<div class="mb-2 datosCLase">
				<div class="d-flex flex-row justify-content-between flex-wrap">
					<span class="text-start"><strong>'.$botonMostrarLink.'</strong> '.$reg->nombreCurso.'</span>  <span class="text-center">Alumnos: '.$reg->totalAlumnos.'</span>
					<span class="text-center">Teacher: '.$reg->nombreMaestro.'</span>
				</div>
				<div class="d-flex flex-row justify-content-between flex-wrap"></div>
				<div class="row">
					<div class="col-lg-6 col-xl-6 mb-4 text-body">
						<span class="float-start"><strong>Avance:</strong> '.$reg->paginaCurso.'/'.$reg->paginas_totales.'</span> <span class="float-end">'.$avance.'%</span><br>
						<div class="progress"> 
							<div class="progress-bar bg-'.$color.'" role="progressbar" style="width: '.$avance.'%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="'.$reg->paginas_totales.'"></div>
						</div>
					</div>
					<div class="col-lg-6 col-xl-6 mb-4 text-body">
						<span class="float-start"><strong>Asistencia:</strong></span> <span class="float-end">'.$porcentaje.'%</span><br>
						<div class="progress">
							<div class="progress-bar bg-'.$color.'" role="progressbar" style="width: '.$porcentaje.'%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>';
			$flag = !$flag;
		}
	break;

	case 'progresoClasesTodos':
		$rspta = $escritorios->progresoClasesTodos();
		$flag = true;
		while ($reg = $rspta->fetch_object()){
			$botonMostrarLink = '<div class="tooltipClass"><button class="btn btn-link" onclick="mostrarClase('.$reg->claseID.')">'.$reg->nombreClase.'</button><span class="tooltiptext"><b>Alumnos:</b><br>'.$reg->nombreAlumnos.'</span></div>';
			$resultadoPorcentaje= ($reg->asistenciasTotales)? number_format((($reg->asistenciasAlumnos+1.25)*100)/$reg->asistenciasTotales,2,'.','') : 0;
			$porcentaje = ($reg->asistenciasAlumnos==0)? '0' : (($resultadoPorcentaje>=100)? 100 : $resultadoPorcentaje);
			$avance = ($reg->paginaCurso==0)? '0' : number_format(($reg->paginaCurso*100)/$reg->paginas_totales,2,'.','');
			$color = ($flag)? 'success':'primary';
			echo '<div class="mb-2 datosCLase">
				<div class="d-flex flex-row justify-content-between flex-wrap">
					<span class="text-start"><strong>'.$botonMostrarLink.'</strong> '.$reg->nombreCurso.'</span>  <span class="text-center">Alumnos: '.$reg->totalAlumnos.'</span>
					<span class="text-center">Teacher: '.$reg->nombreMaestro.'</span>
				</div>
				<div class="d-flex flex-row justify-content-between flex-wrap"></div>
				<div class="row">
					<div class="col-lg-6 col-xl-6 mb-4 text-body">
						<span class="float-start"><strong>Avance:</strong> '.$reg->paginaCurso.'/'.$reg->paginas_totales.'</span> <span class="float-end">'.$avance.'%</span><br>
						<div class="progress"> 
							<div class="progress-bar bg-'.$color.'" role="progressbar" style="width: '.$avance.'%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="'.$reg->paginas_totales.'"></div>
						</div>
					</div>
					<div class="col-lg-6 col-xl-6 mb-4 text-body">
						<span class="float-start"><strong>Asistencia:</strong></span> <span class="float-end">'.$porcentaje.'%</span><br>
						<div class="progress">
							<div class="progress-bar bg-'.$color.'" role="progressbar" style="width: '.$porcentaje.'%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>';
			$flag = !$flag;
		}
	break;

	case 'listadoClases':
		$cli = $_GET["cli"];
		$rspta=$escritorios->listadoClases($cli);
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$porcentaje = ($reg->asistenciasAlumnos==0)? '0' : number_format((($reg->asistenciasAlumnos)*100)/$reg->asistenciasTotales,2,'.','');
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar Grupo" onclick="mostrarClase('.$reg->claseID.')"><i class="fas fa-fw fa-eye"></i></button>';
			$botonMostrarLink = '<button class="btn btn-link" title="Mostrar Grupo" onclick="mostrarClase('.$reg->claseID.')">'.$reg->nombre.'</button>';
			$data[]=array(
				"0"=>$botonMostrarLink,
				"1"=>$reg->nombreCurso,
				"2"=>$reg->alumnos,
				"3"=>$reg->paginas.'/'.$reg->paginasCurso,
				"4"=>$reg->nombreMaestro,
				"5"=>$porcentaje.'%',
				"6"=>$botonMostrar
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'listadoAlumnos':
		$cli = $_GET["cli"];
		$rspta=$escritorios->listadoAlumnos($cli);
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar Alumno" onclick="mostrarAlumno('.$reg->alumnoID.')"><i class="fas fa-fw fa-eye"></i></button>';
			$botonMostrarLink = '<button class="btn btn-link" title="Mostrar Alumno" onclick="mostrarAlumno('.$reg->alumnoID.')">'.$reg->nombre.'</button>';
			$porcentaje = ($reg->asistenciasAlumnos==0 || empty($reg->asistenciasTotales))? '0' : number_format((($reg->asistenciasAlumnos)*100)/$reg->asistenciasTotales,2,'.','');
			$avance = ($reg->paginas==0)? '0' : number_format(($reg->paginas*100)/$reg->paginasCurso,2,'.','');
			$porcentaje2 = ($porcentaje>=100)? '100' : $porcentaje;
			$data[]=array(
				"0"=>$botonMostrarLink,
				"1"=>($reg->nombreClase)? $reg->nombreClase : 'Sin regristro' ,
				"2"=>$reg->paginas.'/'.$reg->paginasCurso.' ('.$avance.'%)',
				"3"=>$reg->nombreMaestro,
				"4"=>$porcentaje2.'%',
				"5"=>$botonMostrar
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'listadoAlumnosTodos':
		$rspta=$escritorios->listadoAlumnosTodos();
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar Alumno" onclick="mostrarAlumno('.$reg->alumnoID.')"><i class="fas fa-fw fa-eye"></i></button>';
			$botonMostrarLink = '<button class="btn btn-link" title="Mostrar Alumno" onclick="mostrarAlumno('.$reg->alumnoID.')">'.$reg->nombre.'</button>';
			$porcentaje = ($reg->asistenciasTotales)? (($reg->asistenciasAlumnos==0)? '0' : number_format((($reg->asistenciasAlumnos)*100)/$reg->asistenciasTotales,2,'.','')) : 0;
			$avance = ($reg->paginas==0)? '0' : (($reg->paginasCurso)? number_format(($reg->paginas*100)/$reg->paginasCurso,2,'.','') : '0');
			$porcentaje2 = ($porcentaje>=100)? '100' : $porcentaje;
			$data[]=array(
				"0"=>$botonMostrarLink,
				"1"=>($reg->nombreClase)? $reg->nombreClase : 'Sin regristro' ,
				"2"=>$reg->paginas.'/'.$reg->paginasCurso.' ('.$avance.'%)',
				"3"=>$reg->nombreMaestro,
				"4"=>$porcentaje2.'%',
				"5"=>$botonMostrar
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'abreAsistencia':
		$rspta=$escritorios->abreAsistencia($asistenciaID);
		echo json_encode($rspta);
	break;

	case 'listadoAlumnosClase':
		$cla = $_GET["cla"];
		$rspta=$escritorios->listadoAlumnosClase($cla);
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$botonMostrarLink = '<button class="btn btn-link" title="Mostrar Alumno" onclick="mostrarAlumno('.$reg->alumnoID.')">'.$reg->nombre.'</button>';
			$porcentaje = ($reg->asistenciasAlumnos==0)? '0' : number_format((($reg->asistenciasAlumnos)*100)/$reg->asistenciasTotales,2,'.','');

			$data[]=array(
				"0"=>$botonMostrarLink,
				"1"=>$porcentaje.'%',
				"2"=>'N/A'
			);
		}
		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'mostrarCalendarioAlumno':
		$rspta=$escritorios->mostrarCalendarioAlumno($alumnoID);
		$data= Array();
		$color = '#000000';
		$colorText = '#FFFFFF';
		$icono = '<i class="fa-solid fa-star"></i>';
		while ($reg=$rspta->fetch_object()){
			$finanzas = '<i class="fa-solid fa-dollar-sign"></i>';
			$horariofin = ($reg->horario_fin=='00:00:00')? $reg->horario_inicio : $reg->horario_fin;
			$data[]=array(
				"title"=>$reg->claseID.' | ('.$reg->nombreCliente.') '.$reg->nombreMaestro,
				"asistenciaID"=>$reg->asistenciaID,
				"start"=>$reg->asistencia_inicio.' '.$reg->horario_inicio,
				"end"=>$reg->asistencia_inicio.' '.$reg->horario_fin,
				"color"=>($reg->nombresAlumnos)? $reg->color : '#DDDDDD',
				"icon"=>$icono,
				"colorText"=>$colorText,
				"clienteID"=>$reg->cliente_id,
				"claseID"=>$reg->claseID,
				"nombresAlumnos"=>($reg->nombresAlumnos)? $reg->nombresAlumnos : '',
				"nombreMaestro"=>$reg->nombreMaestro,
				"nombreCliente"=>$reg->nombreCliente
			);
		}
		echo json_encode($data);
	break;

	case 'mostrarCalendarioClase':
		$rspta=$escritorios->mostrarCalendarioClase($claseID);
		$data= Array();
		$color = '#000000';
		$colorText = '#FFFFFF';
		$icono = '<i class="fa-solid fa-star"></i>';
		while ($reg=$rspta->fetch_object()){
			$finanzas = '<i class="fa-solid fa-dollar-sign"></i>';
			$horariofin = ($reg->horario_fin=='00:00:00')? $reg->horario_inicio : $reg->horario_fin;
			$data[]=array(
				"title"=>$reg->claseID.' | ('.$reg->nombreCliente.') '.$reg->nombreMaestro,
				"asistenciaID"=>$reg->asistenciaID,
				"start"=>$reg->asistencia_inicio.' '.$reg->horario_inicio,
				"end"=>$reg->asistencia_inicio.' '.$reg->horario_fin,
				"color"=>$reg->color,
				"icon"=>$icono,
				"colorText"=>$colorText,
				"clienteID"=>$reg->cliente_id,
				"claseID"=>$reg->claseID,
				"nombresAlumnos"=>($reg->nombresAlumnos)? $reg->nombresAlumnos : '',
				"nombreMaestro"=>$reg->nombreMaestro,
				"nombreCliente"=>$reg->nombreCliente
			);
		}
		echo json_encode($data);
	break;
}
?>