<?php

// app/Models/Customer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model {
    use HasFactory, SoftDeletes;
    
    protected $primaryKey = 'id';

    protected $fillable = ['id', 'full_name', 'email', 'phone', 'address'];    
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
