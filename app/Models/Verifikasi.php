<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verifikasi extends Model
{
    protected $guarded = ['id'];
    protected $table = 'verifikasi';

    public function pengajuan() {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
