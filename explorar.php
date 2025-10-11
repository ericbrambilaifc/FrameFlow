<?php
session_start();
require_once('src/SerieDAO.php');
// Buscar séries para exibir
$series = SerieDao::listar();

// Busca de séries
$resultadoBusca = [];
if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
    $resultadoBusca = SerieDao::buscar($_GET['buscar']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="modal.css">
    <link rel="stylesheet" href="explorar.css">
    <title>FrameFlow | Opiniões que guiam suas próximas maratonas</title>
</head>

<body>
    <header>
        <ul>
            <li><a href="explorar.php">Explorar</a></li>
            <li><a href="comunidade.php">Comunidade</a></li>
        </ul>
        <div>
            <?php if (isset($_SESSION['usuario_id'])): ?>
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
                <a href="logout.php" style="margin-left: 15px; color: #6A53B8; text-decoration: underline;">Sair</a>
            <?php else: ?>
                <a href="#" id="openModal">
                    <i>
                        <svg width="30" height="30" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M217.898 244.3C217.898 223.056 209.459 202.683 194.438 187.661C179.416 172.639 159.042 164.2 137.798 164.2C116.555 164.2 96.1808 172.639 81.1591 187.661C66.1375 202.683 57.6984 223.056 57.6984 244.3" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M137.798 164.2C167.29 164.2 191.198 140.292 191.198 110.8C191.198 81.3081 167.29 57.4001 137.798 57.4001C108.306 57.4001 84.3983 81.3081 84.3983 110.8C84.3983 140.292 108.306 164.2 137.798 164.2Z" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M137.799 271C211.529 271 271.299 211.23 271.299 137.5C271.299 63.77 211.529 4 137.799 4C64.0686 4 4.29858 63.77 4.29858 137.5C4.29858 211.23 64.0686 271 137.799 271Z" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </i>
                    Seu Perfil
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Mensagens de sucesso/erro -->
    <?php if (isset($_SESSION['sucesso'])): ?>
        <div class="mensagem sucesso" style="background: #4caf50; color: white; padding: 15px; margin: 20px; border-radius: 5px; text-align: center;">
            <?php
            echo htmlspecialchars($_SESSION['sucesso']);
            unset($_SESSION['sucesso']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="mensagem erro" style="background: #f44336; color: white; padding: 15px; margin: 20px; border-radius: 5px; text-align: center;">
            <?php
            echo htmlspecialchars($_SESSION['erro']);
            unset($_SESSION['erro']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Barra de busca de séries -->
    <section style="padding: 20px; max-width: 1200px; margin: 0 auto;">
        <form method="GET" action="explorar.php" style="margin-bottom: 30px;">
            <input
                type="text"
                name="buscar"
                placeholder="Buscar série por título..."
                class="input-estilizado"
                value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>"
                style="width: 100%; padding: 12px; font-size: 16px;">
            <button type="submit" class="botao-entrar" style="margin-top: 10px;">Buscar</button>
        </form>

        <!-- Resultados da busca -->
        <?php if (isset($_GET['buscar'])): ?>
            <h2 style="color: #6A53B8; margin-bottom: 20px;">
                Resultados para: "<?php echo htmlspecialchars($_GET['buscar']); ?>"
            </h2>
            <?php if (count($resultadoBusca) > 0): ?>
                <div class="grid-series">
                    <?php foreach ($resultadoBusca as $serie): ?>
                        <div class="card-serie">
                            <img src="<?php echo htmlspecialchars($serie['imagem_url']); ?>" alt="<?php echo htmlspecialchars($serie['titulo']); ?>">
                            <h3><?php echo htmlspecialchars($serie['titulo']); ?></h3>
                            <p>Avaliações: <?php echo $serie['total_avaliacoes']; ?></p>
                            <p>Nota média: <?php echo number_format($serie['media_nota'], 1); ?>/10</p>
                            <a href="serie.php?id=<?php echo $serie['id']; ?>" class="botao-entrar">Ver detalhes</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666;">Nenhuma série encontrada.</p>
            <?php endif; ?>
        <?php else: ?>
            <!-- Exibir todas as séries -->
            <h2 style="color: #6A53B8; margin-bottom: 20px;">Todas as Séries</h2>
            <?php if (count($series) > 0): ?>
                <div class="grid-series">
                    <?php foreach ($series as $serie): ?>
                        <div class="card-serie">
                            <img src="<?php echo htmlspecialchars($serie['imagem_url']); ?>" alt="<?php echo htmlspecialchars($serie['titulo']); ?>">
                            <h3><?php echo htmlspecialchars($serie['titulo']); ?></h3>
                            <p>Avaliações: <?php echo $serie['total_avaliacoes']; ?></p>
                            <p>Nota média: <?php echo number_format($serie['media_nota'], 1); ?>/10</p>
                            <a href="serie.php?id=<?php echo $serie['id']; ?>" class="botao-entrar">Ver detalhes</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666;">Nenhuma série cadastrada ainda.</p>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <!-- Modal fazer login -->
    <div id="modal" class="modal">
        <div class="modal-login">
            <button class="close">
                <svg width="24" height="24" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M137.798 271C211.528 271 271.298 211.23 271.298 137.5C271.298 63.77 211.528 4 137.798 4C64.0683 4 4.29834 63.77 4.29834 137.5C4.29834 211.23 64.0683 271 137.798 271Z" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M177.848 97.4497L97.7479 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M97.7479 97.4497L177.848 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <h2 class="titulo">Fazer Login</h2>
            <form action="login.php" method="post">
                <div class="form-grupo">
                    <div class="label-estilizado">
                        <input type="email" name="email" placeholder="Digite seu e-mail" class="input-estilizado" required>
                        <input type="password" name="senha" placeholder="Digite sua senha" class="input-estilizado" required>
                    </div>
                    <a href="#" id="abrirCriarConta">Não tem uma conta? <strong>Cadastre-se clicando aqui</strong></a>
                </div>

                <button class="botao-entrar" type="submit">Entrar</button>
            </form>
        </div>
    </div>

    <!-- Modal criar conta -->
    <div id="modalCriarConta" class="modal">
        <div class="modal-login">
            <button class="close" id="closeCriar">
                <svg width="24" height="24" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M137.798 271C211.528 271 271.298 211.23 271.298 137.5C271.298 63.77 211.528 4 137.798 4C64.0683 4 4.29834 63.77 4.29834 137.5C4.29834 211.23 64.0683 271 137.798 271Z" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M177.848 97.4497L97.7479 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M97.7479 97.4497L177.848 177.55" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <h2 class="titulo">Criar Conta</h2>
            <form action="cadastro.php" method="post" id="formCadastro">
                <div class="form-grupo">
                    <div class="label-estilizado">
                        <input type="email" name="email" placeholder="Digite seu e-mail" class="input-estilizado" required>
                        <p class="texto-ajuda">*Após criar sua conta, não será possível alterar o endereço de e-mail</p>

                        <input type="text" name="nome_completo" placeholder="Digite seu nome completo" class="input-estilizado" required>

                        <input type="password" name="senha" id="senha" placeholder="Crie sua senha" class="input-estilizado" required>

                        <input type="password" name="confirmar_senha" id="confirmar_senha" placeholder="Repita sua senha" class="input-estilizado" required>
                    </div>
                </div>

                <button class="botao-entrar" type="submit">Criar conta</button>
            </form>
        </div>
    </div>

    <script>
        // Abrir o modal de LOGIN
        document.getElementById('openModal').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('modal').style.display = 'block';
        });

        // Abrir o modal de CRIAR CONTA
        document.getElementById('abrirCriarConta').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('modal').style.display = 'none';
            document.getElementById('modalCriarConta').style.display = 'block';
        });

        // Fechar o modal de LOGIN
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('modal').style.display = 'none';
        });

        // Fechar o modal de CRIAR CONTA
        document.getElementById('closeCriar').addEventListener('click', function() {
            document.getElementById('modalCriarConta').style.display = 'none';
        });

        // Fechar modais clicando fora
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('modal');
            const modalCriarConta = document.getElementById('modalCriarConta');

            if (event.target === modal) {
                modal.style.display = 'none';
            }
            if (event.target === modalCriarConta) {
                modalCriarConta.style.display = 'none';
            }
        });

        // Validar senhas no cadastro
        document.getElementById('formCadastro').addEventListener('submit', function(event) {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;

            if (senha !== confirmarSenha) {
                event.preventDefault();
                alert('As senhas não coincidem!');
                return false;
            }

            if (senha.length < 6) {
                event.preventDefault();
                alert('A senha deve ter no mínimo 6 caracteres!');
                return false;
            }
        });

        // Auto-fechar mensagens após 5 segundos
        setTimeout(function() {
            const mensagens = document.querySelectorAll('.mensagem');
            mensagens.forEach(function(mensagem) {
                mensagem.style.display = 'none';
            });
        }, 5000);
    </script>

    <style>
        .grid-series {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card-serie {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card-serie img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .card-serie h3 {
            color: #6A53B8;
            margin: 10px 0;
            font-size: 18px;
        }

        .card-serie p {
            color: #666;
            margin: 5px 0;
            font-size: 14px;
        }

        .card-serie .botao-entrar {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 20px;
            font-size: 14px;
            text-decoration: none;
        }
    </style>
</body>

</html>