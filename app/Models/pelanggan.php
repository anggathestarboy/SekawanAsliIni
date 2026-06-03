<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Guarded("")]
#[Table("pelanggan")]
class pelanggan extends Model
{
    protected $primaryKey = 'pelanggan_id';

    public static function getDetail(int $pelanggan_id)
    {
        $pelanggan = self::find($pelanggan_id);
        return $pelanggan;
    }
}
