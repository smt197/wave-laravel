<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory,Notifiable;

    protected $fillable = ['surname','telephone', 'adresse', 'user_id','qr_code', 'solde', 'soldeMax', 'cumulTransaction'];
    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function routeNotificationForSms()
    {
        return $this->telephone;  // Champ utilisé pour l'envoi des SMS
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at'); // Retourne le constructeur de requêtes
    }

    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('read_at'); // Retourne le constructeur de requêtes
    }

}




