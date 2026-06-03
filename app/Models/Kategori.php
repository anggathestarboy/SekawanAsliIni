<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;


#[Guarded("")]
#[Table("kategori")]
class Kategori extends Model
{
    protected $primaryKey = 'kategori_id';


    public static function getDetail(int $kategori_id)
    {
        $kategori = self::find($kategori_id);
        return $kategori;
    }
}
