<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Repositories\MoleculeRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class MoleculeController extends Controller {

    protected $moleculeRepository;

    public function __construct(MoleculeRepository $moleculeRepository)
    {
        $this->moleculeRepository = $moleculeRepository;
    }

    public function getAll()
    {
        return ApiSuccessResponse::create($this->moleculeRepository->getAll(), 'All molecules fetched successfully');
    }

    public function getAllActive()
    {
        return ApiSuccessResponse::create($this->moleculeRepository->getActive(), 'Active molecules fetched successfully');
    }

    public function getById($id)
    {
        try {
            $molecule = $this->moleculeRepository->find($id);
            return ApiSuccessResponse::create($molecule, 'Molecule fetched successfully');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 404);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:molecules,name',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $request->all();
            $data['is_active'] = true;
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();
            $molecule = $this->moleculeRepository->create($data);

            return ApiSuccessResponse::create($molecule, 'Molecule created successfully', 201);
        } catch (ValidationException $e) {
            return ApiErrorResponse::create($e, 422, $e->errors());
        } catch (QueryException $e) {
            if ($e->getCode() == '23505') { // Unique violation
                return ApiErrorResponse::create(new Exception('Molecule name must be unique'), 409);
            }
            return ApiErrorResponse::create($e, 500);
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:molecules,name,' . $id,
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $request->all();
            $data['updated_by'] = auth()->id();
            $molecule = $this->moleculeRepository->update($id, $data);

            return ApiSuccessResponse::create($molecule, 'Molecule updated successfully');
        } catch (ValidationException $e) {
            return ApiErrorResponse::create($e, 422, $e->errors());
        } catch (QueryException $e) {
            if ($e->getCode() == '23505') { // Unique violation
                return ApiErrorResponse::create(new Exception('Molecule name must be unique'), 409);
            }
            return ApiErrorResponse::create($e, 500);
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }

    public function delete($id)
    {
        try {
            $molecule = $this->moleculeRepository->softDelete($id);
            return ApiSuccessResponse::create($molecule, 'Molecule deleted successfully');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }

    public function restore($id)
    {
        try {
            $molecule = $this->moleculeRepository->restore($id);
            return ApiSuccessResponse::create($molecule, 'Molecule restored successfully');
        } catch (Exception $e) {
            return ApiErrorResponse::create($e, 500);
        }
    }
}