<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Models\PublishedProduct;
use App\Repositories\PublishedProductRepository;
use Exception;
use Illuminate\Http\Request;

class PublishedProductController extends Controller {

    protected $publishProductRepository;

    public function __construct(PublishedProductRepository $publishProductRepository)
    {
        $this->publishProductRepository = $publishProductRepository;
    }

    public function getAll()
    {
        $perPage = request()->get('per_page', 15);
        $publishedProducts = $this->publishProductRepository->getAll($perPage);
        return ApiSuccessResponse::create($publishedProducts, 'All published products fetched successfully');
    }

    public function getAllActive()
    {
        $perPage = request()->get('per_page', 15);
        $publishedProducts = $this->publishProductRepository->getActive($perPage);
        return ApiSuccessResponse::create($publishedProducts, 'Active published products fetched successfully');
    }

    public function getById($id)
    {
        try {
            $publishedProduct = $this->publishProductRepository->getById($id);
            return ApiSuccessResponse::create($publishedProduct, 'Published product fetched successfully');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 404);
        }
    }
    
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');

            $results = PublishedProduct::search($query)->get();

            return ApiSuccessResponse::create($results, 'Search results fetched successfully');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }
}