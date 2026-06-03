<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Guarded("")]
#[Table("pelanggan_data")]
class pelangganData extends Model
{
    protected $primaryKey = 'pelanggan_data_id';

    public static function getDetail(int $pelanggan_data_id)
    {
        $pelangganData = self::find($pelanggan_data_id);
        return $pelangganData;
    }
}
