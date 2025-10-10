<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="modal.css">
    <title>Explore e encontre as melhores avaliações</title>
</head>

<body>
    <header>
        <ul>
            <li><a href="explorar.php">Explorar</a></li>
            <li><a href="comunidade.php"></a>Comunidade</li>
        </ul>
        <a href="#" id="openModal">Fazer Login</a>
    </header>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="titulo">Fazer Login</h2>
            <form action="login.php" method="post">
                <div>
                    <div class="label-estilizado">
                        <input type="email" placeholder="Digite seu e-mail" class="input-estilizado">
                        <input type="password" placeholder="Digite sua senha" class="input-estilizado">
                    </div>
                    <a class="conta" href="">Não tem uma conta? <strong>Cadastre-se clicando aqui</strong></a>
                </div>

                <button class="botao-entrar" type="submit">Entrar</button>
            </form>
        </div>
    </div>



    <script>
        // Abrir o modal
        document.getElementById('openModal').addEventListener('click', function(event) {
            event.preventDefault(); // Impede o link de recarregar a página
            document.getElementById('modal').style.display = 'block';
        });

        // Fechar o modal
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('modal').style.display = 'none';
        });

        // Fechar o modal clicando fora
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('modal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>

</body>

</html>