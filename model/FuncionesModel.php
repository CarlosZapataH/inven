<?php

class FuncionesModel{

    function quitar_caracteresEspeciales($string){
        try {
            $string = trim($string);
            $string = str_replace(
                array("º", "~", "<br>",
                    "#", "@", "|", "!", '"',
                    "·", "$", "%", "&",
                    "(", ")", "?", "'", "¡",
                    "¿", "[", "^", "<code>", "]",
                    "+", "}", "{", "¨", "´",
                    ">", "< "),'',$string);
            return $string;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'hzbL3ayiYU';;
        $secret_iv = 'Tys34&210';
        // hash
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    public function fecha_ENG_ESP($fecha){
        try {
            $fechaM = "";
            if(!empty($fecha)) {
                $fecha = explode("-", $fecha);
                $d = $fecha[2];
                $m = $fecha[1];
                $y = $fecha[0];
                if ((int)$d != 0 && (int)$m != 0 && (int)$y != 0) {
                    $fechaM = $d . "/" . $m . "/" . $y;
                }
            }
            return $fechaM;
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function fecha_ENG_ESP_format($fecha,$separador){
        try {
            $fechaM = "";
            if(!empty($fecha)) {
                $fecha = explode("-", $fecha);
                $d = $fecha[2];
                $m = $fecha[1];
                $y = $fecha[0];
                if ((int)$d != 0 && (int)$m != 0 && (int)$y != 0) {
                    $fechaM = $d . ".$separador." . $m . ".$separador." . $y;
                }
            }
            return $fechaM;
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function fecha_ESP_ENG($fecha){
        try {
            $fecha = explode("/",$fecha);
            $d = $fecha[0];
            $m = $fecha[1];
            $y = $fecha[2];
            return $y."-".$m."-".$d;
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function texto_mes($mes){
        try {
            $mes = (int)$mes;
            $array_meses = array(1=>'Enero', 2=>'Febrero',3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 7=>'Julio',
                                 8=>'Agosto',9=>'Setiembre',10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre');

            return  $array_meses[$mes];
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function texto_mes_abrev($mes){
        try {
            $mes = (int)$mes;
            $array_meses = array(1=>'Ene', 2=>'Feb',3=>'Mar', 4=>'Abr', 5=>'May', 6=>'Jun', 7=>'Jul',
                8=>'Ago',9=>'Set',10=>'Oct', 11=>'Nov', 12=>'Dic');

            return $array_meses[$mes];
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function fechaHora_ENG_ESP($datetime){
        try {
            $fechaHora = explode(" ",$datetime);
            $fecha = explode("-",trim($fechaHora[0]));
            $d = $fecha[2];
            $m = $fecha[1];
            $y = $fecha[0];
            return $d."/".$m."/".$y. " ".trim($fechaHora[1]);
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    function difMeses($fini,$ffin){
        $f1 = new DateTime( $fini );
        $f2 = new DateTime( $ffin );
        // obtener la diferencia de fechas
        $diferencia = $f1->diff($f2);
        $meses = ( $diferencia->y * 12 ) + $diferencia->m;
        return $meses;
    }

    function difDias($fini,$ffin){
        $f1 = new DateTime( $fini );
        $f2 = new DateTime( $ffin );
        // obtener la diferencia de fechas
        $diferencia = $f1->diff($f2);
        return $diferencia->days ;
    }

    function semaforoInventario_tipo($fechaRecep,$verde,$ambar,$rojo){
        $semVerde = explode("|",$verde);
        $semAmbar = explode("|",$ambar);
        $semRojo = explode("|",$rojo);
        date_default_timezone_set("America/Lima");
        setlocale(LC_TIME, 'es_PE.UTF-8');
        $date1 = new DateTime($fechaRecep);
        $date2 = new DateTime(date("Y-m-d"));
        $diff = $date1->diff($date2);
        $ndias = $diff->days;

        $color = 'rojo';
        if ($ndias >= (int)$semVerde[0] && $ndias <= (int)$semVerde[1]) {
            $color = 'verde';
        } else if ($ndias >= (int)$semAmbar[0] && $ndias <= (int)$semAmbar[1]) {
            $color = 'ambar';
        } else if ($ndias >= (int)$semRojo[0] && $ndias <= (int)$semRojo[1]) {
            $color = 'rojo';
        }
        return $color;
    }

    function dias_transc_semaforoInventario_tipo($fechaRecep,$verde,$ambar,$rojo){
        $semVerde = explode("|",$verde);
        $semAmbar = explode("|",$ambar);
        $semRojo = explode("|",$rojo);
        date_default_timezone_set("America/Lima");
        setlocale(LC_TIME, 'es_PE.UTF-8');
        $date1 = new DateTime($fechaRecep);
        $date2 = new DateTime(date("Y-m-d"));
        $diff = $date1->diff($date2);
        $ndias = $diff->days;
        return $ndias;
    }

    function saber_dia($fecha) {
        $dias = array('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');
        $nombredia = $dias[(date('N', strtotime($fecha))) - 1];
        return $nombredia;
    }

    function sumar_dias_fecha($fecha,$ndia) {
        $nuevafecha = strtotime ( '+'.$ndia.' day' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'd/m/Y' , $nuevafecha );
        return $nuevafecha;
    }

    function sumar_meses_fecha($fecha,$month) {
        //sumo n mes
        $nuevafecha = strtotime ( '+'.$month.' month' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-j' , $nuevafecha );
        return $nuevafecha;
    }

    function reemplazar_string($string)
    {
        try {
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
                array('ñ', 'Ñ', 'c', 'C',),
                $string
            );

            //Esta parte se encarga de eliminar cualquier caracter extraño
            $string = str_replace(
                array("\\", "¨", "º", "~",
                    "#", "@", "|", "!", "\"",
                    "·", "$", "%", "&",
                    "(", ")", "?", "'", "¡",
                    "¿", "[", "^", "<code>", "]",
                    "+", "}", "{", "¨", "´",
                    ">", "< "),
                ' ',
                $string
            );
            return $string;
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function getRandomCode(){
        try {
            $an = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $su = strlen($an) - 1;
            return  substr($an, rand(0, $su), 1).
                substr($an, rand(0, $su), 1).
                substr($an, rand(0, $su), 1).
                substr($an, rand(0, $su), 1).
                substr($an, rand(0, $su), 1).
                substr($an, rand(0, $su), 1);

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }
}
