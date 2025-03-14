@extends('layouts.app')

@section('content')
<div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-10 py-12">
        <!-- Header Section avec ombre portée et dégradé subtil -->
        <div class="mb-12 relative">
            <div class="absolute inset-0 bg-indigo-500/5 dark:bg-indigo-800/10 rounded-3xl blur-xl"></div>
            <div class="relative bg-white/80 dark:bg-gray-800/90 backdrop-blur-sm rounded-3xl shadow-xl p-8 border border-slate-200 dark:border-slate-700">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6">
                    <div>
                        <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-700 to-purple-700 dark:from-indigo-400 dark:to-purple-400 mb-2">
                            Prédictions Hospitalières
                            <span class="block mt-2 text-lg font-medium text-indigo-700 dark:text-indigo-400">Anticipez les besoins</span>
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Basé sur {{ $totalAdmissions }} admissions historiques
                        </p>
                    </div>

                    <!-- Bouton de prédiction avec effet de profondeur -->
                    <a href="{{ route('predictions.next15days') }}" class="group relative inline-flex items-center justify-center px-7 py-3.5 overflow-hidden rounded-full bg-gradient-to-br from-indigo-600 to-violet-600 text-white font-medium shadow-lg transition-all duration-300 ease-out hover:shadow-indigo-500/30 hover:scale-105">
                        <span class="absolute w-0 h-0 transition-all duration-300 ease-out bg-white rounded-full group-hover:w-full group-hover:h-full opacity-10"></span>
                        <svg class="w-5 h-5 mr-2 transition-transform duration-200 group-hover:-translate-y-px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="relative">Prédiction 15 prochains jours</span>
                    </a>
                </div>

                @if(session('message'))
                    <div class="mt-6 bg-indigo-50 dark:bg-indigo-900/30 border-l-4 border-indigo-500 text-indigo-700 dark:text-indigo-300 p-4 rounded-r-lg animate-fadeIn">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd"></path>
                            </svg>
                            {{ session('message') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Section "Comment fonctionnent nos prédictions" avec design en carte -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-10 mb-10 border border-slate-200 dark:border-slate-700 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-indigo-100 dark:bg-indigo-900/20 rounded-full blur-2xl opacity-70"></div>
            
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-8 relative">
                <span class="inline-block align-middle mr-3 w-1.5 h-8 bg-indigo-500 rounded-full"></span>
                Comment fonctionnent nos prédictions ?
            </h2>
            
            <div class="prose prose-slate dark:prose-invert max-w-none relative">
                <p class="text-lg text-slate-600 dark:text-slate-300">Notre système de prédiction analyse les données historiques des admissions pour vous aider à anticiper les besoins futurs de votre établissement.</p>
                
                <h3 class="text-xl font-semibold text-slate-800 dark:text-white mt-8 mb-4">Méthodologie</h3>
                <ul class="space-y-3 text-slate-600 dark:text-slate-300 list-none pl-0">
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 mr-3 flex-shrink-0 mt-0.5">1</span>
                        Analyse des données de la même période l'année précédente
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 mr-3 flex-shrink-0 mt-0.5">2</span>
                        Calcul des tendances par rapport aux périodes antérieures
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 mr-3 flex-shrink-0 mt-0.5">3</span>
                        Évaluation des besoins en lits et en soins intensifs
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 mr-3 flex-shrink-0 mt-0.5">4</span>
                        Identification des services les plus sollicités
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 mr-3 flex-shrink-0 mt-0.5">5</span>
                        Prévision des médicaments nécessaires
                    </li>
                </ul>
                
                <h3 class="text-xl font-semibold text-slate-800 dark:text-white mt-8 mb-4">Précision des résultats</h3>
                <p class="text-slate-600 dark:text-slate-300">La précision des prédictions dépend de la qualité et de la quantité des données historiques disponibles. Plus vous avez d'admissions enregistrées, plus les prévisions seront fiables.</p>
                
                <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 border border-blue-100 dark:border-blue-800/30 relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-20 h-20 bg-blue-200 dark:bg-blue-700/20 rounded-full blur-xl opacity-70"></div>
                    <p class="text-blue-800 dark:text-blue-300 font-medium flex items-start relative">
                        <svg class="w-6 h-6 mr-3 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd"></path>
                        </svg>
                        Conseil : Utilisez régulièrement l'outil de prédiction pour ajuster vos ressources et optimiser la prise en charge des patients.
                    </p>
                </div>
            </div>
        </div>

        <!-- Formulaire de prédiction personnalisée avec style moderne -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-10 mb-10 border border-slate-200 dark:border-slate-700 relative overflow-hidden">
            <div class="absolute -bottom-20 -left-20 w-60 h-60 bg-purple-100 dark:bg-purple-900/20 rounded-full blur-2xl opacity-50"></div>
            
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-8 relative">
                <span class="inline-block align-middle mr-3 w-1.5 h-8 bg-purple-500 rounded-full"></span>
                Prédiction personnalisée
            </h2>
            
            <form action="{{ route('predictions.calculate') }}" method="POST" class="space-y-8 relative">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="group">
                        <label for="start_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 transition-colors">Date de début</label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input type="date" name="start_date" id="start_date" required 
                                value="{{ old('start_date', date('Y-m-d')) }}"
                                class="block w-full pl-12 pr-4 py-4 border-2 border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-0 transition-colors">
                        </div>
                        @error('start_date')
                            <p class="mt-2 text-sm text-rose-600 dark:text-rose-400 flex items-center">
                                <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div class="group">
                        <label for="end_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 transition-colors">Date de fin</label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input type="date" name="end_date" id="end_date" required 
                                value="{{ old('end_date', date('Y-m-d', strtotime('+14 days'))) }}"
                                class="block w-full pl-12 pr-4 py-4 border-2 border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-0 transition-colors">
                        </div>
                        @error('end_date')
                            <p class="mt-2 text-sm text-rose-600 dark:text-rose-400 flex items-center">
                                <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="relative inline-flex items-center overflow-hidden px-8 py-4 bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 text-white font-medium rounded-xl transition-all duration-300 shadow-lg hover:shadow-indigo-500/30 group">
                        <span class="absolute top-0 left-0 w-full h-full bg-white/10 transform -translate-x-full group-hover:translate-x-0 transition-transform duration-300"></span>
                        <svg class="w-5 h-5 mr-2 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="relative">Analyser la période</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection