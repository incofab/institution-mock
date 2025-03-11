<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait QueryInstitution
{
  protected static function boot()
  {
    parent::boot();

    static::addGlobalScope('institution', function (Builder $builder) {
      $institution = currentInstitution();
      if ($institution) {
        $table = (new self())->getTable();
        $builder->where($table . '.institution_id', $institution->id);
      }
    });
  }

  //   function scopeForInstitution($query, $institution)
  //   {
  //     $institutionId =
  //       $institution instanceof Institution ? $institution->id : $institution;
  //     return $query->where(
  //       fn($q) => $q
  //         ->whereNull('institution_id')
  //         ->orWhere('institution_id', $institutionId),
  //     );
  //   }
}
