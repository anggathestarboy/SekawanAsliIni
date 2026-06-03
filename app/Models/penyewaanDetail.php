<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Guarded("")]
#[Table("penyewaan_detail")]
class penyewaanDetail extends Model
{
    protected $primaryKey = 'id';

    public static function getDetail(int $id)
    {
        $penyewaanDetail = self::find($id);
        return $penyewaanDetail;
    }
}
