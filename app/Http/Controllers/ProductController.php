<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index () {
		try {
            $products = ProductModel::getProducts();
            $response = array(
                'success' => true,
                'message' => 'Successfully get products data.',
                'data' => $products
            );

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => 'Sorry, there error in internal server',
                'data' => null,
                'errors' => $error->getMessage()
            );

            return response()->json($response, 500);
        }
	}
	
	public function show (int $product_id) {
		try {
            $products = ProductModel::getProductById($product_id);
            $response = array(
                'success' => true,
                'message' => 'Successfully get products data.',
                'data' => $products
            );

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => 'Sorry, there error in internal server',
                'data' => null,
                'errors' => $error->getMessage()
            );

            return response()->json($response, 500);
        }
	}
	
	public function store (Request $request) {
		try {
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:100',
                'product_stock' => 'required|numeric',
                'product_price' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to create data product. Data not completed, please check your data.',
                    'data' => null,
                    'errors' => $validator->errors()
                );

                return response()->json($response, 400);
            }

            $product = ProductModel::createProduct($validator->validated());
            $response = array(
                'success' => true,
                'message' => 'Successfully create product data',
                'data' => $product,
            );

            return response()->json($response, 201);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => 'Sorry, there error in internal server',
                'data' => null,
                'errors' => $error->getMessage()
            );

            return response()->json($response, 500);
        }
	}
	
	public function update (Request $request, int $product_id) {
		try {
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:100',
                'product_stock' => 'required|numeric',
                'product_price' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to create data product. Data not completed, please check your data.',
                    'data' => null,
                    'errors' => $validator->errors()
                );

                return response()->json($response, 400);
            }

            $product = ProductModel::updateProduct($product_id, $validator->validated());
            $response = array(
                'success' => true,
                'message' => 'Successfully update product data',
                'data' => $product,
            );

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => 'Sorry, there error in internal server',
                'data' => null,
                'errors' => $error->getMessage()
            );

            return response()->json($response, 500);
        }
	}
	
	public function destroy (int $product_id) {
		try {
            $product = ProductModel::deleteProduct($product_id);
            $response = array(
                'success' => true,
                'message' => 'Successfully delete product data',
                'data' => $product,
            );

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => 'Sorry, there error in internal server',
                'data' => null,
                'errors' => $error->getMessage()
            );

            return response()->json($response, 500);
        }
	}
}
