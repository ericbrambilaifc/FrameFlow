<?php
require_once('ConexaoBD.php');

class UsuarioDAO
{

    // Método para fazer login do usuário
    public static function login($email, $senha)
    {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica se o usuário existe e se a senha está correta
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                return $usuario;
            }

            return null;
        } catch (PDOException $e) {
            error_log("Erro ao fazer login: " . $e->getMessage());
            return null;
        }
    }


    // Método para obter usuário por ID
    public static function obterPorId($id)
    {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "SELECT * FROM usuarios WHERE id = :id";
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário por ID: " . $e->getMessage());
            return null;
        }
    }

    // ADICIONE ESTE MÉTODO - Alias para obterPorId
    public static function buscarPorId($id)
    {
        return self::obterPorId($id);
    }

    // Método para obter perfil do usuário com estatísticas
    public static function obterPerfil($usuario_id)
    {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "SELECT 
                        u.*,
                        COUNT(DISTINCT a.id) as total_avaliacoes,
                        COUNT(DISTINCT s.id) as total_seguidores,
                        COUNT(DISTINCT s2.id) as total_seguindo
                    FROM usuarios u
                    LEFT JOIN avaliacoes a ON u.id = a.usuario_id
                    LEFT JOIN seguidores s ON u.id = s.seguindo_id
                    LEFT JOIN seguidores s2 ON u.id = s2.seguidor_id
                    WHERE u.id = :usuario_id
                    GROUP BY u.id";

            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao obter perfil: " . $e->getMessage());
            return null;
        }
    }

    public static function obterFotoPerfil($usuario_id)
    {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "SELECT foto_perfil FROM usuarios WHERE id = :id LIMIT 1";
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? ($row['foto_perfil'] ?? null) : null;
        } catch (PDOException $e) {
            error_log("Erro ao obter foto de perfil: " . $e->getMessage());
            return null;
        }
    }

    // Método para obter ranking de avaliações
    public static function obterRankingAvaliacoes($limite = 50)
    {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "SELECT 
                        u.id,
                        u.nome_completo,
                        u.foto_perfil,
                        COUNT(a.id) as total_avaliacoes,
                        (SELECT COUNT(*) FROM seguidores WHERE seguindo_id = u.id) as total_seguidores
                    FROM usuarios u
                    LEFT JOIN avaliacoes a ON u.id = a.usuario_id
                    WHERE u.is_admin = 0
                    GROUP BY u.id, u.nome_completo, u.foto_perfil
                    ORDER BY total_avaliacoes DESC, u.nome_completo ASC
                    LIMIT :limite";

            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar ranking de avaliações: " . $e->getMessage());
            return [];
        }
    }

    // Método para obter ranking de jogadores
    public static function obterRankingJogadores($limite = 50)
    {
        try {
            $conexao = ConexaoBD::conectar();

            // Primeiro verifica se a tabela pontuacoes_jogos existe
            $checkTable = "SHOW TABLES LIKE 'pontuacoes_jogos'";
            $result = $conexao->query($checkTable);

            if ($result->rowCount() == 0) {
                // Tabela não existe, retorna array vazio
                return [];
            }

            $sql = "SELECT 
                        u.id,
                        u.nome_completo,
                        u.foto_perfil,
                        COALESCE(SUM(pj.pontuacao), 0) as total_pontos,
                        COUNT(pj.id) as total_jogos,
                        COALESCE(MAX(pj.pontuacao), 0) as melhor_pontuacao
                    FROM usuarios u
                    LEFT JOIN pontuacoes_jogos pj ON u.id = pj.usuario_id
                    WHERE u.is_admin = 0
                    GROUP BY u.id, u.nome_completo, u.foto_perfil
                    ORDER BY total_pontos DESC, u.nome_completo ASC
                    LIMIT :limite";

            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar ranking de jogadores: " . $e->getMessage());
            return [];
        }
    }

    // Método para buscar usuários por termo (busca)
    public static function buscar($termo, $limite = 10)
    {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "SELECT id, nome_completo, email, foto_perfil 
                    FROM usuarios 
                    WHERE (nome_completo LIKE :termo OR email LIKE :termo)
                    AND is_admin = 0
                    ORDER BY nome_completo ASC
                    LIMIT :limite";

            $stmt = $conexao->prepare($sql);
            $termo_busca = '%' . $termo . '%';
            $stmt->bindValue(':termo', $termo_busca, PDO::PARAM_STR);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();

            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Adiciona as iniciais para cada usuário
            foreach ($usuarios as &$usuario) {
                $nomes = explode(' ', $usuario['nome_completo']);
                $iniciais = '';
                foreach ($nomes as $nome) {
                    if (!empty($nome)) {
                        $iniciais .= strtoupper(substr($nome, 0, 1));
                        if (strlen($iniciais) >= 2) break;
                    }
                }
                $usuario['iniciais'] = $iniciais;
            }

            return $usuarios;
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuários: " . $e->getMessage());
            return [];
        }
    }

    public static function buscarUsuarios($termo, $limite = 10)
    {
        try {
            $conexao = ConexaoBD::conectar();

            // SQL para buscar por nome completo ou email
            $sql = "SELECT 
                    id, 
                    nome_completo, 
                    email, 
                    foto_perfil,
                    is_admin
                FROM usuarios 
                WHERE (nome_completo LIKE :termo OR email LIKE :termo)
                AND is_admin = 0
                ORDER BY nome_completo ASC
                LIMIT :limite";

            $stmt = $conexao->prepare($sql);
            $termo_busca = '%' . $termo . '%';
            $stmt->bindValue(':termo', $termo_busca, PDO::PARAM_STR);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();

            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Adiciona as iniciais para cada usuário
            foreach ($usuarios as &$usuario) {
                $nomes = explode(' ', $usuario['nome_completo']);
                $iniciais = '';

                // Pega as primeiras letras dos dois primeiros nomes
                $contador = 0;
                foreach ($nomes as $nome) {
                    if (!empty($nome) && $contador < 2) {
                        $iniciais .= strtoupper(substr($nome, 0, 1));
                        $contador++;
                    }
                }

                // Se conseguiu apenas uma inicial, pega a segunda letra do primeiro nome
                if (strlen($iniciais) < 2 && !empty($nomes[0])) {
                    $iniciais = strtoupper(substr($nomes[0], 0, 2));
                }

                $usuario['iniciais'] = $iniciais;
            }

            return $usuarios;
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuários: " . $e->getMessage());
            return [];
        }
    }


    // Método para listar todos os usuários
    public static function listar()
    {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "SELECT * FROM usuarios WHERE is_admin = 0 ORDER BY nome_completo ASC";
            $stmt = $conexao->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar usuários: " . $e->getMessage());
            return [];
        }
    }

    // Método para inserir novo usuário
    public static function inserir($dados)
    {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "INSERT INTO usuarios (nome_completo, email, senha, is_admin) 
                    VALUES (:nome_completo, :email, :senha, :is_admin)";

            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':nome_completo', $dados['nome_completo']);
            $stmt->bindValue(':email', $dados['email']);
            $stmt->bindValue(':senha', $dados['senha']);
            $stmt->bindValue(':is_admin', $dados['is_admin'] ?? 0, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao inserir usuário: " . $e->getMessage());
            return false;
        }
    }

    // Método para atualizar usuário
    public static function atualizar($dados)
    {
        try {
            $conexao = ConexaoBD::conectar();

            if (isset($dados['senha']) && !empty($dados['senha'])) {
                $sql = "UPDATE usuarios 
                        SET nome_completo = :nome_completo, 
                            email = :email, 
                            senha = :senha,
                            foto_perfil = :foto_perfil
                        WHERE id = :id";
            } else {
                $sql = "UPDATE usuarios 
                        SET nome_completo = :nome_completo, 
                            email = :email,
                            foto_perfil = :foto_perfil
                        WHERE id = :id";
            }

            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':id', $dados['id']);
            $stmt->bindValue(':nome_completo', $dados['nome_completo']);
            $stmt->bindValue(':email', $dados['email']);
            $stmt->bindValue(':foto_perfil', $dados['foto_perfil'] ?? null);

            if (isset($dados['senha']) && !empty($dados['senha'])) {
                $stmt->bindValue(':senha', $dados['senha']);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            return false;
        }
    }

    // Método para deletar usuário
    public static function deletar($id)
    {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "DELETE FROM usuarios WHERE id = :id";
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar usuário: " . $e->getMessage());
            return false;
        }
    }

    // Método para buscar usuário por email
    public static function buscarPorEmail($email)
    {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário por email: " . $e->getMessage());
            return null;
        }
    }

    // Método para verificar se email já existe
    public static function emailExiste($email, $excluir_id = null)
    {
        try {
            $conexao = ConexaoBD::conectar();

            if ($excluir_id) {
                $sql = "SELECT COUNT(*) as total FROM usuarios WHERE email = :email AND id != :id";
                $stmt = $conexao->prepare($sql);
                $stmt->bindValue(':email', $email);
                $stmt->bindValue(':id', $excluir_id, PDO::PARAM_INT);
            } else {
                $sql = "SELECT COUNT(*) as total FROM usuarios WHERE email = :email";
                $stmt = $conexao->prepare($sql);
                $stmt->bindValue(':email', $email);
            }

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar email: " . $e->getMessage());
            return false;
        }
    }

    // Método para atualizar foto de perfil
    public static function atualizarFotoPerfil($usuario_id, $nome_arquivo)
    {
        try {
            $conexao = ConexaoBD::conectar();
            $sql = "UPDATE usuarios SET foto_perfil = :foto_perfil WHERE id = :id";
            $stmt = $conexao->prepare($sql);
            $stmt->bindValue(':foto_perfil', $nome_arquivo);
            $stmt->bindValue(':id', $usuario_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar foto de perfil: " . $e->getMessage());
            return false;
        }
    }
}
