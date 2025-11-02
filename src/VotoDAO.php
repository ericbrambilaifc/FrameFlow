<?php
// src/VotoDAO.php
class VotoDAO
{
    public static function votar($avaliacao_id, $usuario_id, $tipo_voto)
    {
        try {
            $pdo = ConexaoBD::conectar();

            // Verifica se já existe um voto
            $stmt = $pdo->prepare("
                SELECT id, tipo_voto 
                FROM votos_avaliacoes 
                WHERE avaliacao_id = ? AND usuario_id = ?
            ");
            $stmt->execute([$avaliacao_id, $usuario_id]);
            $voto_existente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($voto_existente) {
                // Se o voto é o mesmo, remove (toggle off)
                if ($voto_existente['tipo_voto'] == $tipo_voto) {
                    $stmt = $pdo->prepare("
                        DELETE FROM votos_avaliacoes 
                        WHERE avaliacao_id = ? AND usuario_id = ?
                    ");
                    return $stmt->execute([$avaliacao_id, $usuario_id]);
                } else {
                    // Se o voto é diferente, atualiza
                    $stmt = $pdo->prepare("
                        UPDATE votos_avaliacoes 
                        SET tipo_voto = ?, data_voto = NOW() 
                        WHERE avaliacao_id = ? AND usuario_id = ?
                    ");
                    return $stmt->execute([$tipo_voto, $avaliacao_id, $usuario_id]);
                }
            } else {
                // Insere novo voto
                $stmt = $pdo->prepare("
                    INSERT INTO votos_avaliacoes (avaliacao_id, usuario_id, tipo_voto) 
                    VALUES (?, ?, ?)
                ");
                return $stmt->execute([$avaliacao_id, $usuario_id, $tipo_voto]);
            }
        } catch (PDOException $e) {
            error_log("Erro ao votar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca o voto de um usuário em uma avaliação específica
     * @param int $avaliacao_id
     * @param int $usuario_id
     * @return int|null Retorna 1 (like), -1 (dislike) ou null (sem voto)
     */
    public static function buscarVotoUsuario($avaliacao_id, $usuario_id)
    {
        try {
            $pdo = ConexaoBD::conectar();

            $stmt = $pdo->prepare("
                SELECT tipo_voto 
                FROM votos_avaliacoes 
                WHERE avaliacao_id = ? AND usuario_id = ?
            ");
            $stmt->execute([$avaliacao_id, $usuario_id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado ? intval($resultado['tipo_voto']) : null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar voto: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca os votos de um usuário em múltiplas avaliações
     * @param int $usuario_id
     * @param array $avaliacoes_ids
     * @return array Array associativo [avaliacao_id => tipo_voto]
     */
    public static function buscarVotosUsuario($usuario_id, $avaliacoes_ids)
    {
        if (empty($avaliacoes_ids)) {
            return [];
        }

        try {
            $pdo = ConexaoBD::conectar();

            $placeholders = str_repeat('?,', count($avaliacoes_ids) - 1) . '?';
            $stmt = $pdo->prepare("
                SELECT avaliacao_id, tipo_voto 
                FROM votos_avaliacoes 
                WHERE usuario_id = ? AND avaliacao_id IN ($placeholders)
            ");

            $params = array_merge([$usuario_id], $avaliacoes_ids);
            $stmt->execute($params);

            $votos = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $votos[$row['avaliacao_id']] = intval($row['tipo_voto']);
            }

            return $votos;
        } catch (PDOException $e) {
            error_log("Erro ao buscar votos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Conta o total de likes de uma avaliação
     * @param int $avaliacao_id
     * @return int
     */
    public static function contarLikes($avaliacao_id)
    {
        try {
            $pdo = ConexaoBD::conectar();

            $stmt = $pdo->prepare("
                SELECT COUNT(*) as total 
                FROM votos_avaliacoes 
                WHERE avaliacao_id = ? AND tipo_voto = 1
            ");
            $stmt->execute([$avaliacao_id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return intval($resultado['total']);
        } catch (PDOException $e) {
            error_log("Erro ao contar likes: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Conta o total de dislikes de uma avaliação
     * @param int $avaliacao_id
     * @return int
     */
    public static function contarDislikes($avaliacao_id)
    {
        try {
            $pdo = ConexaoBD::conectar();

            $stmt = $pdo->prepare("
                SELECT COUNT(*) as total 
                FROM votos_avaliacoes 
                WHERE avaliacao_id = ? AND tipo_voto = -1
            ");
            $stmt->execute([$avaliacao_id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return intval($resultado['total']);
        } catch (PDOException $e) {
            error_log("Erro ao contar dislikes: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Conta likes e dislikes de uma avaliação
     * @param int $avaliacao_id
     * @return array ['likes' => int, 'dislikes' => int]
     */
    public static function contarVotos($avaliacao_id)
    {
        try {
            $pdo = ConexaoBD::conectar();

            $stmt = $pdo->prepare("
                SELECT 
                    SUM(CASE WHEN tipo_voto = 1 THEN 1 ELSE 0 END) as likes,
                    SUM(CASE WHEN tipo_voto = -1 THEN 1 ELSE 0 END) as dislikes
                FROM votos_avaliacoes 
                WHERE avaliacao_id = ?
            ");
            $stmt->execute([$avaliacao_id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'likes' => intval($resultado['likes'] ?? 0),
                'dislikes' => intval($resultado['dislikes'] ?? 0)
            ];
        } catch (PDOException $e) {
            error_log("Erro ao contar votos: " . $e->getMessage());
            return ['likes' => 0, 'dislikes' => 0];
        }
    }

    /**
     * Remove todos os votos de uma avaliação
     * @param int $avaliacao_id
     * @return bool
     */
    public static function removerVotosAvaliacao($avaliacao_id)
    {
        try {
            $pdo = ConexaoBD::conectar();

            $stmt = $pdo->prepare("DELETE FROM votos_avaliacoes WHERE avaliacao_id = ?");
            return $stmt->execute([$avaliacao_id]);
        } catch (PDOException $e) {
            error_log("Erro ao remover votos: " . $e->getMessage());
            return false;
        }
    }
}
