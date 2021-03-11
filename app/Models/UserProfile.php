<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model{

    protected $table="user_profile";

    protected $primaryKey = "user_profile_id";

    protected $fillable = [
        "fullname",
        "age",
        "sex",
        "address",
        "userin",
        "usermod"
    ];

    public $timestamp = false;
}