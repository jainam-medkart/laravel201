<?php

namespace App\Http\Controllers;

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

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

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

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

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