<?php
require_once "ConexaoBD.php";

class ComunidadeDAO
{
    // Ranking de usuários por avaliações
    public static function rankingUsuarios($limite = 20)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT u.id, u.nome_completo, u.email,
                COUNT(a.id) as total_avaliacoes,
                (SELECT COUNT(*) FROM seguidores WHERE seguindo_id = u.id) as total_seguidores
                FROM usuarios u
                LEFT JOIN avaliacoes a ON u.id = a.usuario_id
                GROUP BY u.id, u.nome_completo, u.email
                ORDER BY total_avaliacoes DESC
                LIMIT :limite";
        
        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>