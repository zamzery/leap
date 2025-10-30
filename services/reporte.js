var tabla;
var tablaAlumnos;
var tablaAlumnosClase;
//función que se ejecuta al inicio
function init() {
	mostrarform( false );
	mostrar();
	progresoClases();
	listadoAlumnos();

	$( '#listadoAlumnos tfoot th' ).each( function ( i ) {
		if ( i == 5 ) {
		} else {
			var title = $( '#listadoAlumnos thead th' ).eq( $( this ).index() ).text();
			$( this ).html( '<input style="width:100%" type="text" placeholder="' + title + '" />' );
		}
	} );

	$.post( "../ajax/resumen.php?op=select_maestro", function ( r ) {
		$( "#maestro_id_asistencia" ).html( r );
		$( "#maestro_id_asistencia" ).selectpicker( 'refresh' );
	} );
}

function mostrar() {
	var clienteID = document.getElementById( "clienteID" ).value;
	$.post( "../ajax/resumen.php?op=mostrar", {clienteID: clienteID}, function ( data ) {
		data = JSON.parse( data );
		$( "#nombreCliente" ).html( data.nombreCliente );
		$( "#numeroClases" ).html( data.numeroClases );
		$( "#numeroAlumnos" ).html( data.numeroAlumnos );
		var porcentaje = ( data.asistenciasAlumnos ) ? ( ( data.asistenciasAlumnos * 100 ) / data.asistenciasTotales ) : 0;
		$( "#asistenciasAlumnos" ).html( porcentaje.toFixed( 2 ) );
	} );
}

function progresoClases() {
	var clienteID = document.getElementById( "clienteID" ).value;
	$.post( "../ajax/resumen.php?op=progresoClases", {clienteID: clienteID}, function ( r ) {
		document.getElementById( "progreso-clases" ).innerHTML = r;
	} );
}

function listadoAlumnos() {
	var clienteID = document.getElementById( "clienteID" ).value;
	tablaAlumnos = $( '#listadoAlumnos' ).dataTable( {
		"aProcessing": true, //Se activa el procesamiento del datatable
		"aServerSide": true, //Se pagina y filtra por medio del servidor
		dom: "f<'row'<'col-sm-2'l><'col-sm-1'><'col-sm-9'p>> rt <'bottom'ip<'clear'>>",//Se definen los elementos de control de la tabla
		buttons: [
			// {extend: 'excelHtml5', title: 'Listado de Clases', exportOptions: {columns: [ 0, 1, 2, 3 ]}, className: 'btn btn-sm btn-primary'},
			// {extend: 'pdf', title: 'Listado de Clases', exportOptions: {columns: [ 0, 1, 2, 3 ]}, className: 'btn btn-sm btn-primary'},
		],
		"ajax": {
			url: '../ajax/resumen.php?op=listadoAlumnos&cli=' + clienteID,
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"initComplete": function () {
			// Apply the search
			this.api().columns().every( function () {
				var that = this;

				$( 'input', this.footer() ).on( 'keyup change clear', function () {
					if ( that.search() !== this.value ) {
						that
							.search( this.value )
							.draw();
					}
				} );
			} );
		},
		"columnDefs": [
			{"width": "150px", "targets": [ 1 ]},
			{"width": "100px", "targets": [ 2, 3 ]},
			{"width": "50px", "targets": [ 4, 5 ]},
			{"className": "text-center", "targets": [ 2, 3, 4, 5 ]}
		],
		"lengthMenu": [
			[ 10, 20, 50, -1 ],
			[ 10, 20, 50, 'Todos' ],
		],
		"Destroy": true,
		"iDisplayLength": 50, //Número de registros para paginar
		"order": [ [ 0, "asc" ] ] //Ordenar (columna, orden ascendente o descendente, etc)
	} ).DataTable();
}

function listadoAlumnosClase( claseID ) {
	tablaAlumnosClase = $( '#listadoAlumnosClase' ).dataTable( {
		"aProcessing": true, //Se activa el procesamiento del datatable
		"aServerSide": true, //Se pagina y filtra por medio del servidor
		dom: "f<'row'<'col-sm-2'l><'col-sm-1'><'col-sm-9'p>> rt <'bottom'ip<'clear'>>",//Se definen los elementos de control de la tabla
		buttons: [
			// {extend: 'excelHtml5', title: 'Listado de Clases', exportOptions: {columns: [ 0, 1, 2, 3 ]}, className: 'btn btn-sm btn-primary'},
			// {extend: 'pdf', title: 'Listado de Clases', exportOptions: {columns: [ 0, 1, 2, 3 ]}, className: 'btn btn-sm btn-primary'},
		],
		"ajax": {
			url: '../ajax/resumen.php?op=listadoAlumnosClase&cla=' + claseID,
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"columnDefs": [
			{"width": "100px", "targets": [ 1, 2 ]},
			{"className": "text-center", "targets": [ 1, 2 ]}
		],
		"lengthMenu": [
			[ 10, 20, 50, -1 ],
			[ 10, 20, 50, 'Todos' ],
		],
		"Destroy": true,
		"iDisplayLength": 50, //Número de registros para paginar
		"order": [ [ 0, "asc" ] ] //Ordenar (columna, orden ascendente o descendente, etc)
	} ).DataTable();
}

function mostrarform( flag ) {
	if ( flag ) {
		$( ".thead-dark" ).hide();
		$( ".listadoregistros" ).hide();
		$( ".formularioregistros" ).show();
		$( "#btnGuardar" ).prop( "disabled", false );
		$( "#btn-agregar" ).hide();
		$( ".titulos" ).hide();
	} else {
		$( ".thead-dark" ).show();
		$( ".listadoregistros" ).show();
		$( ".formularioregistros" ).hide();
		$( "#btn-agregar" ).show();
		$( ".titulos" ).show();
	}
}

function cancelarform() {
	limpiar();
	mostrarform( false );
}

function limpiar() {
	$( "#alumnoID" ).val( "" );
	$( "#fechaCliente" ).val( "" );
	$( "#nombre" ).val( "" );
	$( "#tipo_horario" ).val( "" );
	$( '#tipo_horario' ).selectpicker( 'refresh' );
	$( "#tel" ).val( "" );
	$( "#email" ).val( "" );
	$( "#comentarios" ).val( "" );
	$( "#listadoAlumnosClase" ).DataTable().destroy();
	$( "#listadoAlumnosClase > tbody" ).empty();
	$( "#listadoAlumnosClase" ).html( '<thead class="bg-dark text-light"><th>Alumno</th><th>%Asistencia</th><th>Ultimo Examen</th></thead><tbody> </tbody><tfoot> <th>Alumno</th><th>%Asistencia</th><th>Ultimo Examen</th></tfoot>' );

	document.getElementById( "tituloSeccion" ).innerHTML = '';
}

function mostrarAlumno( alumnoID ) {
	document.getElementById( "tituloSeccion" ).innerHTML = 'ALUMNO';

	$.post( "../ajax/resumen.php?op=mostrarAlumno", {alumnoID: alumnoID}, function ( data, statusCliente ) {
		mostrarform( true );
		$( "#datosAlumno" ).html( data );
		mostrarCalendarioAlumno( alumnoID );

		$( ".datosClase" ).hide();
		$( ".datosAlumno" ).show();
	} );
}

function mostrarClase( claseID ) {
	document.getElementById( "tituloSeccion" ).innerHTML = 'GRUPO';
	listadoAlumnosClase( claseID );
	$.post( "../ajax/resumen.php?op=mostrarClase", {claseID: claseID}, function ( resp ) {
		resp = JSON.parse( resp );
		var resultadoPorcentaje = ( parseFloat( resp.asistenciasAlumnos ) + 1.25 ) * 100 / parseFloat( resp.asistenciasTotales );
		var asistencia = ( parseFloat( resp.asistenciasAlumnos ) == 0 ) ? '0' : ( ( resultadoPorcentaje >= 100 ) ? 100 : resultadoPorcentaje ).toFixed( 2 );
		var avance = ( parseFloat( resp.paginas ) == 0 ) ? '0' : ( ( ( parseFloat( resp.paginas ) ) * 100 ) / parseFloat( resp.paginasCurso ) ).toFixed( 2 );
		mostrarform( true );

		$( "#datosClase" ).html( '<div class="mb-2 datosCLase">' +
			'		<div class="d-flex flex-row justify-content-between flex-wrap">' +
			'			<span class="text-start"><strong>' + resp.nombre + '</strong> ' + resp.nombreCurso + '</span>  <span class="text-center">Alumnos: ' + resp.totalAlumnos + '</span>' +
			'			<span class="text-center">Teacher: ' + resp.nombreMaestro + '</span>' +
			'		</div>' +
			'		<div class="d-flex flex-row justify-content-between flex-wrap">' +

			'		</div>' +
			'		<div class="row">' +
			'			<div class="col-lg-6 col-xl-6 mb-4 text-body">' +
			'				<span class="float-start"><strong>Avance:</strong> ' + resp.paginas + '/' + resp.paginasCurso + '</span> <span class="float-end">' + avance + '%</span><br>' +
			'				<div class="progress"> ' +
			'					<div class="progress-bar bg-success" role="progressbar" style="width: ' + avance + '%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="' + resp.paginasCurso + '"></div>' +
			'				</div>' +
			'			</div>' +
			'			<div class="col-lg-6 col-xl-6 mb-4 text-body">' +
			'				<span class="float-start"><strong>Asistencia:</strong></span> <span class="float-end">' + asistencia + '%</span><br>' +
			'				<div class="progress">' +
			'					<div class="progress-bar bg-success" role="progressbar" style="width: ' + asistencia + '%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="' + resp.paginasCurso + '"></div>' +
			'				</div>' +
			'			</div>' +
			'		</div>' +
			'	</div>' );
		mostrarCalendarioClase( claseID );

		$( ".datosClase" ).show();
		$( ".datosAlumno" ).hide();
	} );
}

function decifraHtml( html ) {
	var txt = document.createElement( "textarea" );
	txt.innerHTML = html;
	return txt.value;
}

function mostrarCalendarioAlumno( alumnoID ) {
	$.post( "../ajax/resumen.php?op=mostrarCalendarioAlumno", {alumnoID: alumnoID}, function ( eve ) {
		var clases = JSON.parse( eve );
		var calendarEl = document.getElementById( 'calendarioClases' );
		var calendar = new FullCalendar.Calendar( calendarEl, {
			locale: 'es',
			initialView: 'dayGridMonth',
			headerToolbar: {
				left: 'today,prev,next',
				center: 'title',
				right: 'dayGridMonth,dayGridWeek,listWeek'
			},
			eventDisplay: 'block',
			nowIndicator: true,
			displayEventTime: false,
			timeZone: 'America/Mexico',
			height: 'auto',
			selectable: true,
			firstDay: 1,
			navLinks: true,
			events: clases,
			eventClick: function ( e ) {
				abreAsistencia( e.event.extendedProps.asistenciaID, e.event.extendedProps.claseID, 1 );
			},
			// eventMouseEnter: function ( info ) {
			// 	var tooltip = '<div class="tooltipevent" id="tooltip">Nombre: <b>' + info.event.extendedProps.nombreAlumno + '</b><br><b>Horario: ' + info.event.extendedProps.horario + '<br>Inicio: ' + info.event.extendedProps.inicio + '<br> Fin: ' + info.event.extendedProps.fin + '</br></div>';
			// 	var toolBody = $( tooltip ).appendTo( 'body' );

			// 	onmousemove = ( eve ) => {
			// 		toolBody.css( 'z-index', 10000 );
			// 		toolBody.css( 'top', eve.y + 10 );
			// 		toolBody.css( 'left', eve.x + 20 );
			// 	}
			// },
			// eventMouseLeave: function () {
			// 	$( this ).css( 'z-index', 8 );
			// 	$( '.tooltipevent' ).remove();
			// },
		} );
		setTimeout( function () {
			calendar.render();
		}, 300 );
	} );
}

function mostrarCalendarioClase( claseID ) {
	$.post( "../ajax/resumen.php?op=mostrarCalendarioClase", {claseID: claseID}, function ( eve ) {
		var clases = JSON.parse( eve );
		var calendarEl = document.getElementById( 'calendarioClases' );
		var calendar = new FullCalendar.Calendar( calendarEl, {
			locale: 'es',
			initialView: 'dayGridMonth',
			headerToolbar: {
				left: 'today,prev,next',
				center: 'title',
				right: 'dayGridMonth,dayGridWeek,listWeek'
			},
			eventDisplay: 'block',
			nowIndicator: true,
			displayEventTime: false,
			timeZone: 'America/Mexico',
			height: 'auto',
			selectable: true,
			firstDay: 1,
			navLinks: true,
			events: clases,
			eventClick: function ( e ) {
				abreAsistencia( e.event.extendedProps.asistenciaID, e.event.extendedProps.claseID, 1 );
			},
			// eventMouseEnter: function ( info ) {
			// 	var tooltip = '<div class="tooltipevent" id="tooltip">Nombre: <b>' + info.event.extendedProps.nombreAlumno + '</b><br><b>Horario: ' + info.event.extendedProps.horario + '<br>Inicio: ' + info.event.extendedProps.inicio + '<br> Fin: ' + info.event.extendedProps.fin + '</br></div>';
			// 	var toolBody = $( tooltip ).appendTo( 'body' );

			// 	onmousemove = ( eve ) => {
			// 		toolBody.css( 'z-index', 10000 );
			// 		toolBody.css( 'top', eve.y + 10 );
			// 		toolBody.css( 'left', eve.x + 20 );
			// 	}
			// },
			// eventMouseLeave: function () {
			// 	$( this ).css( 'z-index', 8 );
			// 	$( '.tooltipevent' ).remove();
			// },
		} );
		setTimeout( function () {
			calendar.render();
		}, 300 );
	} );
}

async function abreAsistencia( asistenciaID, claseID, clase ) {
	if ( clase == 1 ) {
		$.post( "../ajax/resumen.php?op=check_alumnos", {asistenciaID: asistenciaID, claseID: claseID}, function ( resp ) {
			$( "#alumnosLista" ).html( resp );
		} );
	}

	await $.post( "../ajax/resumen.php?op=abreAsistencia", {asistenciaID: asistenciaID}, function ( resp ) {
		data = JSON.parse( resp );
		$( "#paginasCurso" ).html( data.paginasCurso );
		$( "#maestro_id_asistencia" ).val( data.maestro_id );
		$( "#maestro_id_asistencia" ).selectpicker( "refresh" );
		$( "#asistenciaID" ).val( data.asistenciaID );
		$( "#capitulo" ).val( data.capitulo );
		$( "#paginas" ).val( data.paginas );
		$( "#numero_pagina" ).val( data.numero_pagina );
		$( "#paginaAnterior" ).val( data.paginaAnterior );
		$( "#asistencia_inicioInput" ).val( data.asistencia_inicio );
		var str = data.horario_inicio;
		var horario_inicio = str.substring( 0, str.length - 3 );
		$( "#horario_inicioInput" ).val( horario_inicio );
		if ( data.horario_fin != '00:00:00' ) {
			var str2 = data.horario_inicio;
			var horario_fin = str2.substring( 0, str2.length - 3 );
			$( "#horario_finInput" ).val( horario_fin );
		} else {
			$( "#horario_finInput" ).val( "" )
		}
		$( "#comentarios" ).val( data.comentarios );
		$( "#modalAsistencia" ).modal( "show" );

		$.post( "../ajax/curso.php?op=listadoCapitulos", {cursoID: data.curso_id}, function ( res ) {
			$( "#listadoCapitulos" ).html( res );
		} );
	} );
}

init();