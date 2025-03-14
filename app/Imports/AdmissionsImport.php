<?php

namespace App\Imports;

use App\Models\Admission;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class AdmissionsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Convertir les valeurs textuelles en "oui" ou "non"
        $soinsIntensif = $this->convertToOuiNon($row['soins_intensif'] ?? 'non');
        $lit = $this->convertToOuiNon($row['lit'] ?? 'non');
        
        return new Admission([
            'numero_patient' => $row['numero_patient'],
            'lit' => $lit,
            'date_arrivee' => $this->transformDate($row['date_arrivee']),
            'date_depart' => isset($row['date_depart']) ? $this->transformDate($row['date_depart']) : null,
            'duree_attente' => $row['duree_attente'] ?? null,
            'taux_occupation' => $row['taux_occupation'] ?? null,
            'maladie' => $row['maladie'],
            'soins_intensif' => $soinsIntensif,
            'medicaments' => $row['medicaments'] ?? null,
            'type_admissions' => $row['type_admissions'],
            'service_medical' => $row['service_medical'],
        ]);
    }

    /**
     * Transform a date value from Excel
     */
    private function transformDate($value)
    {
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
        }
        
        // Essayer de parser la date si c'est une chaîne
        if (is_string($value) && !empty($value)) {
            try {
                return Carbon::parse($value);
            } catch (\Exception $e) {
                // Si l'analyse échoue, retourner la valeur d'origine
                return $value;
            }
        }

        return $value;
    }
    
    /**
     * Convert various values to "oui" or "non"
     * 
     * @param mixed $value
     * @return string
     */
    private function convertToOuiNon($value)
    {
        if (is_bool($value)) {
            return $value ? 'oui' : 'non';
        }
        
        if (is_numeric($value)) {
            return $value > 0 ? 'oui' : 'non';
        }
        
        if (is_string($value)) {
            $value = strtolower(trim($value));
            return in_array($value, ['oui', 'yes', 'true', '1', 'y', 'vrai']) ? 'oui' : 'non';
        }
        
        return 'non';
    }
}