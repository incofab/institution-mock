<?php
namespace App\Traits;

use App\Models\Institution;

trait QueryInstitution
{
    function scopeForInstitution($query, $institution)
    {
        $institutionId =
            $institution instanceof Institution
                ? $institution->id
                : $institution;
        return $query->where(
            fn($q) => $q
                ->whereNull('institution_id')
                ->orWhere('institution_id', $institutionId),
        );
    }
}
