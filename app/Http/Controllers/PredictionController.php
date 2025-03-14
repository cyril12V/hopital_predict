<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PredictionController extends Controller
{
    /**
     * Affiche la page d'accueil des prédictions
     */
    public function index()
    {
        // Nombre total d'admissions pour affichage dans la page d'accueil
        $totalAdmissions = Admission::count();
        $historicalAdmissions = $totalAdmissions;
        
        // Années disponibles dans la base de données
        $firstAdmission = Admission::min('date_arrivee');
        $lastAdmission = Admission::max('date_arrivee');
        $yearsAvailable = ($firstAdmission && $lastAdmission) ? 
            Carbon::parse($firstAdmission)->diffInYears(Carbon::parse($lastAdmission)) + 1 : 0;
        
        // Récupérer des statistiques récentes pour la page d'accueil
        $recentTrends = $this->getRecentTrends();
        
        $predictions = [
            'yearsAnalyzed' => $yearsAvailable,
            'recentTrends' => $recentTrends,
            'confidenceScore' => min(100, max(10, $historicalAdmissions / 20 + ($yearsAvailable * 5)))
        ];
        
        return view('predictions.index', compact(
            'totalAdmissions', 
            'historicalAdmissions',
            'predictions'
        ));
    }

    /**
     * Analyse les prédictions basées sur les dates fournies par l'utilisateur
     */
    public function predict(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        
        return $this->generatePredictions($startDate, $endDate);
    }

    /**
     * Génère une prédiction pour les 15 prochains jours
     * Utilise les données récentes pour ajuster la tendance
     */
    public function next15Days()
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(14); // 15 jours au total (aujourd'hui + 14)
        
        // Récupérer des données récentes pour affiner les prédictions
        // On analyse plusieurs périodes pour mieux capturer les tendances
        $past30Days = $this->getRecentAdmissions(30);
        $past90Days = $this->getRecentAdmissions(90);
        $past7Days = $this->getRecentAdmissions(7);
        
        return $this->generatePredictions($startDate, $endDate, [
            '7j' => $past7Days,
            '30j' => $past30Days, 
            '90j' => $past90Days
        ]);
    }
    
    /**
     * Récupère les tendances récentes d'admissions
     */
    private function getRecentTrends()
    {
        $recentTrends = [];
        $lastAdmission = Admission::max('date_arrivee');
        
        if ($lastAdmission) {
            $lastAdmissionDate = Carbon::parse($lastAdmission);
            
            // Tendance mensuelle
            $lastMonth = $lastAdmissionDate->copy()->startOfMonth();
            $prevMonth = $lastMonth->copy()->subMonth();
            
            $lastMonthCount = Admission::whereBetween('date_arrivee', [
                $lastMonth, $lastAdmissionDate
            ])->count();
            
            $prevMonthCount = Admission::whereBetween('date_arrivee', [
                $prevMonth, $lastMonth->copy()->subDay()
            ])->count();
            
            $recentTrends['monthlyChange'] = $prevMonthCount > 0 
                ? round((($lastMonthCount - $prevMonthCount) / $prevMonthCount) * 100, 1) 
                : 0;
            
            // Tendance hebdomadaire
            $lastWeek = $lastAdmissionDate->copy()->startOfWeek();
            $prevWeek = $lastWeek->copy()->subWeek();
            
            $lastWeekCount = Admission::whereBetween('date_arrivee', [
                $lastWeek, $lastAdmissionDate
            ])->count();
            
            $prevWeekCount = Admission::whereBetween('date_arrivee', [
                $prevWeek, $lastWeek->copy()->subDay()
            ])->count();
            
            $recentTrends['weeklyChange'] = $prevWeekCount > 0 
                ? round((($lastWeekCount - $prevWeekCount) / $prevWeekCount) * 100, 1) 
                : 0;
            
            // Évolution quotidienne de la dernière semaine
            $dailyTrend = [];
            for ($i = 6; $i >= 0; $i--) {
                $day = $lastAdmissionDate->copy()->subDays($i);
                $count = Admission::whereDate('date_arrivee', $day)->count();
                $dailyTrend[] = [
                    'date' => $day->format('Y-m-d'),
                    'count' => $count,
                    'day' => $day->format('D')
                ];
            }
            
            $recentTrends['dailyTrend'] = $dailyTrend;
        }
        
        return $recentTrends;
    }
    
    /**
     * Récupère les admissions récentes sur une période donnée
     */
    private function getRecentAdmissions($days)
    {
        $startDate = Carbon::today()->subDays($days);
        $endDate = Carbon::yesterday();
        
        return Admission::whereBetween('date_arrivee', [$startDate, $endDate])->get();
    }

    /**
     * Méthode commune pour générer les prédictions avancées
     * @param Carbon $startDate Date de début de la période à prédire
     * @param Carbon $endDate Date de fin de la période à prédire
     * @param array|null $recentData Données récentes pour ajuster la tendance
     */
    private function generatePredictions($startDate, $endDate, $recentData = null)
    {
        // ---- RÉCUPÉRATION DES DONNÉES HISTORIQUES SUR PLUSIEURS ANNÉES ----
        
        // Déterminer d'abord combien d'années de données sont disponibles
        $firstAdmission = Admission::min('date_arrivee');
        $lastAdmission = Admission::max('date_arrivee');
        
        if (!$firstAdmission || !$lastAdmission) {
            return view('predictions.result', [
                'hasData' => false,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'message' => "Impossible de générer des prédictions : aucune donnée d'admission n'est disponible dans la base de données."
            ]);
        }
        
        $firstDate = Carbon::parse($firstAdmission);
        $lastDate = Carbon::parse($lastAdmission);
        $yearsAvailable = $firstDate->diffInYears($lastDate) + 1;
        
        // Limiter à 5 années maximum pour l'analyse historique
        $maxYearsToAnalyze = min(5, $yearsAvailable);
        
        // Récupérer des données sur les années disponibles pour la même période
        $historicalData = [];
        $totalHistoricalAdmissions = 0;
        $rawYearlyData = []; // Pour stocker les données brutes par année pour analyse des anomalies
        
        // Récupérer aussi les données semaine par semaine pour une analyse plus fine
        $weeklyData = [];
        
        for ($year = 1; $year <= $maxYearsToAnalyze; $year++) {
            // Même période mais année précédente
            $periodStartDate = $startDate->copy()->subYears($year);
            $periodEndDate = $endDate->copy()->subYears($year);
            
            // Ne pas aller chercher des données avant la première admission
            if ($periodStartDate->lt($firstDate)) {
                continue;
            }
            
            $yearData = Admission::whereBetween('date_arrivee', [$periodStartDate, $periodEndDate])->get();
            $yearCount = $yearData->count();
            
            if ($yearCount > 0) {
                // Stocker les données brutes pour analyse
                $rawYearlyData[$year] = $yearCount;
                
                // Analyse semaine par semaine
                $weekData = [];
                $currentStart = $periodStartDate->copy();
                while ($currentStart->lte($periodEndDate)) {
                    $weekEnd = $currentStart->copy()->addDays(6)->min($periodEndDate);
                    $weekAdmissions = $yearData->whereBetween('date_arrivee', [
                        $currentStart->format('Y-m-d'), 
                        $weekEnd->format('Y-m-d')
                    ])->count();
                    
                    $weekData[] = [
                        'start' => $currentStart->format('Y-m-d'),
                        'end' => $weekEnd->format('Y-m-d'),
                        'count' => $weekAdmissions
                    ];
                    
                    $currentStart = $weekEnd->copy()->addDay();
                }
                
                $historicalData[$year] = [
                    'data' => $yearData,
                    'count' => $yearCount,
                    'start' => $periodStartDate,
                    'end' => $periodEndDate,
                    'weeklyData' => $weekData
                ];
                
                $weeklyData[$year] = $weekData;
                $totalHistoricalAdmissions += $yearCount;
            }
        }
        
        // ---- GESTION DES VARIATIONS SAISONNIÈRES ET DÉTECTION DES ANOMALIES ----
        
        // Calculer les facteurs saisonniers au lieu d'exclure les données
        $seasonalFactors = $this->calculateSeasonalFactors();
        
        // Ajuster les données historiques avec les facteurs saisonniers
        $deseasnalizedData = $this->adjustForSeasonality($historicalData, $seasonalFactors, $startDate);
        
        // Ne faire l'analyse des anomalies que sur les données désaisonnalisées
        // pour éviter de confondre les variations saisonnières avec des anomalies
        $anomalies = [];
        
        if (count($deseasnalizedData) >= 3) {
            // Calculer la moyenne et l'écart-type sur les données désaisonnalisées
            $adjustedCounts = array_column($deseasnalizedData, 'adjustedCount');
            $mean = array_sum($adjustedCounts) / count($adjustedCounts);
            $sumSquaredDifferences = 0;
            
            foreach ($adjustedCounts as $count) {
                $sumSquaredDifferences += pow($count - $mean, 2);
            }
            
            $stdDev = sqrt($sumSquaredDifferences / count($adjustedCounts));
            
            // Identifier les anomalies (valeurs à +/- 2 écarts-types de la moyenne)
            foreach ($deseasnalizedData as $year => $data) {
                $count = $data['adjustedCount'];
                $zScore = ($count - $mean) / max(1, $stdDev); // Éviter division par zéro
                
                if (abs($zScore) > 2) {
                    $anomalies[$year] = [
                        'year' => Carbon::now()->subYears($year)->year,
                        'count' => $rawYearlyData[$year],
                        'adjustedCount' => $count,
                        'zScore' => $zScore
                    ];
                }
            }
            
            // Si des anomalies ont été détectées, ajuster le message
            if (!empty($anomalies)) {
                $anomalyYears = array_column($anomalies, 'year');
                $anomalyMessage = "Attention : Variations atypiques détectées pour les années : " . implode(', ', $anomalyYears);
                // Mais on ne supprime pas les données, on les ajuste
            }
        }
        
        // ---- INTÉGRATION DES DONNÉES SAISONNIÈRES ET RÉCENTES ----
        
        // Si nous n'avons pas suffisamment de données historiques pour cette période précise,
        // essayons d'utiliser des périodes similaires (même saison)
        if ($totalHistoricalAdmissions < 10) {
            $seasonalData = $this->getSeasonalData($startDate, $endDate);
            
            if ($seasonalData['count'] > $totalHistoricalAdmissions) {
                // Utiliser les données saisonnières si elles sont plus nombreuses
                $historicalData = [
                    1 => [
                        'data' => $seasonalData['data'],
                        'count' => $seasonalData['count'],
                        'start' => $seasonalData['start'],
                        'end' => $seasonalData['end']
                    ]
                ];
                $totalHistoricalAdmissions = $seasonalData['count'];
                $message = "Attention : Prédictions basées sur des périodes similaires (même saison) des années précédentes.";
            } else if ($totalHistoricalAdmissions == 0) {
                // Si toujours pas assez de données, utiliser toutes les données
                $allData = Admission::all();
                $allDataCount = $allData->count();
                
                if ($allDataCount == 0) {
                    return view('predictions.result', [
                        'hasData' => false,
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                        'message' => "Impossible de générer des prédictions : aucune donnée d'admission n'est disponible dans la base de données."
                    ]);
                }
                
                $historicalData = [
                    1 => [
                        'data' => $allData,
                        'count' => $allDataCount,
                        'start' => $firstDate,
                        'end' => $lastDate
                    ]
                ];
                $totalHistoricalAdmissions = $allDataCount;
                $message = "Attention : Prédictions basées sur toutes les données disponibles, la précision peut être réduite.";
            } else {
                $message = "Attention : Données limitées pour cette période précise. Prédictions basées sur " . count($historicalData) . " année(s) antérieure(s).";
            }
        } else {
            if (count($historicalData) == 1) {
                $message = "Prédictions basées sur les données de l'année précédente pour la même période.";
            } else {
                $message = "Prédictions basées sur les données de " . count($historicalData) . " années précédentes pour la même période.";
            }
            
            // Ajouter l'information sur les anomalies si nécessaire
            if (!empty($anomalyMessage)) {
                $message .= " " . $anomalyMessage;
            }
        }
        
        // Nombre de jours dans la période demandée
        $nombreJours = $startDate->diffInDays($endDate) + 1;
        
        // ---- APPROCHE AVANCÉE DE PRÉDICTION ----
        
        // 1. Moyenne mobile pondérée exponentielle (EWMA)
        // 2. Avec détection de tendance (Holt-Winters sans saisonnalité)
        // 3. Ajustement par les données récentes
        
        // Pour chaque année, on calcule le nombre d'admissions par jour
        $dailyAdmissionsTimeSeries = [];
        foreach ($historicalData as $year => $yearData) {
            // Nombre d'admissions normalisé par jour avec pondération exponentielle
            // Plus le year est petit, plus l'année est récente
            $weight = exp(-0.5 * ($year - 1)); // Poids exponentiellement décroissant
            $admissionsParJour = $yearData['count'] / max(1, $yearData['start']->diffInDays($yearData['end']) + 1);
            
            // Ajouter à la série temporelle
            $dailyAdmissionsTimeSeries[] = [
                'year' => Carbon::now()->subYears($year)->year,
                'admissions' => $admissionsParJour,
                'weight' => $weight
            ];
        }
        
        // Calculer la moyenne mobile pondérée exponentielle
        $ewma = $this->calculateEWMA($dailyAdmissionsTimeSeries);
        
        // Détecter la tendance en calculant la pente de la régression linéaire
        $trend = $this->calculateTrend($dailyAdmissionsTimeSeries);
        
        // Ajuster avec les données récentes si disponibles
        $recentAdjustment = 0;
        if ($recentData && isset($recentData['7j']) && $recentData['7j']->count() > 0) {
            // Calculer la moyenne des 7 derniers jours
            $last7DaysAvg = $recentData['7j']->count() / 7;
            
            // Comparer avec la prédiction baseline (EWMA)
            if ($ewma > 0) {
                $recentAdjustment = ($last7DaysAvg - $ewma) / $ewma;
                // Limiter l'ajustement
                $recentAdjustment = max(-0.2, min(0.2, $recentAdjustment));
            }
            
            // Utiliser aussi les données sur 30 jours pour vérifier la tendance
            if (isset($recentData['30j']) && $recentData['30j']->count() > 0) {
                $last30DaysAvg = $recentData['30j']->count() / 30;
                
                // Si tendance sur 30 jours est forte, l'intégrer à l'ajustement
                if ($ewma > 0) {
                    $trend30 = ($last30DaysAvg - $ewma) / $ewma;
                    // Combiner les deux ajustements (poids plus important aux données récentes)
                    $recentAdjustment = $recentAdjustment * 0.7 + $trend30 * 0.3;
                }
            }
        }
        
        // Prédiction finale: EWMA + tendance + ajustement récent
        $admissionsQuotidiennes = max(1, ceil($ewma * (1 + $trend) * (1 + $recentAdjustment)));
        
        // Calculer l'erreur standard pour estimer la marge d'erreur
        $errorMargin = $this->calculatePredictionErrorMargin($dailyAdmissionsTimeSeries, $ewma);
        
        // Nombre total d'admissions prévues pour la période
        $admissionsPrevisionnelles = $admissionsQuotidiennes * $nombreJours;
        
        // Calculer la tendance en pourcentage par rapport à l'année précédente
        $tendance = 0;
        if (isset($historicalData[1])) {
            $lastYearAdmissions = $historicalData[1]['count'] / max(1, $historicalData[1]['start']->diffInDays($historicalData[1]['end']) + 1);
            if ($lastYearAdmissions > 0) {
                $tendance = round((($admissionsQuotidiennes - $lastYearAdmissions) / $lastYearAdmissions) * 100, 1);
            }
        }
        
        // ---- ANALYSES AVANCÉES DES PATRONS SAISONNIERS POUR LITS ET SOINS INTENSIFS ----
        
        // Analyse des besoins en lits avec pondération et facteurs saisonniers
        $totalPoids = 0;
        $litsPonderes = 0;
        
        foreach ($historicalData as $year => $yearData) {
            // Poids exponentiellement décroissant
            $poids = exp(-0.5 * ($year - 1));
            $totalPoids += $poids;
            
            // Taux d'utilisation des lits pondéré
            $tauxLits = $yearData['data']->where('lit', 'oui')->count() / max(1, $yearData['count']);
            $litsPonderes += $tauxLits * $poids;
        }
        
        // Taux moyen pondéré
        $tauxLitsHistorique = $totalPoids > 0 ? $litsPonderes / $totalPoids : 0;
        
        // Ajuster le taux en fonction du facteur saisonnier pour cette période
        $moisActuel = $startDate->month;
        $facteurSaisonnierLits = isset($seasonalFactors['lits'][$moisActuel]) ? $seasonalFactors['lits'][$moisActuel] : 1;
        $tauxLitsAjuste = $tauxLitsHistorique * $facteurSaisonnierLits;
        
        // Calculer les besoins en lits en tenant compte des variations saisonnières
        $litsNecessaires = ceil($admissionsQuotidiennes * $tauxLitsAjuste);
        
        // Même approche pour les soins intensifs
        $siPonderes = 0;
        
        foreach ($historicalData as $year => $yearData) {
            $poids = exp(-0.5 * ($year - 1));
            $tauxSI = $yearData['data']->where('soins_intensif', 'oui')->count() / max(1, $yearData['count']);
            $siPonderes += $tauxSI * $poids;
        }
        
        $tauxSoinsIntensifsHistorique = $totalPoids > 0 ? $siPonderes / $totalPoids : 0;
        $facteurSaisonnierSI = isset($seasonalFactors['soins_intensifs'][$moisActuel]) ? $seasonalFactors['soins_intensifs'][$moisActuel] : 1;
        $tauxSIAjuste = $tauxSoinsIntensifsHistorique * $facteurSaisonnierSI;
        
        $soinsIntensifsNecessaires = ceil($admissionsQuotidiennes * $tauxSIAjuste);
        
        // Temps d'attente prévu avec modèle amélioré
        $tempsAttenteMoyenPrevu = $this->predictWaitTime($historicalData, $startDate, $admissionsQuotidiennes);
        
        // ---- ANALYSE DES SERVICES, MALADIES ET MÉDICAMENTS ----
        
        // Combiner les données de toutes les années avec pondération
        $services = [];
        $maladies = [];
        $medicaments = [];
        
        foreach ($historicalData as $year => $yearData) {
            $poids = exp(-0.5 * ($year - 1));
            
            // Services
            $yearServices = $yearData['data']->groupBy('service_medical')
                ->map(function ($items) {
                    return count($items);
                })
                ->toArray();
                
            foreach ($yearServices as $service => $count) {
                if (!isset($services[$service])) $services[$service] = 0;
                $services[$service] += $count * $poids;
            }
            
            // Maladies
            $yearMaladies = $yearData['data']->groupBy('maladie')
                ->map(function ($items) {
                    return count($items);
                })
                ->toArray();
                
            foreach ($yearMaladies as $maladie => $count) {
                if (!isset($maladies[$maladie])) $maladies[$maladie] = 0;
                $maladies[$maladie] += $count * $poids;
            }
            
            // Médicaments
            $yearMeds = $yearData['data']->pluck('medicaments')->filter()->toArray();
            
            foreach ($yearMeds as $medList) {
                if (empty($medList)) continue;
                
                $meds = explode(',', $medList);
                foreach ($meds as $med) {
                    $med = trim($med);
                    if (!empty($med)) {
                        if (!isset($medicaments[$med])) $medicaments[$med] = 0;
                        $medicaments[$med] += $poids;
                    }
                }
            }
        }
        
        // Trier et prendre les 5 premiers
        arsort($services);
        arsort($maladies);
        arsort($medicaments);
        
        $topServices = array_slice($services, 0, 5, true);
        $topMaladies = array_slice($maladies, 0, 5, true);
        $topMedicaments = array_slice($medicaments, 0, 5, true);
        
        // ---- ÉVOLUTION HISTORIQUE POUR AFFICHAGE ----
        // Préparer un tableau d'évolution sur les années précédentes pour affichage graphique
        $evolutionHistorique = [];
        
        foreach ($historicalData as $year => $yearData) {
            $evolutionHistorique[$year] = [
                'annee' => Carbon::now()->subYears($year)->year,
                'admissions' => $yearData['count'],
                'admissionsParJour' => round($yearData['count'] / max(1, $yearData['start']->diffInDays($yearData['end']) + 1), 2),
                'lits' => $yearData['data']->where('lit', 'oui')->count(),
                'soinsIntensifs' => $yearData['data']->where('soins_intensif', 'oui')->count(),
                'tempsAttente' => round($yearData['data']->avg('duree_attente') ?: 0),
                'periodeExacte' => $yearData['start']->format('d/m/Y') . ' - ' . $yearData['end']->format('d/m/Y'),
                'weeklyData' => $yearData['weeklyData'] ?? []
            ];
        }
        
        // ---- DÉTERMINATION DU NIVEAU DE CRITICITÉ ET RECOMMANDATIONS ----
        
        $criticality = 'Normal';
        $recommendations = [];
        
        // Analyse du taux d'occupation
        $capaciteLits = 100; // À ajuster selon votre hôpital
        $tauxOccupation = ($litsNecessaires / $capaciteLits) * 100;
        
        if ($tauxOccupation > 90) {
            $criticality = 'Critique';
            $recommendations[] = "Préparez des lits supplémentaires, le taux d'occupation prévu dépasse 90%.";
        } elseif ($tauxOccupation > 75) {
            $criticality = 'Élevé';
            $recommendations[] = "Surveillez attentivement le taux d'occupation des lits qui pourrait dépasser 75%.";
        } elseif ($tauxOccupation > 60) {
            $criticality = 'Modéré';
        }
        
        // Analyse des soins intensifs
        $capaciteSI = 20; // À ajuster selon votre hôpital
        if ($soinsIntensifsNecessaires > $capaciteSI * 0.8) {
            if ($criticality != 'Critique') {
                $criticality = 'Critique';
            }
            $recommendations[] = "La demande en soins intensifs pourrait dépasser 80% de la capacité disponible.";
        } elseif ($soinsIntensifsNecessaires > $capaciteSI * 0.6) {
            if ($criticality != 'Critique') {
                $criticality = 'Élevé';
            }
            $recommendations[] = "Prévoyez du personnel supplémentaire pour les soins intensifs.";
        }
        
        // Analyse du temps d'attente
        if ($tempsAttenteMoyenPrevu > 45) {
            $recommendations[] = "Le temps d'attente moyen prévu est élevé (>45min). Envisagez d'organiser des équipes supplémentaires.";
        }
        
        // Recommandations pour les médicaments et services
        if (!empty($topMedicaments)) {
            $topMed = array_key_first($topMedicaments);
            $recommendations[] = "Assurez-vous d'avoir des stocks suffisants de $topMed, le médicament le plus demandé pour cette période.";
        }
        
        if (!empty($topServices)) {
            $topService = array_key_first($topServices);
            $recommendations[] = "Le service de $topService sera probablement le plus sollicité. Prévoyez des ressources adéquates.";
        }
        
        // Recommandation basée sur la tendance
        if ($tendance > 15) {
            $recommendations[] = "Hausse significative de {$tendance}% prévue par rapport à l'année précédente. Assurez-vous d'avoir suffisamment de personnel.";
        } elseif ($tendance < -15) {
            $recommendations[] = "Baisse significative de " . abs($tendance) . "% prévue par rapport à l'année précédente.";
        }
        
        // Analyse des risques
        if (!empty($anomalies)) {
            $recommendations[] = "Des variations atypiques ont été détectées. Prévoyez une marge de sécurité supplémentaire.";
        }
        
        // Ajout d'une métrique de confiance basée sur la qualité des données
        // Plus de données = meilleur score, mais une forte marge d'erreur = score réduit
        $confidenceScore = min(100, max(10, 
            ($totalHistoricalAdmissions / 10) + // Nombre d'admissions
            (count($historicalData) * 5) - // Années de données
            min(30, $errorMargin / 2) // Pénalité pour marge d'erreur importante
        ));
        
        // ---- PRÉPARATION DES DONNÉES DE RETOUR ----
        
        $predictions = [
            'criticality' => $criticality,
            'tendance' => $tendance,
            'admissionsPrevisionnelles' => $admissionsPrevisionnelles,
            'admissionsQuotidiennes' => $admissionsQuotidiennes,
            'litsNecessaires' => $litsNecessaires,
            'soinsIntensifsNecessaires' => $soinsIntensifsNecessaires,
            'tempsAttenteMoyenPrevu' => $tempsAttenteMoyenPrevu,
            'tauxOccupation' => round($tauxOccupation, 1),
            'margeErreur' => round($errorMargin, 1),
            'confidenceScore' => round($confidenceScore),
            'recommendations' => $recommendations,
            'topServices' => $topServices,
            'topMaladies' => $topMaladies, 
            'topMedicaments' => $topMedicaments,
            'evolutionHistorique' => $evolutionHistorique
        ];
        
        return view('predictions.result', [
            'hasData' => true,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'predictions' => $predictions,
            'message' => $message,
            'historicalAdmissions' => $totalHistoricalAdmissions // Ajoutez cette ligne
        ]);

    }

    /**
 * Calcule les facteurs saisonniers en analysant les données historiques
 * @return array Facteurs saisonniers par mois
 */
private function calculateSeasonalFactors()
{
    // Structure pour stocker les facteurs saisonniers par mois
    $seasonalFactors = [
        'admissions' => [],  // Facteurs pour les admissions
        'lits' => [],        // Facteurs pour l'utilisation des lits
        'soins_intensifs' => [] // Facteurs pour les soins intensifs
    ];
    
    // Récupérer les données des 3 dernières années pour analyser les tendances mensuelles
    $endDate = Carbon::today();
    $startDate = Carbon::today()->subYears(3);
    
    $admissions = Admission::whereBetween('date_arrivee', [$startDate, $endDate])
        ->select(
            DB::raw('MONTH(date_arrivee) as month'),
            DB::raw('YEAR(date_arrivee) as year'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(CASE WHEN lit = "oui" THEN 1 ELSE 0 END) as lit_count'),
            DB::raw('SUM(CASE WHEN soins_intensif = "oui" THEN 1 ELSE 0 END) as si_count')
        )
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();
    
    // Si pas assez de données, utiliser des valeurs par défaut
    if ($admissions->count() < 12) {
        // Valeurs par défaut basées sur des tendances hospitalières typiques
        for ($i = 1; $i <= 12; $i++) {
            // Les mois d'hiver (en France) ont généralement plus d'admissions
            if (in_array($i, [1, 2, 12])) { // Janvier, Février, Décembre
                $seasonalFactors['admissions'][$i] = 1.2;
                $seasonalFactors['lits'][$i] = 1.15;
                $seasonalFactors['soins_intensifs'][$i] = 1.25;
            } 
            // Les mois d'été ont généralement moins d'admissions
            else if (in_array($i, [6, 7, 8])) { // Juin, Juillet, Août
                $seasonalFactors['admissions'][$i] = 0.85;
                $seasonalFactors['lits'][$i] = 0.8;
                $seasonalFactors['soins_intensifs'][$i] = 0.75;
            } 
            // Les mois intermédiaires
            else {
                $seasonalFactors['admissions'][$i] = 1.0;
                $seasonalFactors['lits'][$i] = 1.0;
                $seasonalFactors['soins_intensifs'][$i] = 1.0;
            }
        }
        return $seasonalFactors;
    }
    
    // Calculer les moyennes mensuelles
    $monthlyData = [];
    $totalAvg = 0;
    $litTotalAvg = 0;
    $siTotalAvg = 0;
    $monthCount = 0;
    
    foreach ($admissions as $admission) {
        $month = $admission->month;
        if (!isset($monthlyData[$month])) {
            $monthlyData[$month] = [
                'count' => 0,
                'total' => 0,
                'lit_total' => 0,
                'si_total' => 0,
                'years' => 0
            ];
        }
        
        $monthlyData[$month]['total'] += $admission->count;
        $monthlyData[$month]['lit_total'] += $admission->lit_count;
        $monthlyData[$month]['si_total'] += $admission->si_count;
        $monthlyData[$month]['years']++;
        
        $totalAvg += $admission->count;
        $litTotalAvg += $admission->lit_count;
        $siTotalAvg += $admission->si_count;
        $monthCount++;
    }
    
    // Calculer la moyenne générale pour normaliser
    $globalAvg = $monthCount > 0 ? $totalAvg / $monthCount : 1;
    $globalLitAvg = $monthCount > 0 ? $litTotalAvg / $monthCount : 1;
    $globalSiAvg = $monthCount > 0 ? $siTotalAvg / $monthCount : 1;
    
    // Calculer les facteurs saisonniers normalisés
    for ($i = 1; $i <= 12; $i++) {
        if (isset($monthlyData[$i]) && $monthlyData[$i]['years'] > 0) {
            $monthAvg = $monthlyData[$i]['total'] / $monthlyData[$i]['years'];
            $litMonthAvg = $monthlyData[$i]['lit_total'] / $monthlyData[$i]['years'];
            $siMonthAvg = $monthlyData[$i]['si_total'] / $monthlyData[$i]['years'];
            
            // Utiliser une valeur par défaut de 1 si la moyenne globale est 0
            $seasonalFactors['admissions'][$i] = $globalAvg > 0 ? $monthAvg / $globalAvg : 1;
            $seasonalFactors['lits'][$i] = $globalLitAvg > 0 ? $litMonthAvg / $globalLitAvg : 1;
            $seasonalFactors['soins_intensifs'][$i] = $globalSiAvg > 0 ? $siMonthAvg / $globalSiAvg : 1;
        } else {
            // Valeur par défaut pour les mois sans données
            $seasonalFactors['admissions'][$i] = 1;
            $seasonalFactors['lits'][$i] = 1;
            $seasonalFactors['soins_intensifs'][$i] = 1;
        }
    }
    
    return $seasonalFactors;
}

/**
 * Ajuste les données historiques en fonction des facteurs saisonniers
 * @param array $historicalData Les données historiques par année
 * @param array $seasonalFactors Les facteurs saisonniers
 * @param Carbon $startDate Date de début pour déterminer la saison
 * @return array Données désaisonnalisées
 */
private function adjustForSeasonality($historicalData, $seasonalFactors, $startDate)
{
    $deseasnalizedData = [];
    $month = $startDate->month;
    $seasonFactor = isset($seasonalFactors['admissions'][$month]) ? $seasonalFactors['admissions'][$month] : 1;
    
    foreach ($historicalData as $year => $yearData) {
        $rawCount = $yearData['count'];
        // Neutraliser l'effet de la saisonnalité en divisant par le facteur saisonnier
        $adjustedCount = $seasonFactor > 0 ? $rawCount / $seasonFactor : $rawCount;
        
        $deseasnalizedData[$year] = [
            'rawCount' => $rawCount,
            'adjustedCount' => $adjustedCount,
            'seasonFactor' => $seasonFactor
        ];
    }
    
    return $deseasnalizedData;
}

/**
 * Récupère des données de la même saison pour les années disponibles
 * @param Carbon $startDate Date de début
 * @param Carbon $endDate Date de fin
 * @return array Données saisonnières
 */
private function getSeasonalData($startDate, $endDate)
{
    // Déterminer la saison de la période demandée
    $season = $this->determineSeason($startDate);
    $duration = $startDate->diffInDays($endDate);
    
    // Récupérer les données pour la même saison de toutes les années disponibles
    $query = Admission::query();
    
    switch ($season) {
        case 'winter':
            $query->whereMonth('date_arrivee', '>=', 12)
                  ->orWhereMonth('date_arrivee', '<=', 2);
            break;
        case 'spring':
            $query->whereMonth('date_arrivee', '>=', 3)
                  ->whereMonth('date_arrivee', '<=', 5);
            break;
        case 'summer':
            $query->whereMonth('date_arrivee', '>=', 6)
                  ->whereMonth('date_arrivee', '<=', 8);
            break;
        case 'autumn':
            $query->whereMonth('date_arrivee', '>=', 9)
                  ->whereMonth('date_arrivee', '<=', 11);
            break;
    }
    
    $seasonalData = $query->get();
    $seasonStart = Carbon::today()->subYears(5); // Par défaut
    $seasonEnd = Carbon::today();
    
    // Trouver les dates de début et fin pour la saison
    if ($seasonalData->count() > 0) {
        $seasonStart = Carbon::parse($seasonalData->min('date_arrivee'));
        $seasonEnd = Carbon::parse($seasonalData->max('date_arrivee'));
    }
    
    return [
        'data' => $seasonalData,
        'count' => $seasonalData->count(),
        'start' => $seasonStart,
        'end' => $seasonEnd,
        'season' => $season
    ];
}

/**
 * Détermine la saison en fonction de la date
 * @param Carbon $date Date
 * @return string Saison (hiver, printemps, été, automne)
 */
private function determineSeason($date)
{
    $month = $date->month;
    
    if ($month == 12 || $month == 1 || $month == 2) {
        return 'winter';
    } else if ($month >= 3 && $month <= 5) {
        return 'spring';
    } else if ($month >= 6 && $month <= 8) {
        return 'summer';
    } else {
        return 'autumn';
    }
}

/**
 * Calcule la moyenne mobile pondérée exponentielle
 * @param array $timeSeries Série temporelle
 * @return float EWMA
 */
private function calculateEWMA($timeSeries)
{
    $sumWeights = 0;
    $sumWeightedValues = 0;
    
    foreach ($timeSeries as $dataPoint) {
        $sumWeights += $dataPoint['weight'];
        $sumWeightedValues += $dataPoint['admissions'] * $dataPoint['weight'];
    }
    
    return $sumWeights > 0 ? $sumWeightedValues / $sumWeights : 0;
}

/**
 * Calcule la tendance à partir d'une série temporelle
 * @param array $timeSeries Série temporelle
 * @return float Tendance (pente de la régression linéaire)
 */
private function calculateTrend($timeSeries)
{
    // Pas assez de points pour calculer une tendance
    if (count($timeSeries) <= 1) return 0;
    
    // Calcul de la régression linéaire pondérée
    $sumX = 0;
    $sumY = 0;
    $sumXY = 0;
    $sumX2 = 0;
    $sumWeights = 0;
    
    $x = 0;
    foreach ($timeSeries as $dataPoint) {
        $weight = $dataPoint['weight'];
        $y = $dataPoint['admissions'];
        
        $sumWeights += $weight;
        $sumX += $x * $weight;
        $sumY += $y * $weight;
        $sumXY += $x * $y * $weight;
        $sumX2 += $x * $x * $weight;
        
        $x++;
    }
    
    // Éviter division par zéro
    if ($sumWeights == 0 || ($sumX2 * $sumWeights - $sumX * $sumX) == 0) {
        return 0;
    }
    
    // Calculer la pente de la régression
    $slope = ($sumXY * $sumWeights - $sumX * $sumY) / ($sumX2 * $sumWeights - $sumX * $sumX);
    
    // Normaliser la pente en pourcentage par rapport à la moyenne
    $avgY = $sumY / $sumWeights;
    if ($avgY == 0) return 0;
    
    $normalizedSlope = ($slope * (count($timeSeries) - 1)) / $avgY;
    
    // Limiter les valeurs extrêmes
    return max(-0.3, min(0.3, $normalizedSlope));
}

/**
 * Calcule la marge d'erreur pour les prédictions
 * @param array $timeSeries Série temporelle
 * @param float $prediction Prédiction
 * @return float Marge d'erreur en pourcentage
 */
private function calculatePredictionErrorMargin($timeSeries, $prediction)
{
    if (count($timeSeries) <= 1 || $prediction <= 0) return 25; // Valeur par défaut élevée
    
    // Calculer l'écart quadratique moyen pondéré
    $sumSqDiff = 0;
    $sumWeights = 0;
    
    foreach ($timeSeries as $dataPoint) {
        $diff = $dataPoint['admissions'] - $prediction;
        $sumSqDiff += pow($diff, 2) * $dataPoint['weight'];
        $sumWeights += $dataPoint['weight'];
    }
    
    // Racine carrée de l'écart quadratique moyen
    $rmse = sqrt($sumSqDiff / max(1, $sumWeights));
    
    // Erreur en pourcentage
    $errorPercent = ($rmse / $prediction) * 100;
    
    // Limiter l'erreur entre 5% et 50%
    return max(5, min(50, $errorPercent));
}

/**
 * Prédit le temps d'attente moyen
 * @param array $historicalData Données historiques
 * @param Carbon $date Date pour laquelle prédire
 * @param float $dailyAdmissions Admissions quotidiennes prévues
 * @return float Temps d'attente prévu (minutes)
 */
private function predictWaitTime($historicalData, $date, $dailyAdmissions)
{
    $waitTimes = [];
    $weights = [];
    
    // Récupérer les temps d'attente historiques avec pondération
    foreach ($historicalData as $year => $yearData) {
        $avgWaitTime = $yearData['data']->avg('duree_attente') ?: 0;
        $avgAdmissions = $yearData['count'] / max(1, $yearData['start']->diffInDays($yearData['end']) + 1);
        
        // Plus de poids aux années récentes et avec un volume d'admissions similaire
        $yearWeight = exp(-0.5 * ($year - 1));
        
        // Ajuster la pondération en fonction de la similarité du volume d'admissions
        $volumeSimilarity = $avgAdmissions > 0 ? min(1, max(0.5, 1 - abs(($dailyAdmissions - $avgAdmissions) / $avgAdmissions))) : 0.5;
        $finalWeight = $yearWeight * $volumeSimilarity;
        
        $waitTimes[] = $avgWaitTime;
        $weights[] = $finalWeight;
    }
    
    // Si pas de données historiques, estimation basée uniquement sur le volume prévu
    if (empty($waitTimes)) {
        // Formule empirique : 15 minutes + 2 minutes par patient au-delà de 5 patients
        return max(15, 15 + 2 * max(0, $dailyAdmissions - 5));
    }
    
    // Calculer le temps d'attente pondéré
    $weightedSum = 0;
    $weightSum = 0;
    
    for ($i = 0; $i < count($waitTimes); $i++) {
        $weightedSum += $waitTimes[$i] * $weights[$i];
        $weightSum += $weights[$i];
    }
    
    $baseWaitTime = $weightSum > 0 ? $weightedSum / $weightSum : $waitTimes[0];
    
    // Ajuster le temps d'attente en fonction du volume prévu
    $baselineAdmissions = 10; // Nombre de référence pour le temps d'attente de base
    $admissionFactor = $dailyAdmissions / $baselineAdmissions;
    
    // Formule : temps d'attente augmente de façon non linéaire avec le nombre d'admissions
    $adjustedWaitTime = $baseWaitTime * pow($admissionFactor, 0.7);
    
    return round(max(10, min(180, $adjustedWaitTime))); // Limiter entre 10 et 180 minutes
}

}