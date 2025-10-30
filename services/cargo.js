var tabla;
var tabla_permiso;
var flagPermiso = true;
//función que se ejecuta al inicio
function init() {
	mostrarform( false );
	listar();

	$( "#formulario" ).on( "submit", function ( e ) {
		guardaryeditar( e );
	} );

	listar_permiso( 0 );
}

//Función limpiar
function limpiar() {
	$( "#cargoID" ).val( "" );
	$( "#nombre" ).val( "" );
	$( "#descripcion" ).val( "" );

	$( "#tblPermiso > tbody" ).empty();
	$( "#tblPermiso" ).html( '<thead style="background-color:#A9D0F5"><th></th><th></th><th>Nombre Permiso</th><th></th></thead><tbody> </tbody><tfoot> <th></th><th></th><th>Nombre Permiso</th><th></th></tfoot>' );

	listar_permiso( 0 );
}

//Función mostrar formulario
function mostrarform( flag ) {
	if ( flag ) {
		$( ".listadoregistros" ).hide();
		$( ".formularioregistros" ).show();
		$( "#btnGuardar" ).prop( "disabled", false );
	} else {
		$( ".listadoregistros" ).show();
		$( ".formularioregistros" ).hide();
	}
}

//Función para cancelar el formulario
function cancelarform() {
	limpiar();
	mostrarform( false );
}

//Función listar
function listar() {
	tabla = $( '#tbllistado' ).dataTable( {
		"aProcessing": true, //Se activa el procesamiento del datatable
		"aServerSide": true, //Se pagina y filtra por medio del servidor
		dom: "f<'row'<'col-sm-2'B><'col-sm-1'l><'col-sm-9'p>> rt <'bottom'ip<'clear'>>",//Se definen los elementos de control de la tabla
		buttons: [
			{extend: 'excelHtml5', title: 'Listado de Cargos', exportOptions: {columns: [ 0, 1, 2, 3 ]}, className: 'btn btn-sm btn-primary'},
			{extend: 'pdf', title: 'Listado de Cargos', exportOptions: {columns: [ 0, 1, 2, 3 ]}, className: 'btn btn-sm btn-primary'},
		],
		
		"ajax": {
			url: '../ajax/cargo.php?op=listar',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"createdRow": function ( row, data, dataIndex ) {
			if ( data[ 2 ] == '<span class="badge bg-danger">Desactivado</span>' ) {
				$( row ).addClass( 'table-danger' );
			}
		},
		"columnDefs": [
			{"width": "80px", "targets": [ 2,3 ]},
			{"className": "text-center", "targets": [ 2,3 ]},
		],
		"Destroy": true,
		"iDisplayLength": 10, //Número de registros para paginar
		"order": [ [ 2, "asc" ], [ 0, "asc" ] ] //Ordenar (columna, orden ascendente o descendente, etc)
	} ).DataTable();
}

function guardaryeditar( e ) {
	e.preventDefault(); //Para que no se active la acción predeterminada del evento
	$( "#btnGuardar" ).prop( "disabled", true );
	var formData = new FormData( $( "#formulario" )[ 0 ] );

	$.ajax( {
		url: "../ajax/cargo.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function ( datos ) {
			bootbox.alert( datos );
			mostrarform( false );
			tabla.clear().draw();
			tabla.ajax.reload();
		}
	} );
	limpiar();
}

function desactivar( cargoID ) {
	bootbox.confirm( "¿Quieres desactivar el Cargo?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/cargo.php?op=desactivar", {cargoID: cargoID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function activar( cargoID ) {
	bootbox.confirm( "¿Quieres activar el Cargo?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/cargo.php?op=activar", {cargoID: cargoID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function mostrar( cargoID ) {
	$.post( "../ajax/cargo.php?op=mostrar", {cargoID: cargoID}, function ( data, statusUsuario ) {
		data = JSON.parse( data );
		mostrarform( true );
		$( "#cargoID" ).val( data.cargoID );
		$( "#nombre" ).val( data.nombre );
		$( "#descripcion" ).val( data.descripcion );
	});

	listar_permiso( cargoID )
}

//Función listar
function listar_permiso( cargoID ) {
	tabla_permiso = $( '#tblPermiso' ).dataTable( {
		"aProcessing": true, //Se activa el procesamiento del datatable
		"aServerSide": true, //Se pagina y filtra por medio del servidor
		dom: "<'row' <'col-sm-4'B><'col-sm-8'f>> rt <'bottom'ip<'clear'>>",//Se definen los elementos de control de la tabla
		buttons: [
			{
				text: '<i class="far fa-square"></i> / <i class="far fa-check-square"></i>',
				action: function ( e, dt, node, config ) {
					togglepermiso();
				},
				className: "botonesDatatables btn btn-sm btn-primary "
			},
		],
		"autoWidth": false,
		"ajax": {
			url: '../ajax/cargo.php?op=listar_permiso&id=' + cargoID,
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"ordering": false,
		"columnDefs": [
			{"width": "50px", "targets": [ 1, 2, 3 ]},
			{"width": "80px", "targets": [ 5 ]},
			{"className": "text-center", "targets": [ 1, 2, 3 ]},
			{"searchable": false, "targets": [ 1, 2, 3, 4, 5 ]},
			{"visible": false, "targets": [ 4,5 ]},
		],
		"bDestroy": true,
		"iDisplayLength": -1, //Número de registros para paginar
		"order": [ 4, "asc" ] //Ordenar (columna, orden ascendente o descendente, etc)
	} ).DataTable();
	$( "#tblPermiso" ).css( "width", "100%" );
}

function togglepermiso() {
	if ( flagPermiso == true ) {
		$( "input[class ='permisos']" ).attr( "checked", true ).trigger( "change" );
		flagPermiso = false;
	} else {
		$( "input[class ='permisos']" ).attr( "checked", false ).trigger( "change" );
		flagPermiso = true;
	}
}


function cambiaValorPermiso( permiso, area ) {
	if ( area == 'ver' ) {
		if ( document.getElementById( permiso + 'verCheck' ).checked ) {
			document.getElementById( permiso + area ).value = 1;
			if ( document.getElementById( permiso + 'editarCheck' ) ) {document.getElementById( permiso + 'editarCheck' ).disabled = false}
			if ( document.getElementById( permiso + 'historialCheck' ) ) {document.getElementById( permiso + 'historialCheck' ).disabled = false}
		} else {
			document.getElementById( permiso + area ).value = 0;
			if ( document.getElementById( permiso + 'ver' ) ) {document.getElementById( permiso + 'ver' ).value = 0}
			if ( document.getElementById( permiso + 'editar' ) ) {document.getElementById( permiso + 'editar' ).value = 0}
			if ( document.getElementById( permiso + 'historial' ) ) {document.getElementById( permiso + 'historial' ).value = 0}

			if ( document.getElementById( permiso + 'editarCheck' ) ) {document.getElementById( permiso + 'editarCheck' ).checked = false}
			if ( document.getElementById( permiso + 'historialCheck' ) ) {document.getElementById( permiso + 'historialCheck' ).checked = false}

			if ( document.getElementById( permiso + 'editarCheck' ) ) {document.getElementById( permiso + 'editarCheck' ).disabled = true}
			if ( document.getElementById( permiso + 'historialCheck' ) ) {document.getElementById( permiso + 'historialCheck' ).disabled = true}
		}
	} else {
		if ( document.getElementById( permiso + area + 'Check' ).checked ) {
			document.getElementById( permiso + area ).value = 1;
		} else {
			document.getElementById( permiso + area ).value = 0;
		}
	}
}


init();