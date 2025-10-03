<?php
class ConexaoBD
{

    public static function conectar():PDO
    {        

        return new PDO("pgsql:host=localhost;dbname=projeto_cinema", "postgres", "postgres");
    }
}