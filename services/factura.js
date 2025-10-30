var tabla;
var tablaProductos;
var tabla_detalles;

//Función que se ejecuta al inicio
function init() {
	listar();
	mostrarform( false );

	var date = new Date(), y = date.getFullYear(), m = date.getMonth();
	var primerDia = new Date( y, 0, 1 );

	$( '#fechaInicio' ).datetimepicker( {
		format: 'YYYY-MM-DD',
		defaultDate: moment( primerDia )
	} );

	$( '#fechaCompromiso' ).datetimepicker( {
		format: 'YYYY-MM-DD'
	} );

	$( "#fechaInicio" ).on( 'change.datetimepicker', function ( e ) {
		tabla.draw();
	} ).keyup( function () {
		tabla.draw();
	} );

	$( "#fechaFin" ).datetimepicker( {
		format: 'YYYY-MM-DD',
	} );

	$( "#fechaFin" ).on( 'change.datetimepicker', function ( e ) {
		tabla.draw();
	} ).keyup( function () {
		tabla.draw();
	} );

	$( document ).ready( function () {
		$.fn.dataTable.ext.search.push(
			function ( settings, data, dataIndex ) {
				if ( settings.nTable.id !== 'tbllistado' ) {
					return true;
				}
				var iniInput = $( '#fechaInicioInput' ).val();
				if ( iniInput ) {
					var min = $( '#fechaInicio' ).datetimepicker( 'viewDate' ).subtract( 1, "days" );
				} else {
					var min = new Date( '2015-01-01' );
				}

				var ultInput = $( '#fechaFinInput' ).val();
				if ( ultInput ) {
					var max = $( '#fechaFin' ).datetimepicker( 'viewDate' );
				} else {
					var max = new Date( '2050-01-01' );
				}

				var fechaFactura = new Date( data[ 1 ] );
				if ( min == null && max == null ) {return true;}
				if ( min == null && fechaFactura <= max ) {return true;}
				if ( max == null && fechaFactura >= min ) {return true;}
				if ( fechaFactura <= max && fechaFactura >= min ) {return true;}
				return false;
			}
		);
	} );

	$( "#formulario" ).on( "submit", function ( e ) {
		guardaryeditar( e );
	} );

	$( "#formulario_enviafactura" ).on( "submit", function ( e ) {
		enviaEmailFactura( e );
	} );

	$( "#formulario_copiafactura" ).on( "submit", function ( e ) {
		copiaFactura( e );
	} );

	$.post( "../ajax/factura.php?op=select_factura", function ( r ) {
		$( "#facturaRelacionada" ).html( r );
		$( "#facturaRelacionada" ).selectpicker( 'refresh' );
	} );

	$( "#facturaRelacionada" ).on( "change", function () {
		var total_fac = $( this ).children( 'option:selected' ).data( 'total_fac' );
		$( '#totalFacturaRelacionada' ).val( total_fac );
		$( '#descuento' ).val( total_fac );
		var uuid = $( this ).children( 'option:selected' ).data( 'uuid' );
		$( '#facturaCfdiRelacionada' ).val( uuid );
	} );

	//Cargamos los items del cliente
	$.post( "../ajax/cliente.php?op=select_cliente_factura", function ( r ) {
		$( "#cliente_id" ).html( r );
		$( "#cliente_id" ).selectpicker( 'refresh' );

		$( "#cliente_copia" ).html( r );
		$( "#cliente_copia" ).selectpicker( 'refresh' );
	} );

	$.post( "../ajax/metodopago.php?op=select_metodopago", function ( r ) {
		$( "#formadePago" ).html( r );
		$( "#formadePago" ).selectpicker( 'refresh' );

		$( "#formaPagoAut" ).html( r );
		$( "#formaPagoAut" ).selectpicker( 'refresh' );
	} );

	//Se cargan los datos del cliente seleccionado
	$( "#cliente_id" ).on( "change", function () {
		var calle = $( this ).children( 'option:selected' ).data( 'calle' );
		var rfccliente = $( this ).children( 'option:selected' ).data( 'rfccliente' );
		var num_ext = $( this ).children( 'option:selected' ).data( 'numext' );
		var num_int = $( this ).children( 'option:selected' ).data( 'numint' );
		var colonia = $( this ).children( 'option:selected' ).data( 'colonia' );
		var poblacion = $( this ).children( 'option:selected' ).data( 'poblacion' );
		var edopais = $( this ).children( 'option:selected' ).data( 'edopais' );
		var cp = $( this ).children( 'option:selected' ).data( 'cp' );
		var contacto = $( this ).children( 'option:selected' ).data( 'contacto' );
		var num_cuenta = $( this ).children( 'option:selected' ).data( 'nocuenta' );
		var regimenfiscal = $( this ).children( 'option:selected' ).data( 'regimenfiscal' );
		var metodopago = $( this ).children( 'option:selected' ).data( 'metodopago' );
		var usoCfdi = $( this ).children( 'option:selected' ).data( 'usocfdi' );
		var formapago = $( this ).children( 'option:selected' ).data( 'formapago' );
		var credito = $( this ).children( 'option:selected' ).data( 'credito' );
		$( '#rfcCliente' ).val( rfccliente );
		$( '#regimenFiscal' ).val( regimenfiscal );
		$( '#regimenFiscal' ).selectpicker( 'refresh' );
		$( '#calle' ).val( calle );
		$( '#num_ext' ).val( num_ext );
		$( '#num_int' ).val( num_int );
		$( '#colonia' ).val( colonia );
		$( '#poblacion' ).val( poblacion );
		$( '#edoPais' ).val( edopais );
		$( '#cp' ).val( cp );
		$( '#contacto' ).val( contacto );
		$( '#num_cuenta' ).val( num_cuenta );
		$( '#usoCfdi' ).val( usoCfdi );
		$( '#usoCfdi' ).selectpicker( 'refresh' );
		$( '#formadePago' ).val( formapago );
		$( '#formadePago' ).selectpicker( 'refresh' );
		$( '#credito' ).val( credito );
		$( '#credito' ).selectpicker( 'refresh' );

		if ( credito == '30' ) {
			let fecha_compromiso = new Date();
			fecha_compromiso.setDate( fecha_compromiso.getDate() + 30 );
			$( '#fechaCompromiso' ).datetimepicker( 'date', moment( fecha_compromiso ).format( 'YYYY-MM-DD' ) );
		} else if ( credito == '15' ) {
			let fecha_compromiso = new Date();
			fecha_compromiso.setDate( fecha_compromiso.getDate() + 15 );
			$( '#fechaCompromiso' ).datetimepicker( 'date', moment( fecha_compromiso ).format( 'YYYY-MM-DD' ) );
		} else if ( credito == '7' ) {
			let fecha_compromiso = new Date();
			fecha_compromiso.setDate( fecha_compromiso.getDate() + 7 );
			$( '#fechaCompromiso' ).datetimepicker( 'date', moment( fecha_compromiso ).format( 'YYYY-MM-DD' ) );
		} else {
			let fecha_compromiso = new Date();
			$( '#fechaCompromiso' ).datetimepicker( 'date', moment( fecha_compromiso ).format( 'YYYY-MM-DD' ) );
		}

		$( '#metodoPago' ).val( metodopago );
		$( '#metodoPago' ).selectpicker( 'refresh' );
		$( '#metodoPago' ).trigger( 'change' );

		calcularTotales();
	} );

	$( "#metodoPago" ).on( "change", function () {
		let metodoPago = document.getElementById( 'metodoPago' ).value;
		if ( metodoPago == 'PPD' ) {
			$( '#formadePago' ).val( '99' );
			$( '#formadePago' ).selectpicker( 'refresh' );
		} else {
			var formapago = $( "#cliente_id" ).children( 'option:selected' ).data( 'formadepago' );
			$( '#formadePago' ).val( formapago );
			$( '#formadePago' ).selectpicker( 'refresh' );
		}
	} );

	//Se cargan el precio del dólar adquirido en la casilla de tipo de cambio, cuando la casilla moneda cambie a dólar
	$( '#moneda' ).on( 'change', function () {
		if ( this.value == 'USD' ) {
			$( document ).ready( function () {
				$.ajax( {
					url: "../ajax/factura.php?op=obtener_precio_dolar",
					success: function ( result ) {
						$( '#tipoCambio' ).val( result );
					}
				} );
			} );
		} else {
			$( '#tipoCambio' ).val( '1' );
		}
	} );
	$( '#tbllistado tfoot th' ).each( function ( i ) {
		if ( i == 4 || i == 5 || i == 6 || i == 7 || i == 8 || i == 10 ) {
		} else {
			var title = $( '#tbllistado thead th' ).eq( $( this ).index() ).text();
			$( this ).html( '<input style="width:100%" type="text" placeholder="Buscar ' + title + '" />' );
		}
	} );

	$( "#credito" ).on( 'change', function () {
		let credito = document.getElementById( 'credito' ).value;
		if ( credito == '30' ) {
			let fecha_compromiso = new Date();
			fecha_compromiso.setDate( fecha_compromiso.getDate() + 30 );
			$( '#fechaCompromiso' ).datetimepicker( 'date', moment( fecha_compromiso ).format( 'YYYY-MM-DD' ) );
		} else if ( credito == '15' ) {
			let fecha_compromiso = new Date();
			fecha_compromiso.setDate( fecha_compromiso.getDate() + 15 );
			$( '#fechaCompromiso' ).datetimepicker( 'date', moment( fecha_compromiso ).format( 'YYYY-MM-DD' ) );
		} else if ( credito == '7' ) {
			let fecha_compromiso = new Date();
			fecha_compromiso.setDate( fecha_compromiso.getDate() + 7 );
			$( '#fechaCompromiso' ).datetimepicker( 'date', moment( fecha_compromiso ).format( 'YYYY-MM-DD' ) );
		} else {
			let fecha_compromiso = new Date();
			$( '#fechaCompromiso' ).datetimepicker( 'date', moment( fecha_compromiso ).format( 'YYYY-MM-DD' ) );
		}
	} );

	listarProductos();
}

//Función limpiar
function limpiar() {
	$( "#facturaID" ).val( "" );
	$( "#tipoCajaID" ).val( "" );
	$( '#tipoCajaID' ).selectpicker( 'refresh' );
	$( "#tipoCambio" ).val( "1" );
	$( "#metodoPago" ).val( "" );
	$( '#metodoPago' ).selectpicker( 'refresh' );
	$( "#formadePago" ).val( "" );
	$( '#formadePago' ).selectpicker( 'refresh' );
	$( "#credito" ).val( "" );
	$( '#credito' ).selectpicker( 'refresh' );
	$( "#usoCfdi" ).val( "" );
	$( '#usoCfdi' ).selectpicker( 'refresh' );
	$( "#tipoRelacion" ).val( "" );
	$( '#tipoRelacion' ).selectpicker( 'refresh' );
	$( "#cliente_id" ).val( "" );
	$( '#cliente_id' ).selectpicker( 'refresh' );
	$( "#rfcCliente" ).val( "" );
	$( "#num_cuenta" ).val( "" );
	$( "#calle" ).val( "" );
	$( "#num_ext" ).val( "" );
	$( "#num_int" ).val( "" );
	$( "#colonia" ).val( "" );
	$( "#poblacion" ).val( "" );
	$( "#cp" ).val( "" );
	$( "#cliente" ).val( "" );
	$( "#descuento" ).val( "" );
	$( "#facturaCfdiRelacionada" ).val( "" );
	$( '#facturaCfdiRelacionada' ).selectpicker( 'refresh' );
	$( "#facturaRelacionada" ).val( "" );
	$( '#facturaRelacionada' ).selectpicker( 'refresh' );
	$( "#totalFacturaRelacionada" ).val( "" );
	$( "#folioFiscal" ).val( "" );
	$( '#fechaCompromiso' ).datetimepicker( 'clear' );

	detalles = 0;
	cont = 0;
	$( ".fila" ).remove();
	$( ".filas" ).remove();
	$( "#detalles > tbody" ).empty();
	$( "#detalles" ).html( '<thead style="background-color:#A9D0F5"><th></th><th>Producto</th><th>Descripción</th><th>Cantidad</th><th>$Unitario</th><th>$Subtotal</th></thead><tfoot><th colspan="4"></th><th style="text-align:right;width:220px;">Subtotal: $<span id="subtotal_fac_imp">0.00</span><br><span id="iva"></span></th><th style="text-align:right;width:220px;"><span id="isrRetencion"></span><span id="ivaRetencion"></span><span style="font-weight:bold;">Total: $<span id="total_fac_imp">0.00</span></span></th></tfoot><tbody></tbody>' );

	document.getElementById( "btnGuardar" ).disabled = true;
}

//Función mostrar formulario
function mostrarform( flag ) {
	if ( flag ) {
		$( ".listadoregistros" ).hide();
		$( ".formularioregistros" ).show();
		$( "#btnagregar" ).hide();
		detalles = 0;
	} else {
		$( ".listadoregistros" ).show();
		$( ".formularioregistros" ).hide();
		$( "#btnagregar" ).show();
	}
}

//Función cancelarform
function cancelarform() {
	limpiar();
	mostrarform( false );
}

//Función Listar
function listar() {
	tabla = $( '#tbllistado' ).DataTable( {
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: "f<'row'<'col-sm-2'B><'col-sm-1'l><'col-sm-9'p>> rt <'bottom'ip<'clear'>>",//Definimos los elementos del control de tabla
		buttons: [
			{extend: 'excelHtml5', title: 'Listado de Factura', exportOptions: {columns: [ 0, 1, 2, 3, 4, 5, 6, 9 ]}, className: 'btn btn-sm btn-primary'},
			{extend: 'pdf', title: 'Listado de Factura', exportOptions: {columns: [ 0, 1, 2, 3, 4, 5, 6, 9 ]}, orientation: 'landscape', className: 'btn btn-sm btn-primary'},
		],
		"ajax": {
			url: '../ajax/factura.php?op=listar',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"columnDefs": [
			{"width": "80px", "targets": 0},
			{"type": "natural", "targets": 0},
			{"width": "65px", "targets": [ 1, 8 ]},
			{"width": "85px", "targets": [ 6, 7 ]},
			{"width": "115px", "targets": [ 2, 4, 5, 6 ]},
			{"width": "130px", "targets": 9},
			{"className": "text-center", "targets": [ 0, 1, 2, 7, 8, 9 ]},
			{"className": "text-end", "targets": [ 4, 5, 6 ]},
		],
		"createdRow": function ( row, data, dataIndex ) {
			if ( data[ 8 ] == '<span class="badge bg-danger">Cancelada</span>' ) {
				$( row ).addClass( 'table-danger' );
			}
			else if ( data[ 8 ] == '<span class="badge bg-success">Pagada</span>' ) {
				$( row ).addClass( 'table-info' );
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
		"footerCallback": function ( row, data, start, end, display ) {
			var api = this.api(), data;
			var intVal = function ( i ) {
				return typeof i === 'string' ? i.replace( /[\$,]/g, '' ) * 1 : typeof i === 'number' ? i : 0;
			};
			var pagado = api.column( 4, {search: 'applied'} ).data().reduce( function ( a, b ) {
				return intVal( a ) + intVal( b );
			}, 0 );
			var saldo = api.column( 5, {search: 'applied'} ).data().reduce( function ( a, b ) {
				return intVal( a ) + intVal( b );
			}, 0 );
			$( api.column( 4 ).footer() ).html( '$' + pagado.toFixed( 2 ).toString().replace( /\B(?=(\d{3})+(?!\d))/g, "," ) );
			$( api.column( 5 ).footer() ).html( '$' + saldo.toFixed( 2 ).toString().replace( /\B(?=(\d{3})+(?!\d))/g, "," ) );
		},
		"bDestroy": true,
		"iDisplayLength": 10,//Paginación
		"order": [ [ 0, "desc" ] ]//Ordenar (columna,orden)
	} );
}

//Función Listar Detalle Activo
function listarProductos() {
	tablaProductos = $( '#tblDetalles' ).DataTable( {
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: 'frtip',//Definimos los elementos del control de tabla
		"ajax": {
			url: '../ajax/factura.php?op=listarProductos',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},

		"columnDefs": [
			{"width": "40px", "targets": [ 0, 4 ]},
			{"width": "100px", "targets": [ 3 ]},
			{"className": "text-center", "targets": [ 0, 4 ]},
			{"className": "text-end", "targets": [ 3 ]},
		],
		"bDestroy": true,
		"iDisplayLength": 10,//Paginación
		"order": [ 0, "desc" ]//Ordenar (columna,orden)
	} );
	$( "#tblDetalles" ).css( "width", "100%" );
}

function mostrarPagos( facturaID ) {
	document.getElementById( "facturaPago" ).innerHTML = facturaID;
	tabla_detalles = $( '#tblPagos' ).dataTable( {
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: 'frtip',//Definimos los elementos del control de tabla
		"ajax": {
			url: '../ajax/factura.php?op=mostrarPagos&fac=' + facturaID,
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"columnDefs": [
			{"width": "50px", "targets": [ 0 ]},
			{"width": "75px", "targets": [ 2 ]},
			{"width": "90px", "targets": [ 4, 6 ]},
			{"width": "120px", "targets": [ 1, 5 ]},
			{"className": "text-center", "targets": [ 0, 1, 4, 6 ]},
			{"className": "text-end", "targets": [ 5 ]}
		],
		"bDestroy": true,
		"iDisplayLength": 10,//Paginación
		"order": [ 0, "desc" ]//Ordenar (columna,orden)
	} ).DataTable();
	$( "#tblPagos" ).css( "width", "100%" );
	$( "#modalPagos" ).modal( "toggle" );
}

function guardaryeditar( e ) {
	e.preventDefault(); //No se activará la acción predeterminada del evento
	var formData = new FormData( $( "#formulario" )[ 0 ] );

	$.ajax( {
		url: "../ajax/factura.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function ( datos ) {
			bootbox.alert( datos );
			mostrarform( false );
			listar();
		}
	} );
	limpiar();
}

function timbra( facturaID ) {
	bootbox.confirm( "¿Quieres Timbrar la Factura?<br/> <span style='color:red;font-weight:bold;'>¡Esta acción no se puede deshacer!</span>", function ( result ) {
		if ( result ) {
			var dialog = bootbox.dialog( {
				message: '<h5><i class="fa fa-cog fa-spin fa-fw" font-size="2"></i> Por favor espera mientras se timbra la factura...</h5>',
				closeButton: false
			} );
			$.post( "../ajax/factura.php?op=timbra", {facturaID: facturaID}, function ( data, status ) {
				dialog.modal( 'hide' );
				bootbox.alert( data );
				tabla.clear().draw();
				tabla.ajax.reload();
			} ).fail( function () {
				dialog.modal( 'hide' );
				bootbox.alert( 'No se ha podido timbrar la factura' );
			} ).done( function () {
				dialog.modal( 'hide' );
			} );
			dialog.modal( 'hide' );
		}
	} );
}

function mostrar( facturaID ) {
	$.post( "../ajax/factura.php?op=mostrar", {facturaID: facturaID}, function ( data, status ) {
		data = JSON.parse( data );
		mostrarform( true );

		$( "#facturaID" ).val( data.facturaID );
		$( "#status" ).val( data.status );
		$( "#folio" ).val( data.facturaID );
		$( "#serie" ).val( data.serie );
		$( '#serie' ).selectpicker( 'refresh' );
		$( "#moneda" ).val( data.moneda );
		$( '#moneda' ).selectpicker( 'refresh' );
		$( "#tipoCambio" ).val( data.tipoCambio );
		$( "#metodoPago" ).val( data.metodoPago );
		$( '#metodoPago' ).selectpicker( 'refresh' );
		$( "#usoCfdi" ).val( data.usoCfdi );
		$( '#usoCfdi' ).selectpicker( 'refresh' );
		$( "#tipoRelacion" ).val( data.tipoRelacion );
		$( '#tipoRelacion' ).selectpicker( 'refresh' );
		$( "#cliente_id" ).val( data.cliente_id );
		$( '#cliente_id' ).selectpicker( 'refresh' );
		$( "#rfcCliente" ).val( data.rfcCliente );
		$( "#num_cuenta" ).val( data.num_cuenta );
		$( "#formadePago" ).val( data.formadePago );
		$( '#formadePago' ).selectpicker( 'refresh' );
		$( "#credito" ).val( data.credito );
		$( '#credito' ).selectpicker( 'refresh' );
		if ( data.fechaCompromiso ) {
			$( '#fechaCompromiso' ).datetimepicker( 'date', moment( data.fechaCompromiso ).format( 'YYYY-MM-DD' ) );
		} else {
			$( '#fechaCompromiso' ).datetimepicker( 'clear' );
		}

		$( "#calle" ).val( data.calle );
		$( "#num_ext" ).val( data.num_ext );
		$( "#num_int" ).val( data.num_int );
		$( "#colonia" ).val( data.colonia );
		$( "#poblacion" ).val( data.poblacion );
		$( "#cp" ).val( data.cp );
		$( "#edoPais" ).val( data.edoPais );
		$( "#cliente" ).val( data.cliente );
		$( "#descuento" ).val( data.descuento );
		$( "#totalFacturaRelacionada" ).val( data.totalFacturaRelacionada );
		$( "#facturaCfdiRelacionada" ).val( data.facturaCfdiRelacionada );
		$( "#tipoRelacion" ).val( data.tipoRelacion );
		$( '#tipoRelacion' ).selectpicker( 'refresh' );
		$( "#regimenFiscal" ).val( data.regimenFiscal );
		$( '#regimenFiscal' ).selectpicker( 'refresh' );
		$( "#facturaRelacionada" ).val( data.facturaCfdiRelacionada );
		$( '#facturaRelacionada' ).selectpicker( 'refresh' );
		$( "#folioFiscal" ).val( data.nombrePDF );

		//Ocultar y mostrar los botones
		if ( data.nombrePDF ) {
			document.getElementById( "btnGuardar" ).disabled = true;
			document.getElementById( "btnAgregarArt" ).disabled = true;
		} else {
			document.getElementById( "btnGuardar" ).disabled = false;
			document.getElementById( "btnAgregarArt" ).disabled = false;
		}
	} );

	$.post( "../ajax/factura.php?op=listarDetalle&id=" + facturaID, function ( r ) {
		$( "#detalles" ).html( r );
	} );

	setTimeout( function () {listarProductos();}, 500 );
}

//Función para marcar como Espera el Envio
function espera( facturaID ) {
	bootbox.confirm( "¿Quieres poner Espera el La Factura?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/factura.php?op=espera", {facturaID: facturaID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

//Función para Marcar como Pagada la Factura
function parcial( facturaID ) {
	bootbox.confirm( "Quieres marcar como Pago Parcial en la Factura?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/factura.php?op=parcial", {facturaID: facturaID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

//Función para Marcar como Pagada la Factura
function BootboxContent() {
	var datepicker_opts = {
		format: 'yyyy-mm-dd',
		maxViewMode: 2,
		todayBtn: 'linked',
		language: 'es',
		autoclose: true,
		todayHighlight: true
	};
	var frm_str = '<form id="formaFechaEntrega">'
		+ '<div class="form-group">'
		+ '<label for="date">Fecha</label>'
		+ '<input id="date" class="date span2 form-control input-sm" name="date" placeholder="yyyy-mm-dd" type="text" onclick="muestra_boton_guardar_produccion()">'
		+ '</div>'
		+ '</form><script>$(".boton_guardar_producido").hide();</script>';

	var objeto = $( '<div/>' ).html( frm_str ).contents();

	objeto.find( '.date' ).datepicker( datepicker_opts );
	return objeto
}

function pagada( facturaID ) {
	$( "#modalFacturaPagada" ).modal( "show" );
	document.getElementById( "facturaID_pagada" ).value = facturaID;
	document.getElementById( "facturaID_modal" ).innerHTML = facturaID;
}

function pagadaFactura() {
	var facturaID = document.getElementById( "facturaID_pagada" ).value;
	var formaPagoAut = document.getElementById( "formaPagoAut" ).value;
	var fechaPago = document.getElementById( "fechaPago" ).value;

	if ( fechaPago == '' || formaPagoAut == '' ) {
		( fechaPago == '' && formaPagoAut == '' ) ? mensaje = "No se ha seleccionado ninguna <strong>Fecha y Forma de Pago.</strong>" : ( ( fechaPago != '' && formaPagoAut == '' ) ? mensaje = "No se ha seleccionado ninguna <strong>Forma de Pago.</strong>" : mensaje = "No se ha seleccionado ninguna <strong>Fecha.</strong>" );
		bootbox.alert( mensaje );
	} else {
		$.post( "../ajax/factura.php?op=pagada", {facturaID: facturaID, fechaPago: fechaPago, formaPagoAut: formaPagoAut}, function ( resp ) {
			bootbox.alert( resp );
			tabla.clear().draw();
			tabla.ajax.reload();
			$( "#modalFacturaPagada" ).modal( "hide" );
			limpiarModal();
		} );
	}
}

function limpiarModal() {
	$( "#formaPagoAut" ).val( '' );
	$( "#fechaPago" ).val( '' );
}

function muestra_boton_guardar_produccion() {
	$( ".boton_guardar_producido" ).show();
}

function modalCopiarFactura( facturaID, cliente_id ) {
	document.getElementById( "factura_id_copia" ).value = facturaID;
	document.getElementById( "noFacturaCopia" ).innerHTML = facturaID;
	$( "#cliente_copia" ).val( cliente_id );
	$( "#cliente_copia" ).selectpicker( 'refresh' );
	$( "#copiaFactura" ).modal( "show" );

	$( "#copiaFactura" ).on( "hidden.bs.modal", function () {
		document.getElementById( "noFacturaCopia" ).innerHTML = '';
		document.getElementById( "factura_id_copia" ).value = '';
		$( "#cliente_copia" ).val( '' );
		$( "#cliente_copia" ).selectpicker( 'refresh' );
	} );
}

function copiaFactura( e ) {
	var dialog = bootbox.dialog( {
		message: '<p class="text-center">Espera mientras se copia la factura</p>',
		closeButton: false
	} );
	e.preventDefault(); //No se activará la acción predeterminada del evento
	var formData = new FormData( $( "#formulario_copiafactura" )[ 0 ] );

	$.ajax( {
		url: "../ajax/factura.php?op=copiaFactura",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function ( datos ) {
			dialog.modal( 'hide' );
			bootbox.alert( datos );
			tabla.clear().draw();
			tabla.ajax.reload();
		},
		error: function ( datos ) {
			bootbox.alert( datos );
			dialog.modal( 'hide' );
		}
	} );
	dialog.modal( 'hide' );
}

//Función para Cancelar la Factura
function modalCancelaFactura( facturaID, cliente_id ) {
	$( '#facturaID_cancela' ).val( facturaID );
	$.post( "../ajax/factura.php?op=obtener_facturas_cliente", {cliente_id: cliente_id}, function ( e ) {
		$( "#folioSustitucion" ).html( e );
		$( "#folioSustitucion" ).selectpicker( 'refresh' );
	} );
	$( "#cancelaFactura" ).modal( 'show' );
	$( '#cancelaFactura' ).on( 'hidden.bs.modal', function () {
		$( '#facturaID_cancela' ).val( '' );
		document.getElementById( "motivo01" ).checked = true;
		document.getElementById( "folioSustitucion" ).disabled = false;
		$( "#folioSustitucion" ).val( "" );
		$( "#folioSustitucion" ).selectpicker( 'refresh' );
	} );
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
			dialog.modal( 'hide' );
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

function retimbrar( facturaID ) {
	bootbox.confirm( "¿Quieres de Reiniciar el Timbrado de la Factura?, <br><span style='color:red;font-weight:bold;'>¡Esta acción no elimina una factura correctamente timbrada en el SAT, pero reescribe el folio interno!</span>", function ( result ) {
		if ( result ) {
			$.post( "../ajax/factura.php?op=retimbrar", {facturaID: facturaID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function enviaEmailFactura( e ) {
	var dialog = bootbox.dialog( {
		message: '<p class="text-center">Espera mientras se envía la factura por correo</p>',
		closeButton: false
	} );
	e.preventDefault(); //No se activará la acción predeterminada del evento
	var formData = new FormData( $( "#formulario_enviafactura" )[ 0 ] );

	$.ajax( {
		url: "../ajax/factura.php?op=enviaEmailFactura",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function ( datos ) {
			dialog.modal( 'hide' );
			bootbox.alert( datos );
			marcarFacturaEnviada();
		}
	} );
}

function marcarFacturaEnviada() {
	var numeroFac = document.getElementById( "numeroFac2" ).value;
	$.post( "../ajax/factura.php?op=marcarFacturaEnviada", {facturaID: numeroFac}, function ( results ) {

	} );
}

function mostrarEmailsFactura( facturaID ) {
	$.post( "../ajax/factura.php?op=mostrarEmailsFactura", {facturaID: facturaID}, function ( r ) {
		if ( r == 'Se debe generar la factura antes de enviarla por email' ) {
			$( "#emailsEnvioFactura" ).html( r );
			document.getElementById( "btnEnviarFactura" ).disabled = true;
		} else {
			$( "#emailsEnvioFactura" ).html( r );
			document.getElementById( "btnEnviarFactura" ).disabled = false;
		}
	} );

	$.post( "../ajax/factura.php?op=mostrarRutaFactura", {facturaID: facturaID}, function ( data ) {
		var data = data.split( ',' );
		$( "#nombreXML" ).val( data[ 0 ] );
		$( "#nombrePDF" ).val( data[ 1 ] );
		$( "#Fact_NoFact" ).val( data[ 2 ] );
		$( "#numeroFac" ).html( data[ 2 ] );
		$( "#numeroFac2" ).val( data[ 3 ] );
		$( "#cliente2" ).val( data[ 4 ] );
	} );
}

function activarEmailExtra() {
	var inputEmailExtra = document.getElementById( "emailextrainput" );
	if ( document.querySelector( '#emailextra' ).checked ) {
		inputEmailExtra.disabled = false;
		inputEmailExtra.classList.remove( "readonly-input" );
	} else {
		inputEmailExtra.value = '';
		inputEmailExtra.disabled = true;
		inputEmailExtra.classList.add( "readonly-input" );
	}
}

//Declaración de variables necesarias para trabajar con las compras y sus detalles
document.getElementById( "btnGuardar" ).disabled = true;
var detalles = 0;
var cont = 0;

function agregarDetalle( producto_id, nombreProducto, totalVenta ) {
	if ( document.getElementsByName( "contador_actual[]" ).length != 0 ) {
		var contadorprod = document.getElementsByName( "contador_actual[]" );
		var arrcontadorprod = [];
		for ( var p = 0; p < contadorprod.length; p++ ) {
			var contador0 = parseInt( contadorprod[ p ].value );
			arrcontadorprod.push( contador0 );
		}
		var numbersprod = arrcontadorprod;
		var numberprod2 = Math.max.apply( null, numbersprod );
		var numberprod = parseInt( numberprod2 ) + 1;
		var cont = parseInt( numberprod );
		var detalles = parseInt( numberprod );
	}
	if ( !cont ) {var cont = 0;} if ( !detalles ) {var detalles = 0;}
	var preciounitario = parseFloat( totalVenta ).toFixed( 2 );
	if ( producto_id != "" ) {
		var subtotal = preciounitario;
		var fila = '<tr class="filas" id="fila' + producto_id + '">' +
		//Botones
			'<td style="text-align:center;"><button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(' + producto_id + ')"><i class="fa fa-close"></i></button><input type="hidden" value="' + cont + '" id="contador' + cont + '" name="contador_actual[]"></td>' +

		//Nombre Producto
			'<td><input type="hidden" name="producto_id[]" id="producto_id' + cont + '" value="' + producto_id + '">' + nombreProducto + '</td>' +

		//Descripción
			'<td style="width:210px;"><textarea style="width:210px;" name="descripcion[]" id="descripcion' + cont + '"></textarea></td>' +

		//Cantidad Factura
			'<td style="text-align:right;width:90px;"><input type="number" style="width:80px;text-align:right;" onchange="modificarSubtotales()" min="0" step="1" name="cantidad[]" id="cantidad' + cont + '" value="1"></td>' +

		//Precio Unitario
			'<td style="text-align:right;width:220px;"><input type="hidden" name="precioVenta[]" id="precioVenta' + cont + '" value="' + preciounitario + '" onchange="modificarSubtotales()">$ ' + preciounitario + '</td>' +

		//Subtotal
			'<td style="text-align:right;width:220px;">$ <span name="subtotal" id="subtotal' + cont + '">' + subtotal + '</span><input type="hidden" name="subtotal[]" id="subtotal' + cont + '" value="' + subtotal + '"></td>' +

			'</tr>' +
			cont++;
		detalles = detalles + 1;
		$( '#detalles' ).append( fila );
		modificarSubtotales();
	} else {
		alert( "Error al ingresar el detalle, revisar los datos del artículo" );
	}
}

function modificarSubtotales() {
	var cant = document.getElementsByName( "cantidad[]" );
	var prec = document.getElementsByName( "precioVenta[]" );
	var sub = document.getElementsByName( "subtotal[]" );

	for ( var i = 0; i < cant.length; i++ ) {
		var inpC = cant[ i ];
		var inpP = prec[ i ];
		var inpS = sub[ i ];

		var subtotal2 = inpC.value * inpP.value;
		inpS.value = subtotal2.toFixed( 2 );
		document.getElementsByName( "subtotal" )[ i ].innerHTML = subtotal2.toFixed( 2 );
		setTimeout( function () {calcularTotales();}, 500 );
	}
}

function calcularTotales() {
	var sub = document.getElementsByName( "subtotal[]" );
	var total1 = 0.00;

	for ( var i = 0; i < sub.length; i++ ) {
		total1 += parseFloat( sub[ i ].value );
	}

	var subtotal_fac = total1.toFixed( 2 );
	var iva_fac1 = subtotal_fac * 0.16;
	var iva_fac = iva_fac1.toFixed( 2 );
	console.log( "iva_fac: " + iva_fac )
	$( "#iva" ).html( 'IVA: $' + iva_fac.replace( /./g, function ( c, i, a ) {return i && c !== "." && ( ( a.length - i ) % 3 === 0 ) ? ',' + c : c;} ) + '<br>' );

	var regimen = $( "#cliente_id" ).children( 'option:selected' ).data( 'regimenfiscal' );
	if ( regimen == '601' ) {
		var isr2 = subtotal_fac * 0.0125;
		var iva2 = subtotal_fac * 0.106667;
		var isr = isr2.toFixed( 2 );
		var iva = iva2.toFixed( 2 );
		var total_fac1 = ( iva_fac1 + total1 ) - ( isr2 + iva2 );
		var total_fac = total_fac1.toFixed( 2 );
		$( "#isrRetencion" ).html( 'Retención ISR: $' + isr.replace( /./g, function ( c, i, a ) {return i && c !== "." && ( ( a.length - i ) % 3 === 0 ) ? ',' + c : c;} ) + '<br>' );
		$( "#ivaRetencion" ).html( 'Retención IVA: $' + iva.replace( /./g, function ( c, i, a ) {return i && c !== "." && ( ( a.length - i ) % 3 === 0 ) ? ',' + c : c;} ) + '<br>' );
	} else {
		var total_fac1 = iva_fac1 + total1;
		var total_fac = total_fac1.toFixed( 2 );
		$( "#isrRetencion" ).val( "" );
		$( "#ivaRetencion" ).val( "" );
	}
	$( "#subtotal_fac_imp" ).html( subtotal_fac.replace( /./g, function ( c, i, a ) {return i && c !== "." && ( ( a.length - i ) % 3 === 0 ) ? ',' + c : c;} ) );
	$( "#subtotal_fac" ).val( subtotal_fac );
	$( "#iva_fac_imp" ).html( iva_fac.replace( /./g, function ( c, i, a ) {return i && c !== "." && ( ( a.length - i ) % 3 === 0 ) ? ',' + c : c;} ) );
	$( "#total_fac_imp" ).html( total_fac.replace( /./g, function ( c, i, a ) {return i && c !== "." && ( ( a.length - i ) % 3 === 0 ) ? ',' + c : c;} ) );
	evaluar();
}

function evaluar() {
	let folioFiscal = document.getElementById( "folioFiscal" ).value;
	if ( folioFiscal ) {
		document.getElementById( "btnGuardar" ).disabled = true;
	} else {
		let detalle = document.getElementsByClassName( "filas" ).length;
		if ( detalle > 0 ) {
			document.getElementById( "btnGuardar" ).disabled = false;
		} else {
			document.getElementById( "btnGuardar" ).disabled = true;
			cont = 0;
		}
	}
}

function eliminarDetalle( indice ) {
	$( "#fila" + indice ).remove();
	detalles = detalles - 1;
}

init();