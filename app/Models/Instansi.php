<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Instansi extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'instansi';

    public function pengajuan() {
        return $this->hasMany(Pengajuan::class, 'id_instansi');
    }
    public function users() {
        return $this->hasMany(User::class, 'id_instansi');
    }
}
