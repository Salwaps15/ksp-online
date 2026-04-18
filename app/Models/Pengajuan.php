<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    protected $guarded = ['id'];
    protected $table = 'pengajuan';

    public function instansi() {
        return $this->belongsTo(Instansi::class, 'id_instansi');
    }
    public function documents() {
        return $this->hasMany(Document::class, 'id_pengajuan');
    }
    public function verifikasis() {
        return $this->hasMany(Verifikasi::class, 'id_pengajuan');
    }
}
