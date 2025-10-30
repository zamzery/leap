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
	$( "#claveID" ).val( "" );
	$( "#nombre" ).val( "" );
	$( "#clave" ).val( "" );
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
			{extend: 'excelHtml5', title: 'Listado de Claves de Facturación', exportOptions: {columns: [ 0, 1, 2, 3 ]}, className: 'btn btn-sm btn-primary'},
			{extend: 'pdf', title: 'Listado de Claves de Facturación', exportOptions: {columns: [ 0, 1, 2, 3 ]}, className: 'btn btn-sm btn-primary'},
		],
		
		"ajax": {
			url: '../ajax/clavefacturacion.php?op=listar',
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
		url: "../ajax/clavefacturacion.php?op=guardaryeditar",
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

function desactivar( claveID ) {
	bootbox.confirm( "¿Quieres desactivar la Clave de Facturación?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/clavefacturacion.php?op=desactivar", {claveID: claveID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function activar( claveID ) {
	bootbox.confirm( "¿Quieres activar la Clave de Facturación?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/clavefacturacion.php?op=activar", {claveID: claveID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function mostrar( claveID ) {
	$.post( "../ajax/clavefacturacion.php?op=mostrar", {claveID: claveID}, function ( data, statusUsuario ) {
		data = JSON.parse( data );
		mostrarform( true );
		$( "#claveID" ).val( data.claveID );
		$( "#nombre" ).val( data.nombre );
		$( "#clave" ).val( data.clave );
	});
}

init();