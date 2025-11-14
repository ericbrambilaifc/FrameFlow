<?php

class VotoDAO
{
    public static function votar($avaliacao_id, $usuario_id, $tipo_voto)
    {
        try {
            $pdo = ConexaoBD::conectar();

            $stmt = $pdo->prepare("
                SELECT id, tipo_voto 
                FROM votos_avaliacoes 
                WHERE avaliacao_id = ? AND usuario_id = ?
            ");
            $stmt->execute([$avaliacao_id, $usuario_id]);
            $voto_existente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($voto_existente) {
                
                if ($voto_existente['tipo_voto'] == $tipo_voto) {
                    $stmt = $pdo->prepare("
                        DELETE FROM votos_avaliacoes 
                        WHERE avaliacao_id = ? AND usuario_id = ?
                    ");
                    return $stmt->execute([$avaliacao_id, $usuario_id]);
                } else {
                    
                    $stmt = $pdo->prepare("
                        UPDATE votos_avaliacoes 
                        SET tipo_voto = ?, data_voto = NOW() 
                        WHERE avaliacao_id = ? AND usuario_id = ?
                    ");
                    return $stmt->execute([$tipo_voto, $avaliacao_id, $usuario_id]);
                }
            } else {
                
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
