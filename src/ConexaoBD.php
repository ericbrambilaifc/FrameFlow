<?php
class ConexaoBD
{

    public static function conectar():PDO
    {        

return new PDO("mysql:host=localhost;port=3306;dbname=frameflow", "root", "");    }
}