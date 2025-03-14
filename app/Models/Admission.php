<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_patient',
        'lit',
        'date_arrivee',
        'date_depart',
        'duree_attente',
        'taux_occupation',
        'maladie',
        'soins_intensif',
        'medicaments',
        'type_admissions',
        'service_medical'
    ];

    protected $casts = [
        'date_arrivee' => 'datetime',
        'date_depart' => 'datetime',
    ];

 // Accessor pour s'assurer que soins_intensif retourne toujours "oui" ou "non"
public function getSoinsIntensifAttribute($value)
{
    if ($value === true || $value === 1 || $value === '1' || strtolower($value) === 'oui' || strtolower($value) === 'yes') {
        return 'oui';
    }
    return 'non';
}

// Mutator pour s'assurer que soins_intensif est toujours stocké comme "oui" ou "non"
public function setSoinsIntensifAttribute($value)
{
    if ($value === true || $value === 1 || $value === '1' || strtolower($value) === 'oui' || strtolower($value) === 'yes') {
        $this->attributes['soins_intensif'] = 'oui';
    } else {
        $this->attributes['soins_intensif'] = 'non';
    }
}
    
}