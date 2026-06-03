<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Guarded("")]
#[Table("penyewaan")]
class penyewaan extends Model
{
    protected $primaryKey = 'penyewaan_id';

    public static function getDetail(int $penyewaan_id)
    {
        $penyewaan = self::with('details')->find($penyewaan_id);
        return $penyewaan;
    }

    public function details()
    {
        return $this->hasMany(penyewaanDetail::class, 'penyewaan_detail_penyewaan_id', 'penyewaan_id');
    }
}
