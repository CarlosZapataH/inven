<?php

class Util {

    public static function rigistrarLog( PDOException $e, $query ){
        $mensaje = "File: " . $e->getFile() . "\n" .
            "Line: " . $e->getLine() . "\n" .
            "Code: " . $e->getCode() . "\n" .
            "Message: " . $e->getMessage() . "\n" .
            "Query: " . $query . "\n\n" ;
        $archivo = "../log/error.log";
        error_log($mensaje, 3, $archivo);
    }

	 public static function nullToSpace( $value ) {
		 if( is_null($value) ) {
			 $value = '&nbsp;';
		 }
		 return $value;
	 }
}
