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
    <title>Cadastrar Série | FrameFlow</title>
    <style>
        .container-cadastro {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .titulo-pagina {
            color: #6A53B8;
            font-size: 28px;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #6A53B8;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #E5E5E5;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #6A53B8;
        }

        .preview-imagem {
            margin-top: 15px;
            text-align: center;
        }

        .preview-imagem img {
            max-width: 300px;
            max-height: 400px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .botoes-acao {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .botao-cadastrar {
            flex: 1;
            background: #6A53B8;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .botao-cadastrar:hover {
            background: #5a4398;
        }

        .botao-voltar {
            flex: 1;
            background: #E5E5E5;
            color: #666;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .botao-voltar:hover {
            background: #d5d5d5;
        }

        .campo-obrigatorio {
            color: #f44336;
        }
    </style>
</head>

<body>
    <header>
        <ul>
            <li><a href="explorar.php">Explorar</a></li>
            <li><a href="comunidade.php">Comunidade</a></li>
        </ul>
        <div>
            <a href="perfil.php?id=<?php echo $_SESSION['usuario_id']; ?>">
                <i>
                    <svg width="30" height="30" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M217.898 244.3C217.898 223.056 209.459 202.683 194.438 187.661C179.416 172.639 159.042 164.2 137.798 164.2C116.555 164.2 96.1808 172.639 81.1591 187.661C66.1375 202.683 57.6984 223.056 57.6984 244.3" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M137.798 164.2C167.29 164.2 191.198 140.292 191.198 110.8C191.198 81.3081 167.29 57.4001 137.798 57.4001C108.306 57.4001 84.3983 81.3081 84.3983 110.8C84.3983 140.292 108.306 164.2 137.798 164.2Z" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M137.799 271C211.529 271 271.299 211.23 271.299 137.5C271.299 63.77 211.529 4 137.799 4C64.0686 4 4.29858 63.77 4.29858 137.5C4.29858 211.23 64.0686 271 137.799 271Z" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </i>
                <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
            </a>
        </div>
    </header>

    <div class="container-cadastro">
        <h1 class="titulo-pagina">📺 Cadastrar Nova Série</h1>

        <form action="processar_cadastro_serie.php" method="POST" id="formCadastroSerie">
            <div class="form-group">
                <label for="titulo">Título da Série <span class="campo-obrigatorio">*</span></label>
                <input type="text" id="titulo" name="titulo" placeholder="Ex: Breaking Bad" required>
            </div>

            <div class="form-group">
                <label for="imagem_url">URL da Imagem <span class="campo-obrigatorio">*</span></label>
                <input type="url" id="imagem_url" name="imagem_url" placeholder="https://exemplo.com/imagem.jpg" required>
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
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

            <div class="preview-imagem" id="previewContainer" style="display: none;">
                <p style="color: #6A53B8; font-weight: 600; margin-bottom: 10px;">Preview da Imagem:</p>
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