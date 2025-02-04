<?php

namespace App\Repositories;

use App\Models\Molecule;

class MoleculeRepository
{
    public function getActive()
    {
        return Molecule::where('is_active', true)->whereNull('deleted_at')->get();
    }

    public function getAll()
    {
        return Molecule::withTrashed()->get();
    }

    public function find($id)
    {
        return Molecule::findOrFail($id);
    }

    public function create(array $data)
    {
        return Molecule::create($data);
    }

    public function update($id, array $data)
    {
        $molecule = Molecule::findOrFail($id);
        $molecule->update($data);
        return $molecule;
    }

    public function softDelete($id)
    {
        $molecule = Molecule::findOrFail($id);
        $molecule->update([
            'is_active' => false,
            'deleted_by' => auth()->id(),
        ]);
        $molecule->delete();
        return $molecule;
    }

    public function restore($id)
    {
        $molecule = Molecule::withTrashed()->findOrFail($id);
        $molecule->update([
            'is_active' => true,
            'deleted_by' => null,
            'deleted_at' => null,
        ]);
        $molecule->restore();
        return $molecule;
    }
}
