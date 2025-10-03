<?php
require_once "ConexaoBD.php";

class ClassificacaoDAO
{
    // A tabela `classificacao` tem apenas 'idclassificacao' e 'nome'.
    // A função de inserir pode não ser necessária se a tabela é estática,
    // mas aqui está a implementação caso precise.
    public static function inserir($dados)
    {
        $conexao = ConexaoBD::conectar();

        $nome = $dados['nome'];

        $sql = "INSERT INTO classificacao (nome) VALUES (:nome)";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->execute();
    }

    public static function listar()
    {
        $conexao = ConexaoBD::conectar();
        
        $sql = "SELECT * FROM classificacao";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();
        $classificacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $classificacoes;
    }
}