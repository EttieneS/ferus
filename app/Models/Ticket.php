<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Farm;

class Ticket extends Model {
   use HasFactory;
   
   protected $primaryKey = 'id';
   
   protected $fillable = [
      'id',
      'title',
      'subject',
      'message_id',
      'queue_id'
   ];
}
?>