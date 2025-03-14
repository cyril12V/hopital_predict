@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admissions.index') }}" class="mr-3 text-gray-500 hover:text-blue-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Nouvelle Admission</h1>
    </div>

    <!-- Affichage des erreurs de validation globales -->
    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700 font-medium">
                    Il y a des problèmes avec votre formulaire. Veuillez corriger les erreurs ci-dessous.
                </p>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admissions.store') }}" method="POST">
            @csrf
            
            <!-- Informations patient -->
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4 pb-2 border-b">Informations du patient</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="numero_patient" class="block text-sm font-medium text-gray-700 mb-1">Numéro Patient</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="numero_patient" id="numero_patient" value="{{ old('numero_patient') }}" class="pl-10 w-full px-4 py-2 border {{ $errors->has('numero_patient') ? 'border-red-500' : 'border-gray-300' }} rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Format: PAT-XXXXX</p>
                        @error('numero_patient')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="lit" class="block text-sm font-medium text-gray-700 mb-1">Lit</label>
                        <select name="lit" id="lit" class="w-full px-4 py-2 border {{ $errors->has('lit') ? 'border-red-500' : 'border-gray-300' }} rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Sélectionner un lit</option>
                            <option value="oui" {{ old('lit') == 'oui' ? 'selected' : '' }}>Oui</option>
                            <option value="non" {{ old('lit') == 'non' ? 'selected' : '' }}>Non</option>
                        </select>
                        @error('lit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Dates et durées -->
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4 pb-2 border-b">Dates et durées</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="date_arrivee" class="block text-sm font-medium text-gray-700 mb-1">Date d'arrivée</label>
                        <input type="date" name="date_arrivee" id="date_arrivee" value="{{ old('date_arrivee') }}" class="w-full px-4 py-2 border {{ $errors->has('date_arrivee') ? 'border-red-500' : 'border-gray-300' }} rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('date_arrivee')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="date_depart" class="block text-sm font-medium text-gray-700 mb-1">Date de départ (prévisionnelle)</label>
                        <input type="date" name="date_depart" id="date_depart" value="{{ old('date_depart') }}" class="w-full px-4 py-2 border {{ $errors->has('date_depart') ? 'border-red-500' : 'border-gray-300' }} rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Laissez vide si non applicable</p>
                        @error('date_depart')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="duree_attente" class="block text-sm font-medium text-gray-700 mb-1">Durée d'attente (minutes)</label>
                        <input type="number" name="duree_attente" id="duree_attente" value="{{ old('duree_attente') }}" min="0" class="w-full px-4 py-2 border {{ $errors->has('duree_attente') ? 'border-red-500' : 'border-gray-300' }} rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('duree_attente')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="taux_occupation" class="block text-sm font-medium text-gray-700 mb-1">Taux d'occupation (%)</label>
                        <div class="relative">
                            <input type="range" name="taux_occupation" id="taux_occupation" min="0" max="100" step="1" value="{{ old('taux_occupation', 85) }}" 
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer {{ $errors->has('taux_occupation') ? 'border-red-500' : '' }}"
                                oninput="rangeValue.innerText = this.value + '%'">
                            <span id="rangeValue" class="absolute right-0 -top-6 text-sm font-medium text-gray-700">{{ old('taux_occupation', 85) }}%</span>
                        </div>
                        @error('taux_occupation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Informations médicales -->
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4 pb-2 border-b">Informations médicales</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="maladie" class="block text-sm font-medium text-gray-700 mb-1">Diagnostic / Maladie</label>
                        <input type="text" name="maladie" id="maladie" value="{{ old('maladie') }}" class="w-full px-4 py-2 border {{ $errors->has('maladie') ? 'border-red-500' : 'border-gray-300' }} rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('maladie')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="service_medical" class="block text-sm font-medium text-gray-700 mb-1">Service médical</label>
                        <select name="service_medical" id="service_medical" class="w-full px-4 py-2 border {{ $errors->has('service_medical') ? 'border-red-500' : 'border-gray-300' }} rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Sélectionner un service</option>
                            <option value="Cardiologie" {{ old('service_medical') == 'Cardiologie' ? 'selected' : '' }}>Cardiologie</option>
                            <option value="Medecine interne" {{ old('service_medical') == 'Medecine interne' ? 'selected' : '' }}>Médecine interne</option>
                            <option value="Pneumologie" {{ old('service_medical') == 'Pneumologie' ? 'selected' : '' }}>Pneumologie</option>
                            <option value="Neurologie" {{ old('service_medical') == 'Neurologie' ? 'selected' : '' }}>Neurologie</option>
                            <option value="Reanimation" {{ old('service_medical') == 'Reanimation' ? 'selected' : '' }}>Reanimation</option>
                        </select>
                        @error('service_medical')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="type_admissions" class="block text-sm font-medium text-gray-700 mb-1">Type d'admission</label>
                        <select name="type_admissions" id="type_admissions" class="w-full px-4 py-2 border {{ $errors->has('type_admissions') ? 'border-red-500' : 'border-gray-300' }} rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Sélectionner un type</option>
                            <option value="Urgences" {{ old('type_admissions') == 'Urgences' ? 'selected' : '' }}>Urgences</option>
                            <option value="Rendez-vous" {{ old('type_admissions') == 'Rendez-vous' ? 'selected' : '' }}>Rendez-vous</option>
                        </select>
                        @error('type_admissions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="medicaments" class="block text-sm font-medium text-gray-700 mb-1">Médicaments</label>
                        <textarea name="medicaments" id="medicaments" rows="3" class="w-full px-4 py-2 border {{ $errors->has('medicaments') ? 'border-red-500' : 'border-gray-300' }} rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Entrez les médicaments prescrits, séparés par des virgules">{{ old('medicaments') }}</textarea>
                        @error('medicaments')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nécessite des soins intensifs</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="soins_intensif" value="oui" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" {{ old('soins_intensif', $admission->soins_intensif ?? '') == 'oui' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Oui</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="soins_intensif" value="non" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" {{ old('soins_intensif', $admission->soins_intensif ?? '') == 'non' || old('soins_intensif', $admission->soins_intensif ?? '') === '' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Non</span>
                            </label>
                        </div>
                        @error('soins_intensif')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admissions.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Annuler
                </a>
                <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Enregistrer l'admission
                </button>
            </div>
        </form>
    </div>
</div>
@endsection