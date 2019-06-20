<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\Model;

class Customer extends Authenticatable implements JWTSubject
{
    protected $table = 'customer';
    protected $primaryKey = "customer_id";
    protected $fillable = ['name', 'email', 'password'];
    public $timestamps = false;


    //getJWTIdentifier: gets the identifier that will be stored in the subject claim of the JWT
    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    //getJWTCustomClaims: allow us to add any custom claims we want added to the JWT.
    public function getJWTCustomClaims()
    {
      return [];
    }

    public function generateToken($password)
    {
        $credentials = [
            'email' => $this->email,
            'password' => $password
        ];
        return auth()->attempt($credentials);
    }
}
