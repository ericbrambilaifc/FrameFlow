<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['erro'] = "Você precisa estar logado para cadastrar séries!";
    header("Location: explorar.php");
    exit();
}

require_once('src/ClassificacaoDAO.php');
require_once('src/GeneroDAO.php');

$classificacoes = ClassificacaoDAO::listar();
$generos = GeneroDAO::listar();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="modal.css">
    <link rel="stylesheet" href="alert.css">
    <link rel="stylesheet" href="cadastrar_serie.css">
    <title>Cadastrar Série | FrameFlow</title>
</head>

<body>
    <a href="javascript:history.back()" class="btn-voltar">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Voltar
    </a>

    <div class="container-cadastro">
        <h1 class="titulo-pagina">Cadastrar Nova Série</h1>

        <form action="processar_cadastro_serie.php" method="POST" id="formCadastroSerie">
            <div class="form-group">
                <label for="titulo">Título da Série <span class="campo-obrigatorio">*</span></label>
                <input type="text" id="titulo" name="titulo" placeholder="Ex: Breaking Bad" required>
            </div>

            <div class="form-group">
                <label for="imagem_url">URL da Imagem <span class="campo-obrigatorio">*</span></label>
                <input type="url" id="imagem_url" name="imagem_url" placeholder="https://exemplo.com/imagem.jpg" required>
                <small class="texto-ajuda">
                    Cole o link direto da imagem (termina com .jpg, .png, etc)
                </small>
            </div>

            <div class="form-group">
                <label for="genero_id">Gênero <span class="campo-obrigatorio">*</span></label>
                <select id="genero_id" name="genero_id" required>
                    <option value="">Selecione um gênero</option>
                    <?php foreach ($generos as $genero): ?>
                        <option value="<?php echo $genero['id']; ?>">
                            <?php echo htmlspecialchars($genero['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="classificacao_id">Classificação Indicativa <span class="campo-obrigatorio">*</span></label>
                <select id="classificacao_id" name="classificacao_id" required>
                    <option value="">Selecione uma classificação</option>
                    <?php foreach ($classificacoes as $classificacao): ?>
                        <option value="<?php echo $classificacao['id']; ?>">
                            <?php echo htmlspecialchars($classificacao['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="preview-imagem" id="previewContainer">
                <p class="preview-titulo">Preview da Imagem:</p>
                <img id="previewImagem" src="" alt="Preview">
            </div>

            <div class="botoes-acao">
                <a href="explorar.php" class="botao-voltar">Cancelar</a>
                <button type="submit" class="botao-cadastrar">Cadastrar Série</button>
            </div>
        </form>
    </div>

    <script>
        // Preview da imagem
        document.getElementById('imagem_url').addEventListener('input', function() {
            const url = this.value;
            const previewContainer = document.getElementById('previewContainer');
            const previewImg = document.getElementById('previewImagem');

            if (url && (url.match(/\.(jpeg|jpg|gif|png|webp)$/i) || url.includes('imgur') || url.includes('tmdb'))) {
                previewImg.src = url;
                previewContainer.style.display = 'block';

                // Esconder se a imagem não carregar
                previewImg.onerror = function() {
                    previewContainer.style.display = 'none';
                };
            } else {
                previewContainer.style.display = 'none';
            }
        });

        // Validação do formulário
        document.getElementById('formCadastroSerie').addEventListener('submit', function(e) {
            const titulo = document.getElementById('titulo').value.trim();
            const imagemUrl = document.getElementById('imagem_url').value.trim();
            const genero = document.getElementById('genero_id').value;
            const classificacao = document.getElementById('classificacao_id').value;

            if (!titulo || !imagemUrl || !genero || !classificacao) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios!');
                return false;
            }

            if (!imagemUrl.startsWith('http')) {
                e.preventDefault();
                alert('A URL da imagem deve começar com http:// ou https://');
                return false;
            }
        });
    </script>
</body>

</html>