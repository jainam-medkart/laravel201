<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryCreateRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Repositories\CategoryRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class CategoryController extends Controller {

    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAll()
    {
        return ApiSuccessResponse::create($this->categoryRepository->getAll(), 'All categories fetched successfully');
    }

    public function getAllActive()
    {
        return ApiSuccessResponse::create($this->categoryRepository->getActive(), 'Active categories fetched successfully');
    }

    public function getById($id)
    {
        try {
            $category = $this->categoryRepository->find($id);
            return ApiSuccessResponse::create($category, 'Category fetched successfully');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 404);
        }
    }

    public function create(CategoryCreateRequest $request)
    {
        try {
            $data = $request->all();
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();
            $category = $this->categoryRepository->create($data);

            return ApiSuccessResponse::create($category, 'Category created successfully', 201);
        } catch (ValidationException $e) {
            return ApiErrorResponse::create($e, 422, $e->errors());
        } catch (QueryException $e) {
            return ApiErrorResponse::create($e, 400);
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }

    public function update(CategoryUpdateRequest $request, $id)
    {
        try {
            $data = $request->all();
            $data['updated_by'] = auth()->id();
            $category = $this->categoryRepository->update($id, $data);

            return ApiSuccessResponse::create($category, 'Category updated successfully');
        } catch (ValidationException $e) {
            return ApiErrorResponse::create($e, 422, $e->errors());
        } catch (QueryException $e) {
            return ApiErrorResponse::create($e, 400);
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }

    public function delete($id)
    {
        try {
            $category = $this->categoryRepository->softDelete($id);
            return ApiSuccessResponse::create($category, 'Category deleted successfully');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }

    public function restore($id)
    {
        try {
            $category = $this->categoryRepository->restore($id);
            return ApiSuccessResponse::create($category, 'Category restored successfully');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }
}