let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
    showSlides(slideIndex += n);
}

function showSlides(n) {
    let i;
    const slides = document.getElementsByClassName("slide");

    if (n > slides.length) {
        slideIndex = 1;
    }

    if (n < 1) {
        slideIndex = slides.length;
    }

    for (i = 0; i < slides.length; i++) {
        slides[i].classList.remove("active");
    }

    slides[slideIndex - 1].classList.add("active");
}

// Cambiar automáticamente cada 5 segundos
setInterval(function() {
    plusSlides(1);
}, 5000);

$( "#loginLeap" ).on( 'submit', function ( e ) {
    e.preventDefault();
    let emaila = document.getElementById( "inputEmailAddress" ).value;
    let clavea = document.getElementById( "inputPassword" ).value;
    $.post( "../ajax/user.php?op=verificar", {"emaila": emaila, "clavea": clavea}, function ( data ) {
        var obj = jQuery.parseJSON( data );
        if ( obj ) {
            if ( obj.redireccion == "" ) {
                $( location ).attr( "href", "../pages/pedidos.php" );
            } else {
                $( location ).attr( "href", "../pages/" + obj.redireccion + ".php" );
			}
        } else {
            bootbox.alert( "Usuario y/o contraseña incorrectos" );
        }
    } );
} );

$( "#formRecuperaContrasena" ).on( 'submit', function ( e ) {
    e.preventDefault();
    let emailrec = document.getElementById( "emailRecuperacion" ).value;
    if(emailrec!=''){
        $.post( "../ajax/user.php?op=recuperar_contrasenna", {emailrec: emailrec}, function ( data ) {
            bootbox.alert(data);
            $("#modalRecuperaContrasena").modal("hide");
        });
    } else {
        $("#modalRecuperaContrasena").modal("hide");
    }
});

$( "#recuperaContrasenna" ).on( 'submit', function ( e ) {
    e.preventDefault();
    let emailrec = document.getElementById( "emailRecuperacion" ).value;
    let tokenrec = document.getElementById( "tokenRecuperacion" ).value;
    let claverec = document.getElementById( "contrasennaRecuperacion" ).value;
    if(emailrec!='' && tokenrec!=''){
        if((claverec.length < 8 || claverec=='')){
            bootbox.alert("La contraseña debe tener al menos 8 caracteres.");
        } else {
            $.post( "../ajax/auth.php?op=verificar", {emailrec: emailrec,tokenrec:tokenrec,claverec:claverec}, function ( data ) {
                bootbox.alert(data);
                setTimeout(function(){ window.location.replace("https://eos.leap.works"); }, 3000);
            });
        }
    } else {
        window.location.replace("https://eos.leap.works");
    }
});