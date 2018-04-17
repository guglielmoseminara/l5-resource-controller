<?php

namespace RafflesArgentina\ResourceController\Traits;

use Lang;
use Storage;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait WorksWithFileUploads
{
    /**
     * Upload request files and update or create relations handling array type request data.
     *
     * @param Request     $request      The Request object.
     * @param Model       $model        The eloquent model.
     * @param string|null $relativePath The file uploads relative path.
     *
     * @return void
     */
    public function uploadFiles(Request $request, Model $model, $relativePath = null)
    {
        if (!$relativePath) {
            $relativePath = $this->getDefaultRelativePath();
        }

        $files = $request->files;
        foreach ($files as $relationName => $uploadedFiles) {
            $this->_checkFileRelationExists($model, $relationName);
            $relation = $model->{$relationName}();

            foreach ($uploadedFiles as $id => $uploadedFile) {
                if (!$uploadedFile->isValid()) {
                    throw new UploadException($uploadedFile->getError());
                }

                $location = $this->getUploadedFileLocation($uploadedFile, $relativePath);
                $this->handleFileRelations($model, $relation, $location, $id);
            }
        }
    }

    /**
     * Get storage path for the configured driver.
     *
     * @param string $relativePath The relative path.
     *
     * @return string
     */
    protected function getStoragePath($relativePath)
    {
        return Storage::disk()->getDriver()->getAdapter()->getPathPrefix().$relativePath;
    }

    /**
     * Get default relative path.
     *
     * @return string
     */
    protected function getDefaultRelativePath()
    {
        return 'uploads/';
    }

    /**
     * Get location column.
     *
     * @return string
     */
    protected function getLocationColumn()
    {
        return 'location';
    }

    /**
     * Handle file relations.
     *
     * @param Model    $model    The eloquent model.
     * @param Relation $relation The eloquent relation.
     * @param string   $location The uploaded file location.
     * @param string   $id       The related model id.
     *
     * @return void
     */
    protected function handleFileRelations(Model $model, Relation $relation, $location, $id)
    {
        switch (true) {
        case $relation instanceof HasOne:
            $this->updateOrCreateFileHasOne($model, $relation, $location, $id);
            break;
        case $relation instanceof BelongsTo:
            $this->updateOrCreateFileBelongsToOne($model, $relation, $location, $id);
            break;
        case $relation instanceof HasMany:
            $this->updateOrCreateFileHasMany($model, $relation, $location, $id);
            break;
        case $relation instanceof BelongsToMany:
            $this->updateOrCreateFileBelongsToMany($model, $relation, $location, $id);
            break;
        }
    }

    /**
     * Get the uploaded file location from the UploadedFile object.
     *
     * @param UploadedFile $uploadedFile The UploadedFile object.
     * @param string       $relativePath The uploaded file relative path.
     *
     * @return string
     */
    protected function getUploadedFileLocation(UploadedFile $uploadedFile, $relativePath)
    {
        $extension = $uploadedFile->guessExtension();
        $filename = str_random().'.'.$extension;

        $storagePath = $this->getStoragePath($relativePath);

        $uploadedFile->move($storagePath, $filename);

        $location = $relativePath.$filename;

        return $location; 
    }

    /**
     * HasOne file relation updateOrCreate logic.
     *
     * @param Model       $model    The eloquent model.
     * @param Relation    $relation The eloquent relation.
     * @param string      $location The uploaded file location.
     * @param string|null $id       The related model id.
     *
     * @return void
     */
    protected function updateOrCreateFileHasOne(Model $model, Relation $relation, $location, $id = null)
    {
        $column = $this->getLocationColumn();

        if (!$relation->first()) {
            $relation->create([$column => $location]);
        } else {
            $relation->update([$column => $location]);
        }
    }

    /**
     * BelongsToOne file relation updateOrCreate logic.
     *
     * @param Model       $model    The eloquent model.
     * @param Relation    $relation The eloquent relation.
     * @param string      $location The uploaded file location.
     * @param string|null $id       The related model id.
     *
     * @return void
     */
    protected function updateOrCreateFileBelongsToOne(Model $model, Relation $relation, $location, $id = null)
    {
        $related = $relation->getRelated();

        $column = $this->getLocationColumn();

        if (!$relation->first()) {
            $relation->associate($related->create([$column => $location]));
            $model->save();
        } else {
            $relation->update([$column => $location]);
        }
    }

    /**
     * HasMany file relation updateOrCreate logic.
     *
     * @param Model       $model    The eloquent model.
     * @param Relation    $relation The eloquent relation.
     * @param string      $location The uploaded file location.
     * @param string|null $id       The related model id.
     *
     * @return void
     */
    protected function updateOrCreateFileHasMany(Model $model, Relation $relation, $location, $id = null)
    {
        $column = $this->getLocationColumn();

        $relation->updateOrCreate(['id' => $id], [$column => $location]);
    }

    /**
     * BelongsToMany file relation updateOrCreate logic.
     *
     * @param Model       $model    The eloquent model.
     * @param Relation    $relation The eloquent relation.
     * @param string      $location The uploaded file location.
     * @param string|null $id       The related model id.
     *
     * @return void
     */
    protected function updateOrCreateFileBelongsToMany(Model $model, Relation $relation, $location, $id = null)
    {
        $related = $relation->getRelated();

        $column = $this->getLocationColumn();

        $related->updateOrCreate(['id' => $id], [$column => $location]);
        $relation->syncWithoutDetaching($id, [$column => $location]);
    }

    /**
     * Throw an exception if request file is not named after an existent relation.
     *
     * @param Model  $model        The eloquent model.
     * @param string $relationName The eloquent relation name.
     *
     * @throws UploadException
     *
     * @return void
     */
    private function _checkFileRelationExists(Model $model, $relationName)
    {
        if (!method_exists($model, $relationName) || !$model->{$relationName}() instanceof Relation) {
            if (Lang::has('resource-controller.filerelationinexistent')) {
                $message = trans('resource-controller.filerelationinexistent', ['relationName' => $relationName]);
            } else {
                $message = "Request file '{$relationName}' is not named after an existent relation.";
            }

            throw new UploadException($message);
        }
    }
}
