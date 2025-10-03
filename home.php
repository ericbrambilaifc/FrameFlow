<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap"
        rel="stylesheet">
    <title>Lumio OS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
    body {
        font-family: "Mona Sans", sans-serif;
    }
</style>

<body class="bg-gray-200 min-h-screen">
    <div class="max-w-6xl mx-auto p-4">
        <div class="bg-slate-800 rounded-lg p-4 mb-6 flex justify-between items-center">
            <h1 class="text-white text-2xl font-semibold">Filmes IFC+</h1>
        </div>

        <div class="flex flex-wrap gap-3 mb-6 items-center">
            <div class="relative">
                <input type="text" placeholder="Pesquise filmes, séries"
                    class="w-full bg-gradient-to-r from-[#07182F] to-[#174D95] text-white placeholder-gray-300 px-8 py-2 rounded-full text-[18px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-gradient-to-r focus:from-[#07182F] focus:to-[#174D95] transition-colors">
                <svg class="absolute right-3 top-2.5 h-6 w-6 text-gray-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <?php
            require_once 'src/FilmeDAO.php';
            require_once 'src/SerieDAO.php';

            $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'filme';


            if ($tipo === 'serie') {
                if (isset($_GET['classificacao'])) {
                    $idClassificacao = $_GET['classificacao'];
                    $itens = SerieDAO::listarPorClassificacao($idClassificacao);
                } elseif (isset($_GET['categoria'])) {
                    $idCategoria = $_GET['categoria'];
                    $itens = SerieDAO::listarPorCategoria($idCategoria);
                } else {
                    $itens = SerieDAO::listar();
                }
            } else {
                if (isset($_GET['classificacao'])) {
                    $idClassificacao = $_GET['classificacao'];
                    $itens = FilmeDAO::listarPorClassificacao($idClassificacao);
                } elseif (isset($_GET['categoria'])) {
                    $idCategoria = $_GET['categoria'];
                    $itens = FilmeDAO::listarPorCategoria($idCategoria);
                } else {
                    $itens = FilmeDAO::listar();
                }
            }
            ?>

            <select onchange="window.location.href=this.value"
                class="bg-gradient-to-r from-[#07182F] to-[#174D95] hover:opacity-90 text-white px-8 py-2 rounded-full text-[18px] transition-opacity whitespace-nowrap appearance-none text-center">
                <option value="home.php?tipo=filme" <?= $tipo === 'filme' ? 'selected' : '' ?>>Filme</option>
                <option value="home.php?tipo=serie" <?= $tipo === 'serie' ? 'selected' : '' ?>>Série</option>
            </select>



            <?php
            require_once 'src/FilmeDAO.php';
            require_once 'src/CategoriaDAO.php';
            $categorias = CategoriaDAO::listar();
            ?>

            <div class="relative inline-block text-left">
                <button type="button" id="dropdownButton"
                    class="bg-gradient-to-r from-[#07182F] to-[#174D95] text-white px-8 py-2 rounded-full text-[18px] transition-all whitespace-nowrap">
                    Genêro


                </button>



                <ul id="dropdownMenu" class="hidden absolute mt-2 w-40 bg-[#07182F] text-white rounded shadow-lg z-10">
                    <li>
                        <a href="home.php" class="block px-4 py-2 hover:bg-[#174D95]">
                            Todos
                        </a>
                    </li>
                    <?php foreach ($categorias as $categoria): ?>
                        <li>
                            <a href="home.php?categoria=<?= $categoria['idcategoria'] ?>"
                                class="block px-4 py-2 hover:bg-[#174D95]">
                                <?= $categoria['nomecategoria'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php
            require_once 'src/FilmeDAO.php';
            require_once 'src/ClassificacaoDAO.php';


            $classificacoes = ClassificacaoDAO::listar();
            ?>
            <div class="relative inline-block text-left">
                <button type="button" id="dropdownClassificacaoButton"
                    class="bg-gradient-to-r from-[#07182F] to-[#174D95] text-white px-8 py-2 rounded-full text-[18px] transition-all whitespace-nowrap ms-2">
                    Classificações
                </button>

                <ul id="dropdownClassificacaoMenu"
                    class="hidden absolute mt-2 w-full bg-[#07182F] text-white rounded shadow-lg z-10">


                    <li>
                        <a href="home.php" class="block px-4 py-2 hover:bg-[#174D95]">
                            Todos
                        </a>
                    </li>
                    <?php foreach ($classificacoes as $classificacao): ?>
                        <li>
                            <a href="home.php?classificacao=<?= $classificacao['idclassificacao'] ?>"
                                class="block px-4 py-2 hover:bg-[#174D95]">
                                <?= $classificacao['nomeclassificacao'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>



            <script>
                const classButton = document.getElementById('dropdownClassificacaoButton');
                const classMenu = document.getElementById('dropdownClassificacaoMenu');

                classButton.addEventListener('click', () => {
                    classMenu.classList.toggle('hidden');
                });

                // Fecha o menu ao clicar fora
                document.addEventListener('click', (e) => {
                    if (!classButton.contains(e.target) && !classMenu.contains(e.target)) {
                        classMenu.classList.add('hidden');
                    }
                });
            </script>

        </div>

        <script>
            const button = document.getElementById('dropdownButton');
            const menu = document.getElementById('dropdownMenu');

            button.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });

            // Fecha o menu ao clicar fora
            document.addEventListener('click', (e) => {
                if (!button.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        </script>


    </div>

    <?php
    require_once 'src/FilmeDAO.php';


    if (isset($_GET['classificacao'])) {
        $idClassificacao = $_GET['classificacao'];
        $filmes = FilmeDAO::listarPorClassificacao($idClassificacao);
    } elseif (isset($_GET['categoria'])) {
        $idCategoria = $_GET['categoria'];
        $filmes = FilmeDAO::listarPorCategoria($idCategoria);
    } else {
        $filmes = FilmeDAO::listar();
    }

    ?>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <?php foreach ($itens as $item): ?>
        <div class="bg-white rounded-lg shadow p-4">
            <h5 class="font-bold text-lg"><?= $item['titulo'] ?></h5>
            
            <!-- Mostrar Oscars apenas para filmes -->
            <?php if ($tipo === 'filme' && isset($item['oscar'])): ?>
                <p class="text-sm text-gray-500">Oscar: <?= $item['oscar'] ?></p>
            <?php endif; ?>
            
            <!-- Mostrar Temporadas/Episódios para séries -->
            <?php if ($tipo === 'serie'): ?>
                <?php if (isset($item['temporadas'])): ?>
                    <p class="text-sm text-gray-500">Temporadas: <?= $item['temporadas'] ?></p>
                <?php endif; ?>
                <?php if (isset($item['episodios'])): ?>
                    <p class="text-sm text-gray-500">Episódios: <?= $item['episodios'] ?></p>
                <?php endif; ?>
            <?php endif; ?>
            
            <img src="uploads/<?= $item['imagem'] ?>" alt="<?= $item['titulo'] ?>"
                class="w-full h-64 object-cover rounded-md my-2">
            <p class="text-sm"><?= $item['elenco'] ?? '' ?></p>
            <p class="text-sm">Ano: <?= $item['ano'] ?? '' ?></p>
            <p class="text-sm"><?= $item['detalhes'] ?? '' ?></p>
        </div>
    <?php endforeach; ?>
</div>




    <div class="relative bg-gray-50 py-8 group">
        <button id="prevBtn"
            class="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 bg-opacity-90 hover:bg-opacity-100 text-gray-700 p-3 rounded-full shadow-lg transition-all duration-300 opacity-0 group-hover:opacity-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>



        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const track = document.getElementById('carouselTrack');
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');

                const totalFilmes = <?= count($filmes) ?>;
                const itemWidth = 200; // 192px (w-48) + 16px margin
                let currentIndex = 0;
                let autoSlideInterval;

                function updateCarousel() {
                    const translateX = -currentIndex * itemWidth;
                    track.style.transform = `translateX(${translateX}px)`;
                }

                function nextSlide() {
                    currentIndex++;
                    if (currentIndex >= totalFilmes) {
                        track.style.transition = 'none';
                        currentIndex = 0;
                        updateCarousel();
                        requestAnimationFrame(() => {
                            track.style.transition = 'transform 0.5s ease-in-out';
                            currentIndex = 1;
                            updateCarousel();
                        });
                    } else {
                        updateCarousel();
                    }
                }

                function prevSlide() {
                    if (currentIndex === 0) {
                        track.style.transition = 'none';
                        currentIndex = totalFilmes;
                        updateCarousel();
                        requestAnimationFrame(() => {
                            track.style.transition = 'transform 0.5s ease-in-out';
                            currentIndex--;
                            updateCarousel();
                        });
                    } else {
                        currentIndex--;
                        updateCarousel();
                    }
                }

                // Event listeners
                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        stopAutoSlide();
                        nextSlide();
                        startAutoSlide();
                    });
                }
                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        stopAutoSlide();
                        prevSlide();
                        startAutoSlide();
                    });
                }

                // Pausar quando hover no carrossel
                const carousel = document.querySelector('.carousel-container') ? document.querySelector('.carousel-container').parentElement : null;
                if (carousel) {
                    carousel.addEventListener('mouseenter', stopAutoSlide);
                    carousel.addEventListener('mouseleave', startAutoSlide);
                }

                // Iniciar o auto slide se houver filmes
                if (totalFilmes > 0) {
                    startAutoSlide();
                }

            });
        </script>

        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .line-clamp-3 {
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }


            .carousel-item:hover {
                z-index: 30;
            }
        </style>
    </div>
</body>

</html>