var tablaFacturas;
var tablaRecibidos;

function init() {
	mostrarform( false );
	cargarResumenFinanciero();
	listarFacturas();
	listarPagos( '#tblRecibidos' );
}

function formatoMoneda( valor ) {
	var numero = parseFloat( valor || 0 );
	return numero.toFixed( 2 ).replace( /\B(?=(\d{3})+(?!\d))/g, "," );
}

function cargarResumenFinanciero() {
	$.post( "../ajax/escritorio.php?op=resumen_financiero", function ( data ) {
		data = JSON.parse( data );
		$( "#numeroVentas" ).html( formatoMoneda( data.ventas ) );
		$( "#numeroFacturas" ).html( formatoMoneda( data.facturas ) );
		$( "#numeroRegistrados" ).html( formatoMoneda( data.pagos ) );
		$( "#balance" ).html( "Pendiente" );
	} );
}

function configurarBuscadores( selectorTabla ) {
	$( selectorTabla + ' tfoot th' ).each( function () {
		var title = $( selectorTabla + ' thead th' ).eq( $( this ).index() ).text();
		$( this ).html( '<input style="width:100%" type="text" placeholder="' + title + '" />' );
	} );
}

function aplicarBuscadores( tabla ) {
	tabla.api().columns().every( function () {
		var that = this;
		$( 'input', this.footer() ).on( 'keyup change clear', function () {
			if ( that.search() !== this.value ) {
				that.search( this.value ).draw();
			}
		} );
	} );
}

function listarFacturas() {
	configurarBuscadores( '#tblFacturas' );
	tablaFacturas = $( '#tblFacturas' ).DataTable( {
		"aProcessing": true,
		"aServerSide": true,
		dom: "f<'row'<'col-sm-2'l><'col-sm-2'B><'col-sm-8'p>>rt<'bottom'ip<'clear'>>",
		buttons: [
			{extend: 'excelHtml5', title: 'Listado de Facturas', exportOptions: {columns: [ 0, 1, 2, 3, 4, 5, 7 ]}, className: 'btn btn-sm btn-primary'},
			{extend: 'pdf', title: 'Listado de Facturas', exportOptions: {columns: [ 0, 1, 2, 3, 4, 5, 7 ]}, orientation: 'landscape', className: 'btn btn-sm btn-primary'},
		],
		"ajax": {
			url: '../ajax/escritorio.php?op=listar_facturas',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"columnDefs": [
			{"width": "90px", "targets": [ 0, 1, 6, 7 ]},
			{"className": "text-center", "targets": [ 0, 1, 6, 7 ]},
			{"className": "text-end", "targets": [ 3, 4, 5 ]},
		],
		"createdRow": function ( row, data ) {
			if ( data[ 7 ] == '<span class="badge bg-danger">Cancelada</span>' ) {
				$( row ).addClass( 'table-danger' );
			}
		},
		"initComplete": function () {
			aplicarBuscadores( this );
		},
		"bDestroy": true,
		"iDisplayLength": 25,
		"order": [ [ 0, "desc" ] ]
	} );
}

function listarPagos( selectorTabla ) {
	configurarBuscadores( selectorTabla );
	tablaRecibidos = $( selectorTabla ).DataTable( {
		"aProcessing": true,
		"aServerSide": true,
		dom: "f<'row'<'col-sm-2'l><'col-sm-2'B><'col-sm-8'p>>rt<'bottom'ip<'clear'>>",
		buttons: [
			{extend: 'excelHtml5', title: 'Listado de Complementos de Pago', exportOptions: {columns: [ 0, 1, 2, 3, 4, 6 ]}, className: 'btn btn-sm btn-primary'},
			{extend: 'pdf', title: 'Listado de Complementos de Pago', exportOptions: {columns: [ 0, 1, 2, 3, 4, 6 ]}, orientation: 'landscape', className: 'btn btn-sm btn-primary'},
		],
		"ajax": {
			url: '../ajax/escritorio.php?op=listar_pagos',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"columnDefs": [
			{"width": "75px", "targets": [ 0, 1, 5, 6 ]},
			{"className": "text-center", "targets": [ 0, 1, 5, 6 ]},
			{"className": "text-end", "targets": [ 4 ]},
		],
		"initComplete": function () {
			aplicarBuscadores( this );
		},
		"bDestroy": true,
		"iDisplayLength": 25,
		"order": [ [ 0, "desc" ] ]
	} );
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

function cancelarformAlumno() {
	mostrarform( false );
}

init();
