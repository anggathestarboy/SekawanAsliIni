<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $fillable = array(
        'product_name',
        'product_stock',
        'product_price'
    );

    // Method untuk GET semua data Products
    public static function getProducts () {
        $products = self::all();

        return $products;
    }

    // Method untuk GET data Product by ID
    public static function getProductById (int $product_id) {
        $product = self::find($product_id);

        return $product;
    }

    // Method untuk POST (create) data Product
    public static function createProduct ($data) {
        $product = self::create($data);

        return $product;
    }

    // Method untuk PATCH (update) data Product
    public static function updateProduct (int $product_id, $data) {
        $product = self::find($product_id);
        $product->update($data);

        return $product;
    }

    // Method untuk DELETE (delete) data Product
    public static function deleteProduct (int $product_id) {
        $product = self::find($product_id);
        $product->destroy($product);

        return $product;
    }
}
