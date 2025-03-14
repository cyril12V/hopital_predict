@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Header avec animation subtile -->
        <div class="mb-8 transform transition duration-500 hover:translate-x-1">
            <div class="flex items-center">
                <a href="{{ route('predictions.index') }}" class="group mr-4">
                    <div class="p-2 rounded-full bg-white dark:bg-gray-800 shadow-sm transition-all duration-300 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </div>
                </a>
                <div>
                    <h1 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-blue-500 dark:from-indigo-400 dark:to-blue-300">Résultats de Prédiction</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</span>
                        
                        @if(isset($predictions['confidenceScore']))
                            <span class="ml-3 px-3 py-0.5 rounded-full text-xs font-bold {{ $predictions['confidenceScore'] > 75 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : ($predictions['confidenceScore'] > 50 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400') }}">
                                Fiabilité: {{ $predictions['confidenceScore'] }}%
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        @if (!$hasData)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <div class="p-6 border-l-4 border-yellow-400">
                    <div class="flex">
                        <div class="flex-shrink-0 bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded-full">
                            <svg class="h-6 w-6 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-yellow-700 dark:text-yellow-400 font-medium">
                                {{ $message }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center py-12 bg-gray-50 dark:bg-gray-900/50">
                    <a href="{{ route('predictions.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent shadow-md text-sm font-medium rounded-full text-white bg-gradient-to-r from-indigo-600 to-blue-500 hover:from-indigo-700 hover:to-blue-600 transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring focus:ring-indigo-300 dark:focus:ring-indigo-800">
                        <svg class="-ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        Retour aux prédictions
                    </a>
                </div>
            </div>
        @else
            <!-- Section résumé avec card design amélioré -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl mb-8 overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1">
                <div class="p-6 border-l-4 
                    @if($predictions['criticality'] == 'Critique') 
                        border-red-500
                    @elseif($predictions['criticality'] == 'Élevé') 
                        border-orange-500
                    @elseif($predictions['criticality'] == 'Modéré') 
                        border-yellow-500
                    @else 
                        border-green-500
                    @endif
                ">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold bg-clip-text text-transparent 
                                @if($predictions['criticality'] == 'Critique') 
                                    bg-gradient-to-r from-red-600 to-pink-500 dark:from-red-400 dark:to-pink-300
                                @elseif($predictions['criticality'] == 'Élevé') 
                                    bg-gradient-to-r from-orange-600 to-amber-500 dark:from-orange-400 dark:to-amber-300
                                @elseif($predictions['criticality'] == 'Modéré') 
                                    bg-gradient-to-r from-yellow-600 to-amber-500 dark:from-yellow-400 dark:to-amber-300
                                @else 
                                    bg-gradient-to-r from-green-600 to-emerald-500 dark:from-green-400 dark:to-emerald-300
                                @endif
                            ">Niveau d'alerte: {{ $predictions['criticality'] }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Basé sur {{ $historicalAdmissions }} admissions historiques (même période l'année dernière)
                            </p>
                        </div>
                        <div class="
                            @if($predictions['criticality'] == 'Critique') 
                                bg-gradient-to-r from-red-600 to-pink-500 dark:from-red-500 dark:to-pink-400
                            @elseif($predictions['criticality'] == 'Élevé') 
                                bg-gradient-to-r from-orange-600 to-amber-500 dark:from-orange-500 dark:to-amber-400
                            @elseif($predictions['criticality'] == 'Modéré') 
                                bg-gradient-to-r from-yellow-600 to-amber-500 dark:from-yellow-500 dark:to-amber-400
                            @else 
                                bg-gradient-to-r from-green-600 to-emerald-500 dark:from-green-500 dark:to-emerald-400
                            @endif
                            text-white rounded-full px-6 py-2 text-sm font-bold shadow-md transform transition-transform duration-300 hover:scale-105">
                            {{ $predictions['criticality'] }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section statistiques principales avec cartes interactives améliorées -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Prédiction d'admissions -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-2xl shadow-sm">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold 
                                @if($predictions['tendance'] > 10) 
                                    text-white bg-gradient-to-r from-red-500 to-pink-500
                                @elseif($predictions['tendance'] > 0) 
                                    text-white bg-gradient-to-r from-yellow-500 to-amber-500
                                @else 
                                    text-white bg-gradient-to-r from-green-500 to-emerald-500
                                @endif
                                rounded-full shadow-sm">
                                {{ $predictions['tendance'] > 0 ? '+' : '' }}{{ number_format($predictions['tendance'], 1) }}% vs mois précédent
                            </span>
                        </div>
                        <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $predictions['admissionsPrevisionnelles'] }}</h3>
                        <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Admissions prévisionnelles</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">({{ $predictions['admissionsQuotidiennes'] }} par jour)</p>
                    </div>
                </div>

                <!-- Besoins en Lits -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-2xl shadow-sm">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $predictions['litsNecessaires'] }}</h3>
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Lits nécessaires par jour</p>
                    </div>
                </div>

                <!-- Soins Intensifs -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 rounded-2xl shadow-sm">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $predictions['soinsIntensifsNecessaires'] }}</h3>
                        <p class="text-sm font-medium text-red-600 dark:text-red-400">Soins intensifs par jour</p>
                    </div>
                </div>

                <!-- Temps d'Attente -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 rounded-2xl shadow-sm">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold 
                                @if($predictions['tempsAttenteMoyenPrevu'] > 30) 
                                    text-white bg-gradient-to-r from-red-500 to-pink-500
                                @elseif($predictions['tempsAttenteMoyenPrevu'] > 15) 
                                    text-white bg-gradient-to-r from-yellow-500 to-amber-500
                                @else 
                                    text-white bg-gradient-to-r from-green-500 to-emerald-500
                                @endif
                                rounded-full shadow-sm">
                                {{ $predictions['tempsAttenteMoyenPrevu'] > 30 ? 'Long' : ($predictions['tempsAttenteMoyenPrevu'] > 15 ? 'Moyen' : 'Court') }}
                            </span>
                        </div>
                        <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $predictions['tempsAttenteMoyenPrevu'] }} <span class="text-xl font-bold text-gray-600 dark:text-gray-400">min</span></h3>
                        <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Temps d'attente moyen</p>
                    </div>
                </div>
            </div>

            <!-- Services, Maladies et Médicaments avec cartes interactives -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Services -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="p-2 mr-3 bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Services les plus sollicités</h3>
                        </div>
                        <ul class="space-y-3">
                            @foreach($predictions['topServices'] as $service => $count)
                            <li class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl transition-all duration-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                                <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $service }}</span>
                                <span class="px-3 py-1 bg-gradient-to-r from-indigo-500 to-blue-500 text-white text-xs font-bold rounded-full shadow-sm">{{ $count }} patients</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Maladies -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="p-2 mr-3 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Maladies les plus fréquentes</h3>
                        </div>
                        <ul class="space-y-3">
                            @foreach($predictions['topMaladies'] as $maladie => $count)
                            <li class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                <span class="text-gray-700 dark:text-gray-300 font-medium truncate pr-2">{{ $maladie }}</span>
                                <span class="px-3 py-1 bg-gradient-to-r from-blue-500 to-cyan-500 text-white text-xs font-bold rounded-full shadow-sm shrink-0">{{ $count }} cas</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Médicaments -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="p-2 mr-3 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Médicaments à prévoir</h3>
                        </div>
                        <ul class="space-y-3">
                            @foreach($predictions['topMedicaments'] as $medicament => $count)
                            <li class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl transition-all duration-200 hover:bg-green-50 dark:hover:bg-green-900/20">
                                <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $medicament }}</span>
                                <span class="px-3 py-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold rounded-full shadow-sm">{{ $count }} doses</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recommandations -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="p-3 mr-3 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl shadow-sm">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Recommandations</h3>
                    </div>
                    
                    @if(count($predictions['recommendations']) > 0)
                        <ul class="space-y-4">
                            @foreach($predictions['recommendations'] as $recommendation)
                                <li class="flex items-start p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl transition-all duration-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-full mr-3">
                                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $recommendation }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center">
                            <svg class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-400 font-medium">Aucune recommandation particulière pour cette période.</p>
                        </div>
                    @endif
                    
                    <div class="mt-8 flex justify-center">
                        <a href="{{ route('predictions.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent shadow-md text-sm font-bold rounded-full text-white bg-gradient-to-r from-indigo-600 to-blue-500 hover:from-indigo-700 hover:to-blue-600 transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring focus:ring-indigo-300 dark:focus:ring-indigo-800">
                            <svg class="-ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                            </svg>
                            Retour aux prédictions
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection