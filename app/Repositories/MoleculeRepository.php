<?php

namespace App\Repositories;

use App\Models\Molecule;

class MoleculeRepository
{
    public function getActive()
    {
        return Molecule::where('is_active', true)->get();
    }

    public function getAll()
    {
        return Molecule::all();
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
        
        return $molecule;
    }
}
