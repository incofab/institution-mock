<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
/**
 * @author Incofab
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $phone
 * @property float $balance
 * @property int $pin_balance
 *
 */
class User extends Authenticatable
{
  use Notifiable, HasApiTokens, HasFactory;

  protected $casts = [
    'email_verified_at' => 'datetime',
  ];
  protected $guarded = [];

  protected $hidden = ['password', 'remember_token'];

  static function ruleCreate($prefix = '')
  {
    return [
      $prefix . 'name' => ['required', 'string', 'max:255'],
      $prefix . 'email' => [
        'required',
        'string',
        'email',
        'max:255',
        'unique:users',
      ],
      $prefix . 'phone' => ['required', 'digits:11'],
      // 'username' => ['required', 'alpha_dash', 'unique:users', new \App\Rules\NotEntirelyDigits()],
      $prefix . 'password' => ['required', 'string', 'min:4', 'confirmed'],
    ];
  }

  function createLoginToken()
  {
    $loginTokenName = 'Login Token';

    // Delete old tokens, if any
    //         $this->tokens()->where('name', '=', $loginTokenName)
    //         ->where('tokenable_id', '=', $this->id)
    //         ->where('tokenable_type', '=', User::class)->delete();

    return $this->createToken($loginTokenName)->plainTextToken;
  }

  function isAdmin()
  {
    return $this->email === config('app.admin.email');
  }
}
