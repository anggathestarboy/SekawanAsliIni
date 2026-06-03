<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Guarded("")]
#[Table("alat")]
class alat extends Model
{
    protected $primaryKey = 'alat_id';

    public static function getDetail(int $alat_id)
    {
        $alat = self::find($alat_id);
        return $alat;
    }
}
