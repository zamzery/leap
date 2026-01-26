var tabla;
var tabla;

//Función que se ejecuta al inicio
function init() {
	mostrarform( false );
	listar();

	$( "#formulario_enviaComplemento" ).on( "submit", function ( e ) {
		enviaEmailFactura( e );
	} );

	$( "#formulario" ).on( "submit", function ( e ) {
		guardaryeditar( e );
	} );

	listar_facturas();

	//Cargamos los items al select cliente
	$.post( "../ajax/cliente.php?op=select_cliente", function ( r ) {
		$( "#cliente_id" ).html( r );
		$( '#cliente_id' ).selectpicker( 'refresh' );
	} );

	$( "#cliente_id" ).on( "change", function () {
		var cliente_id = document.getElementById( "cliente_id" ).value;
		var nocuenta = $( this ).children( 'option:selected' ).data( 'nocuenta' );
		var banco = $( this ).children( 'option:selected' ).data( 'banco' );
		var formapago = $( this ).children( 'option:selected' ).data( 'formapago' );
		$( '#numCuenta' ).val( nocuenta );
		$( "#banco" ).val( banco );
		$( "#formadePago" ).val( formapago );
		$( '#formadePago' ).selectpicker( 'refresh' );
	} );

	$.post( "../ajax/metodopago.php?op=select_metodopago", function ( r ) {
		$( "#formadePago" ).html( r );
		$( "#formadePago" ).selectpicker( 'refresh' );
	} );

	$( '#fechaInicio' ).datetimepicker( {
		format: 'YYYY-MM-DD',
	} );

	$( "#fechaInicio" ).on( 'change.datetimepicker', function ( e ) {
		tabla.draw();
	} ).keyup( function () {
		tabla.draw();
	} );

	$( "#fechaFin" ).datetimepicker( {
		format: 'YYYY-MM-DD',
	} );

	$( "#fechaPago" ).datetimepicker( {
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

				var fechaFactura = new Date( data[ 2 ] );
				if ( min == null && max == null ) {return true;}
				if ( min == null && fechaFactura <= max ) {return true;}
				if ( max == null && fechaFactura >= min ) {return true;}
				if ( fechaFactura <= max && fechaFactura >= min ) {return true;}
				return false;
			}
		);
	} );

	$( '#tbllistado tfoot th' ).each( function ( i ) {
		if ( i == 4 || i == 6 ) {
		} else {
			var title = $( '#tbllistado thead th' ).eq( $( this ).index() ).text();
			$( this ).html( '<input style="width:100%" type="text" placeholder="' + title + '" />' );
		}
	} );
}

function limpiar() {
	$( "#complementoID" ).val( "" );
	$( "#banco" ).val( "" );
	$( "#numCuenta" ).val( "" );
	$( "#formadePago" ).val( "" );
	$( '#formadePago' ).selectpicker( 'refresh' );
	$( "#cliente_id" ).val( "" );
	$( '#cliente_id' ).selectpicker( 'refresh' );
	$( "#fechaPago" ).datetimepicker( 'clear' );
	$( "#statusPago" ).val( "" );

	detalles = 0;
	cont = 0;
	$( ".fila" ).remove();
	$( ".filas" ).remove();
	$( "#detalles > tbody" ).empty();
	$( "#detalles" ).html( '<thead class="bg-primary text-light"><th></th><th>Complemento</th><th>Fecha Complemento</th><th>Cliente</th><th>Parcialidad</th><th>Saldo Anterior</th><th>Este Pago</th><th>Saldo Restante</th><th></th></thead><tbody></tbody><tfoot><th></th><th></th><th></th><th></th><th></th><th></th><th>Total: $ <span id="totalPago" style="font-weight:400;">0.00</span><input step=".01" type="hidden" name="totalPago" id="totalPago"></th><th></th><th></th>' );
	document.getElementById( "btnGuardar" ).disabled = true;
}

//Función mostrar formulario
function mostrarform( flag ) {
	if ( flag ) {
		$( ".listadoregistros" ).hide();
		$( ".formularioregistros" ).show();
	} else {
		$( ".listadoregistros" ).show();
		$( ".formularioregistros" ).hide();
	}

}

//Función cancelarform
function cancelarform() {
	limpiar();
	mostrarform( false );
	//window.location.reload(); //Recarga la página
}

//Función Listar
function listar() {
	tabla = $( '#tbllistado' ).dataTable( {
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: "f<'row'<'col-sm-2'B><'col-sm-1'l><'col-sm-9'p>> rt <'bottom'ip<'clear'>>",//Definimos los elementos del control de tabla
		buttons: [
			{extend: 'excelHtml5', title: 'Listado de Complementos de Pago', exportOptions: {columns: [ 0, 1, 2, 3, 4, 5 ]}, className: 'btn btn-sm btn-primary'},
			{extend: 'pdf', title: 'Listado de Complementos de Pago', exportOptions: {columns: [ 0, 1, 2, 3, 4, 5 ]}, orientation: 'landscape', className: 'btn btn-sm btn-primary'},
		],
		"ajax": {
			url: '../ajax/complemento.php?op=listar',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"columnDefs": [
			{"width": "80px", "targets": [ 0, 4, 5 ]},
			{"type": "natural", "targets": 0},
			{"width": "120px", "targets": [ 2, 3 ]},
			{"width": "150px", "targets": [ 6 ]},
			{"className": "text-center", "targets": [ 0, 2, 5, 6 ]},
			{"className": "text-end", "targets": [ 3 ]},
		],
		"createdRow": function ( row, data, dataIndex ) {
			if ( data[ 5 ] == '<span class="label bg-red">Cancelada</span>' ) {
				$( row ).addClass( 'danger' );
			}
			else if ( data[ 5 ] == '<span class="label bg-green">Pagada</span>' ) {
				$( row ).addClass( 'success' );
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
		"Destroy": true,
		"iDisplayLength": 10,//Paginación
		"order": [ [ 0, "desc" ] ]//Ordenar (columna,orden)
	} ).DataTable();
}

function mostrar( complementoID ) {
	$.post( "../ajax/complemento.php?op=mostrar", {complementoID: complementoID}, function ( resp, statusOrden ) {
		data = JSON.parse( resp );
		mostrarform( true );
		$( "#complementoID" ).val( data.complementoID );
		$( "#folioFiscal" ).val( data.folioFiscal );
		$( "#banco" ).val( data.banco );
		$( "#numCuenta" ).val( data.numCuenta );
		$( "#formadePago" ).val( data.formadePago );
		$( '#formadePago' ).selectpicker( 'refresh' );
		$( "#comentarioAdicional" ).val( data.comentarioAdicional );
		$( "#cliente_id" ).val( data.cliente_id );
		$( "#cliente_id" ).selectpicker( 'refresh' );
		$( "#fechaPago" ).datetimepicker( 'date', moment( data.fechaPago ) );
		$( "#statusPago" ).val( data.statusPago );

		//Ocultar y mostrar los botones
		if ( data.pagoPDF ) {
			document.getElementById( "btnGuardar" ).disabled = true;
			document.getElementById( "btnAgregarArt" ).disabled = true;
		} else {
			document.getElementById( "btnGuardar" ).disabled = false;
			document.getElementById( "btnAgregarArt" ).disabled = false;
		}
	} );
	$.post( "../ajax/complemento.php?op=listarDetalle&comp=" + complementoID, function ( r ) {
		$( "#detalles" ).html( r );
	} );
}

function listar_facturas() {
	tabla_factura = $( '#tblFacturas' ).dataTable( {
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		dom: 'Bfrtip',//Definimos los elementos del control de tabla
		buttons: [

		],
		"ajax": {
			url: '../ajax/complemento.php?op=listar_facturas',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},

		"columnDefs": [
			{"type": "natural", "targets": 0},
			{"width": "50px", "targets": [ 0, 4, 6, 7 ]},
			{"width": "100px", "targets": [ 1, 3, 5 ]},
			{"searchable": false, "targets": 7},
			{"className": "text-center", "targets": [ 0, 1, 4, 6, 7 ]},
			{"className": "text-end", "targets": [ 3, 5 ]},
		],
		"bDestroy": true,
		"iDisplayLength": 10,//Paginación
		"order": [ 0, "desc" ]//Ordenar (columna,orden)
	} ).DataTable();
	$( "#tblFacturas" ).css( "width", "100%" );
}

function muestraModalFacturas() {
	$( "#modalFacturas" ).modal( 'show' );
}

function guardaryeditar( e ) {
	e.preventDefault(); //No se activará la acción predeterminada del evento
	var formData = new FormData( $( "#formulario" )[ 0 ] );

	$.ajax( {
		url: "../ajax/complemento.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function ( datos ) {
			mostrarform( false );
			bootbox.alert( datos );
			tabla.clear().draw();
			tabla.ajax.reload();
		},
		error: function ( datos ) {
			bootbox.alert( datos );
		}
	} );
	limpiar();
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

function timbra( complementoID ) {
	var dialog = bootbox.dialog( {
		message: '<p class="text-center"><h5><i class="fa fa-cog fa-spin fa-3x fa-fw" width="10px" font-size="2"></i> Por favor espera mientras se timbra el Pago...</h5></p>',
		closeButton: false
	} );
	bootbox.confirm( "¿Quieres Timbrar el Pago?, <span style='color:red;font-weight:bold;'>¡Esta acción no se puede deshacer!</span>", function ( result ) {
		if ( result ) {
			$.post( "../ajax/complemento.php?op=timbra", {complementoID: complementoID}, function ( data, status ) {
				bootbox.alert( data );
				tabla.clear().draw();
				tabla.ajax.reload();
				dialog.modal( 'hide' );
			} );
		} else {dialog.modal( 'hide' );}
	} );
}

function retimbrar( complementoID ) {
	bootbox.confirm( "¿Quieres de Reiniciar el Timbrado del Pago?, <br><span style='color:red;font-weight:bold;'>¡Esta acción no elimina una factura correctamente timbrada en el SAT, pero reescribe el folio interno!</span>", function ( result ) {
		if ( result ) {
			$.post( "../ajax/complemento.php?op=retimbrar", {complementoID: complementoID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		} else {dialog.modal( 'hide' );}
	} );
}

function modalCancelaComplemento( complementoID, cliente_id ) {
	$( '#complementoID_cancela' ).val( complementoID );
	$.post( "../ajax/complemento.php?op=obtener_complementos_cliente", {cliente_id: cliente_id}, function ( e ) {
		$( "#folioSustitucion" ).html( e );
		$( "#folioSustitucion" ).selectpicker( 'refresh' );
	} );
	$( "#cancelaComplemento" ).modal( 'show' );
	$( '#cancelaComplemento' ).on( 'hidden.bs.modal', function () {
		$( '#complementoID_cancela' ).val( '' );
		document.getElementById( "motivo01" ).checked = true;
		document.getElementById( "folioSustitucion" ).disabled = false;
		$( "#folioSustitucion" ).val( "" );
		$( "#folioSustitucion" ).selectpicker( 'refresh' );
	} );
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

//Función para Cancelar el Pago
function cancelar() {
	let complementoID_cancela = document.getElementById( "complementoID_cancela" ).value;
	let folioSustitucion = document.getElementById( "folioSustitucion" ).value;
	var complementoIDRelacionado = $( "#folioSustitucion" ).children( 'option:selected' ).data( 'folio' );
	let motivo = $( "input[type=radio][name=motivo]:checked" ).val();

	if ( ( motivo == '01' || motivo == '04' ) && folioSustitucion == '' ) {
		bootbox.alert( "Selecciona una <strong>Complemento Relacionado</strong>" );
	} else {
		var dialog = bootbox.dialog( {
			message: '<p class="text-center"><h4><i class="fa fa-cog fa-spin fa-fw"></i> Por favor espera mientras se cancela el complemento...</h4></p>',
			closeButton: false
		} );
		$.post( "../ajax/complemento.php?op=cancelar", {complementoID: complementoID_cancela, complementoIDRelacionado: complementoIDRelacionado, folioSustitucion: folioSustitucion, motivo: motivo}, function ( e ) {
			bootbox.alert( e );
			dialog.modal( 'hide' );
			tabla.clear().draw();
			tabla.ajax.reload();
		} );
		setTimeout( function () {
			dialog.modal( 'hide' );
		}, 10000 );
	}
}

var detalles = 0;
var cont = 0;
function agregarDetalle( factura, fecha, saldoAnterior, total, saldoRestante, folioFiscal, cliente_id, nombreCliente, parcialidad, moneda ) {
	console.log( cliente_id );
	$( "#cliente_id" ).val( cliente_id );
	$( '#cliente_id' ).selectpicker( 'refresh' );
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
		var detalles = parseInt( numberprod );
	}
	if ( !cont ) {var cont = 0;}
	function formatDate( fecha ) {
		var nombresMes = [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ];
		var dia = fecha.getDate();
		var mesIndex = fecha.getMonth();
		var anno = fecha.getFullYear();
		return dia + '/' + nombresMes[ mesIndex ] + '/' + anno;
	}
	var parcialidad = +parcialidad + 1;
	if ( saldoRestante != 0 ) {var saldoAnterior = saldoRestante; var total = saldoRestante;} else {var saldoAnterior = total;}
	if ( factura != "" ) {
		var fila = '<tr class="filas" id="fila' + cont + '">' +

			//Botón Elimina Factura
			'<td style="text-align:center;"><button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(' + cont + ')"><i class="fa fa-close"></i></button><input type="hidden" name="contador[]" value="' + cont + '"></td>' +

			//Factura
			'<td><input type="hidden" name="folioFiscalFac[]" id="folioFiscalFac' + cont + '" value="' + folioFiscal + '"> <input type="hidden" name="factura[]" id="factura' + cont + '" value="' + factura + '">' + factura + '-A</td>' +

			//Fecha de la Factura
			'<td><input type="hidden" name="fechaFactura[]" id="fechaFactura' + cont + '" value="' + fecha + '">' + formatDate( new Date( fecha ) ) + '</td>' +

			//Cliente
			'<td>' + nombreCliente + '</td>' +

			//Parcialidad
			'<td style="width:100px;"><input class="form-control" type="number" maxlength="2" name="parcialidad[]" id="parcialidad' + cont + '" value="' + parcialidad + '" style="width:100px;"></td>' +

			//Saldo Anterior
			'<td style="text-align:right;"><small>(' + moneda + ')</small> $<span id="saldoAnterior' + cont + '">' + saldoAnterior + '</span> <input type="hidden" name="saldoAnterior[]" id="saldoAnteriorInput' + cont + '" value="' + saldoAnterior + '"></td>' +

			//Este Pago
			'<td style="width:220px;"><div class="form-group"><div class="input-group date" id="fechaPago" data-target-input="nearest"><div class="input-group-text">$</div><input type="number" class="form-control" lang="en-US" step=".01" min="0" name="estePago[]" id="estePago' + cont + '" value="' + total + '" onkeyup="modificarSubtotales()"/></div></div> <input type="hidden" name="estePagoOriginal[]" id="estePagoOriginal' + cont + '" value="' + total + '"></td>' +

			//Saldo Restante
			'<td style="text-align:right;">$ <span id="saldoRestante2' + cont + '">' + saldoRestante + '</span> <input type="hidden" name="saldoRestante[]" id="saldoRestante' + cont + '" value="' + saldoRestante + '"></td>' +
			'</tr>';
		cont++;
		detalles = detalles + 1;
		$( '#detalles' ).append( fila );
		modificarSubtotales();
		document.getElementById( "factura" + factura + '-A' ).disabled = true;
	} else {
		alert( "Error al ingresar el detalle, revisar los datos del artículo" );
	}
}

const no_cuenta = () => {
	let cuenta = document.getElementById( "numCuenta" );
	//colocar ceros a la izquierda hasta completar 10 digitos
	while ( cuenta.value.length < 10 ) {
		cuenta.value = '0' + cuenta.value;
	}
}

const no_tarjeta = () => {
	let tarjeta = document.getElementById( "numCuenta" );
	//colocar ceros a la izquierda hasta completar 16 digitos
	while ( tarjeta.value.length < 16 ) {
		tarjeta.value = '0' + tarjeta.value;
	}
}

const no_clabe = () => {
	//'000000000000000000'
	let clabe = document.getElementById( "numCuenta" );
	//colocar ceros a la izquierda hasta completar 18 digitos
	while ( clabe.value.length < 18 ) {
		clabe.value = '0' + clabe.value;
	}
}

function modificarSubtotales() {
	var saldoanterior = document.getElementsByName( "saldoAnterior[]" );
	var estepago = document.getElementsByName( "estePago[]" );
	var contador = document.getElementsByName( "contador[]" );
	var estepagooriginal = document.getElementsByName( "estePagoOriginal[]" );

	for ( var i = 0; i < estepago.length; i++ ) {
		var saldoanterior1 = saldoanterior[ i ].value;
		var estepago1 = estepago[ i ].value;
		var estepagooriginal1 = estepagooriginal[ i ].value;
		var contador1 = contador[ i ].value;
		let sumatotal = parseFloat( saldoanterior1 ) - parseFloat( estepago1 );
		sumatotal = sumatotal.toFixed( 2 );
		let sumatotal2 = sumatotal.replace( /./g, function ( c, i, a ) {return i && c !== "." && ( ( a.length - i ) % 3 === 0 ) ? ',' + c : c;} );
		document.getElementById( "saldoRestante2" + contador1 ).innerHTML = sumatotal2;
		document.getElementById( "saldoRestante" + contador1 ).value = sumatotal;
		calcularTotales();
	}
}

function espera_factura( facturaID ) {
	bootbox.confirm( "¿Quieres poner En Espera el La Factura?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/factura_iconograma.php?op=espera_factura", {facturaID: facturaID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function calcularTotales() {
	var salTot = document.getElementsByName( "estePago[]" );
	var contador = document.getElementsByName( "contador[]" );
	var total1 = 0.00;
	for ( var i = 0; i < salTot.length; i++ ) {
		var contador1 = contador[ i ].value;
		var totales = document.getElementById( "estePago" + contador1 ).value;
		total1 += parseFloat( totales );
	}
	var total1 = total1.toFixed( 2 );
	$( "#totalPago" ).html( total1.replace( /./g, function ( c, i, a ) {return i && c !== "." && ( ( a.length - i ) % 3 === 0 ) ? ',' + c : c;} ) );
	document.getElementById( "totalPago" ).value = total1;
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
	calcularTotales();
	detalles = detalles - 1;
	evaluar();
}

function enviaEmailFactura( e ) {
	var dialog = bootbox.dialog( {
		message: '<p class="text-center">Espera mientras se envía la factura por correo</p>',
		closeButton: false
	} );
	e.preventDefault(); //No se activará la acción predeterminada del evento
	var formData = new FormData( $( "#formulario_enviaComplemento" )[ 0 ] );

	$.ajax( {
		url: "../ajax/complemento.php?op=enviaEmailFactura",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function ( datos ) {
			dialog.modal( 'hide' );
			bootbox.alert( datos );
			marcarFacturaEnviada();
		},
		error: function ( datos ) {
			bootbox.alert( datos );
			dialog.modal( 'hide' );
		}
	} );
}

function marcarFacturaEnviada() {
	var numeroPago = document.getElementById( "numeroPago2" ).value;
	$.post( "../ajax/complemento.php?op=marcarFacturaEnviada", {complementoID: numeroPago}, function ( results ) {

	} );
}

function mostrarEmails( complementoID ) {
	$.post( "../ajax/complemento.php?op=mostrarEmails", {complementoID: complementoID}, function ( r ) {
		$( "#emailsEnvio" ).html( r );
	} );

	$.post( "../ajax/complemento.php?op=mostrarRuta", {complementoID: complementoID}, function ( data ) {
		var data = data.split( ',' );
		$( "#nombreXML" ).val( data[ 0 ] );
		$( "#nombrePDF" ).val( data[ 1 ] );
		$( "#numeroPagoTxt" ).html( data[ 2 ] );
		$( "#numeroPago" ).val( data[ 2 ] );
		$( "#numeroPago2" ).val( data[ 3 ] );
		$( "#hotel2" ).val( data[ 4 ] );
		$( "#vendedor2" ).val( data[ 5 ] );
	} );
}

init();