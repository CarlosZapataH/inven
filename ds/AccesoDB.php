<?php
class AccesoDB {

    private static $pdo = null;

    public static function getPDO() {
        if( self::$pdo == null ) {
            try {
                $parm = parse_ini_file("connect.ini");
                $url = $parm["01"];
                $user = $parm["02"];
                $pass = $parm["03"];
                self::$pdo = new PDO($url,$user,$pass);
                self::$pdo->exec("set names utf8");
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$pdo->setAttribute(PDO::ATTR_CASE,PDO::CASE_LOWER);
                self::$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            }catch (PDOException $e) {
                self::$pdo = null;
                throw $e;
            }
        }
        return self::$pdo;
    }
}
