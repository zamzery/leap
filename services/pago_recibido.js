var tabla;
//función que se ejecuta al inicio
function init() {
	mostrarform( false );
	listar();

	$( "#formulario" ).on( "submit", function ( e ) {
		guardaryeditar( e );
	} );

	$.post( "../ajax/cliente.php?op=select_cliente", function ( r ) {
		$( "#cliente_id" ).html( r );
		$( "#cliente_id" ).selectpicker( 'refresh' );
	} );

	$( "#cliente_id" ).change( function () {
		var parcialidad = $( this ).children( 'option:selected' ).data( 'parcialidad' );
		$( "#parcialidad" ).val( parcialidad );
		var saldoanterior = $( this ).children( 'option:selected' ).data( 'saldoanterior' );
		$( "#saldoAnterior" ).val( saldoanterior );
		$( "#pago" ).val( saldoanterior );
		$( "#pago" ).trigger( "input" );
		var metodopago = $( this ).children( 'option:selected' ).data( 'metodopago' );
		$( "#metodopago_id" ).val( metodopago );
		$( "#metodopago_id" ).selectpicker( 'refresh' );
		var numerocuenta = $( this ).children( 'option:selected' ).data( 'numerocuenta' );
		$( "#numero_cuenta" ).val( numerocuenta );
	} );

	$.post( "../ajax/metodopago_recibido.php?op=select_metodopago", function ( r ) {
		$( "#metodopago_id" ).html( r );
		$( "#metodopago_id" ).selectpicker( 'refresh' );
	} );

	// $( "#metodopago_id" ).change( function () {
	// 	if ( $( this ).val() == '02' || $( this ).val() == '03' || $( this ).val() == '04' || $( this ).val() == '28' ) {
	// 		document.getElementById( "numero_cuenta" ).disabled = false;
	// 	} else {
	// 		document.getElementById( "numero_cuenta" ).disabled = true;
	// 	}
	// } );

	$( "#fechaPago" ).datetimepicker( {
		format: 'YYYY-MM-DD',
	} );

	$( "#comprobantePagoMuestra" ).hide();

	$.fn.datetimepicker.Constructor.Default = $.extend( {}, $.fn.datetimepicker.Constructor.Default, {
		locale: 'es-us',
		icons: {
			time: 'fas fa-clock',
			date: 'fas fa-calendar',
			up: 'fas fa-arrow-up',
			down: 'fas fa-arrow-down',
			previous: 'fas fa-chevron-left',
			next: 'fas fa-chevron-right',
			today: 'fas fa-calendar-check-o',
			clear: 'fas fa-trash',
			close: 'fas fa-times'
		},
	} );
}

//Función limpiar
function limpiar() {
	$( "#pagoID" ).val( "" );
	$( "#cliente_id" ).val( "" );
	$( "#cliente_id" ).selectpicker( 'refresh' );
	$( "#fechaPago" ).datetimepicker( 'clear' );
	$( "#metodopago_id" ).val( "" );
	$( "#metodopago_id" ).selectpicker( 'refresh' );
	$( "#banco" ).val( "" );
	$( "#numero_cuenta" ).val( "" );
	$( "#parcialidad" ).val( "" );
	document.getElementById( "numero_cuenta" ).disabled = true;
	$( "#saldoAnterior" ).val( "" );
	$( "#pago" ).val( "" );
	$( "#saldoRestante" ).val( "" );
	$( "#comprobantePagoMuestra" ).hide();
	$( "#comprobantePagoMuestra" ).html( "" ).attr( "" );
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
			{extend: 'excelHtml5', title: 'Listado de Pagos', exportOptions: {columns: [ 0, 1, 2, 3, 4, 5, 6 ]}, className: 'btn btn-sm btn-primary'},
			{extend: 'pdf', title: 'Listado de Pagos', exportOptions: {columns: [ 0, 1, 2, 3, 4, 5, 6 ]}, className: 'btn btn-sm btn-primary'},
		],
		"ajax": {
			url: '../ajax/pago_recibido.php?op=listar',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"createdRow": function ( row, data, dataIndex ) {
			if ( data[ 6 ] === '<span class="badge bg-danger">Desactivado</span>' ) {
				$( row ).addClass( 'table-danger' );
			} else if ( data[ 6 ] === '<span class="badge bg-success">Aceptado</span>' ) {
				$( row ).addClass( 'table-success' );
			}
		},
		"columnDefs": [
			{"width": "80px", "targets": [ 6, 7 ]},
			{"width": "60px", "targets": [ 0, 5 ]},
			{"width": "120px", "targets": [ 1, 4 ]},
			{"className": "text-end", "targets": [ 4 ]},
			{"className": "text-center", "targets": [ 0, 1, 5, 6, 7 ]},
		],
		"Destroy": true,
		"iDisplayLength": 10, //Número de registros para paginar
		"order": [ [ 6, "asc" ], [ 0, "desc" ] ] //Ordenar (columna, orden ascendente o descendente, etc)
	} ).DataTable();
}

function guardaryeditar( e ) {
	e.preventDefault(); //Para que no se active la acción predeterminada del evento
	$( "#btnGuardar" ).prop( "disabled", true );
	var formData = new FormData( $( "#formulario" )[ 0 ] );

	$.ajax( {
		url: "../ajax/pago_recibido.php?op=guardaryeditar",
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

function desactivar( pagoID ) {
	bootbox.confirm( "¿Quieres desactivar el Pago?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/pago_recibido.php?op=desactivar", {pagoID: pagoID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function activar( pagoID ) {
	bootbox.confirm( "¿Quieres activar el Pago?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/pago_recibido.php?op=activar", {pagoID: pagoID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function mostrar( pagoID ) {
	$.post( "../ajax/pago_recibido.php?op=mostrar", {pagoID: pagoID}, function ( data, statusUsuario ) {
		data = JSON.parse( data );
		mostrarform( true );
		$( "#pagoID" ).val( data.pagoID );
		$( "#cliente_id" ).val( data.cliente_id );
		$( "#cliente_id" ).selectpicker( 'refresh' );
		$( "#fechaPago" ).datetimepicker( 'date', moment( data.fechaPago ).format( 'YYYY-MM-DD' ) );
		$( "#metodopago_id" ).val( data.metodopago_id );
		$( "#metodopago_id" ).selectpicker( 'refresh' );
		$( "#banco" ).val( data.banco );
		$( "#numero_cuenta" ).val( data.numero_cuenta );
		$( "#parcialidad" ).val( data.parcialidad );
		$( "#saldoAnterior" ).val( data.saldoAnterior );
		$( "#pago" ).val( data.pago );
		$( "#saldoRestante" ).val( data.saldoRestante );
		$( "#comprobantePagoActual" ).val( data.comprobantePago );

		if ( data.comprobantePago ) {
			$( "#comprobantePagoMuestra" ).show();
			$( "#comprobantePagoMuestra" ).html( "Archivo <i class='fas fa-file-alt'></i>" ).attr( "href", "../public/files/pagos/" + data.comprobantePago );
		} else {
			$( "#comprobantePagoMuestra" ).hide();
		}
	} );
}

function calculaRestante( este ) {
	var saldoAnterior = document.getElementById( "saldoAnterior" ).value;
	var saldoRestante = parseFloat( saldoAnterior ) - parseFloat( este );
	$( "#saldoRestante" ).val( saldoRestante );
}

function muestraHistorial() {
	var pagoID = document.getElementById( "pagoID" ).value;
	$( "#modalHistorial" ).modal( "toggle" );
	tablaHistorial = $( '#tblHistorial' ).DataTable( {
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: "f<'row'<'col-sm-3'rl><'col-sm-9'p>> rt <'bottom'ip<'clear'>>",//Definimos los elementos del control de tabla
		"ajax": {
			url: '../ajax/pago_recibido.php?op=muestraHistorial&hist=' + pagoID,
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"columnDefs": [
			{"width": "50px", "targets": [ 0 ]},
			{"width": "120px", "targets": [ 1 ]},
			{"className": "text-center", "targets": [ 0, 1 ]},
		],
		"language": {
			"emptyTable": "Aún no se ha guardado ningún historial de este pago."
		},
		"autoWidth": false,
		"bDestroy": true,
		"iDisplayLength": 10,//Paginación
		"order": [ 1, "desc" ]//Ordenar (columna,orden)
	} );
	$( '#modalHistorial' ).on( 'hidden.bs.modal', function () {
		$( '#tblHistorial' ).DataTable().destroy();
	} );
}

init();