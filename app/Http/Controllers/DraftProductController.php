<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Repositories\DraftProductRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class DraftProductController extends Controller {

    protected $draftProductRepository;

    public function __construct(DraftProductRepository $draftProductRepository)
    {
        $this->draftProductRepository = $draftProductRepository;
    }

    public function getAll()
    {
        return ApiSuccessResponse::create($this->draftProductRepository->getAll(), 'All draft products fetched successfully');
    }

    public function getAllActive()
    {
        return ApiSuccessResponse::create($this->draftProductRepository->getActive(), 'Draft products fetched successfully');
    }

    public function getById($id)
    {
        try {
            $draftProduct = $this->draftProductRepository->getById($id);
            return ApiSuccessResponse::create($draftProduct, 'Draft product fetched successfully');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 404);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:draft_products,name',
                'description' => 'nullable|string',
                'manufacturer' => 'required|string|max:255',
                'price' => 'required|numeric',
                'mrp' => 'required|numeric',
                'is_active' => 'boolean',
                'is_banned' => 'boolean',
                'is_assured' => 'boolean',
                'is_discountinued' => 'boolean',
                'is_refrigerated' => 'boolean',
                'is_published' => 'boolean',
                'status' => 'required|string|in:draft,pending,approved,rejected',
                'category_id' => 'required|exists:categories,id',
                'molecule_ids' => 'array|exists:molecules,id',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $request->all();
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();
            $draftProduct = $this->draftProductRepository->create($data);

            return ApiSuccessResponse::create($draftProduct, 'Draft product created successfully', 201);
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
                'name' => 'required|string|max:255|unique:draft_products,name,' . $id,
                'description' => 'nullable|string',
                'manufacturer' => 'required|string|max:255',
                'price' => 'required|numeric',
                'mrp' => 'required|numeric',
                'is_active' => 'boolean',
                'is_banned' => 'boolean',
                'is_assured' => 'boolean',
                'is_discountinued' => 'boolean',
                'is_refrigerated' => 'boolean',
                'is_published' => 'boolean',
                'status' => 'required|string|in:draft,pending,approved,rejected',
                'category_id' => 'required|exists:categories,id',
                'molecule_ids' => 'array|exists:molecules,id',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $request->all();
            $data['updated_by'] = auth()->id();
            $draftProduct = $this->draftProductRepository->update($id, $data);

            return ApiSuccessResponse::create($draftProduct, 'Draft product updated successfully');
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
            $draftProduct = $this->draftProductRepository->softDelete($id);
            return ApiSuccessResponse::create($draftProduct, 'Draft product deleted successfully');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }

    public function restore($id)
    {
        try {
            $draftProduct = $this->draftProductRepository->restore($id);
            return ApiSuccessResponse::create($draftProduct, 'Draft product restored successfully');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }
}