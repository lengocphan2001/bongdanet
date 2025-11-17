<?php

namespace App\Repositories;

/**
 * Base Repository Class
 * All repository classes should extend this class
 */
abstract class BaseRepository
{
    protected $model;

    /**
     * Get all records
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Find record by ID
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create new record
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update record
     */
    public function update($id, array $data)
    {
        $record = $this->find($id);
        if ($record) {
            $record->update($data);
            return $record;
        }
        return null;
    }

    /**
     * Delete record
     */
    public function delete($id)
    {
        $record = $this->find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}

