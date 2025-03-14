<!-- filepath: /c:/Users/mario/Desktop/projet_ecole2/hopital-predict/resources/views/layouts/sidebar.blade.php -->
<div class="flex min-h-screen">
    <div class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 min-h-screen fixed">
        <!-- Logo -->
        <div class="p-6">
            <a href="/" class="block">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <span class="ml-3 text-xl font-bold text-gray-900 dark:text-white">HôpitalPredict</span>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <div class="px-3">
            <div class="space-y-4">
                <!-- Menu Principal -->
                <div>
                    <div class="mt-2 space-y-1">
                        <a href="/dashboard" class="{{ Route::is('dashboard') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Tableau de bord
                        </a>

                        <a href="/admissions" class="{{ Route::is('admissions.*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Admissions
                        </a>

                      
                        <a href="/predictions" class="{{ Route::is('predictions') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5" />
                            </svg>
                            Prédiction
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal - ajoutez cette partie si elle n'existe pas déjà dans votre layout -->
    <div class="ml-64 flex-grow">
        {{ $slot ?? '' }}
    </div>
</div>