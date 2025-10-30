var tabla;
var tabla_permiso;
var flagPermiso = false;
//función que se ejecuta al inicio
function init() {
	mostrarform( false );
	listar();

	listar_permiso( 0 );

	$( "#formulario" ).on( "submit", function ( e ) {
		guardaryeditar( e );
	} );

	$( "#avatarmuestra" ).hide();

	$.post( "../ajax/cargo.php?op=select_cargo", function ( r ) {
		$("#cargo_id").html(r);
		$("#cargo_id").selectpicker('refresh');
	} );

	$("#cargo_id").on('change', function(){
		$( "#tblPermiso > tbody" ).empty();
		$( "#tblPermiso" ).html( '<thead style="background-color:#A9D0F5"><th></th><th></th><th>Nombre Permiso</th><th></th></thead><tbody> </tbody><tfoot> <th></th><th></th><th>Nombre Permiso</th><th></th></tfoot>' );
		var cargoID = $(this).val();
		listar_permiso_cargo( cargoID );
	});
}

function cambiaColor(color){
	if(color.id == "muestracolor"){
		document.getElementById("color").value = color.value;
	} else {
		document.getElementById("colorText").value = color.value;
	}
}

function maestroCheck() {
	if ( document.getElementById( 'maestro_check' ).checked ) {
		document.getElementById( "maestro" ).value = 1;
	} else {
		document.getElementById( "maestro" ).value = 0;
	}
}

//Función limpiar
function limpiar() {
	$( "#usuarioID" ).val( "" );
	$( "#nombre" ).val( "" );
	$( "#email" ).val( "" );
	$( "#clave" ).val( "" );
	$( "#cargo_id" ).val( "" );
	$( "#cargo_id" ).selectpicker('refresh');
	$( "#telefono" ).val( "" );
	$( "#direccion" ).val( "" );
	$( "#color" ).val( "" );
	$( "#colorText" ).val( "" );
	$( "#muestracolor" ).val( "" );
	$( "#muestracolorText" ).val( "" );
	$( "#avatar" ).val( "" );
	$( "#avatarmuestra" ).attr( "src", "" );
	$( "#avatarmuestra" ).hide();
	$( "#avataractual" ).val( "" );
	$( "#redireccion" ).val( "" );
	$( "#redireccion" ).selectpicker( 'refresh' );
	$( "input[name ='permiso[]']" ).attr( "checked", false );

	$( "#maestro" ).val( "0" );
	$( "#maestro_check" ).attr( "checked", false );

	document.getElementById( "formulario" ).reset();

	// $( '#email' ).prop( 'readonly', false );
	// document.getElementById( "email" ).style.backgroundColor = "#fff";

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
			{extend: 'excelHtml5', title: 'Listado de Usuarios', exportOptions: {columns: [ 0, 1, 2, 3, 4, 6 ]}, className: 'btn btn-sm btn-primary'},
			{extend: 'pdf', title: 'Listado de Usuarios', exportOptions: {columns: [ 0, 1, 2, 3, 4, 6 ]}, className: 'btn btn-sm btn-primary'},
		],
		
		"ajax": {
			url: '../ajax/user.php?op=listar',
			type: "get",
			dataType: "json",
			error: function ( e ) {
				console.log( e.responseText );
			}
		},
		"createdRow": function ( row, data, dataIndex ) {
			if ( data[ 7 ] == '<span class="badge bg-danger">Desactivado</span>' ) {
				$( row ).addClass( 'table-danger' );
			}
		},
		"columnDefs": [
			{"width": "55px", "targets": [ 4, 5, 6 ]},
			{"width": "80px", "targets": [ 1, 7, 8 ]},
			{"width": "120px", "targets": [ 1, 2, 3 ]},
			{"className": "text-center", "targets": [ 1, 2, 3, 4, 5, 6, 7, 8 ]},
		],
		"Destroy": true,
		"iDisplayLength": 10, //Número de registros para paginar
		"order": [ [ 7, "asc" ], [ 0, "asc" ] ] //Ordenar (columna, orden ascendente o descendente, etc)
	} ).DataTable();
}

function guardaryeditar( e ) {
	e.preventDefault(); //Para que no se active la acción predeterminada del evento
	$( "#btnGuardar" ).prop( "disabled", true );
	var formData = new FormData( $( "#formulario" )[ 0 ] );

	$.ajax( {
		url: "../ajax/user.php?op=guardaryeditar",
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

function desactivar( usuarioID ) {
	bootbox.confirm( "¿Quieres desactivar el Usuario?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/user.php?op=desactivar", {usuarioID: usuarioID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function activar( usuarioID ) {
	bootbox.confirm( "¿Quieres activar el Usuario?", function ( result ) {
		if ( result ) {
			$.post( "../ajax/user.php?op=activar", {usuarioID: usuarioID}, function ( e ) {
				bootbox.alert( e );
				tabla.clear().draw();
				tabla.ajax.reload();
			} );
		}
	} );
}

function mostrar( usuarioID ) {
	$.post( "../ajax/user.php?op=mostrar", {usuarioID: usuarioID}, function ( data, statusUsuario ) {
		data = JSON.parse( data );
		mostrarform( true );
		$( "#usuarioID" ).val( data.id );
		$( "#nombre" ).val( data.nombre );
		$( "#telefono" ).val( data.telefono );
		$( "#email" ).val( data.email );
		$( "#direccion" ).val( data.direccion );
		$( "#cargo_id" ).val( data.cargo_id );
		$( "#cargo_id" ).selectpicker( 'refresh' );
		$( "#clave" ).val( "" );
		$( "#color" ).val( data.color );
		$( "#muestracolor" ).val( data.color );
		$( "#colorText" ).val( data.colorText );
		$( "#muestracolorText" ).val( data.colorText );
		$( "#redireccion" ).val( data.redireccion );
		$( "#redireccion" ).selectpicker( 'refresh' );

		$( "#maestro" ).val( data.maestro );
		if ( data.maestro == 1 ) {
			$( "#maestro_check" ).attr( "checked", true );
		} else {
			$( "#maestro_check" ).attr( "checked", false );
		}

		if ( data.avatar ) {
			$( "#avatarmuestra" ).show();
			$( "#avatarmuestra" ).attr( "src", "../public/files/avatars/" + data.avatar );
			$( "#avataractual" ).val( data.avatar );
		}
	} );

	listar_permiso( usuarioID );

	// $( '#email' ).prop( 'readonly', true );
	// document.getElementById( "email" ).style.backgroundColor = "#dedede";
}

//Función listar
function listar_permiso( usuarioID ) {
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
			url: '../ajax/user.php?op=listar_permiso&id=' + usuarioID,
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

function listar_permiso_cargo( cargoID ) {
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