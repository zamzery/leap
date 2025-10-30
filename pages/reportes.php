<?php
if (strlen(session_id()) < 1) 
	session_start();

require_once "../models/Reporte.php";
$reportes=new Reporte();

$clienteID=isset($_POST["clienteID"])? limpiarCadena($_POST["clienteID"]):"";
$asistenciaID=isset($_POST["asistenciaID"])? limpiarCadena($_POST["asistenciaID"]):"";
$claseID=isset($_POST["claseID"])? limpiarCadena($_POST["claseID"]):"";
$alumnoID=isset($_POST["alumnoID"])? limpiarCadena($_POST["alumnoID"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$costo=isset($_POST["costo"])? limpiarCadena($_POST["costo"]):"";
$comentarios=isset($_POST["comentarios"])? limpiarCadena($_POST["comentarios"]):"";
$activo=isset($_POST["activo"])? limpiarCadena($_POST["activo"]):"";

switch ($_GET["op"]){
	case 'mostrar':
		$rspta=$reportes->mostrar($clienteID);
		echo json_encode($rspta);
	break;

	case 'mostrarAlumno':
		$rspta=$reportes->mostrarAlumno($alumnoID);
		while ($reg = $rspta->fetch_object()){
			$botonMostrarLink = '<button class="btn btn-link" title="Mostrar Grupo" onclick="mostrarClase('.$reg->claseID.')"></button>';
			$resultadoPorcentaje= number_format((($reg->asistenciasAlumnos)*100)/$reg->asistenciasTotales,2,'.','');
			$porcentaje = ($reg->asistenciasAlumnos==0)? '0' : (($resultadoPorcentaje>=100)? 100 : $resultadoPorcentaje);
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
						<span class="float-start"><strong>Asistencia:</strong></span> <span class="float-end">'.$porcentaje.'%</span><br>
						<div class="progress">
							<div class="progress-bar bg-primary" role="progressbar" style="width: '.$porcentaje.'%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="'.$reg->paginasCurso.'"></div>
						</div>
					</div>
				</div>
			</div>';
		}
	break;

	case 'mostrarClase':
		$rspta=$reportes->mostrarClase($claseID);
		echo json_encode($rspta);
	break;

	case 'progresoClases':
		$rspta = $reportes->progresoClases($clienteID);
		$flag = true;
		while ($reg = $rspta->fetch_object()){
			$botonMostrarLink = '<button class="btn btn-link" title="Mostrar Grupo" onclick="mostrarClase('.$reg->claseID.')">'.$reg->nombreClase.'</button>';
			$resultadoPorcentaje= number_format((($reg->asistenciasAlumnos+1.25)*100)/$reg->asistenciasTotales,2,'.','');
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
		$rspta=$reportes->listadoClases($cli);
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
		$rspta=$reportes->listadoAlumnos($cli);
		//Vamos a declarar un array
		$data= Array();

		while ($reg=$rspta->fetch_object()){
			$botonMostrar = '<button class="btn btn-dark btn-sm" title="Mostrar Alumno" onclick="mostrarAlumno('.$reg->alumnoID.')"><i class="fas fa-fw fa-eye"></i></button>';
			$botonMostrarLink = '<button class="btn btn-link" title="Mostrar Alumno" onclick="mostrarAlumno('.$reg->alumnoID.')">'.$reg->nombre.'</button>';
			$porcentaje = ($reg->asistenciasAlumnos==0)? '0' : number_format((($reg->asistenciasAlumnos)*100)/$reg->asistenciasTotales,2,'.','');
			$avance = ($reg->paginas==0)? '0' : number_format(($reg->paginas*100)/$reg->paginasCurso,2,'.','');
			$data[]=array(
				"0"=>$botonMostrarLink,
				"1"=>($reg->nombreClase)? $reg->nombreClase : 'Sin regristro' ,
				"2"=>$reg->paginas.'/'.$reg->paginasCurso.' ('.$avance.'%)',
				"3"=>$reg->nombreMaestro,
				"4"=>$porcentaje.'%',
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
		$rspta=$reportes->abreAsistencia($asistenciaID);
		echo json_encode($rspta);
	break;

	case 'listadoAlumnosClase':
		$cla = $_GET["cla"];
		$rspta=$reportes->listadoAlumnosClase($cla);
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
		$rspta=$reportes->mostrarCalendarioAlumno($alumnoID);
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
		$rspta=$reportes->mostrarCalendarioClase($claseID);
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

	case 'check_alumnos':
		$data='';
		$rspta=$reportes->check_alumnos($asistenciaID,$claseID);
		while ($reg=$rspta->fetch_object()){
			$disabled = 'disabled';
			$checked = ($reg->asistenciasAlumnos)? 'checked' : '';
			$textDecoration = ($reg->activo)? '' : 'text-decoration-line: line-through;';
			$data .= '<div class="form-check">
				<input '.$disabled.' class="form-check-input" type="checkbox" id="'.htmlspecialchars_decode($reg->nombre).'" name="alumno_id[]" value="'.$reg->alumnoID.'" '.$checked.'>
				<label style="'.$textDecoration.'color:#000!important;" class="form-check-label" for="'.htmlspecialchars_decode($reg->nombre).'">'.htmlspecialchars_decode($reg->nombre).'</label>
			</div>';
		}
		echo $data;
	break;

	case 'select_maestro':
		$rspta=$reportes->select_maestro();
		while ($reg = $rspta->fetch_object()){
			echo '<option value="'.$reg->id.'">'.$reg->nombre.'</option>';
		}
	break;
}
?>