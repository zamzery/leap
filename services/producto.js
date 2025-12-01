var tabla;
var tablaVariantes;
//función que se ejecuta al inicio
function init() {
	mostrarform( false );
	listar();

	$( "#formulario" ).on( "submit", function ( e ) {
		guardaryeditar( e );
	} );

	$.post( "../ajax/unidadmedida.php?op=select_unidadmedida", function ( r ) {
		$( "#medida_id" ).html( r );
		$( "#medida_id" ).selectpicker( 'refresh' );
	} );

	$.post( "../ajax/clavefacturacion.php?op=select_clavefacturacion", function ( r ) {
		$( "#clave_id" ).html( r );
		$( "#clave_id" ).selectpicker( 'refresh' );
	} );
}

//Función limpiar
function limpiar() {
	$( "#id" ).val( "" );
	$( "#productoID" ).val( "" );
	$( "#nombre" ).val( "" );
	$( "#precioVenta" ).val( "" );
	$( "#medida_id" ).val( "" );
	$( "#medida_id" ).selectpicker( 'refresh' );
	$( "#clave_id" ).val( "" );
	$( "#clave_id" ).selectpicker( 'refresh' );
	$( "#observaciones" ).val( "" );
	$( "#sku" ).val( "" );
	$( "#imagenActual" ).val( "" );
	$( "#imagen" ).val( "" );
	$( "#imagenMuestra" ).hide();
	$( "#imagenMuestra" ).html( "" ).attr( "" );
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
			{extend: 'excelHtml5', title: 'Listado de Productos', exportOptions: {columns: [ 0, 1, 2, 3 ]}, className: 'btn btn-sm btn-primary'},
			{extend: 'pdf', title: 'Listado de Productos', exportOptions: {columns: [ 0, 1, 2, 3 ]}, className: 'btn btn-sm btn-primary'},
		],

		"ajax": {
			url: '../ajax/producto.php?op=listar',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"createdRow": function ( row, data, dataIndex ) {
			if ( data[ 1 ] == '<span class="badge bg-danger">Desactivado</span>' ) {
				$( row ).addClass( 'table-danger' );
			}
		},
		"columnDefs": [
			{"width": "135px", "targets": [ 1, 2 ]},
			{"width": "80px", "targets": [ 5, 6 ]},
			{"width": "50px", "targets": [ 3, 4 ]},
			{"className": "text-center", "targets": [ 4, 5, 6 ]},
			{"className": "text-right", "targets": [ 3 ]},
		],
		"Destroy": true,
		"iDisplayLength": 25, //Número de registros para paginar
		"order": [ [ 0, "asc" ] ] //Ordenar (columna, orden ascendente o descendente, etc)
	} ).DataTable();
}

function guardaryeditar( e ) {
	e.preventDefault(); //Para que no se active la acción predeterminada del evento
	$( "#btnGuardar" ).prop( "disabled", true );
	var formData = new FormData( $( "#formulario" )[ 0 ] );

	$.ajax( {
		url: "../ajax/producto.php?op=guardaryeditar",
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

function mostrar( productoID ) {
	$.post( "../ajax/producto.php?op=mostrar", {productoID: productoID}, function ( data, statusUsuario ) {
		data = JSON.parse( data );
		mostrarform( true );
		$( "#id" ).val( data.id );
		$( "#productoID" ).val( data.productoID );
		$( "#nombre" ).val( data.nombre );
		$( "#precioVenta" ).val( data.precioVenta );
		$( "#medida_id" ).val( data.medida_id );
		$( "#medida_id" ).selectpicker( 'refresh' );
		$( "#clave_id" ).val( data.clave_id );
		$( "#clave_id" ).selectpicker( 'refresh' );
		$( "#sku" ).val( data.sku );
		$( "#observaciones" ).val( data.observaciones );
		$( "#imagenActual" ).val( data.imagen );
		if ( data.imagen ) {
			$( "#imagenMuestra" ).show();
			$( "#imagenMuestra" ).html( '<a href="' + data.imagen + '" data-featherlight="image"><img id="imagenMuestra" src="' + data.imagen + '" style="height:85px;width:85px;"></a>' );
		} else {
			$( "#imagenMuestra" ).hide();
		}
	} );
}

function modalVariantes( productoID ) {
	$( "#productoID_variante" ).val( productoID );
	$( "#modalVariantes" ).modal( "show" );
	listar_variante( productoID );
	$( "#modalVariantes" ).on( "hidden.bs.modal", function () {
		$( "#tblVariantes" ).DataTable().destroy();
		$( "#productoID_variante" ).val( "" );
	} );
}

function listar_variante( productoID ) {
	tablaVariantes = $( "#tblVariantes" ).dataTable( {
		"aProcessing": true,
		"aServerSide": true,
		"ajax": {
			url: '../ajax/producto.php?op=listar_variante&prod=' + productoID,
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"columnDefs": [
			{"width": "135px", "targets": [ 1, 2 ]},
			{"className": "text-right", "targets": [ 2 ]},
			{"width": "80px", "targets": [ 3 ]},
			{"className": "text-center", "targets": [ 3 ]},
		],
		"Destroy": true,
		"iDisplayLength": 10,
		"order": [ [ 0, "asc" ] ]
	} ).DataTable();
}

init();