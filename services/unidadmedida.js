var tabla;
//función que se ejecuta al inicio
function init() {
	mostrarform( false );
	listar();

	$( "#formulario" ).on( "submit", function ( e ) {
		guardaryeditar( e );
	} );
}

//Función limpiar
function limpiar() {
	$( "#unidadmedidaID" ).val( "" );
	$( "#nombre" ).val( "" );
	$( "#unidad" ).val( "" );
	$( "#abreviatura" ).val( "" );
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
			{extend: 'excelHtml5', title: 'Listado de Unidades de Medida', exportOptions: {columns: [ 0, 1, 2, 3, 4 ]}, className: 'btn btn-sm btn-primary'},
			{extend: 'pdf', title: 'Listado de Unidades de Medida', exportOptions: {columns: [ 0, 1, 2, 3, 4 ]}, className: 'btn btn-sm btn-primary'},
		],
		
		"ajax": {
			url: '../ajax/unidadmedida.php?op=listar',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"createdRow": function ( row, data, dataIndex ) {
			if ( data[ 3 ] == '<span class="badge bg-danger">Desactivado</span>' ) {
				$( row ).addClass( 'table-danger' );
			}
		},
		"columnDefs": [
			{"width": "80px", "targets": [ 1, 3, 4 ]},
			{"className": "text-center", "targets": [ 1, 3, 4 ]},
			{"visible": false, "targets": [ 2 ]}
		],
		"Destroy": true,
		"iDisplayLength": 10, //Número de registros para paginar
		"order": [ [ 3, "asc" ], [ 0, "asc" ] ] //Ordenar (columna, orden ascendente o descendente, etc)
	} ).DataTable();
}

function guardaryeditar( e ) {
	e.preventDefault(); //Para que no se active la acción predeterminada del evento
	$( "#btnGuardar" ).prop( "disabled", true );
	var formData = new FormData( $( "#formulario" )[ 0 ] );

	$.ajax( {
		url: "../ajax/unidadmedida.php?op=guardaryeditar",
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

function desactivar( unidadmedidaID ) {
	bootbox.confirm( "¿Quieres desactivar la Unidad de Medida?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/unidadmedida.php?op=desactivar", {unidadmedidaID: unidadmedidaID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function activar( unidadmedidaID ) {
	bootbox.confirm( "¿Quieres activar la Unidad de Medida?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/unidadmedida.php?op=activar", {unidadmedidaID: unidadmedidaID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function mostrar( unidadmedidaID ) {
	$.post( "../ajax/unidadmedida.php?op=mostrar", {unidadmedidaID: unidadmedidaID}, function ( data, statusUsuario ) {
		data = JSON.parse( data );
		mostrarform( true );
		$( "#unidadmedidaID" ).val( data.unidadmedidaID );
		$( "#nombre" ).val( data.nombre );
		$( "#unidad" ).val( data.unidad );
		$( "#abreviatura" ).val( data.abreviatura );
	});
}

init();