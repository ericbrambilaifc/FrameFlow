<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap"
        rel="stylesheet">
    <title>Cadastro de Filmes e Séries</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: "Mona Sans", sans-serif;
            background: linear-gradient(293deg, #07182F 0%, #094492 100%);
        }

        .file-upload-area {
            border: 2px dashed #d1d5db;
            transition: all 0.3s ease;
        }

        .file-upload-area:hover {
            border-color: #3b82f6;
            background-color: #f8fafc;
        }

        .tab-button {
            transition: all 0.3s ease;
        }

        .tab-button.active {
            background-color: #1f2937;
            color: white;
        }

        .tab-button:not(.active) {
            background-color: transparent;
            color: #6b7280;
        }

        .tab-button:not(.active):hover {
            background-color: #f3f4f6;
            color: #374151;
        }

        .campo-serie {
            display: none;
        }

        .campo-serie.active {
            display: block;
        }

        .campo-filme.active {
            display: block;
        }

        .campo-filme {
            display: none;
        }
    </style>
</head>

<body class="bg-gray-200 min-h-screen">
    <?php
    require_once 'src/ConexaoBD.php';
    require_once 'src/CategoriaDAO.php';
    require_once 'src/ClassificacaoDAO.php';
    require_once 'src/FilmeDAO.php';
    require_once 'src/SerieDAO.php';

    $categorias = CategoriaDAO::listar();
    $classificacoes = ClassificacaoDAO::listar();

    if ($_POST) {
        $tipo = $_POST['tipo'];

        if ($tipo == 'filme') {
            FilmeDAO::inserir($_POST);
            echo "Filme cadastrado com sucesso!";
        } else {
            SerieDAO::inserir($_POST);
            echo "Série cadastrada com sucesso!";
        }
    }
    ?>

    <h3 class="text-3xl text-white font-semibold text-center my-12" id="titulo-principal">Faça o cadastro de seu filme
        agora!</h3>

    <div class="max-w-4xl mx-auto p-4">
        <div class="bg-gray-100 rounded-2xl p-8 shadow-xl">
            <div class="flex mb-8">
                <button class="tab-button active flex items-center space-x-2 px-4 py-2 rounded-lg" id="btn-filme"
                    onclick="alternarTipo('filme')">
                    <div class="w-2 h-2 bg-current rounded-full"></div>
                    <span class="text-sm">Filme</span>
                </button>
                <button class="tab-button flex items-center space-x-2 px-4 py-2 rounded-lg ml-4" id="btn-serie"
                    onclick="alternarTipo('serie')">
                    <div class="w-2 h-2 bg-current rounded-full"></div>
                    <span class="text-sm">Série</span>
                </button>
            </div>

            <form action="cadastro.php" method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <div class="space-y-4">
                        <div>
                            <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                            <input type="text" name="titulo" id="titulo" placeholder="Digite o nome do seu filme/série"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                required>
                        </div>

                        <div>
                            <label for="diretor" class="block text-sm font-medium text-gray-700 mb-1">Diretor</label>
                            <input type="text" name="diretor" id="diretor" placeholder="Digite o nome do diretor"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        </div>

                        <div>
                            <label for="elenco" class="block text-sm font-medium text-gray-700 mb-1">Elenco</label>
                            <input type="text" name="elenco" id="elenco" placeholder="Digite o nome do elenco"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="ano" class="block text-sm font-medium text-gray-700 mb-1">Ano</label>
                                <input type="number" name="ano" id="ano" placeholder="2024" min="1900" max="2030"
                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>
                            <div class="campo-filme">
                                <label for="oscar" class="block text-sm font-medium text-gray-700 mb-1">Oscars</label>
                                <input type="number" name="oscar" id="oscar" placeholder="0" min="0"
                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>
                        </div>

                        <!-- Campos específicos para séries -->
                        <div class="campo-serie">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="temporadas"
                                        class="block text-sm font-medium text-gray-700 mb-1">Temporadas</label>
                                    <input type="number" name="temporadas" id="temporadas" placeholder="1" min="1"
                                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>
                                <div>
                                    <label for="episodios"
                                        class="block text-sm font-medium text-gray-700 mb-1">Episódios</label>
                                    <input type="number" name="episodios" id="episodios" placeholder="10" min="1"
                                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="idcategoria"
                                    class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                                <select name="idcategoria" id="idcategoria"
                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    <option value="">Selecione a Categoria</option>
                                    <?php
                                    foreach ($categorias as $categoria) {
                                    ?>
                                        <option value="<?= $categoria['idcategoria'] ?>"><?= $categoria['nomecategoria'] ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label for="idclassificacao"
                                    class="block text-sm font-medium text-gray-700 mb-1">Classificação</label>
                                <select name="idclassificacao" id="idclassificacao"
                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    <option value="">Selecione a Classificação</option>
                                    <?php
                                    foreach ($classificacoes as $classificacao) {
                                    ?>
                                        <option value="<?= $classificacao['idclassificacao'] ?>">
                                            <?= $classificacao['nomeclassificacao'] ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="detalhes" class="block text-sm font-medium text-gray-700 mb-1"
                                id="label-detalhes">Detalhes/Sinopse</label>
                            <textarea name="detalhes" id="detalhes" placeholder="Digite os detalhes do seu filme/série"
                                rows="4"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"></textarea>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Imagem da Capa -->
                        <div>
                            <label for="imagem" class="block text-sm font-medium text-gray-700 mb-2">Inserir a imagem da capa</label>
                            <div class="file-upload-area bg-white rounded-lg p-8 text-center cursor-pointer"
                                onclick="document.getElementById('imagem').click()" id="upload-area-capa">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p class="text-gray-500 text-sm">Clique para fazer upload da capa</p>
                                    <p class="text-gray-400 text-xs mt-1">PNG, JPG, JPEG até 5MB</p>
                                </div>
                            </div>
                            <input type="file" name="imagem" id="imagem" class="hidden" accept="image/*">
                        </div>

                        <input type="hidden" name="tipo" value="filme" id="tipo-hidden">
                    </div>
                </div>
                

                <div class="mt-8 flex justify-center">
                    <button type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white px-12 py-3 rounded-lg transition-colors duration-200 font-medium"
                        id="btn-submit">
                        Cadastrar Filme
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function alternarTipo(tipo) {
            const btnFilme = document.getElementById('btn-filme');
            const btnSerie = document.getElementById('btn-serie');
            const camposFilme = document.querySelectorAll('.campo-filme');
            const camposSerie = document.querySelectorAll('.campo-serie');
            const tipoHidden = document.getElementById('tipo-hidden');
            const btnSubmit = document.getElementById('btn-submit');
            const tituloPrincipal = document.getElementById('titulo-principal');
            const placeholderTitulo = document.getElementById('titulo');
            const placeholderDetalhes = document.getElementById('detalhes');

            if (tipo === 'filme') {
                // Ativar botão filme
                btnFilme.classList.add('active');
                btnSerie.classList.remove('active');

                // Mostrar campos de filme, esconder de série
                camposFilme.forEach(campo => campo.classList.add('active'));
                camposSerie.forEach(campo => campo.classList.remove('active'));

                // Atualizar textos
                tipoHidden.value = 'filme';
                btnSubmit.textContent = 'Cadastrar Filme';
                tituloPrincipal.textContent = 'Faça o cadastro de seu filme agora!';
                placeholderTitulo.placeholder = 'Digite o nome do seu filme';
                placeholderDetalhes.placeholder = 'Digite os detalhes do seu filme';

                // Limpar campos específicos de série
                document.getElementById('temporadas').value = '';
                document.getElementById('episodios').value = '';

            } else if (tipo === 'serie') {
                // Ativar botão série
                btnSerie.classList.add('active');
                btnFilme.classList.remove('active');

                // Mostrar campos de série, esconder de filme
                camposSerie.forEach(campo => campo.classList.add('active'));
                camposFilme.forEach(campo => campo.classList.remove('active'));

                // Atualizar textos
                tipoHidden.value = 'serie';
                btnSubmit.textContent = 'Cadastrar Série';
                tituloPrincipal.textContent = 'Faça o cadastro de sua série agora!';
                placeholderTitulo.placeholder = 'Digite o nome da sua série';
                placeholderDetalhes.placeholder = 'Digite os detalhes da sua série';

                // Limpar campos específicos de filme
                document.getElementById('oscar').value = '';
            }
        }

        // Event listener para imagem da capa
        document.getElementById('imagem').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const uploadArea = document.getElementById('upload-area-capa');
                uploadArea.innerHTML = `
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-gray-700 text-sm font-medium">Capa: ${file.name}</p>
                        <p class="text-gray-500 text-xs mt-1">Arquivo selecionado</p>
                    </div>
                `;
            }
        });

        // Event listener para imagem do banner
        document.getElementById('imagemBanner').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const uploadArea = document.getElementById('upload-area-banner');
                uploadArea.innerHTML = `
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-gray-700 text-sm font-medium">Banner: ${file.name}</p>
                        <p class="text-gray-500 text-xs mt-1">Arquivo selecionado</p>
                    </div>
                `;
            }
        });

        // Inicializar como filme por padrão
        document.addEventListener('DOMContentLoaded', function() {
            alternarTipo('filme');
        });
    </script>
</body>

</html>