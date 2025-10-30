<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Reporte {
	//Implementamos nuestro constructor
	public function __construct(){
		
	}

	public function mostrar($clienteID){
		$sql="SELECT cli.id AS clienteID,cli.nombre AS nombreCliente,IFNULL(cla.numeroClases,0) AS numeroClases,IFNULL(alu.numeroAlumnos,0) AS numeroAlumnos,IFNULL(alunum.asistenciasAlumnos,0) AS asistenciasAlumnos,IFNULL(alunum.asistenciasTotales,0) AS asistenciasTotales FROM clientes cli LEFT JOIN (SELECT id,cliente_id,COUNT(id) AS numeroClases FROM clases WHERE activo='1' GROUP BY cliente_id) cla ON cli.id=cla.cliente_id LEFT JOIN (SELECT cliente_id,COUNT(id) AS numeroAlumnos FROM alumnos WHERE activo='1' GROUP BY cliente_id) alu ON cli.id=alu.cliente_id

		LEFT JOIN (SELECT a.cliente_id,a.id AS alumnoID,a.activo,usr.nombre AS nombreVendedor,IFNULL(a.updated_at,a.created_at) AS fechaCreacion,a.user_id,cli.nombre AS nombreCliente,cla.nombre AS nombreClase,SUM(asist.asistenciasTotales) AS asistenciasTotales,SUM(alu.asistenciasAlumnos) AS asistenciasAlumnos FROM alumnos a LEFT JOIN users usr ON a.user_id=usr.id INNER JOIN clientes cli ON cli.id=a.cliente_id LEFT JOIN (SELECT nombre,id FROM clases WHERE activo='1') cla ON a.clase_id=cla.id LEFT JOIN (SELECT ast.clase_id,COUNT(DISTINCT ast.id) AS asistenciasTotales FROM asistencias ast INNER JOIN clases cl ON ast.clase_id=cl.id LEFT JOIN asistenciaAlumno a ON ast.id=a.asistencia_id GROUP BY ast.clase_id) asist ON cla.id=asist.clase_id LEFT JOIN (SELECT a.alumno_id,ast.clase_id,COUNT(DISTINCT ast.id) AS asistenciasTotales,COUNT(a.alumno_id) AS asistenciasAlumnos,COUNT(DISTINCT a.alumno_id) AS alumnos FROM asistencias ast INNER JOIN clases cl ON ast.clase_id=cl.id LEFT JOIN asistenciaAlumno a ON ast.id=a.asistencia_id GROUP BY a.alumno_id) alu ON a.id=alu.alumno_id INNER JOIN alumnos alm ON alu.alumno_id=alm.id WHERE a.cliente_id='$clienteID' AND alm.activo='1' GROUP BY a.cliente_id) alunum ON cli.id=alunum.cliente_id WHERE cli.id='$clienteID' GROUP BY cli.id";
		return ejecutarConsultaSimpleFila($sql);
	}

	//LEFT JOIN (SELECT examen_id,alumno_id,SUM(IF(respuesta=correcta,1,0)) AS respuestas,COUNT(id) AS preguntas FROM examenAlumnoRespuesta WHERE alumno_id='$alumnoID' GROUP BY examen_id LIMIT 1) resp ON a.id=resp.alumno_id

	public function progresoClases($clienteID){
		$sql="SELECT cla.id AS claseID,cla.cliente_id,cla.nombre AS nombreClase,cur.nombre AS nombreCurso,det.paginas_totales,IFNULL(asi.paginaCurso,0) AS paginaCurso,alu.totalAlumnos,mae.nombre AS nombreMaestro,asist.asistenciasTotales,asial.asistenciasAlumnos FROM clases cla INNER JOIN cursos cur ON cla.curso_id=cur.id INNER JOIN (SELECT curso_id,SUM(paginas) AS paginas_totales FROM cursoSecciones GROUP BY curso_id) det ON cur.id=det.curso_id LEFT JOIN (SELECT a.clase_id,cur.nombre AS nombreCurso,MAX(a.numero_pagina) AS paginaCurso,cl.curso_id FROM asistencias a INNER JOIN clases cl ON a.clase_id=cl.id INNER JOIN cursos cur ON cl.curso_id=cur.id WHERE cl.curso_id=a.curso_id GROUP BY a.clase_id) asi ON cla.id=asi.clase_id LEFT JOIN (SELECT COUNT(id) AS totalAlumnos,clase_id FROM alumnos WHERE activo='1' GROUP BY clase_id) alu ON cla.id=alu.clase_id INNER JOIN users mae ON cla.maestro_id=mae.id

		LEFT JOIN (SELECT asist.clase_id,ROUND(COUNT(a.alumno_id)/COUNT(DISTINCT a.alumno_id) ,0) AS asistenciasAlumnos FROM asistenciaAlumno a INNER JOIN asistencias asist ON asist.id=a.asistencia_id INNER JOIN alumnos alm ON a.alumno_id=alm.id WHERE alm.activo='1' GROUP BY asist.clase_id) asial ON cla.id=asial.clase_id
		
		LEFT JOIN (SELECT ast.clase_id,COUNT(DISTINCT ast.id) AS asistenciasTotales FROM asistencias ast INNER JOIN asistenciaAlumno a ON ast.id=a.asistencia_id INNER JOIN alumnos alm ON a.alumno_id=alm.id WHERE alm.activo='1' AND ast.created_at>=IFNULL(alm.updated_at,alm.created_at) GROUP BY ast.clase_id) asist ON cla.id=asist.clase_id

		WHERE cla.cliente_id='$clienteID' GROUP BY cla.id ORDER BY cla.id DESC";
		return ejecutarConsulta($sql);
	}

	public function listadoClases($clienteID){
		$sql="SELECT cla.id AS claseID,cla.cliente_id,cla.nombre,cla.curso_id,cla.maestro_id,alu.alumnos,mae.nombre AS nombreMaestro,IFNULL(asi.nombreCurso,curso.nombre) AS nombreCurso,IFNULL(asi.paginas,0) AS paginas,IFNULL(curs.paginasCurso,curso.paginasCurso) AS paginasCurso,cla.activo,cli.nombre AS nombreCliente,asist.asistenciasTotales,asist.asistenciasAlumnos FROM clases cla LEFT JOIN (SELECT clase_id,COUNT(alumno_id) AS alumnos FROM claseDetalle GROUP BY clase_id) alu ON cla.id=alu.clase_id LEFT JOIN (SELECT a.clase_id,cur.nombre AS nombreCurso,MAX(a.numero_pagina) AS paginas,cl.curso_id FROM asistencias a INNER JOIN clases cl ON a.clase_id=cl.id INNER JOIN cursos cur ON cl.curso_id=cur.id WHERE cl.curso_id=a.curso_id GROUP BY a.clase_id) asi ON cla.id=asi.clase_id LEFT JOIN (SELECT curso_id,SUM(paginas) AS paginasCurso FROM cursoSecciones GROUP BY curso_id) curs ON curs.curso_id=asi.curso_id INNER JOIN users mae ON cla.maestro_id=mae.id INNER JOIN (SELECT rep.nombre,rep.id,SUM(dea.paginas) AS paginasCurso FROM cursos rep INNER JOIN cursoSecciones dea ON rep.id=dea.curso_id GROUP BY rep.id) curso ON cla.curso_id=curso.id INNER JOIN clientes cli ON cla.cliente_id=cli.id LEFT JOIN (SELECT ast.clase_id,COUNT(DISTINCT ast.id) AS asistenciasTotales,COUNT(DISTINCT a.alumno_id) AS asistenciasAlumnos,COUNT(DISTINCT a.alumno_id) AS alumnos FROM asistencias ast INNER JOIN clases cl ON ast.clase_id=cl.id LEFT JOIN asistenciaAlumno a ON ast.id=a.asistencia_id INNER JOIN alumnos alm ON a.alumno_id=alm.id WHERE alm.activo='1' GROUP BY ast.clase_id) asist ON cla.id=asist.clase_id WHERE cla.cliente_id='$clienteID' GROUP BY cla.id ORDER BY cla.created_at DESC";
		return ejecutarConsulta($sql);
		// WHERE MONTH(ast.asistencia_inicio) = MONTH(CURRENT_DATE()) AND YEAR(ast.asistencia_inicio) = YEAR(CURRENT_DATE())
	}

	public function listadoAlumnos($clienteID){
		$sql="SELECT a.id AS alumnoID,a.nombre,a.calle,a.num_ext,a.num_int,a.telefono,a.email,a.activo,mae.nombre AS nombreMaestro,IFNULL(a.updated_at,a.created_at) AS fechaCreacion,a.user_id,cla.nombre AS nombreClase,asist.asistenciasTotales,IFNULL(alu.asistenciasAlumnos,0) AS asistenciasAlumnos,IFNULL(pag.paginas,0) AS paginas,IFNULL(curs.paginasCurso,curso.paginasCurso) AS paginasCurso FROM alumnos a LEFT JOIN (SELECT maestro_id,nombre,id,curso_id FROM clases WHERE activo='1') cla ON a.clase_id=cla.id LEFT JOIN (SELECT curso_id,SUM(paginas) AS paginasCurso FROM cursoSecciones GROUP BY curso_id) curs ON curs.curso_id=cla.curso_id LEFT JOIN users mae ON cla.maestro_id=mae.id LEFT JOIN (SELECT rep.nombre,rep.id,SUM(dea.paginas) AS paginasCurso FROM cursos rep LEFT JOIN cursoSecciones dea ON rep.id=dea.curso_id GROUP BY rep.id) curso ON cla.curso_id=curso.id LEFT JOIN (SELECT a.clase_id,cur.nombre AS nombreCurso,MAX(a.numero_pagina) AS paginas,cl.curso_id FROM asistencias a INNER JOIN clases cl ON a.clase_id=cl.id INNER JOIN cursos cur ON cl.curso_id=cur.id WHERE cl.curso_id=a.curso_id GROUP BY a.clase_id) pag ON a.clase_id=pag.clase_id
		
		LEFT JOIN (SELECT alm.id AS alumno_id,COUNT(DISTINCT ast.id) AS asistenciasTotales FROM alumnos alm INNER JOIN asistencias ast ON alm.clase_id=ast.clase_id WHERE ast.created_at>=IFNULL(alm.updated_at,alm.created_at) GROUP BY alm.id) asist ON a.id=asist.alumno_id
		
		LEFT JOIN (SELECT a.alumno_id,ast.clase_id,COUNT(a.alumno_id) AS asistenciasAlumnos FROM asistencias ast LEFT JOIN clases cl ON ast.clase_id=cl.id LEFT JOIN asistenciaAlumno a ON ast.id=a.asistencia_id GROUP BY a.alumno_id) alu ON a.id=alu.alumno_id
		
		WHERE a.cliente_id='$clienteID' AND a.activo='1' GROUP BY a.id";
		return ejecutarConsulta($sql);
	}

	public function mostrarAlumno($alumnoID){
		$sql="SELECT a.id AS alumnoID,a.nombre AS nombreAlumno,a.calle,a.num_ext,a.num_int,a.telefono,a.email,a.activo,mae.nombre AS nombreMaestro,IFNULL(a.updated_at,a.created_at) AS fechaCreacion,a.user_id,cla.nombre AS nombreClase,asist.asistenciasTotales,IFNULL(alu.asistenciasAlumnos,0) AS asistenciasAlumnos,IFNULL(pag.paginas,0) AS paginas,IFNULL(curs.paginasCurso,curso.paginasCurso) AS paginasCurso FROM alumnos a LEFT JOIN (SELECT maestro_id,nombre,id,curso_id FROM clases WHERE activo='1') cla ON a.clase_id=cla.id LEFT JOIN (SELECT curso_id,SUM(paginas) AS paginasCurso FROM cursoSecciones GROUP BY curso_id) curs ON curs.curso_id=cla.curso_id LEFT JOIN users mae ON cla.maestro_id=mae.id LEFT JOIN (SELECT rep.nombre,rep.id,SUM(dea.paginas) AS paginasCurso FROM cursos rep LEFT JOIN cursoSecciones dea ON rep.id=dea.curso_id GROUP BY rep.id) curso ON cla.curso_id=curso.id LEFT JOIN (SELECT a.clase_id,cur.nombre AS nombreCurso,MAX(a.numero_pagina) AS paginas,cl.curso_id FROM asistencias a INNER JOIN clases cl ON a.clase_id=cl.id INNER JOIN cursos cur ON cl.curso_id=cur.id WHERE cl.curso_id=a.curso_id GROUP BY a.clase_id) pag ON a.clase_id=pag.clase_id

		LEFT JOIN (SELECT alm.id AS alumno_id,COUNT(DISTINCT ast.id) AS asistenciasTotales FROM alumnos alm INNER JOIN asistencias ast ON alm.clase_id=ast.clase_id WHERE ast.created_at>=IFNULL(alm.updated_at,alm.created_at) GROUP BY alm.id) asist ON a.id=asist.alumno_id
		
		LEFT JOIN (SELECT a.alumno_id,ast.clase_id,COUNT(a.alumno_id) AS asistenciasAlumnos FROM asistencias ast LEFT JOIN clases cl ON ast.clase_id=cl.id LEFT JOIN asistenciaAlumno a ON ast.id=a.asistencia_id GROUP BY a.alumno_id) alu ON a.id=alu.alumno_id
		
		LEFT JOIN alumnos alm ON alu.alumno_id=alm.id WHERE a.id='$alumnoID' GROUP BY a.id";
		return ejecutarConsulta($sql);
	}

	public function mostrarClase($claseID){
		$sql="SELECT cla.id AS claseID,cla.cliente_id,cla.nombre,alu.totalAlumnos,mae.nombre AS nombreMaestro,IFNULL(asi.nombreCurso,curso.nombre) AS nombreCurso,IFNULL(asi.paginas,0) AS paginas,IFNULL(curs.paginasCurso,curso.paginasCurso) AS paginasCurso,cla.activo,cli.nombre AS nombreCliente,asist.asistenciasTotales,asial.asistenciasAlumnos FROM clases cla LEFT JOIN (SELECT clase_id,COUNT(alumno_id) AS totalAlumnos FROM claseDetalle GROUP BY clase_id) alu ON cla.id=alu.clase_id LEFT JOIN (SELECT a.clase_id,cur.nombre AS nombreCurso,MAX(a.numero_pagina) AS paginas,cl.curso_id FROM asistencias a INNER JOIN clases cl ON a.clase_id=cl.id INNER JOIN cursos cur ON cl.curso_id=cur.id WHERE cl.curso_id=a.curso_id GROUP BY a.clase_id) asi ON cla.id=asi.clase_id LEFT JOIN (SELECT curso_id,SUM(paginas) AS paginasCurso FROM cursoSecciones GROUP BY curso_id) curs ON curs.curso_id=asi.curso_id INNER JOIN users mae ON cla.maestro_id=mae.id INNER JOIN (SELECT rep.nombre,rep.id,SUM(dea.paginas) AS paginasCurso FROM cursos rep INNER JOIN cursoSecciones dea ON rep.id=dea.curso_id GROUP BY rep.id) curso ON cla.curso_id=curso.id INNER JOIN clientes cli ON cla.cliente_id=cli.id
		
		LEFT JOIN (SELECT asist.clase_id,ROUND(COUNT(a.alumno_id)/COUNT(DISTINCT a.alumno_id) ,0) AS asistenciasAlumnos FROM asistenciaAlumno a INNER JOIN asistencias asist ON asist.id=a.asistencia_id INNER JOIN alumnos alm ON a.alumno_id=alm.id WHERE alm.activo='1' GROUP BY asist.clase_id) asial ON cla.id=asial.clase_id
		
		LEFT JOIN (SELECT ast.clase_id,COUNT(DISTINCT ast.id) AS asistenciasTotales FROM asistencias ast INNER JOIN asistenciaAlumno a ON ast.id=a.asistencia_id INNER JOIN alumnos alm ON a.alumno_id=alm.id WHERE alm.activo='1' AND ast.created_at>=IFNULL(alm.updated_at,alm.created_at) GROUP BY ast.clase_id) asist ON cla.id=asist.clase_id
		
		WHERE cla.id='$claseID' GROUP BY cla.id ORDER BY cla.created_at DESC";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listadoAlumnosClase($claseID){
		$sql="SELECT a.id AS alumnoID,a.nombre,a.activo,mae.nombre AS nombreMaestro,IFNULL(a.updated_at,a.created_at) AS fechaCreacion,a.user_id,cla.nombre AS nombreClase,asist.asistenciasTotales,IFNULL(alu.asistenciasAlumnos,0) AS asistenciasAlumnos,IFNULL(pag.paginas,0) AS paginas,IFNULL(curs.paginasCurso,curso.paginasCurso) AS paginasCurso FROM alumnos a LEFT JOIN (SELECT maestro_id,nombre,id,curso_id FROM clases WHERE activo='1') cla ON a.clase_id=cla.id LEFT JOIN (SELECT curso_id,SUM(paginas) AS paginasCurso FROM cursoSecciones GROUP BY curso_id) curs ON curs.curso_id=cla.curso_id LEFT JOIN users mae ON cla.maestro_id=mae.id LEFT JOIN (SELECT rep.nombre,rep.id,SUM(dea.paginas) AS paginasCurso FROM cursos rep LEFT JOIN cursoSecciones dea ON rep.id=dea.curso_id GROUP BY rep.id) curso ON cla.curso_id=curso.id LEFT JOIN (SELECT ast.clase_id,MAX(ast.numero_pagina) AS paginas FROM asistencias ast LEFT JOIN asistenciaAlumno a ON ast.id=a.asistencia_id GROUP BY ast.clase_id) pag ON cla.id=pag.clase_id

		LEFT JOIN (SELECT alm.id AS alumno_id,COUNT(DISTINCT ast.id) AS asistenciasTotales FROM alumnos alm INNER JOIN asistencias ast ON alm.clase_id=ast.clase_id WHERE ast.created_at>=IFNULL(alm.updated_at,alm.created_at) GROUP BY alm.id) asist ON a.id=asist.alumno_id
		
		LEFT JOIN (SELECT a.alumno_id,ast.clase_id,COUNT(a.alumno_id) AS asistenciasAlumnos FROM asistencias ast LEFT JOIN clases cl ON ast.clase_id=cl.id LEFT JOIN asistenciaAlumno a ON ast.id=a.asistencia_id GROUP BY a.alumno_id) alu ON a.id=alu.alumno_id

		WHERE cla.id='$claseID' AND a.activo='1' GROUP BY a.id";
		return ejecutarConsulta($sql);
	}

	public function mostrarCalendarioAlumno($alumnoID){
		$sql="SELECT asi.id AS asistenciaID,asi.clase_id AS claseID,cla.cliente_id,cla.nombre_clase_id,cla.curso_id,asi.maestro_id,cla.costo,cla.comentarios,cla.user_id,cla.activo,cli.nombre AS nombreCliente,mae.nombre AS nombreMaestro,mae.color,mae.colorText,asi.asistencia_inicio,asi.horario_inicio,asi.horario_fin,alu.nombresAlumnos FROM (SELECT a.alumno_id,b.clase_id FROM asistenciaAlumno a INNER JOIN asistencias b ON a.asistencia_id=b.id WHERE a.alumno_id='$alumnoID' GROUP BY a.alumno_id) asicla INNER JOIN clases cla ON asicla.clase_id=cla.id INNER JOIN asistencias asi ON cla.id=asi.clase_id LEFT JOIN (SELECT asis.asistencia_id,GROUP_CONCAT(DISTINCT al.nombre SEPARATOR ', ') AS nombresAlumnos FROM asistenciaAlumno asis INNER JOIN alumnos al ON asis.alumno_id=al.id WHERE asis.alumno_id='$alumnoID' GROUP BY asis.asistencia_id) alu ON asi.id=alu.asistencia_id INNER JOIN clientes cli ON cla.cliente_id=cli.id LEFT JOIN users mae ON asi.maestro_id=mae.id";
		return ejecutarConsulta($sql);
	}

	public function mostrarCalendarioClase($claseID){
		$sql="SELECT asi.id AS asistenciaID,asi.clase_id AS claseID,cla.cliente_id,cla.nombre_clase_id,cla.curso_id,asi.maestro_id,cla.costo,cla.comentarios,cla.user_id,cla.activo,cli.nombre AS nombreCliente,mae.nombre AS nombreMaestro,mae.color,mae.colorText,asi.asistencia_inicio,asi.horario_inicio,asi.horario_fin FROM (SELECT a.alumno_id,b.clase_id FROM asistenciaAlumno a INNER JOIN asistencias b ON a.asistencia_id=b.id WHERE b.clase_id='$claseID' GROUP BY a.alumno_id) asicla INNER JOIN clases cla ON asicla.clase_id=cla.id INNER JOIN asistencias asi ON cla.id=asi.clase_id INNER JOIN clientes cli ON cla.cliente_id=cli.id LEFT JOIN users mae ON asi.maestro_id=mae.id GROUP BY asi.id";
		return ejecutarConsulta($sql);
	}

	public function abreAsistencia($asistenciaID){
		$sql="SELECT asi.id AS asistenciaID,asi.clase_id AS claseID,asi.asistencia_inicio,asi.horario_inicio,asi.horario_fin,asi.capitulo,asi.paginas,asi.comentarios,asi.numero_pagina,IF(asi.numero_pagina-asi.paginas<0,0,asi.numero_pagina-asi.paginas) AS paginaAnterior,asi.maestro_id,curs.paginasCurso,curs.curso_id,asi.examen_id FROM asistencias asi INNER JOIN clases cla ON asi.clase_id=cla.id LEFT JOIN (SELECT curso_id,SUM(paginas) AS paginasCurso FROM cursoSecciones GROUP BY curso_id) curs ON curs.curso_id=cla.curso_id LEFT JOIN (SELECT IFNULL(updated_at,created_at) AS fecha,clase_id,numero_pagina AS paginaAnterior FROM asistencias ORDER BY IFNULL(updated_at,created_at) DESC) ant ON asi.clase_id=ant.clase_id WHERE asi.id='$asistenciaID' GROUP BY asi.id";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function check_alumnos($asistenciaID,$claseID){
		$sql="SELECT alu.id AS alumnoID,alu.nombre,det.asistencia_id,det.alumno_id AS asistenciasAlumnos,alu.activo FROM alumnos alu LEFT JOIN (SELECT alumno_id,asistencia_id FROM asistenciaAlumno WHERE asistencia_id='$asistenciaID') det ON alu.id=det.alumno_id WHERE alu.clase_id='$claseID' GROUP BY alu.id";
		return ejecutarConsulta($sql);
	}

	public function select_maestro(){
		$sql="SELECT id,nombre FROM users WHERE activo='1' AND maestro='1'";
		return ejecutarConsulta($sql);
	}
}
?>