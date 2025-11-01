<?php
require_once('ConexaoBD.php');

class CurtidaDAO
{
    /**
     * Adiciona ou remove uma curtida em uma avaliação
     * 
     * @param int $usuario_id ID do usuário que está curtindo
     * @param int $avaliacao_id ID da avaliação a ser curtida
     * @return array ['sucesso' => bool, 'acao' => 'curtiu'|'descurtiu', 'total_curtidas' => int]
     */
    public static function toggleCurtida($usuario_id, $avaliacao_id)
    {
        try {
            $conexao = ConexaoBD::conectar();
            
            // Verifica se já curtiu
            $sqlVerifica = "SELECT id FROM curtidas_avaliacoes 
                           WHERE usuario_id = :usuario_id 
                           AND avaliacao_id = :avaliacao_id";
            
            $stmt = $conexao->prepare($sqlVerifica);
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':avaliacao_id', $avaliacao_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $jaCurtiu = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($jaCurtiu) {
                // Remove curtida
                $sqlRemove = "DELETE FROM curtidas_avaliacoes 
                             WHERE usuario_id = :usuario_id 
                             AND avaliacao_id = :avaliacao_id";
                
                $stmtRemove = $conexao->prepare($sqlRemove);
                $stmtRemove->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmtRemove->bindValue(':avaliacao_id', $avaliacao_id, PDO::PARAM_INT);
                $stmtRemove->execute();
                
                $acao = 'descurtiu';
            } else {
                // Adiciona curtida
                $sqlAdiciona = "INSERT INTO curtidas_avaliacoes (usuario_id, avaliacao_id) 
                               VALUES (:usuario_id, :avaliacao_id)";
                
                $stmtAdiciona = $conexao->prepare($sqlAdiciona);
                $stmtAdiciona->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmtAdiciona->bindValue(':avaliacao_id', $avaliacao_id, PDO::PARAM_INT);
                $stmtAdiciona->execute();
                
                $acao = 'curtiu';
            }
            
            // Busca total de curtidas da avaliação
            $totalCurtidas = self::contarCurtidas($avaliacao_id);
            
            return [
                'sucesso' => true,
                'acao' => $acao,
                'total_curtidas' => $totalCurtidas
            ];
            
        } catch (PDOException $e) {
            error_log("Erro ao processar curtida: " . $e->getMessage());
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao processar curtida'
            ];
        }
    }
    
    /**
     * Conta total de curtidas de uma avaliação
     * 
     * @param int $avaliacao_id ID da avaliação
     * @return int Total de curtidas
     */
    public static function contarCurtidas($avaliacao_id)
    {
        try {
            $conexao = ConexaoBD::conectar();
            
            $sql = "SELECT COUNT(*) as total 
                    FROM curtidas_avaliacoes 
                    WHERE avaliacao_id = :avaliacao_id";
            
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':avaliacao_id', $avaliacao_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado['total'];
            
        } catch (PDOException $e) {
            error_log("Erro ao contar curtidas: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Verifica se um usuário curtiu uma avaliação
     * 
     * @param int $usuario_id ID do usuário
     * @param int $avaliacao_id ID da avaliação
     * @return bool True se já curtiu, False caso contrário
     */
    public static function verificarCurtida($usuario_id, $avaliacao_id)
    {
        try {
            $conexao = ConexaoBD::conectar();
            
            $sql = "SELECT COUNT(*) as curtiu 
                    FROM curtidas_avaliacoes 
                    WHERE usuario_id = :usuario_id 
                    AND avaliacao_id = :avaliacao_id";
            
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':avaliacao_id', $avaliacao_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['curtiu'] > 0;
            
        } catch (PDOException $e) {
            error_log("Erro ao verificar curtida: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Busca curtidas de múltiplas avaliações para um usuário
     * 
     * @param int $usuario_id ID do usuário
     * @param array $avaliacoes_ids Array de IDs das avaliações
     * @return array Array com IDs das avaliações curtidas pelo usuário
     */
    public static function buscarCurtidasUsuario($usuario_id, $avaliacoes_ids)
    {
        try {
            if (empty($avaliacoes_ids)) {
                return [];
            }
            
            $conexao = ConexaoBD::conectar();
            
            $placeholders = implode(',', array_fill(0, count($avaliacoes_ids), '?'));
            $sql = "SELECT avaliacao_id 
                    FROM curtidas_avaliacoes 
                    WHERE usuario_id = ? 
                    AND avaliacao_id IN ($placeholders)";
            
            $stmt = $conexao->prepare($sql);
            $stmt->execute(array_merge([$usuario_id], $avaliacoes_ids));
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (PDOException $e) {
            error_log("Erro ao buscar curtidas do usuário: " . $e->getMessage());
            return [];
        }
    }
}
