<?php

namespace App\Http\Controllers;

use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    try {
         $categories = ProductCategoryModel::getCategories();
         $response = array(
                'success' => true,
                'message' => 'Successfully get product categories data.',
                'data' => $categories
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store (Request $request) {
		try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
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

            $product = ProductCategoryModel::createProductCategory($validator->validated());
            $response = array(
                'success' => true,
                'message' => 'Successfully create product category data',
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

    /**
     * Display the specified resource.
     */
   public function show (int $category_id) {
		try {
            $category = ProductCategoryModel::getProductCategoryById($category_id);
            $response = array(
                'success' => true,
                'message' => 'Successfully get products Category data.',
                'data' => $category
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
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategoryModel $productCategories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
public function update (Request $request, int $category_id) {
		try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
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

            $product = ProductCategoryModel::updateProductCategory($category_id, $validator->validated());
            $response = array(
                'success' => true,
                'message' => 'Successfully update product category data',
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

    /**
     * Remove the specified resource from storage.
     */
   	public function destroy (int $category_id) {
		try {
            $product = ProductCategoryModel::deleteProductCategory($category_id);
            $response = array(
                'success' => true,
                'message' => 'Successfully delete product category data',
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
