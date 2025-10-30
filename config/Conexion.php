<?php 
require_once "global.php";

$conexion = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);
date_default_timezone_set('America/Mexico_City');
mysqli_query( $conexion, 'SET NAMES "'.DB_ENCODE.'"');
setlocale(LC_ALL,"es_MX");

//Si tenemos un posible error en la conexión lo mostramos
if (mysqli_connect_errno()){
	printf("Falló conexión a la base de datos: %s\n",mysqli_connect_error());
	exit();
}

if (!function_exists('ejecutarConsulta')){
	function ejecutarConsulta($sql){
		global $conexion;
		$query = $conexion->query($sql);
		return $query;
	}

	function ejecutarConsultaSimpleFila($sql){
		global $conexion;
		$query = $conexion->query($sql);		
		$row = $query->fetch_assoc();
		return $row;
	}

	function ejecutarConsultaArray($sql){
		global $conexion;
		$query = $conexion->query($sql);		
		$row = $query->fetch_array(MYSQLI_ASSOC);
		return $row;
	}

	function ejecutarConsulta_retornarID($sql){
		global $conexion;
		$query = $conexion->query($sql);		
		return $conexion->insert_id;			
	}

	function limpiarCadena($str){
		global $conexion;
		$str = mysqli_real_escape_string($conexion,trim($str));
		return htmlspecialchars($str); //return htmlspecialchars($str);
	}

	function limpiarSTR($string) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}

	function fechaEspanol($fecha){
		$date = date_create($fecha);
		$dia=date_format($date,"d");
		$mes=date_format($date,"F");
		if ($mes=="January") $mes="Ene"; if ($mes=="February") $mes="Feb"; if ($mes=="March") $mes="Mar"; if ($mes=="April") $mes="Abr"; if ($mes=="May") $mes="May"; if ($mes=="June") $mes="Jun"; if ($mes=="July") $mes="Jul"; if ($mes=="August") $mes="Ago"; if ($mes=="September") $mes="Sep"; if ($mes=="October") $mes="Oct"; if ($mes=="November") $mes="Nov"; if ($mes=="December") $mes="Dic";
		$ano=date_format($date,"Y");
		$fecha = "$dia/$mes/$ano";
		return $fecha;
	}

	function regresaMes($fecha){
		$date = date_create($fecha);
		$mes=date_format($date,"F");
		if ($mes=="January") $mes="Enero"; if ($mes=="February") $mes="Febrero"; if ($mes=="March") $mes="Marzo"; if ($mes=="April") $mes="Abril"; if ($mes=="May") $mes="Mayo"; if ($mes=="June") $mes="Junio"; if ($mes=="July") $mes="Julio"; if ($mes=="August") $mes="Agosto"; if ($mes=="September") $mes="Septiembre"; if ($mes=="October") $mes="Octubre"; if ($mes=="November") $mes="Noviembre"; if ($mes=="December") $mes="Diciembre";
		return $mes;
	}

	function fechaCompletaEspanol($fecha){
		$date = date_create($fecha);
		$dia=date_format($date,"d");
		$mes=date_format($date,"F");
		if ($mes=="January") $mes="Enero"; if ($mes=="February") $mes="Febrero"; if ($mes=="March") $mes="Marzo"; if ($mes=="April") $mes="Abril"; if ($mes=="May") $mes="Mayo"; if ($mes=="June") $mes="Junio"; if ($mes=="July") $mes="Julio"; if ($mes=="August") $mes="Agosto"; if ($mes=="September") $mes="Septiembre"; if ($mes=="October") $mes="Octubre"; if ($mes=="November") $mes="Noviembre"; if ($mes=="December") $mes="Diciembre";
		$ano=date_format($date,"Y");
		$fecha = "$dia de $mes de $ano";
		return $fecha;
	}

	function fechaDiasEspanol($fecha){
		$date = date_create($fecha);
		$dia=date_format($date,"d");
		$dia2=date_format($date,"l");
		$mes=date_format($date,"F");
		if ($mes=="January") $mes="Enero"; if ($mes=="February") $mes="Febrero"; if ($mes=="March") $mes="Marzo"; if ($mes=="April") $mes="Abril"; if ($mes=="May") $mes="Mayo"; if ($mes=="June") $mes="Junio"; if ($mes=="July") $mes="Julio"; if ($mes=="August") $mes="Agosto"; if ($mes=="September") $mes="Septiembre"; if ($mes=="October") $mes="Octubre"; if ($mes=="November") $mes="Noviembre"; if ($mes=="December") $mes="Diciembre";
		$ano=date_format($date,"Y");
		if ($dia2=="Monday") $dia2="Lunes"; if ($dia2=="Tuesday") $dia2="Martes"; if ($dia2=="Wednesday") $dia2="Miércoles"; if ($dia2=="Thursday") $dia2="Jueves"; if ($dia2=="Friday") $dia2="Viernes"; if ($dia2=="Saturday") $dia2="Sabado"; if ($dia2=="Sunday") $dia2="Domingo";
		$fecha = "$dia2, $dia de $mes de $ano";
		return $fecha;
	}

	function abreviar($str, $limit=30, $strip = false) {
        $str = ($strip == true)?strip_tags($str):$str;
        if (strlen ($str) > $limit) {
            $str = substr ($str, 0, $limit - 3);
            return (substr ($str, 0, strrpos ($str, ' ')).'');
        }
        return trim($str);
    }

    function abreviar25($str, $limit=25, $strip = false) {
        $str = ($strip == true)?strip_tags($str):$str;
        if (strlen ($str) > $limit) {
            $str = substr ($str, 0, $limit - 3);
            return (substr ($str, 0, strrpos ($str, ' ')).'');
        }
        return trim($str);
    }

	function decodificar_utf8($string) {
		return mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
	}

	function elimina_acentos($string){
		$string = trim($string);
		$string = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$string
		);
	
		$string = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$string
		);
	
		$string = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$string
		);
	
		$string = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$string
		);
	
		$string = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$string
		);
	
		$string = str_replace(
			array('ñ', 'Ñ', 'ç', 'Ç'),
			array('n', 'N', 'c', 'C',),
			$string
		);
	
		$string = str_replace(
			array('&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;', '&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&amp;', '&'),
			array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', '', ''),
			$string
		);
		return $string;
	}

	function NumLet($Cant) {
		$Cad1=0;
		$Resto=0;
		$NumLet = "";
		$Cad1 = number_format($Cant,2);
		$Resto = substr($Cad1,strlen($Cad1)-2,2); //Obtiene decimales.
		$Cad1 = substr($Cad1,0,strlen($Cad1)-3); //Extrae la cantidad sin decimales.
	
		If (strlen($Cad1) < 3){ // Del 1 al 99
			$Cad2 = Del1al99($Cad1);
			$NumLet = $Cad2;
		}
	
		If (strlen($Cad1) == 3){ // Del 100 al 999
			$Cad3 ="";
			if ($Cad1==100){
					$Cad3 = "CIEN";
			}else{
					$Cad3=Cientos(substr($Cad1,0,1));
					$Cad3=$Cad3." ".Del1al99(substr($Cad1,1,2));
			}
			$NumLet = $Cad3;
		}
	
		If (strlen($Cad1) == 5 or strlen($Cad1) == 6){ // Del 1000 al 99999
			if (strlen($Cad1)==5){
					$Cad5=Miles(substr($Cad1,0,1));
					$Cad6=substr($Cad1,2,3);
			}
	
			if (strlen($Cad1)==6){
					$Cad5=Miles(substr($Cad1,0,2));
					$Cad6=substr($Cad1,3,3);
			}
	
				If (strlen($Cad6) < 3){ // Del 1 al 99
					$Cad7 = Del1al99($Cad6);
				}
	
	
				If (strlen($Cad6) == 3){ // Del 100 al 999
					$Cad7 ="";
					if ($Cad6==100){
						$Cad7 = "CIEN";
					} else {
						$Cad7=Cientos(substr($Cad6,0,1));
						$Cad7=$Cad7." ".Del1al99(substr($Cad6,1,2));
					}
				}
				$NumLet = $Cad5." ".$Cad7;
		}
	
		If (strlen($Cad1) == 7){
			$CadA = substr($Cad1,0,3);
			$Cad3 ="";
			if($CadA==100){
					$Cad3 = "CIEN";
			} else {
					$Cad3=Cientos(substr($CadA,0,1));
					$Cad3=$Cad3." ".Del1al99(substr($CadA,1,2));
			}
			$Cad3 = $Cad3." MIL ";
			$CadB = substr($Cad1,4,3);
			if($CadB==100){
					$Cad4 = "CIEN";
			} else {
					$Cad4=Cientos(substr($CadB,0,1));
					$Cad4=$Cad4." ".Del1al99(substr($CadB,1,2));
			}
			$NumLet = $Cad3.$Cad4;
		}
		$NumLet = "(".$NumLet." PESOS ".$Resto."/100 M.N.)";
		return $NumLet;
	}
	
	function Folio5($Num){
		if ($Num < 10){
					$Folio = "0000".$Num;
		}
		if ($Num > 9 and $Num < 100){
					$Folio = "000".$Num;
		}
		if ($Num > 99 and $Num < 1000){
					$Folio = "00".$Num;
		}
		if ($Num > 999 and $Num < 10000){
					$Folio = "0".$Num;
		}
		if ($Num > 9999 and $Num < 100000){
					$Folio = $Num;
		}
		if ($Num > 99999){
					$Folio = "N/A";
		}
		return $Folio;
	}
	
	function Del1al99($Valor)  {
		$Del1al99 = "";
		If ($Valor == 1){$Del1al99 = "UN";}
		If ($Valor == 2) {$Del1al99 = "DOS";}
		If ($Valor == 3) {$Del1al99 = "TRES";}
		If ($Valor == 4) {$Del1al99 = "CUATRO";}
		If ($Valor == 5) {$Del1al99 = "CINCO";}
		If ($Valor == 6) {$Del1al99 = "SEIS";}
		If ($Valor == 7) {$Del1al99 = "SIETE";}
		If ($Valor == 8) {$Del1al99 = "OCHO";}
		If ($Valor == 9) {$Del1al99 = "NUEVE";}
		If ($Valor == 10) {$Del1al99 = "DIEZ";}
		If ($Valor == 11) {$Del1al99 = "ONCE";}
		If ($Valor == 12) {$Del1al99 = "DOCE";}
		If ($Valor == 13) {$Del1al99 = "TRECE";}
		If ($Valor == 14) {$Del1al99 = "CATORCE";}
		If ($Valor == 15) {$Del1al99 = "QUINCE";}
		If ($Valor == 16) {$Del1al99 = "DIEZ Y SEIS";}
		If ($Valor == 17) {$Del1al99 = "DIEZ Y SIETE";}
		If ($Valor == 18) {$Del1al99 = "DIEZ Y OCHO";}
		If ($Valor == 19) {$Del1al99 = "DIEZ Y NUEVE";}
		If ($Valor == 20) {$Del1al99 = "VEINTE";}
		If ($Valor == 21) {$Del1al99 = "VEINTIUNO";}
		If ($Valor == 22) {$Del1al99 = "VEINTIDOS";}
		If ($Valor == 23) {$Del1al99 = "VEINTITRES";}
		If ($Valor == 24) {$Del1al99 = "VEINTICUATRO";}
		If ($Valor == 25) {$Del1al99 = "VEINTICINCO";}
		If ($Valor == 26) {$Del1al99 = "VEINTISEIS";}
		If ($Valor == 27) {$Del1al99 = "VEINTISIETE";}
		If ($Valor == 28) {$Del1al99 = "VEINTIOCHO";}
		If ($Valor == 29) {$Del1al99 = "VEINTINUEVE";}
		If ($Valor == 30) {$Del1al99 = "TREINTA";}
		If ($Valor == 31) {$Del1al99 = "TREINTA Y UNO";}
		If ($Valor == 32) {$Del1al99 = "TREINTA Y DOS";}
		If ($Valor == 33) {$Del1al99 = "TREINTA Y TRES";}
		If ($Valor == 34) {$Del1al99 = "TREINTA Y CUATRO";}
		If ($Valor == 35) {$Del1al99 = "TREINTA Y CINCO";}
		If ($Valor == 36) {$Del1al99 = "TREINTA Y SEIS";}
		If ($Valor == 37) {$Del1al99 = "TREINTA Y SIETE";}
		If ($Valor == 38) {$Del1al99 = "TREINTA Y OCHO";}
		If ($Valor == 39) {$Del1al99 = "TREINTA Y NUEVE";}
		If ($Valor == 40) {$Del1al99 = "CUARENTA";}
		If ($Valor == 41) {$Del1al99 = "CUARENTA Y UNO";}
		If ($Valor == 42) {$Del1al99 = "CUARENTA Y DOS";}
		If ($Valor == 43) {$Del1al99 = "CUARENTA Y TRES";}
		If ($Valor == 44) {$Del1al99 = "CUARENTA Y CUATRO";}
		If ($Valor == 45) {$Del1al99 = "CUARENTA Y CINCO";}
		If ($Valor == 46) {$Del1al99 = "CUARENTA Y SEIS";}
		If ($Valor == 47) {$Del1al99 = "CUARENTA Y SIETE";}
		If ($Valor == 48) {$Del1al99 = "CUARENTA Y OCHO";}
		If ($Valor == 49) {$Del1al99 = "CUARENTA Y NUEVE";}
		If ($Valor == 50) {$Del1al99 = "CINCUENTA";}
		If ($Valor == 51) {$Del1al99 = "CINCUENTA Y UNO";}
		If ($Valor == 52) {$Del1al99 = "CINCUENTA Y DOS";}
		If ($Valor == 53) {$Del1al99 = "CINCUENTA Y TRES";}
		If ($Valor == 54) {$Del1al99 = "CINCUENTA Y CUATRO";}
		If ($Valor == 55) {$Del1al99 = "CINCUENTA Y CINCO";}
		If ($Valor == 56) {$Del1al99 = "CINCUENTA Y SEIS";}
		If ($Valor == 57) {$Del1al99 = "CINCUENTA Y SIETE";}
		If ($Valor == 58) {$Del1al99 = "CINCUENTA Y OCHO";}
		If ($Valor == 59) {$Del1al99 = "CINCUENTA Y NUEVE";}
		If ($Valor == 60) {$Del1al99 = "SESENTA";}
		If ($Valor == 61) {$Del1al99 = "SESENTA Y UNO";}
		If ($Valor == 62) {$Del1al99 = "SESENTA Y DOS";}
		If ($Valor == 63) {$Del1al99 = "SESENTA Y TRES";}
		If ($Valor == 64) {$Del1al99 = "SESENTA Y CUATRO";}
		If ($Valor == 65) {$Del1al99 = "SESENTA Y CINCO";}
		If ($Valor == 66) {$Del1al99 = "SESENTA Y SEIS";}
		If ($Valor == 67) {$Del1al99 = "SESENTA Y SIETE";}
		If ($Valor == 68) {$Del1al99 = "SESENTA Y OCHO";}
		If ($Valor == 69) {$Del1al99 = "SESENTA Y NUEVE";}
		If ($Valor == 70) {$Del1al99 = "SETENTA";}
		If ($Valor == 71) {$Del1al99 = "SETENTA Y UNO";}
		If ($Valor == 72) {$Del1al99 = "SETENTA Y DOS";}
		If ($Valor == 73) {$Del1al99 = "SETENTA Y TRES";}
		If ($Valor == 74) {$Del1al99 = "SETENTA Y CUATRO";}
		If ($Valor == 75) {$Del1al99 = "SETENTA Y CINCO";}
		If ($Valor == 76) {$Del1al99 = "SETENTA Y SEIS";}
		If ($Valor == 77) {$Del1al99 = "SETENTA Y SIETE";}
		If ($Valor == 78) {$Del1al99 = "SETENTA Y OCHO";}
		If ($Valor == 79) {$Del1al99 = "SETENTA Y NUEVE";}
		If ($Valor == 80) {$Del1al99 = "OCHENTA";}
		If ($Valor == 81) {$Del1al99 = "OCHENTA Y UNO";}
		If ($Valor == 82) {$Del1al99 = "OCHENTA Y DOS";}
		If ($Valor == 83) {$Del1al99 = "OCHENTA Y TRES";}
		If ($Valor == 84) {$Del1al99 = "OCHENTA Y CUATRO";}
		If ($Valor == 85) {$Del1al99 = "OCHENTA Y CINCO";}
		If ($Valor == 86) {$Del1al99 = "OCHENTA Y SEIS";}
		If ($Valor == 87) {$Del1al99 = "OCHENTA Y SIETE";}
		If ($Valor == 88) {$Del1al99 = "OCHENTA Y OCHO";}
		If ($Valor == 89) {$Del1al99 = "OCHENTA Y NUEVE";}
		If ($Valor == 90) {$Del1al99 = "NOVENTA";}
		If ($Valor == 91) {$Del1al99 = "NOVENTA Y UNO";}
		If ($Valor == 92) {$Del1al99 = "NOVENTA Y DOS";}
		If ($Valor == 93) {$Del1al99 = "NOVENTA Y TRES";}
		If ($Valor == 94) {$Del1al99 = "NOVENTA Y CUATRO";}
		If ($Valor == 95) {$Del1al99 = "NOVENTA Y CINCO";}
		If ($Valor == 96) {$Del1al99 = "NOVENTA Y SEIS";}
		If ($Valor == 97) {$Del1al99 = "NOVENTA Y SIETE";}
		If ($Valor == 98) {$Del1al99 = "NOVENTA Y OCHO";}
		If ($Valor == 99) {$Del1al99 = "NOVENTA Y NUEVE";}
		return $Del1al99;
	}
	
	function Cientos($Valor){
		$Cientos = "";
		If ($Valor == 1) {$Cientos = "CIENTO";}
		If ($Valor == 2) {$Cientos = "DOSCIENTOS";}
		If ($Valor == 3) {$Cientos = "TRESCIENTOS";}
		If ($Valor == 4) {$Cientos = "CUATROCIENTOS";}
		If ($Valor == 5) {$Cientos = "QUINIENTOS";}
		If ($Valor == 6) {$Cientos = "SEISCIENTOS";}
		If ($Valor == 7) {$Cientos = "SETECIENTOS";}
		If ($Valor == 8) {$Cientos = "OCHOCIENTOS";}
		If ($Valor == 9) {$Cientos = "NOVECIENTOS";}
		return $Cientos;
	}
	
	function Miles($Valor){
		$Miles = "";
		If ($Valor == 1){$Miles = "MIL";}
		If ($Valor == 2) {$Miles = "DOS MIL";}
		If ($Valor == 3) {$Miles = "TRES MIL";}
		If ($Valor == 4) {$Miles = "CUATRO MIL";}
		If ($Valor == 5) {$Miles = "CINCO MIL";}
		If ($Valor == 6) {$Miles = "SEIS MIL";}
		If ($Valor == 7) {$Miles = "SIETE MIL";}
		If ($Valor == 8) {$Miles = "OCHO MIL";}
		If ($Valor == 9) {$Miles = "NUEVE MIL";}
		If ($Valor == 10) {$Miles = "DIEZ MIL";}
		If ($Valor == 11) {$Miles = "ONCE MIL";}
		If ($Valor == 12) {$Miles = "DOCE MIL";}
		If ($Valor == 13) {$Miles = "TRECE MIL";}
		If ($Valor == 14) {$Miles = "CATORCE MIL";}
		If ($Valor == 15) {$Miles = "QUINCE MIL";}
		If ($Valor == 16) {$Miles = "DIEZ Y SEIS MIL";}
		If ($Valor == 17) {$Miles = "DIEZ Y SIETE MIL";}
		If ($Valor == 18) {$Miles = "DIEZ Y OCHO MIL";}
		If ($Valor == 19) {$Miles = "DIEZ Y NUEVE MIL";}
		If ($Valor == 20) {$Miles = "VEINTE MIL";}
		If ($Valor == 21) {$Miles = "VEINTIUNO MIL";}
		If ($Valor == 22) {$Miles = "VEINTIDOS MIL";}
		If ($Valor == 23) {$Miles = "VEINTITRES MIL";}
		If ($Valor == 24) {$Miles = "VEINTICUATRO MIL";}
		If ($Valor == 25) {$Miles = "VEINTICINCO MIL";}
		If ($Valor == 26) {$Miles = "VEINTISEIS MIL";}
		If ($Valor == 27) {$Miles = "VEINTISIETE MIL";}
		If ($Valor == 28) {$Miles = "VEINTIOCHO MIL";}
		If ($Valor == 29) {$Miles = "VEINTINUEVE MIL";}
		If ($Valor == 30) {$Miles = "TREINTA MIL";}
		If ($Valor == 31) {$Miles = "TREINTA Y UNO MIL";}
		If ($Valor == 32) {$Miles = "TREINTA Y DOS MIL";}
		If ($Valor == 33) {$Miles = "TREINTA Y TRES MIL";}
		If ($Valor == 34) {$Miles = "TREINTA Y CUATRO MIL";}
		If ($Valor == 35) {$Miles = "TREINTA Y CINCO MIL";}
		If ($Valor == 36) {$Miles = "TREINTA Y SEIS MIL";}
		If ($Valor == 37) {$Miles = "TREINTA Y SIETE MIL";}
		If ($Valor == 38) {$Miles = "TREINTA Y OCHO MIL";}
		If ($Valor == 39) {$Miles = "TREINTA Y NUEVE MIL";}
		If ($Valor == 40) {$Miles = "CUARENTA MIL";}
		If ($Valor == 41) {$Miles = "CUARENTA Y UNO MIL";}
		If ($Valor == 42) {$Miles = "CUARENTA Y DOS MIL";}
		If ($Valor == 43) {$Miles = "CUARENTA Y TRES MIL";}
		If ($Valor == 44) {$Miles = "CUARENTA Y CUATRO MIL";}
		If ($Valor == 45) {$Miles = "CUARENTA Y CINCO MIL";}
		If ($Valor == 46) {$Miles = "CUARENTA Y SEIS MIL";}
		If ($Valor == 47) {$Miles = "CUARENTA Y SIETE MIL";}
		If ($Valor == 48) {$Miles = "CUARENTA Y OCHO MIL";}
		If ($Valor == 49) {$Miles = "CUARENTA Y NUEVE MIL";}
		If ($Valor == 50) {$Miles = "CINCUENTA MIL";}
		If ($Valor == 51) {$Miles = "CINCUENTA Y UNO MIL";}
		If ($Valor == 52) {$Miles = "CINCUENTA Y DOS MIL";}
		If ($Valor == 53) {$Miles = "CINCUENTA Y TRES MIL";}
		If ($Valor == 54) {$Miles = "CINCUENTA Y CUATRO MIL";}
		If ($Valor == 55) {$Miles = "CINCUENTA Y CINCO MIL";}
		If ($Valor == 56) {$Miles = "CINCUENTA Y SEIS MIL";}
		If ($Valor == 57) {$Miles = "CINCUENTA Y SIETE MIL";}
		If ($Valor == 58) {$Miles = "CINCUENTA Y OCHO MIL";}
		If ($Valor == 59) {$Miles = "CINCUENTA Y NUEVE MIL";}
		If ($Valor == 60) {$Miles = "SESENTA MIL";}
		If ($Valor == 61) {$Miles = "SESENTA Y UNO MIL";}
		If ($Valor == 62) {$Miles = "SESENTA Y DOS MIL";}
		If ($Valor == 63) {$Miles = "SESENTA Y TRES MIL";}
		If ($Valor == 64) {$Miles = "SESENTA Y CUATRO MIL";}
		If ($Valor == 65) {$Miles = "SESENTA Y CINCO MIL";}
		If ($Valor == 66) {$Miles = "SESENTA Y SEIS MIL";}
		If ($Valor == 67) {$Miles = "SESENTA Y SIETE MIL";}
		If ($Valor == 68) {$Miles = "SESENTA Y OCHO MIL";}
		If ($Valor == 69) {$Miles = "SESENTA Y NUEVE MIL";}
		If ($Valor == 70) {$Miles = "SETENTA MIL";}
		If ($Valor == 71) {$Miles = "SETENTA Y UNO MIL";}
		If ($Valor == 72) {$Miles = "SETENTA Y DOS MIL";}
		If ($Valor == 73) {$Miles = "SETENTA Y TRES MIL";}
		If ($Valor == 74) {$Miles = "SETENTA Y CUATRO MIL";}
		If ($Valor == 75) {$Miles = "SETENTA Y CINCO MIL";}
		If ($Valor == 76) {$Miles = "SETENTA Y SEIS MIL";}
		If ($Valor == 77) {$Miles = "SETENTA Y SIETE MIL";}
		If ($Valor == 78) {$Miles = "SETENTA Y OCHO MIL";}
		If ($Valor == 79) {$Miles = "SETENTA Y NUEVE MIL";}
		If ($Valor == 80) {$Miles = "OCHENTA MIL";}
		If ($Valor == 81) {$Miles = "OCHENTA Y UNO MIL";}
		If ($Valor == 82) {$Miles = "OCHENTA Y DOS MIL";}
		If ($Valor == 83) {$Miles = "OCHENTA Y TRES MIL";}
		If ($Valor == 84) {$Miles = "OCHENTA Y CUATRO MIL";}
		If ($Valor == 85) {$Miles = "OCHENTA Y CINCO MIL";}
		If ($Valor == 86) {$Miles = "OCHENTA Y SEIS MIL";}
		If ($Valor == 87) {$Miles = "OCHENTA Y SIETE MIL";}
		If ($Valor == 88) {$Miles = "OCHENTA Y OCHO MIL";}
		If ($Valor == 89) {$Miles = "OCHENTA Y NUEVE MIL";}
		If ($Valor == 90) {$Miles = "NOVENTA MIL";}
		If ($Valor == 91) {$Miles = "NOVENTA Y UNO MIL";}
		If ($Valor == 92) {$Miles = "NOVENTA Y DOS MIL";}
		If ($Valor == 93) {$Miles = "NOVENTA Y TRES MIL";}
		If ($Valor == 94) {$Miles = "NOVENTA Y CUATRO MIL";}
		If ($Valor == 95) {$Miles = "NOVENTA Y CINCO MIL";}
		If ($Valor == 96) {$Miles = "NOVENTA Y SEIS MIL";}
		If ($Valor == 97) {$Miles = "NOVENTA Y SIETE MIL";}
		If ($Valor == 98) {$Miles = "NOVENTA Y OCHO MIL";}
		If ($Valor == 99) {$Miles = "NOVENTA Y NUEVE MIL";}
		return $Miles;
	}
}
?>