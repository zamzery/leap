let bandera = true;
function init() {
	$( '#tblListado tfoot th' ).each( function ( i ) {
		if ( i == 5 ) {
		} else {
			var title = $( '#tblListado thead th' ).eq( $( this ).index() ).text();
			$( this ).html( '<input style="width:100%" type="text" placeholder="' + title + '" />' );
		}
	} );
	mostrarform( false ); 
	listarFacturas()
}

function mostrar( este ) {
	// $( "#tblListado" ).DataTable().destroy();
	// if ( este == 0 || este.value == '0' ) {
	// 	$.post( "../ajax/escritorio.php?op=mostrar_todos", function ( data ) {
	// 		data = JSON.parse( data );
	// 		$( "#nombreCliente" ).html( 'TODOS' );
	// 		$( "#numeroClases" ).html( data.numeroClases );
	// 		$( "#numeroAlumnos" ).html( data.numeroAlumnos );
	// 		let porcentaje = ( data.asistenciasAlumnos >= 1 ) ? ( ( data.asistenciasAlumnos * 100 ) / data.asistenciasTotales ) : 0;
	// 		$( "#asistenciasAlumnos" ).html( porcentaje.toFixed( 2 ) );
	// 		let promedio = ( data.correctas >= 1 ) ? ( ( data.correctas * 100 ) / data.preguntas ) : 0;
	// 		$( "#promedioExamen" ).html( promedio.toFixed( 2 ) );
	// 	} );
	// } else {
	// 	var clienteID = este.value;
	// 	$.post( "../ajax/escritorio.php?op=mostrar", {clienteID: clienteID}, function ( data ) {
	// 		data = JSON.parse( data );
	// 		$( "#nombreCliente" ).html( data.nombreCliente );
	// 		$( "#numeroClases" ).html( data.numeroClases );
	// 		$( "#numeroAlumnos" ).html( data.numeroAlumnos );
	// 		var porcentaje = ( data.asistenciasAlumnos ) ? ( ( data.asistenciasAlumnos * 100 ) / data.asistenciasTotales ) : 0;
	// 		$( "#asistenciasAlumnos" ).html( porcentaje.toFixed( 2 ) );
	// 		let promedio = ( data.correctas >= 1 ) ? ( ( data.correctas * 100 ) / data.preguntas ) : 0;
	// 		$( "#promedioExamen" ).html( promedio.toFixed( 2 ) );
	// 	} );
	// }
}

function listarFacturas() {
	tablaAlumnos = $( '#tblListado' ).dataTable( {
		"aProcessing": true, //Se activa el procesamiento del datatable
		"aServerSide": true, //Se pagina y filtra por medio del servidor
		dom: "f<'row'<'col-sm-2'l><'col-sm-2'B><'col-sm-8'p>> rt <'bottom'ip<'clear'>>",//Se definen los elementos de control de la tabla
		buttons: [
			{extend: 'excelHtml5', title: 'Listado de Clases', exportOptions: {columns: [ 0, 1, 2, 3, 4 ]}, className: 'btn btn-sm btn-primary'},
			{extend: 'pdf', title: 'Listado de Clases', exportOptions: {columns: [ 0, 1, 2, 3, 4 ]}, className: 'btn btn-sm btn-primary'},
		],
		"ajax": {
			url: '../ajax/facturas.php?op=listar',
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

function limpiar() {
	$( "#clienteID" ).val( "" );
	$( "#fechaCliente" ).val( "" );
	$( "#nombre" ).val( "" );
	$( "#tipo_horario" ).val( "" );
	$( '#tipo_horario' ).selectpicker( 'refresh' );
	$( "#tel" ).val( "" );
	$( "#email" ).val( "" );
	$( "#comentarios" ).val( "" );
	$( "#tblListado" ).DataTable().destroy();
	$( "#tblListado > tbody" ).empty();
	$( "#tblListado" ).html( '<thead class="bg-dark text-light"><th>Alumno</th><th>Clase</th><th>Avance</th><th>Maestro</th><th>%Asistencia</th><th></th></thead><tbody> </tbody><tfoot> <th>Alumno</th><th>Clase</th><th>Avance</th><th>Maestro</th><th>%Asistencia</th><th></th></tfoot>' );

	document.getElementById( "tituloSeccion" ).innerHTML = '';

	$( "#clienteID" ).val( "" );
	$( '#clienteID' ).selectpicker( 'refresh' );
}

function mostrarform( flag ) {
	if ( flag ) {
		$( ".formulariogrupos" ).hide();
		$( ".formularioregistros" ).show();
	} else {
		$( ".formulariogrupos" ).show();
		$( ".formularioregistros" ).hide();
	}
}

function decifraHtml( html ) {
	var txt = document.createElement( "textarea" );
	txt.innerHTML = html;
	return txt.value;
}

//Función para listar alumnos en una clase con fechas
function listarTodos( claseID = 0, inicio, fin ) {
	if ( bandera == true ) {
		tablaAlumnos = $( '#tblListado' ).dataTable( {
			"aProcessing": true, //Se activa el procesamiento del datatable
			"aServerSide": true, //Se pagina y filtra por medio del servidor
			dom: "f<'row'<'col-sm-2'B><'col-sm-2'l><'col-sm-8'p>> rt <'bottom'ip<'clear'>>",//Se definen los elementos de control de la tabla
			buttons: [
				{extend: 'excelHtml5', title: 'Listado de Alumnos', exportOptions: {columns: [ 0, 1, 2, 3, 4 ]}, className: 'btn btn-sm btn-primary'},
				{extend: 'pdf', title: 'Listado de Alumnos', exportOptions: {columns: [ 0, 1, 2, 3, 4 ]}, className: 'btn btn-sm btn-primary'},
			],
			"ajax": {
				url: '../ajax/escritorio.php?op=listarTodos&cla=' + claseID + '&inicio=' + inicio + '&fin=' + fin,
				type: "get",
				dataType: "json",
				error: function ( e ) {
					console.log( e.responseText );
				}
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
		bandera = false;
	} else {
		listar( claseID, inicio, fin );
		bandera = false;
	}
}

function listar( claseID = 0, inicio, fin ) {
	$.getJSON( '../ajax/cliente_resumen.php?op=listar&cla=' + claseID + '&inicio=' + inicio + '&fin=' + fin, function ( json ) {
		$( '#tblListado' ).DataTable().clear().draw();
		tablaAlumnos.rows.add( json.aaData );
		tablaAlumnos.draw();
	} );
}

init();