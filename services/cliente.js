var tabla;
//función que se ejecuta al inicio
function init() {
	mostrarform( false );
	listar();

	$( "#formulario" ).on( "submit", function ( e ) {
		guardaryeditar( e );
	} );

	$.post( "../ajax/user.php?op=select_usuario", function ( r ) {
		$( "#usuarioNombre" ).html( r );
		$( "#usuarioNombre" ).selectpicker( 'refresh' );
	} );

	$.post( "../ajax/metodopago.php?op=select_metodopago", function ( r ) {
		$( "#formadePago" ).html( r );
		$( "#formadePago" ).selectpicker( 'refresh' );
	} );

	$( 'select' ).selectpicker();
	$( "#cambiausuario" ).hide();

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
		}
	} );

	$( "select" ).selectpicker( {
		selectOnTab: true
	} );

	$( '#tblListado tfoot th' ).each( function ( i ) {
		if ( i == 6 ) {
		} else {
			var title = $( '#tblListado thead th' ).eq( $( this ).index() ).text();
			$( this ).html( '<input style="width:100%" type="text" placeholder="' + title + '" />' );
		}
	} );
}

function decodeHtml( html ) {
	var txt = document.createElement( "textarea" );
	txt.innerHTML = html;
	return txt.value;
}

function make_slug(nombre) {
	nombre = nombre.toLowerCase();
	nombre = nombre.replace(/ /g, '-');
	nombre = nombre.replace(/[^\w-]+/g, '');
	$( "#user_nicename" ).val( nombre );
}

function limpiar() {
	$( "#clienteID" ).val( "" );
	$( "#display_name" ).val( "" );
	$( "#user_login" ).val( "" );
	$( "#user_nicename" ).val( "" );
	$( "#user_email" ).val( "" );
	$( "#user_pass" ).val( "" );
	$( "#fechaCliente" ).val( "" );
	$( "#usuarioNombre_general" ).val( "" );
	$( "#usuarioNombre" ).val( "" );
	$( '#usuarioNombre' ).selectpicker( 'refresh' );
	$( "#razonSocial" ).val( "" );
	$( "#calle" ).val( "" );
	$( "#num_ext" ).val( "" );
	$( "#num_int" ).val( "" );
	$( "#colonia" ).val( "" );
	$( "#formadePago" ).val( "03" );
	$( '#formadePago' ).selectpicker( 'refresh' );
	$( "#poblacion" ).val( "" );
	$( "#edoPais" ).val( "" );
	$( "#pais" ).val( "" );
	$( "#cp" ).val( "" );
	$( "#rfcCliente" ).val( "XEXX010101000" );
	$( "#regimenFiscal" ).val( "616" );
	$( "#regimenFiscal" ).selectpicker( 'refresh' );
	$( "#telefono" ).val( "" );
	$( "#num_cuenta" ).val( "" );
	$( "#banco" ).val( "" );
	$( "#colonia" ).val( "" );
	$( "#metodoPago" ).val( "PUE" );
	$( "#metodoPago" ).selectpicker( 'refresh' );
	$( "#usoCfdi" ).val( "S01" );
	$( "#usoCfdi" ).selectpicker( 'refresh' );
	$( "#comentarios" ).val( "" );
	$( "#moneda" ).val( "USD" );
	$( '#moneda' ).selectpicker( 'refresh' );
	$( "#constancia" ).val( "" );
	$( "#constanciaactual" ).val( "" );
	$( "#botonConstancia" ).html( "" );
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

function listar() {
	tabla = $( '#tblListado' ).dataTable( {
		aProcessing: true, //Se activa el procesamiento del datatable
		aServerSide: true, //Se pagina y filtra por medio del servidor
		dom: "f<'row'<'col-sm-2'B><'col-sm-1'l><'col-sm-9'p>> rt <'bottom'ip<'clear'>>", //Se definen los elementos de control de la tabla
		buttons: [
			{extend: 'excelHtml5', title: 'Listado de Clientes', exportOptions: {columns: [ 0, 1, 2, 3, 4, 5, 6 ]}, className: 'botonesDatatables btn btn-sm btn-primary'},
			{extend: 'pdf', title: 'Listado de Clientes', exportOptions: {columns: [ 0, 1, 2, 3, 4, 5, 6 ]}, className: 'botonesDatatables btn btn-sm btn-primary'}
		],
		ajax: {
			url: '../ajax/cliente.php?op=listar',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		columnDefs: [
			{visible: false, targets: [ 7 ]},
			{searchable: false, targets: [6, 7]},
			{width: "100px", targets: [ 3 ]},
			{width: "75px", targets: [ 4, 5, 7 ]},
			{className: "text-center", targets: [ 4, 5, 6 ]}
		],
		// initComplete: function () {
		// 	// Apply the search
		// 	this.api().columns().every( function () {
		// 		var that = this;
		// 		$( 'input', this.footer() ).on( 'keyup change clear', function () {
		// 			if ( that.search() !== this.value ) {
		// 				that.search( this.value ).draw();
		// 			}
		// 		} );
		// 	} );
		// },
		createdRow: function ( row, data, dataIndex ) {
			if ( data[ 5 ] == '<span class="badge bg-danger">Desactivado</span>' ) {
				$( row ).addClass( 'table-danger' );
			}
		},
		orderFixed: [ 6, 'asc' ],
		destroy: true,
		iDisplayLength: 10, //Número de registros para paginar
		order: [ [ 7, "desc" ]] //Ordenar (columna, orden ascendente o descendente, etc)
	} ).DataTable();
}

function guardaryeditar( e ) {
	e.preventDefault(); //Para que no se active la acción predeterminada del evento
	var formData = new FormData( $( "#formulario" )[ 0 ] );

	$.ajax( {
		url: "../ajax/cliente.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function ( datos ) {
			mostrarform( false );
			bootbox.alert( datos );
			setTimeout( function () {
				tabla.clear().draw();
				tabla.ajax.reload();
			}, 1000 );
		},
		error: function ( datos ) {
			bootbox.alert( datos );
		}
	} );
}

function aplicaFormatoDinero( num ) {
	var p = parseFloat( num ).toFixed( 2 ).split( "." );
	var chars = p[ 0 ].split( "" ).reverse();
	var newstr = '';
	var count = 0;
	for ( x in chars ) {
		count++;
		if ( count % 3 == 1 && count != 1 ) {
			newstr = chars[ x ] + ',' + newstr;
		} else {
			newstr = chars[ x ] + newstr;
		}
	}
	return newstr + "." + p[ 1 ];
}

function mostrar( clienteID ) {
	$.post( "../ajax/cliente.php?op=mostrar", {clienteID: clienteID}, function ( data, statusCliente ) {
		data = JSON.parse( data );
		mostrarform( true );
		var display_name = decodeHtml( data.display_name );
		$( "#display_name" ).val( display_name );
		$( "#user_login" ).val( data.user_login );
		$( "#user_nicename" ).val( data.user_nicename );
		$( "#user_email" ).val( data.user_email );
		$( "#user_pass" ).val( "" );
		$( "#clienteID" ).val( data.clienteID );
		$( "#fechaCliente" ).val( data.fechaCliente );
		if ( data.regimenFiscal == null || data.regimenFiscal == '' ) {
			var regimenFiscal = '616';
			var formadePago = '3';
			var metodoPago = 'PUE';
			var usoCfdi = 'S01';
			var rfcCliente = 'XEXX010101000';
			var cp = '44520';
			var moneda = 'USD';
		} else {
			var regimenFiscal = data.regimenFiscal;
			var formadePago = data.formadePago;
			var metodoPago = data.metodoPago;
			var usoCfdi = data.usoCfdi;
			var rfcCliente = decodeHtml( data.rfcCliente );
			var cp = data.cp;
			var moneda = data.moneda;
		}
		var razonSocial = decodeHtml( data.razonSocial );
		$( "#razonSocial" ).val( razonSocial );
		$( "#usuarioNombre_general" ).val( data.nombreVendedor );
		$( "#calle" ).val( data.calle );
		$( "#num_ext" ).val( data.num_ext );
		$( "#num_int" ).val( data.num_int );
		$( "#colonia" ).val( data.colonia );
		$( "#poblacion" ).val( data.poblacion );
		$( "#edoPais" ).val( data.edoPais );
		$( "#pais" ).val( data.pais );
		$( "#cp" ).val( cp );
		$( "#rfcCliente" ).val( rfcCliente );
		$( "#regimenFiscal" ).val( regimenFiscal );
		$( '#regimenFiscal' ).selectpicker( 'refresh' );
		$( "#telefono" ).val( data.telefono );
		$( "#usuarioID" ).val( data.usuarioID );
		$( "#num_cuenta" ).val( data.num_cuenta );
		$( "#banco" ).val( data.banco );
		$( "#colonia" ).val( data.colonia );
		$( "#formadePago" ).val( formadePago );
		$( '#formadePago' ).selectpicker( 'refresh' );
		$( "#metodoPago" ).val( metodoPago );
		$( '#metodoPago' ).selectpicker( 'refresh' );
		$( "#moneda" ).val( moneda );
		$( '#moneda' ).selectpicker( 'refresh' );
		$( "#usoCfdi" ).val( usoCfdi );
		$( '#usoCfdi' ).selectpicker( 'refresh' );
		$( "#comentarios" ).val( data.comentarios );
		$( "#constanciaactual" ).val( data.constancia );
		if ( data.constancia ) {
			$( "#botonConstancia" ).html( "<a class='btn btn-primary' target='_blank' href='../public/files/constancia/" + data.constancia + "'><i class='fa-solid fa-eye espaciado-icn'></i> Ver</a> <button type='button' class='btn btn-danger' onclick='eliminarConstancia(" + data.clienteID + ",\"" + data.constancia + "\")'><i class='fa-solid fa-trash'></i></button>" );
		} else {
			$( "#botonConstancia" ).html( "" );
		}

		if ( clienteID ) {
			$( "#cambiausuario" ).show();
		}
	} );
}

function desactivar( clienteID ) {
	bootbox.confirm( "¿Quieres desactivar el Cliente?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/cliente.php?op=desactivar", {clienteID: clienteID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function activar( clienteID ) {
	bootbox.confirm( "¿Quieres activar el Cliente?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/cliente.php?op=activar", {clienteID: clienteID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function eliminarConstancia( clienteID, constancia ) {
	bootbox.confirm( "¿Quieres eliminar la Constancia?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/cliente.php?op=eliminarConstancia", {clienteID: clienteID, constancia: constancia}, function ( e ) {
				bootbox.alert( e );
				setTimeout( function () {
					tabla.clear().draw();
					tabla.ajax.reload();
					mostrar( clienteID );
				}, 500 );
			} );
		}
	} );
}

function cambia_usuario( clickedID ) {
	bootbox.confirm( "¿Quieres cambar el Usuario Asignado?", function ( result ) {
		if ( result ) {
			var usuarioID = clickedID.value;
			var clienteID = document.getElementById( "clienteID" ).value;
			$.post( "../ajax/cliente.php?op=cambia_usuario", {clienteID: clienteID, nuevoUsuario: usuarioID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
			setTimeout( function () {
				mostrar( clienteID );
			}, 300 );
		}
	} );
}

function muestraHistorial() {
	var clienteID = document.getElementById( "clienteID" ).value;
	$( "#modalHistorial" ).modal( "toggle" );
	tablaHistorial = $( '#tblHistorial' ).DataTable( {
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: "f<'row'<'col-sm-3'rl><'col-sm-9'p>> rt <'bottom'ip<'clear'>>",//Definimos los elementos del control de tabla
		"ajax": {
			url: '../ajax/cliente.php?op=muestraHistorial&hist=' + clienteID,
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
			"emptyTable": "Aún no se ha guardado ningún historial de este cliente."
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