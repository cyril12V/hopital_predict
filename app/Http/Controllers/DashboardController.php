<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Date du jour pour filtrer les données
        $today = Carbon::today();
        
        // Récupérer les statistiques globales directement via des requêtes pour compter tous les enregistrements
        $litsOccupes = Admission::where('lit', 'oui')->count();
        
        // Statistiques pour aujourd'hui uniquement
        $litsOccupesJour = Admission::where('lit', 'oui')
            ->whereDate('date_arrivee', $today)
            ->count();
        
        // Patients en soins intensifs - global vs aujourd'hui
        $patientsIntensifs = Admission::where('soins_intensif', 'oui')->count();
        $patientsIntensifsJour = Admission::where('soins_intensif', 'oui')
            ->whereDate('date_arrivee', $today)
            ->count();
        
        // Pour calculer la différence avec hier et ce matin
        $yesterday = Carbon::yesterday();
        $thisMorning = Carbon::today()->addHours(8); // 8h du matin
        
        $patientsIntensifsHier = Admission::where('soins_intensif', 'oui')
            ->where('date_arrivee', '<', $yesterday)
            ->count();
        
        $patientsIntensifsMatin = Admission::where('soins_intensif', 'oui')
            ->whereDate('date_arrivee', $today)
            ->where('date_arrivee', '<=', $thisMorning)
            ->count();
        
        $difference = $patientsIntensifs - $patientsIntensifsHier;
        $differenceJour = $patientsIntensifsJour - $patientsIntensifsMatin;
        
        // Temps d'attente moyen global et du jour
        $tempsAttenteMoyen = round(Admission::whereNotNull('duree_attente')->avg('duree_attente')) ?: 0;
        $tempsAttenteMoyenJour = round(Admission::whereNotNull('duree_attente')
            ->whereDate('date_arrivee', $today)
            ->avg('duree_attente')) ?: 0;
        
        // Admissions d'aujourd'hui
        $admissionsAujourdhui = Admission::whereDate('date_arrivee', $today)->count();
        
        // Services médicaux et leur occupation
        $servicesActifs = Admission::select('service_medical', DB::raw('count(*) as total'))
            ->groupBy('service_medical')
            ->orderBy('total', 'desc')
            ->get();
        
        // Récupérer uniquement les 10 dernières admissions pour affichage
        $admissionsRecentes = Admission::orderBy('date_arrivee', 'desc')
            ->take(10)
            ->get();

        return view('dashboard.index', [
            // Statistiques globales
            'litsOccupes' => $litsOccupes,
            'patientsIntensifs' => $patientsIntensifs,
            'patientsIntensifsHier' => $patientsIntensifsHier,
            'difference' => $difference,
            'tempsAttenteMoyen' => $tempsAttenteMoyen,
            
            // Statistiques du jour
            'litsOccupesJour' => $litsOccupesJour,
            'patientsIntensifsJour' => $patientsIntensifsJour,
            'differenceJour' => $differenceJour,
            'tempsAttenteMoyenJour' => $tempsAttenteMoyenJour,
            'admissionsAujourdhui' => $admissionsAujourdhui,
            
            // Autres données
            'servicesActifs' => $servicesActifs,
            'admissions' => $admissionsRecentes, // Pour l'affichage de la liste
            'totalAdmissions' => Admission::count() // Nombre total d'admissions
        ]);
    }
}