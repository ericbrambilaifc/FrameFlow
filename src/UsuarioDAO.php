<?php
require_once "ConexaoBD.php";

class UsuarioDAO
{



    // Cadastrar novo usuário
    public static function inserir($dados)
    {
        $conexao = ConexaoBD::conectar();
        $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nome_completo, email, senha) VALUES (:nome_completo, :email, :senha)";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':nome_completo', $dados['nome_completo']);
        $stmt->bindParam(':email', $dados['email']);
        $stmt->bindParam(':senha', $senhaHash);

        return $stmt->execute();
    }

    // Fazer login
    public static function login($email, $senha)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }

        return false;
    }

    // Buscar usuários por nome ou email
    public static function buscarUsuarios($termo)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT id, nome_completo, email, foto_perfil, is_admin
            FROM usuarios
            WHERE is_admin = 0 
            AND (nome_completo LIKE :termo OR email LIKE :termo)
            ORDER BY nome_completo ASC
            LIMIT 10";

        $stmt = $conexao->prepare($sql);
        $termoBusca = '%' . $termo . '%';
        $stmt->bindParam(':termo', $termoBusca);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar usuário por ID
    public static function buscarPorId($id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT id, nome_completo, email, foto_perfil FROM usuarios WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar usuário por email
    public static function buscarPorEmail($email)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar usuários (para busca)
    public static function buscar($termo)
    {
        $conexao = ConexaoBD::conectar();
        $termo = "%{$termo}%";

        $sql = "SELECT u.id, u.nome_completo, u.email, u.foto_perfil,
                (SELECT COUNT(*) FROM avaliacoes WHERE usuario_id = u.id) as total_avaliacoes,
                (SELECT COUNT(*) FROM seguidores WHERE seguindo_id = u.id) as total_seguidores
                FROM usuarios u
                WHERE u.nome_completo LIKE :termo OR u.email LIKE :termo
                ORDER BY u.nome_completo
                LIMIT 20";

        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':termo', $termo);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Listar todos os usuários
    public static function listar()
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT id, nome_completo, email, foto_perfil FROM usuarios";
        $stmt = $conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obter perfil completo com estatísticas
    public static function obterPerfil($id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT u.id, u.nome_completo, u.email, u.foto_perfil,
                (SELECT COUNT(*) FROM avaliacoes WHERE usuario_id = u.id) as total_avaliacoes,
                (SELECT COUNT(*) FROM seguidores WHERE seguindo_id = u.id) as total_seguidores,
                (SELECT COUNT(*) FROM seguidores WHERE seguidor_id = u.id) as total_seguindo
                FROM usuarios u
                WHERE u.id = :id";

        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Atualizar foto de perfil
    public static function atualizarFotoPerfil($usuario_id, $nomeFoto)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "UPDATE usuarios SET foto_perfil = :foto_perfil WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':foto_perfil', $nomeFoto);
        $stmt->bindParam(':id', $usuario_id);

        return $stmt->execute();
    }

    // Obter foto de perfil atual
    public static function obterFotoPerfil($usuario_id)
    {
        $conexao = ConexaoBD::conectar();

        $sql = "SELECT foto_perfil FROM usuarios WHERE id = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $usuario_id);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['foto_perfil'] : null;
    }
}
