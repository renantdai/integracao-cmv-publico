<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capture extends Model
{
    use HasFactory;

    protected $fillable = [
        'idCam',
        'idEquipment',
        'plate',
        'statusSend',
        'fileName',
        'captureDateTime'
    ];
}
