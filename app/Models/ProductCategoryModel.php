<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;



#[Table("product_categories")]
#[Guarded("")]
class ProductCategoryModel extends Model
{
    public static function getCategories()
    {
        $categories = self::all();
        return $categories;
    }



     public static function getProductCategoryById (int $category_id) {
        $category = self::find($category_id);

        return $category;
    }


    public static function createProductCategory($data)
    {
        $category = self::create($data);
        return $category;
    }

     public static function updateProductCategory (int $category_id, $data) {
        $category = self::find($category_id);
        $category->update($data);

        return $category;
    }


      public static function deleteProductCategory (int $category_id) {
        $category = self::find($category_id);
        $category->destroy($category_id);

        return $category;
    }
}
