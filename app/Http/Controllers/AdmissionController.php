<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Imports\AdmissionsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Admission::query();
    
        // Recherche par numéro patient ou maladie
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_patient', 'LIKE', "%{$search}%")
                  ->orWhere('maladie', 'LIKE', "%{$search}%");
            });
        }
        
        // Filtre par service médical
        if ($request->filled('service_medical')) {
            $query->where('service_medical', $request->service_medical);
        }
        
        // Filtre par type d'admission
        if ($request->filled('type_admissions')) {
            $query->where('type_admissions', $request->type_admissions);
        }
        
        // Récupérer les admissions avec pagination
        $admissions = $query->orderBy('date_arrivee', 'desc')->paginate(5);
        
        // Récupérer les valeurs distinctes pour les filtres
        $services = Admission::select('service_medical')->distinct()->pluck('service_medical')->toArray();
        $types = Admission::select('type_admissions')->distinct()->pluck('type_admissions')->toArray();
        
        // Passer toutes les variables à la vue
        return view('admissions.index', compact('admissions', 'services', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'numero_patient' => 'required|string|max:255',
'lit' => 'required|string|in:oui,non',
'soins_intensif' => 'required|string|in:oui,non',                'date_arrivee' => 'required|date',
                'date_depart' => 'nullable|date|after_or_equal:date_arrivee',
                'duree_attente' => 'nullable|integer|min:0',
                'taux_occupation' => 'nullable|integer|min:0|max:100',
                'maladie' => 'required|string|max:255',
                'medicaments' => 'nullable|string',
                'type_admissions' => 'required|string|in:Urgences,Rendez-vous',
                'service_medical' => 'required|string|in:Cardiologie,Medecine interne,Pneumologie,Neurologie,Reanimation',
            ], [
                'numero_patient.required' => 'Le numéro du patient est obligatoire.',
                'lit.required' => 'Veuillez indiquer si un lit est attribué.',
                'lit.in' => 'La valeur du lit doit être "oui" ou "non".',
                'date_arrivee.required' => 'La date d\'arrivée est obligatoire.',
                'date_arrivee.date' => 'Le format de la date d\'arrivée est invalide.',
                'date_depart.date' => 'Le format de la date de départ est invalide.',
                'date_depart.after_or_equal' => 'La date de départ doit être égale ou postérieure à la date d\'arrivée.',
                'duree_attente.integer' => 'La durée d\'attente doit être un nombre entier.',
                'duree_attente.min' => 'La durée d\'attente ne peut pas être négative.',
                'taux_occupation.integer' => 'Le taux d\'occupation doit être un nombre entier.',
                'taux_occupation.min' => 'Le taux d\'occupation ne peut pas être négatif.',
                'taux_occupation.max' => 'Le taux d\'occupation ne peut pas dépasser 100%.',
                'maladie.required' => 'Le diagnostic ou la maladie est obligatoire.',
                'soins_intensif.required' => 'Veuillez indiquer si des soins intensifs sont nécessaires.',
                'soins_intensif.boolean' => 'La valeur pour les soins intensifs doit être Oui ou Non.',
                'type_admissions.required' => 'Le type d\'admission est obligatoire.',
                'type_admissions.in' => 'Le type d\'admission doit être "Urgences" ou "Rendez-vous".',
                'service_medical.required' => 'Le service médical est obligatoire.',
                'service_medical.in' => 'Veuillez sélectionner un service médical valide.',
            ]);

            // Formatage des dates si nécessaire
            if (isset($validated['date_arrivee'])) {
                $validated['date_arrivee'] = Carbon::parse($validated['date_arrivee']);
            }
            
            if (isset($validated['date_depart']) && !empty($validated['date_depart'])) {
                $validated['date_depart'] = Carbon::parse($validated['date_depart']);
            }

            Admission::create($validated);

            return redirect()->route('admissions.index')
                ->with('success', 'Admission créée avec succès');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la création de l\'admission: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admission  $admission
     * @return \Illuminate\Http\Response
     */
    public function show(Admission $admission)
    {
        return view('admissions.show', compact('admission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admission  $admission
     * @return \Illuminate\Http\Response
     */
    public function edit(Admission $admission)
    {
        return view('admissions.edit', compact('admission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admission  $admission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admission $admission)
    {
        try {
            $validated = $request->validate([
                'numero_patient' => 'required|string|max:255',
'lit' => 'required|string|in:oui,non',
'soins_intensif' => 'required|string|in:oui,non',               'date_arrivee' => 'required|date',
                'date_depart' => 'nullable|date|after_or_equal:date_arrivee',
                'duree_attente' => 'nullable|integer|min:0',
                'taux_occupation' => 'nullable|integer|min:0|max:100',
                'maladie' => 'required|string|max:255',
                'medicaments' => 'nullable|string',
                'type_admissions' => 'required|string|in:Urgences,Rendez-vous',
                'service_medical' => 'required|string|in:Cardiologie,Medecine interne,Pneumologie,Neurologie,Reanimation',
            ], [
                'numero_patient.required' => 'Le numéro du patient est obligatoire.',
                'lit.required' => 'Veuillez indiquer si un lit est attribué.',
                'lit.in' => 'La valeur du lit doit être "oui" ou "non".',
                'date_arrivee.required' => 'La date d\'arrivée est obligatoire.',
                'date_arrivee.date' => 'Le format de la date d\'arrivée est invalide.',
                'date_depart.date' => 'Le format de la date de départ est invalide.',
                'date_depart.after_or_equal' => 'La date de départ doit être égale ou postérieure à la date d\'arrivée.',
                'duree_attente.integer' => 'La durée d\'attente doit être un nombre entier.',
                'duree_attente.min' => 'La durée d\'attente ne peut pas être négative.',
                'taux_occupation.integer' => 'Le taux d\'occupation doit être un nombre entier.',
                'taux_occupation.min' => 'Le taux d\'occupation ne peut pas être négatif.',
                'taux_occupation.max' => 'Le taux d\'occupation ne peut pas dépasser 100%.',
                'maladie.required' => 'Le diagnostic ou la maladie est obligatoire.',
                'soins_intensif.required' => 'Veuillez indiquer si des soins intensifs sont nécessaires.',
                'soins_intensif.boolean' => 'La valeur pour les soins intensifs doit être Oui ou Non.',
                'type_admissions.required' => 'Le type d\'admission est obligatoire.',
                'type_admissions.in' => 'Le type d\'admission doit être "Urgences" ou "Rendez-vous".',
                'service_medical.required' => 'Le service médical est obligatoire.',
                'service_medical.in' => 'Veuillez sélectionner un service médical valide.',
            ]);

            // Formatage des dates si nécessaire
            if (isset($validated['date_arrivee'])) {
                $validated['date_arrivee'] = Carbon::parse($validated['date_arrivee']);
            }
            
            if (isset($validated['date_depart']) && !empty($validated['date_depart'])) {
                $validated['date_depart'] = Carbon::parse($validated['date_depart']);
            }

            $admission->update($validated);

            return redirect()->route('admissions.index')
                ->with('success', 'Admission mise à jour avec succès');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour de l\'admission: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admission  $admission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admission $admission)
    {
        try {
            $admission->delete();
            return redirect()->route('admissions.index')
                ->with('success', 'Admission supprimée avec succès');
        } catch (\Exception $e) {
            return redirect()->route('admissions.index')
                ->with('error', 'Erreur lors de la suppression de l\'admission: ' . $e->getMessage());
        }
    }

    /**
     * Import admissions from Excel file
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ], [
            'excel_file.required' => 'Veuillez sélectionner un fichier à importer.',
            'excel_file.file' => 'Le fichier importé est invalide.',
            'excel_file.mimes' => 'Le fichier doit être au format Excel (xlsx, xls) ou CSV.'
        ]);

        try {
            Excel::import(new AdmissionsImport, $request->file('excel_file'));
            
            return redirect()->route('admissions.index')
                ->with('success', 'Données importées avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'importation : ' . $e->getMessage());
        }
    }
}