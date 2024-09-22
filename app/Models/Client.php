<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = ['document', 'name', 'email', 'phone'];
    
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
