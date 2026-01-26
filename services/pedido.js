var tabla;
var tablaProductos;

//función que se ejecuta al inicio
function init() {
	mostrarform( false );
	listar();
	ver_productos();

	$( "#formulario" ).on( "submit", function ( e ) {
		guardaryeditar( e );
	} );

	$( "#formFactura" ).on( "submit", function ( e ) {
		guardaryeditar_factura( e );
	} );

	$.post( "../ajax/cliente.php?op=select_cliente", function ( r ) {
		$( "#clienteID" ).html( r );
		$( '#clienteID' ).selectpicker( 'refresh' );

		$( "#clienteID_factura" ).html( r );
		$( '#clienteID_factura' ).selectpicker( 'refresh' );
	} );

	$( "#clienteID" ).change( function () {
		var nombreCliente = $( '#clienteID option:selected' ).data( 'nombre' );
		$( "#nombreCliente" ).val( nombreCliente );
	} );

	$(document).ready(function(){
			$( "#modalFacturas" ).on( "hidden.bs.modal", function () {
			$( "#pedido_id_factura" ).val( "");
			$( "#facturaID" ).val( "" );
				$( "#moneda" ).val( "MXN" );
				$( "#moneda" ).selectpicker( 'refresh' );
			$( "#tipoCambio" ).val( "1" );
			$( "#clienteID_factura" ).val( "0" );
			$( "#clienteID_factura" ).selectpicker( 'refresh' );
			$( "#telefono" ).val( "" );
			$( "#calle" ).val( "" );
			$( "#num_ext" ).val( "" );
			$( "#num_int" ).val( "" );
			$( "#colonia" ).val( "" );
			$( "#poblacion" ).val( "" );
			$( "#edoPais" ).val( "" );
				$( "#cp" ).val( "44460" );
			$( "#razonSocial" ).val( "" );
			$( "#rfcCliente" ).val( "" );
				$( "#regimenFiscal" ).val( "616" );
			$( "#regimenFiscal" ).selectpicker( 'refresh' );
			$( "#num_cuenta" ).val( "" );
			$( "#banco" ).val( "" );
			$( "#metodoPago" ).val( "" );
			$( "#metodoPago" ).selectpicker('refresh');
			$( "#formadePago" ).val( "" );
			$( "#formadePago" ).selectpicker( 'refresh' );
				$( "#iva_muestra" ).val( '002' );
				$( "#iva_muestra" ).selectpicker( 'refresh' );
			$( "#usoCfdi" ).val( "" );
			$( "#usoCfdi" ).selectpicker( 'refresh' );
			$( "#comentarios" ).val( "" );
			$( "#btnGuardarFactura" ).prop( "disabled", false );
		} );
	} );

	setTimeout( function () {
		$( "#clienteID" ).val( "0" );
		$( "#clienteID" ).selectpicker( 'refresh' );
	}, 500 );
}

//Función limpiar
function limpiar() {
	$( "#pedidoID" ).val( "" );
	$( "#nombreCliente" ).val( "" );
	$( "#clienteID" ).val( "0" );
	$( "#clienteID" ).selectpicker( 'refresh' );
	$( "#fecha" ).val( "" );
	$( "#observaciones" ).val( "" );
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
			url: '../ajax/pedido.php?op=listar',
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
			{"width": "140px", "targets": [ 3 ]},
			{"width": "180px", "targets": [ 6 ]},
			{"width": "80px", "targets": [ 0, 1, 4, 5 ]},
			{"className": "text-center", "targets": [ 0, 1, 4, 5, 6 ]},
			{"className": "text-end", "targets": [ 3 ]},
		],
		"Destroy": true,
		"iDisplayLength": 25, //Número de registros para paginar
		"order": [ [ 1, "desc" ] ] //Ordenar (columna, orden ascendente o descendente, etc)
	} ).DataTable();
}

function guardaryeditar( e ) {
	e.preventDefault(); //Para que no se active la acción predeterminada del evento
	$( "#btnGuardar" ).prop( "disabled", true );
	var formData = new FormData( $( "#formulario" )[ 0 ] );

	$.ajax( {
		url: "../ajax/pedido.php?op=guardaryeditar",
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

function guardaryeditar_factura( e ) {
	e.preventDefault(); //Para que no se active la acción predeterminada del evento
	$( "#btnGuardarFactura" ).prop( "disabled", true );
	var formData = new FormData( $( "#formFactura" )[ 0 ] );

	$.ajax( {
		url: "../ajax/pedido.php?op=guardaryeditar_factura",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function ( datos ) {
			bootbox.alert( datos );
			tabla.clear().draw();
			tabla.ajax.reload();
			const modalFacturas = bootstrap.Modal.getInstance( document.getElementById( 'modalFacturas' ) );
			if ( modalFacturas ) modalFacturas.hide();
			$( "#btnGuardarFactura" ).prop( "disabled", false );
		},
		error: function ( xhr, status, error ) {
			bootbox.alert( "Error al guardar la factura: " + error );
			$( "#btnGuardarFactura" ).prop( "disabled", false );
		}
	} );
}

function mostrar( pedidoID ) {
	$.post( "../ajax/pedido.php?op=mostrar", {pedidoID: pedidoID}, function ( data, statusUsuario ) {
		data = JSON.parse( data );
		mostrarform( true );
		$( "#pedidoID" ).val( data.pedidoID );
		let fecha = new Date( data.fecha ).toISOString().split( 'T' )[ 0 ];
		$( "#fecha" ).val( fecha );
		$( "#clienteID" ).val( data.clienteID );
		$( "#clienteID" ).selectpicker( 'refresh' );
		$( "#nombreCliente" ).val( data.nombreCliente );
		$( "#observaciones" ).val( data.observaciones );
	} );

	$.post( "../ajax/pedido.php?op=mostrarDetalles", {pedidoID: pedidoID}, function ( data, statusUsuario ) {
		$("#detalles").html(data);
	});
}

function copiar_pedido( pedidoID ) {
	bootbox.confirm( "¿Quieres copiar el Pedido seleccionado?", function ( result ) {
		if ( result ) {
			var dialog = bootbox.dialog( {
				message: '<h5><i class="fa fa-cog fa-spin fa-fw" font-size="2"></i> Por favor espera mientras se copia el pedido...</h5>',
				closeButton: false
			} );
			$.post( "../ajax/pedido.php?op=copiar_pedido", {pedidoID: pedidoID}, function ( data, status ) {
				bootstrap.Modal.getInstance( dialog ).hide();
				bootbox.alert( data );
				tabla.clear().draw();
				tabla.ajax.reload();
			} ).fail( function () {
				bootstrap.Modal.getInstance( dialog ).hide();
				bootbox.alert( 'No se ha podido copiar el pedido' );
			} ).done( function () {
				bootstrap.Modal.getInstance( dialog ).hide();
			} );
			setTimeout( function () {
				bootstrap.Modal.getInstance( dialog ).hide();
			}, 8000 );
		}
	} );
}

function mostrar_pedido( pedidoID ) {
	$.post( "../ajax/pedido.php?op=mostrar_pedido", {pedidoID: pedidoID}, function ( data, statusUsuario ) {
		data = JSON.parse( data );
		mostrarform( true );
		$( "#pedidoID" ).val( data.pedidoID );
		let fecha = new Date( data.fecha ).toISOString().split( 'T' )[ 0 ];
		$( "#fecha" ).val( fecha );
		$( "#clienteID" ).val( data.clienteID );
		$( "#clienteID" ).selectpicker( 'refresh' );
		$( "#nombreCliente" ).val( data.nombreCliente );
		$( "#observaciones" ).val( data.observaciones );
	} );

	$.post( "../ajax/pedido.php?op=mostrarDetalles_pedido", {pedidoID: pedidoID}, function ( data, statusUsuario ) {
		$( "#detalles" ).html( data );
	} );
}

function verModalProductos() {
	const modalProductos = new bootstrap.Modal( document.getElementById( 'modalProductos' ) );
	modalProductos.show();
}

function ver_productos() {
	tablaProductos = $( '#tblProductos' ).dataTable( {
		"aProcessing": true, //Se activa el procesamiento del datatable
		"aServerSide": true, //Se pagina y filtra por medio del servidor
		dom: "f<'row'<'col-sm-2'><'col-sm-1'l><'col-sm-9'p>> rt <'bottom'ip<'clear'>>",//Se definen los elementos de control de la tabla
		"ajax": {
			url: '../ajax/pedido.php?op=ver_productos',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"columnDefs": [
			{"width": "140px", "targets": [ 3 ]},
			{"width": "80px", "targets": [ 2, 4 ]},
			{"className": "text-center", "targets": [ 2, 4 ]},
			{"className": "text-end", "targets": [ 3 ]},
		],
		"Destroy": true,
		"iDisplayLength": 25, //Número de registros para paginar
		"order": [ [ 1, "desc" ] ] //Ordenar (columna, orden ascendente o descendente, etc)
	} ).DataTable();
	$( '#tblProductos' ).css( 'width', '100%' );
}

function facturar( pedidoID, tipo, clienteID ) {
	$.post( "../ajax/pedido.php?op=obtener_datos_factura", {pedidoID: pedidoID, tipo: tipo}, function ( data, statusUsuario ) {
		data = JSON.parse( data );
		console.log( clienteID );
		obtenerTipoCambio( 'MXN' ); //producción: obtenerTipoCambio( data.moneda );
		$( "#pedido_id_factura" ).val( pedidoID );
		$( "#clienteID_factura" ).val( clienteID );
		$( "#clienteID_factura" ).selectpicker( 'refresh' );
		$( "#telefono" ).val( data.telefono );
		$( "#calle" ).val( data.calle );
		$( "#num_ext" ).val( data.num_ext );
		$( "#num_int" ).val( data.num_int );
		$( "#colonia" ).val( data.colonia );
		$( "#poblacion" ).val( data.poblacion );
		$( "#edoPais" ).val( data.edoPais );
		$( "#email_cliente" ).val( data.email_cliente );
		let cp = data.cp ? data.cp : '44460';
		$( "#cp" ).val( cp );
		$( "#razonSocial" ).val( data.nombreCliente );
		let rfcCliente = data.rfcCliente ? data.rfcCliente : 'XEXX010101000';
		$( "#rfcCliente" ).val( rfcCliente );
		if ( rfcCliente == 'XEXX010101000' ) {
			$( "#iva_muestra" ).val( '001' );
			$( "#iva_muestra" ).selectpicker( 'refresh' );
		} else {
			$( "#iva_muestra" ).val( '002' );
			$( "#iva_muestra" ).selectpicker( 'refresh' );
		}
		let regimenFiscal = data.regimenFiscal ? data.regimenFiscal : '616';
		$( "#regimenFiscal" ).val( regimenFiscal );
		$( "#regimenFiscal" ).selectpicker( 'refresh' );
		$( "#num_cuenta" ).val( data.num_cuenta );
		$( "#banco" ).val( data.banco );
		$( "#metodoPago" ).val( data.metodoPago );
		$( "#metodoPago" ).selectpicker('refresh');
		$( "#formadePago" ).val( data.formadePago );
		$( "#formadePago" ).selectpicker( 'refresh' );
		let usoCfdi = data.usoCfdi ? data.usoCfdi : 'S01';
		$( "#usoCfdi" ).val( usoCfdi );
		$( "#usoCfdi" ).selectpicker( 'refresh' );
		$( "#comentarios" ).val( data.comentarios );
		let moneda = data.moneda ? data.moneda : 'USD';
		$( "#moneda" ).val( moneda );
		$( "#moneda" ).selectpicker( 'refresh' );
		if ( data.facturaID != null && data.facturaID != '' ) {
			$( "#facturaID" ).val( data.facturaID );
		} else {
			$( "#facturaID" ).val( '' );
		}
	});
	$.post( "../ajax/pedido.php?op=mostrarDetallesFactura", {pedidoID: pedidoID, tipo: tipo}, function ( data, statusUsuario ) {
		$("#detallesFactura").html(data);
	});
	const modalFacturas = new bootstrap.Modal( document.getElementById( 'modalFacturas' ) );
	modalFacturas.show();
}

function ver_factura( facturaID, pedidoID ) {
	$.post( "../ajax/pedido.php?op=ver_factura", {facturaID: facturaID, pedidoID: pedidoID}, function ( data, statusUsuario ) {
		data = JSON.parse( data );
		$( "#pedido_id_factura" ).val( pedidoID );
		$( "#facturaID" ).val( data.facturaID );
		$( "#moneda" ).val( data.moneda );
		$( "#moneda" ).selectpicker( 'refresh' );
		$( "#tipoCambio" ).val( data.tipoCambio );
		$( "#clienteID_factura" ).val( data.clienteID );
		$( "#clienteID_factura" ).selectpicker( 'refresh' );
		$( "#telefono" ).val( data.telefono );
		$( "#calle" ).val( data.calle );
		$( "#num_ext" ).val( data.num_ext );
		$( "#num_int" ).val( data.num_int );
		$( "#colonia" ).val( data.colonia );
		$( "#poblacion" ).val( data.poblacion );
		$( "#edoPais" ).val( data.edoPais );
		$( "#email_cliente" ).val( data.email_cliente );
		let cp = data.cp ? data.cp : '44460';
		$( "#cp" ).val( cp );
		$( "#razonSocial" ).val( data.nombreCliente );
		let rfcCliente = data.rfcCliente ? data.rfcCliente : 'XEXX010101000';
		$( "#rfcCliente" ).val( rfcCliente );
		if ( rfcCliente == 'XEXX010101000' ) {
			$( "#iva_muestra" ).val( '001' );
			$( "#iva_muestra" ).selectpicker( 'refresh' );
		} else {
			$( "#iva_muestra" ).val( '002' );
			$( "#iva_muestra" ).selectpicker( 'refresh' );
		}
		let regimenFiscal = data.regimenFiscal ? data.regimenFiscal : '616';
		$( "#regimenFiscal" ).val( regimenFiscal );
		$( "#regimenFiscal" ).selectpicker( 'refresh' );
		$( "#num_cuenta" ).val( data.num_cuenta );
		$( "#banco" ).val( data.banco );
		$( "#metodoPago" ).val( data.metodoPago );
		$( "#metodoPago" ).selectpicker('refresh');
		$( "#formadePago" ).val( data.formadePago );
		$( "#formadePago" ).selectpicker( 'refresh' );
		let usoCfdi = data.usoCfdi ? data.usoCfdi : 'S01';
		$( "#usoCfdi" ).val( usoCfdi );
		$( "#comentarios" ).val( data.comentarios );
	} );
	$.post( "../ajax/pedido.php?op=mostrarDetallesFactura", {pedidoID: pedidoID}, function ( data, statusUsuario ) {
		$("#detallesFactura").html(data);
	});
	$("#btnGuardarFactura").prop( "disabled", true );
	const modalFacturas = new bootstrap.Modal( document.getElementById( 'modalFacturas' ) );
	modalFacturas.show();
}

function edita_factura( pedidoID, facturaID, tipo ) {
	$.post( "../ajax/pedido.php?op=obtener_datos_factura", {tipo: tipo, pedidoID: pedidoID, facturaID: facturaID}, function ( data, statusUsuario ) {
		data = JSON.parse( data );
		$( "#pedido_id_factura" ).val( pedidoID );
		$( "#facturaID" ).val( facturaID );
		let moneda = data.moneda ? data.moneda : 'USD';
		$( "#moneda" ).val( moneda );
		$( "#moneda" ).selectpicker( 'refresh' );
		$( "#tipoCambio" ).val( data.tipoCambio );
		$( "#clienteID_factura" ).val( data.clienteID );
		$( "#clienteID_factura" ).selectpicker( 'refresh' );
		$( "#telefono" ).val( data.telefono );
		$( "#calle" ).val( data.calle );
		$( "#num_ext" ).val( data.num_ext );
		$( "#num_int" ).val( data.num_int );
		$( "#colonia" ).val( data.colonia );
		$( "#poblacion" ).val( data.poblacion );
		$( "#edoPais" ).val( data.edoPais );
		$( "#email_cliente" ).val( data.email_cliente );
		let cp = data.cp ? data.cp : '44460';
		$( "#cp" ).val( cp );
		$( "#razonSocial" ).val( data.nombreCliente );
		let rfcCliente = data.rfcCliente ? data.rfcCliente : 'XEXX010101000';
		$( "#rfcCliente" ).val( rfcCliente );
		if ( rfcCliente == 'XEXX010101000' ) {
			$( "#iva_muestra" ).val( '001' );
			$( "#iva_muestra" ).selectpicker( 'refresh' );
		} else {
			$( "#iva_muestra" ).val( '002' );
			$( "#iva_muestra" ).selectpicker( 'refresh' );
		}
		let regimenFiscal = data.regimenFiscal ? data.regimenFiscal : '616';
		$( "#regimenFiscal" ).val( regimenFiscal );
		$( "#regimenFiscal" ).selectpicker( 'refresh' );
		$( "#num_cuenta" ).val( data.num_cuenta );
		$( "#banco" ).val( data.banco );
		$( "#metodoPago" ).val( data.metodoPago );
		$( "#metodoPago" ).selectpicker( 'refresh' );
		$( "#formadePago" ).val( data.formadePago );
		$( "#formadePago" ).selectpicker( 'refresh' );
		let usoCfdi = data.usoCfdi ? data.usoCfdi : 'S01';
		$( "#usoCfdi" ).val( usoCfdi );
		$( "#usoCfdi" ).selectpicker( 'refresh' );
		$( "#comentarios" ).val( data.comentarios );
	} );
	$.post( "../ajax/pedido.php?op=mostrarDetallesFactura", {pedidoID: pedidoID, tipo: tipo}, function ( data, statusUsuario ) {
		$( "#detallesFactura" ).html( data );
	} );
	$( "#btnGuardarFactura" ).prop( "disabled", false );
	const modalFacturas = new bootstrap.Modal( document.getElementById( 'modalFacturas' ) );
	modalFacturas.show();
}

function modificarSubTotales(id) {
	let totales = 0;
	let uni = document.getElementById("precioVenta"+id).value;
	let cantidad = document.getElementById("cantidad"+id).value;

	totales = parseFloat(uni) * parseFloat(cantidad);
	document.getElementById("subtotal"+id).value = totales.toFixed(2);
	document.getElementById("subtotal_html"+id).innerHTML = totales.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
	modificarTotales();
}

function modificarTotales() {
	let totales = 0;
	let tot = document.getElementsByName("subtotal[]");
	for (let i = 0; i < tot.length; i++) {
		totales += parseFloat( tot[i].value );
	}
	console.log( totales );
	$('#grantotal').html( totales.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
}

function eliminarDetalle( indice ) {
	$( "#fila" + indice ).remove();
	calcularTotales();
	detalles = detalles - 1;
	evaluar();
}

async function obtenerTipoCambio(moneda) {
	if( moneda == 'MXN' ) {
		$( "#tipoCambio" ).val( 1 );
		return;
	} else {
		try {
			const response = await fetch('../tipo_cambio.php');
			const data = await response.json();
			if ( !response.ok ) {
				throw new Error( 'Error al obtener el tipo de cambio' );
			} else {
				let tipo_cambio = parseFloat(data.bmx.series[0].datos[0].dato);
				$( "#tipoCambio" ).val( tipo_cambio.toFixed( 4 ) );
			}
		} catch ( error ) {
			console.error( error );
		}
	}
}

function timbra( facturaID ) {
	bootbox.confirm( "¿Quieres Timbrar la Factura?<br/> <span style='color:red;font-weight:bold;'>¡Esta acción no se puede deshacer!</span>", function ( result ) {
		if ( result ) {
			var dialog = bootbox.dialog( {
				message: '<h5><i class="fa fa-cog fa-spin fa-fw" font-size="2"></i> Por favor espera mientras se timbra la factura...</h5>',
				closeButton: false
			} );
			$.post( "../ajax/factura.php?op=timbra", {facturaID: facturaID}, function ( data, status ) {
				bootstrap.Modal.getInstance( dialog ).hide();
				bootbox.alert( data );
				tabla.clear().draw();
				tabla.ajax.reload();
			} ).fail( function () {
				bootstrap.Modal.getInstance( dialog ).hide();
				bootbox.alert( 'No se ha podido timbrar la factura' );
			} ).done( function () {
				bootstrap.Modal.getInstance( dialog ).hide();
			} );
			setTimeout( function () {
				bootstrap.Modal.getInstance( dialog ).hide();
			}, 8000 );
		}
	} );
}

function modalCancelaFactura( facturaID, cliente_id ) {
	$( '#facturaID_cancela' ).val( facturaID );
	const cancelaFacturaModal = new bootstrap.Modal( document.getElementById( 'cancelaFactura' ) );
	cancelaFacturaModal.show();
	$.post( "../ajax/factura.php?op=obtener_facturas_cliente", {cliente_id: cliente_id}, function ( e ) {
		$( "#folioSustitucion" ).html( e );
		$( "#folioSustitucion" ).selectpicker( 'refresh' );
	} );
	document.getElementById( 'cancelaFactura' ).addEventListener( 'hidden.bs.modal', function () {
		$( '#facturaID_cancela' ).val( '' );
		document.getElementById( "motivo01" ).checked = true;
		document.getElementById( "folioSustitucion" ).disabled = false;
		$( "#folioSustitucion" ).val( "" );
		$( "#folioSustitucion" ).selectpicker( 'refresh' );
	}, {once: true} );
}

function cancelar_factura() {
	let facturaID = document.getElementById( "facturaID_cancela" ).value;
	let folioSustitucion = document.getElementById( "folioSustitucion" ).value;
	var facturaIDRelacionada = $( "#folioSustitucion" ).children( 'option:selected' ).data( 'folio' );
	let motivo = $( "input[type=radio][name=motivo]:checked" ).val();

	if ( ( motivo == '01' || motivo == '04' ) && folioSustitucion == '' ) {
		bootbox.alert( "Selecciona una <strong>Factura Relacionada</strong>" );
	} else {
		var dialog = bootbox.dialog( {
			message: '<p class="text-center"><h4><i class="fa fa-cog fa-spin fa-fw"></i> Por favor espera mientras se cancela la factura...</h4></p>',
			closeButton: false
		} );
		$.post( "../ajax/factura.php?op=cancela_factura", {facturaID: facturaID, facturaIDRelacionada: facturaIDRelacionada, folioSustitucion: folioSustitucion, motivo: motivo}, function ( e ) {
			bootbox.alert( e );
			bootstrap.Modal.getInstance( dialog ).hide();
			//var archivocancelado = "../facturar/archs_cfdi/cancelaciones/Factura - " + facturaID + " - CANCELADA.pdf";
			//window.open( archivocancelado );
			tabla.clear().draw();
			tabla.ajax.reload();
		} );
	}
}

function cambiaValorRadio( nuevoValor ) {
	if ( nuevoValor == '02' || nuevoValor == '03' ) {
		document.getElementById( "folioSustitucion" ).disabled = true;
		$( "#folioSustitucion" ).val( "" );
		$( "#folioSustitucion" ).selectpicker( 'refresh' );
	} else {
		document.getElementById( "folioSustitucion" ).disabled = false;
		$( "#folioSustitucion" ).val( "" );
		$( "#folioSustitucion" ).selectpicker( 'refresh' );
	}
}


function agregarDetalle( productoID, variante_id, nombreProducto, variante, imagen, precioVenta ) {
	let imagenCuadro = ( imagen ) ? '<a href="' + imagen + '" data-featherlight="image"><img class="img-thumbnail" style="width:40px;height:auto;" src="' + imagen + '"></a>' : '<img class="img-thumbnail" style="width:40px;height:auto;" src="../public/images/placeholder.jpg">';
	let cantidad = 1;
	if ( productoID ) {
		if ( document.getElementsByName( "contador[]" ).length != 0 ) {
			var contadorprod = document.getElementsByName( "contador[]" );
			var arrcontadorprod = [];
			for ( var p = 0; p < contadorprod.length; p++ ) {
				var contador0 = parseInt( contadorprod[ p ].value );
				arrcontadorprod.push( contador0 );
			}
			var numbersprod = arrcontadorprod;
			var numberprod2 = Math.max.apply( null, numbersprod );
			var numberprod = parseInt( numberprod2 ) + 1;
			var cont = parseInt( numberprod );
		} else { }
		if ( !cont ) {var cont = 0;}
		let fila = '<tr class="filas" id="fila' + cont + '">' +
			'	<td style="text-align:center;">' +
			'	<input type="hidden" class="form-control" name="contador[]" id="contador' + cont + '" value="' + cont + '">' +
			'		<button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(' + cont + ')"><i class="fas fa-times"></i></button>' +
			'	</td>' +

			//Cantidad
			'	<td style="width:140px;!important">' +
			'		<input type="number" min="1" step="1" style="text-align:right;" class="form-control" class="form-control" name="cantidad[]" id="cantidad' + cont + '" placeholder="Número de Cantidad" value="' + cantidad + '" oninput="modificarSubTotales(' + cont + ')" required>' +
			'</td>' +

			//Producto
			'	<td>' +
			'		<input type="hidden" class="form-control" name="contador[]" id="contador' + cont + '" value="' + cont + '">' +
			'		<input type="hidden" name="variante_id[]" value="' + variante_id + '">' +
			'		<input type="hidden" name="producto_id[]" value="' + productoID + '">' + variante +
			'	</td>' +

			//Variante
			'	<td>' +
			'		<textarea class="form-control" name="descripcion[]" id="descripcion' + cont + '" placeholder="Descripción" rows="3"></textarea>' +
			'	</td>' +

			//Imagen
			'	<td style="width:140px;!important;text-align:center;">' +
			imagenCuadro +
			'	</td>' +

			//Unitario
			'	<td style="width:140px;!important;text-align:right;">' +
			'		<input type="number" min=".01" step=".01" style="text-align:right;" class="form-control" class="form-control" name="precioVenta[]" id="precioVenta' + cont + '" placeholder="Precio Unitario" value="' + precioVenta + '" oninput="modificarSubTotales(' + cont + ')" required>' +
			'	</td>' +
			'	<td style="width:140px;!important;text-align:right;">' +
			'		$<span id="subtotal_html' + cont + '">' + precioVenta + '</span>' +
			'		<input type="hidden" class="form-control" name="subtotal[]" id="subtotal' + cont + '" value="' + precioVenta + '">' +
			'	</td>' +
			'</tr>';
		cont++;
		$( '#detalles' ).append( fila );
		modificarTotales();
	}
}

init();