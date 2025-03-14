@extends('layouts.app')

@section('content')
<div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-10">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-2 tracking-tight">
                        Tableau de Bord Hospitalier
                        <span class="block mt-1 text-lg font-medium text-indigo-600">Vue d'ensemble</span>
                    </h1>
                    
                </div>
                <!-- Boutons d'actions -->
                <div class="flex space-x-4">
                    <a href="{{ route('admissions.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-all duration-200 ease-in-out transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nouvelle Admission
                    </a>
                    <button onclick="document.getElementById('excel-upload').click();" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-all duration-200 ease-in-out transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        Importer Excel
                    </button>
                </div>
            </div>

            @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
            @endif

            <form id="import-form" action="{{ route('admissions.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                @csrf
                <input id="excel-upload" type="file" name="excel_file" onchange="document.getElementById('import-form').submit();" accept=".xlsx,.xls,.csv" class="hidden">
            </form>
        </div>

     
        <!-- Cartes de Statistiques - Grid avec 4 cartes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
           <!-- Occupation des Lits -->
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </div>
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $litsOccupesJour }}</h2>
    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lits occupés aujourd'hui</p>
</div>

            <!-- Soins Intensifs -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-xl">
            <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            </div>
            <span class="px-3 py-1 text-sm font-semibold {{ $patientsIntensifsJour > 10 ? 'text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400' : 'text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-400' }} rounded-full">
            {{ $patientsIntensifsJour > 10 ? 'Critique' : 'Normal' }}
            </span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $patientsIntensifsJour }}</h2>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Patients en Soins Intensifs aujourd'hui</p>
            <div class="mt-4 flex items-center text-sm {{ $differenceJour > 0 ? 'text-red-600' : 'text-green-600' }}">
            <span class="font-medium">{{ $differenceJour > 0 ? '+' : '' }}{{ $differenceJour }} depuis ce matin</span>
            </div>
            </div>

            <!-- Total Admissions -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
            <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-xl">
            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            </div>
            <span class="px-3 py-1 text-sm font-semibold text-green-600 bg-green-50 dark:bg-green-900/20 dark:text-green-400 rounded-full">
            Aujourd'hui
            </span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $admissionsAujourdhui }}</h2>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nouvelles admissions aujourd'hui</p>
            </div>

            <!-- Temps d'Attente Moyen -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl">
            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            </div>
            <span class="px-3 py-1 text-sm font-semibold {{ $tempsAttenteMoyenJour > 30 ? 'text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400' : 'text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-400' }} rounded-full">
            {{ $tempsAttenteMoyenJour > 30 ? 'Long' : 'Normal' }}
            </span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $tempsAttenteMoyenJour }} min</h2>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Temps d'attente moyen aujourd'hui</p>
            </div>
        </div>

        <!-- Tableau des Admissions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Admissions Récentes</h3>
            <a href="{{ route('admissions.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Voir tout →</a>
            </div>
            <div class="overflow-x-auto">
            <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lit</th>
                <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date d'arrivée</th>
                <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Durée d'attente</th>
                <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Maladie</th>
                <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Service médical</th>
                <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($admissions->take(4) as $admission)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $admission->lit }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                @if(is_null($admission->date_arrivee))
                N/A
                @elseif(is_string($admission->date_arrivee))
                {{ \Carbon\Carbon::parse($admission->date_arrivee)->format('d/m/Y') }}
                @else
                {{ $admission->date_arrivee->format('d/m/Y') }}
                @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium {{ $admission->duree_attente > 30 ? 'text-red-600 bg-red-50' : 'text-yellow-600 bg-yellow-50' }} rounded-full">
                {{ $admission->duree_attente }} min
                </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $admission->maladie }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $admission->service_medical }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full
                @if($admission->type_admissions == 'Urgences')
                    text-red-600 bg-red-50
                @elseif($admission->type_admissions == 'Rendez-vous')
                    text-blue-600 bg-blue-50
                @else
                    text-gray-600 bg-gray-50
                @endif
                ">
                {{ $admission->type_admissions }}
                </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                Aucune admission récente trouvée
                </td>
            </tr>
            @endforelse
            </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection