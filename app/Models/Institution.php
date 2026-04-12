<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Institution extends BaseModel
{
  use HasFactory;

  public const DEFAULT_LICENSE_COST = 300;

  protected $casts = [
    'created_by_user_id' => 'integer',
    'licenses' => 'integer',
    'license_cost' => 'float',
  ];
  // protected $fillable = ['added_by', 'code', 'name', 'address', 'phone', 'email', 'status'];
  static function ruleCreate(bool $canSetLicenseCost = true)
  {
    $rules = [
      'name' => ['required', 'string', 'max:255'],
      'address' => ['nullable', 'string', 'max:255'],
      'phone' => ['nullable', 'string', 'max:15'],
      'email' => ['nullable', 'email'],
    ];

    if ($canSetLicenseCost) {
      $rules['licenses'] = ['nullable', 'integer', 'min:0'];
      $rules['license_cost'] = ['required', 'numeric', 'min:1'];
    }

    return $rules;
  }
  public function getRouteKeyName()
  {
    return 'code';
  }

  public function resolveRouteBinding($value, $field = null)
  {
    // $user = currentUser();
    $institutionModel = Institution::query()
      // ->select('institutions.*')
      // ->join(
      //     'institution_users',
      //     'institution_users.institution_id',
      //     'institutions.id',
      // )
      ->where('code', $value)
      // ->when(
      //     $user,
      //     fn($q) => $q
      //         ->where('institution_users.user_id', $user->id)
      //         ->with(
      //             'institutionUsers',
      //             fn($q) => $q->where(
      //                 'institution_users.user_id',
      //                 $user->id,
      //             ),
      //         ),
      // )
      // ->with('institutionSettings')
      ->first();

    abort_unless($institutionModel, 403, 'Institution not found for this user');

    return $institutionModel;
  }

  static function generateInstitutionCode()
  {
    $key = mt_rand(100000, 999999);
    while (Institution::whereCode($key)->first()) {
      $key = mt_rand(100000, 999999);
    }
    return $key;
  }

  function courses()
  {
    return $this->hasMany(Course::class);
  }
  function examContents()
  {
    return $this->hasMany(ExamContent::class);
  }

  function events()
  {
    return $this->hasMany(Event::class);
  }

  function examActivations()
  {
    return $this->hasMany(ExamActivation::class);
  }

  function fundings()
  {
    return $this->hasMany(Funding::class);
  }

  function gatewayPayments()
  {
    return $this->hasMany(GatewayPayment::class);
  }

  function students()
  {
    return $this->hasMany(Student::class);
  }

  function grades()
  {
    return $this->hasMany(Grade::class);
  }

  function institutionUsers()
  {
    return $this->hasMany(InstitutionUser::class);
  }

  function createdByUser()
  {
    return $this->hasMany(User::class, 'created_by_user_id', 'id');
  }
}
