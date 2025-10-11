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
        <div>
            <a href="#" id="openModal"><i>
                    <svg width="30" height="30" viewBox="0 0 276 275" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M217.898 244.3C217.898 223.056 209.459 202.683 194.438 187.661C179.416 172.639 159.042 164.2 137.798 164.2C116.555 164.2 96.1808 172.639 81.1591 187.661C66.1375 202.683 57.6984 223.056 57.6984 244.3" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M137.798 164.2C167.29 164.2 191.198 140.292 191.198 110.8C191.198 81.3081 167.29 57.4001 137.798 57.4001C108.306 57.4001 84.3983 81.3081 84.3983 110.8C84.3983 140.292 108.306 164.2 137.798 164.2Z" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M137.799 271C211.529 271 271.299 211.23 271.299 137.5C271.299 63.77 211.529 4 137.799 4C64.0686 4 4.29858 63.77 4.29858 137.5C4.29858 211.23 64.0686 271 137.799 271Z" stroke="#6A53B8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </i>
                Seu Perfil</a>
        </div>

    </header>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-login ">
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
                        <input type="email" placeholder="Digite seu e-mail" class="input-estilizado">
                        <input type="password" placeholder="Digite sua senha" class="input-estilizado">
                    </div>
                    <a href="">Não tem uma conta? <strong>Cadastre-se clicando aqui</strong></a>
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

        // Fechar o modalFD
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