//función que se ejecuta al inicio
function init() {
	$( "#formulario" ).on( "submit", function ( e ) {
		guardaryeditar( e );
	} );

	$( "#avatarmuestra" ).hide();

	let usuarioID = document.getElementById( "usuarioID" ).value;
	mostrar( usuarioID );
}

function cambiaColor(color){
	if(color.id == "muestracolor"){
		document.getElementById("color").value = color.value;
	} else {
		document.getElementById("colorText").value = color.value;
	}
}

function guardaryeditar( e ) {
	e.preventDefault(); //Para que no se active la acción predeterminada del evento
	$( "#btnGuardar" ).prop( "disabled", true );
	var formData = new FormData( $( "#formulario" )[ 0 ] );

	$.ajax( {
		url: "../ajax/user.php?op=editar_sinpermisos",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function ( datos ) {
			bootbox.alert( datos );
			var usuarioID = document.getElementById( "usuarioID" ).value;
			$( "#btnGuardar" ).prop( "disabled", false );
			$( "#avatar" ).val( "" );
			mostrar( usuarioID );
		}
	} );
}


function mostrar( usuarioID ) {
	$.post( "../ajax/user.php?op=mostrar", {usuarioID: usuarioID}, function ( data, statusUsuario ) {
		data = JSON.parse( data );
		$( "#usuarioID" ).val( data.id );
		$( "#nombre" ).val( data.nombre );
		$( "#telefono" ).val( data.telefono );
		$( "#email" ).val( data.email );
		$( "#direccion" ).val( data.direccion );
		$( "#clave" ).val( "" );
		$( "#color" ).val( data.color );
		$( "#muestracolor" ).val( data.color );
		$( "#colorText" ).val( data.colorText );
		$( "#muestracolorText" ).val( data.colorText );

		if ( data.avatar ) {
			$( "#avatarmuestra" ).show();
			$( "#avatarmuestra" ).attr( "src", "../public/files/avatars/" + data.avatar );
			$( "#avataractual" ).val( data.avatar );
		}
	} );

	$( '#email' ).prop( 'readonly', true );
	document.getElementById( "email" ).style.backgroundColor = "#dedede";
}

init();