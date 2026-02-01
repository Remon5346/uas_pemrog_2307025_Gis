<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    // INI YANG PENTING: Memberi izin kolom mana saja yang boleh diisi
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'notes'
    ];
}