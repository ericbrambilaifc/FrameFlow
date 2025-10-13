<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap"
        rel="stylesheet">
    <title>Login - Sistema</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<style>
    
</style>

<body class="min-h-screen flex items-center justify-center p-5">
    <div class="bg-gray-100 rounded-3xl p-10 w-full max-w-2xl shadow-2xl">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-2xl font-semibold text-gray-800">Escolha sua forma de acesso</h1>
        </div>

        <!-- Login Options -->
        <div class="flex flex-col md:flex-row gap-8 justify-center items-center mb-10">
            <!-- Admin Card -->

            <div id="adminCard"
                class="login-card bg-white rounded-2xl p-8 text-center cursor-pointer transition-all duration-300 border-2 border-transparent hover:shadow-xl hover:-translate-y-1 hover:border-blue-600 min-w-[200px] shadow-lg">
                <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-800 mb-2">Entrar como ADM</h3>
                <p class="text-sm text-gray-600">Acesso administrativo completo</p>
            </div>

            <!-- Client Card -->

            <div id="clientCard"
                class="login-card bg-white rounded-2xl p-8 text-center cursor-pointer transition-all duration-300 border-2 border-transparent hover:shadow-xl hover:-translate-y-1 hover:border-blue-600 min-w-[200px] shadow-lg">
                <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-800 mb-2">Entrar como Cliente</h3>
                <p class="text-sm text-gray-600">Acesso para usuários finais</p>
            </div>
        </div>

        <!-- Continue Button -->
        <div class="text-center">
            <button id="continueBtn"
                class="bg-slate-800 text-white px-10 py-4 rounded-full text-base font-medium hover:bg-blue-600 transition-all duration-300 hover:-translate-y-0.5 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:transform-none"
                disabled>
                Continuar
            </button>
        </div>
    </div>

    <script>
        const adminCard = document.getElementById('adminCard');
        const clientCard = document.getElementById('clientCard');
        const continueBtn = document.getElementById('continueBtn');
        let selectedOption = null;

        function selectCard(card, option) {
            // Remove selection from both cards
            adminCard.classList.remove('border-blue-600', 'bg-blue-50');
            clientCard.classList.remove('border-blue-600', 'bg-blue-50');

            // Add selection to clicked card
            card.classList.add('border-blue-600', 'bg-blue-50');

            selectedOption = option;
            continueBtn.disabled = false;
        }

        adminCard.addEventListener('click', () => {
            selectCard(adminCard, 'admin');
        });

        clientCard.addEventListener('click', () => {
            selectCard(clientCard, 'client');
        });

        continueBtn.addEventListener('click', () => {
            if (selectedOption === 'admin') {
                window.location.href = 'adicionarFilme.php';
            } else if (selectedOption === 'client') {
                window.location.href = 'newDesing.php';
            }
        });

    </script>
</body>

</html>