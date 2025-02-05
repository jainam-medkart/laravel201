<?php

namespace App\Http\Controllers;

use App\Constants\DraftProductStatus;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Jobs\PublishDraftProduct;
use App\Repositories\DraftProductRepository;
use App\Repositories\PublishProductRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class DraftProductController extends Controller {

    protected $draftProductRepository;
    protected $publishProductRepository;

    public function __construct(DraftProductRepository $draftProductRepository, PublishProductRepository $publishProductRepository)
    {
        $this->draftProductRepository = $draftProductRepository;
        $this->publishProductRepository = $publishProductRepository;
    }

    public function getAll()
    {
        $perPage = request()->get('per_page', 15);
        $draftProducts = $this->draftProductRepository->getAll($perPage);
        return ApiSuccessResponse::create($draftProducts, 'All draft products fetched successfully');
    }

    public function getAllActive()
    {
        $perPage = request()->get('per_page', 15);
        $draftProducts = $this->draftProductRepository->getActive($perPage);
        return ApiSuccessResponse::create($draftProducts, 'Draft products fetched successfully');
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
                'mrp' => 'required|numeric',
                'is_active' => 'boolean',
                'is_banned' => 'boolean',
                'is_assured' => 'boolean',
                'is_discountinued' => 'boolean',
                'is_refrigerated' => 'boolean',
                'is_published' => 'boolean',
                'category_id' => 'required|exists:categories,id',
                'molecule_ids' => 'array|exists:molecules,id',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            
            $data = $request->except('status');
            $data = $request->except('is_published');
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

    public function publish($id)
    {
        try {
            $draftProduct = $this->draftProductRepository->getById($id);

            if ($draftProduct->is_published) {
                return ApiErrorResponse::create(new Exception('Draft product is already published.'), 400);
            }

            if ($draftProduct->status !== DraftProductStatus::APPROVED) {
                return ApiErrorResponse::create(new Exception('Draft product status must be approved to publish.'), 400);
            }

            $draftProduct->load(['category', 'molecules']);

            PublishDraftProduct::dispatch($draftProduct, Auth::id());

            return ApiSuccessResponse::create(null, 'Draft product is being published');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }

    public function updatePublished($id)
    {
        try {
            $draftProduct = $this->draftProductRepository->getById($id);

            if (!$draftProduct->is_published) {
                return ApiErrorResponse::create(new Exception('Draft product is not published.'), 400);
            }
            
            if ($draftProduct->status !== DraftProductStatus::APPROVED) {
                return ApiErrorResponse::create(new Exception('Updated Draft product status must be approved to publish.'), 400);
            }

            // Fetch the current draft product data and pass it to the repository
            $this->publishProductRepository->updatePublishedProduct($id);

            return ApiSuccessResponse::create(null, 'Published product update job dispatched successfully');
        } catch (ValidationException $e) {
            return ApiErrorResponse::create($e, 422, $e->errors());
        } catch (QueryException $e) {
            return ApiErrorResponse::create($e, 400);
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|string|in:' . implode(',', [DraftProductStatus::DRAFT, DraftProductStatus::PENDING, DraftProductStatus::APPROVED, DraftProductStatus::REJECTED]),
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $request->only('status');
            $data['updated_by'] = auth()->id();

            $draftProduct = $this->draftProductRepository->update($id, $data);

            return ApiSuccessResponse::create($draftProduct, 'Draft product status updated successfully');
        } catch (ValidationException $e) {
            return ApiErrorResponse::create($e, 422, $e->errors());
        } catch (QueryException $e) {
            return ApiErrorResponse::create($e, 400);
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }
}