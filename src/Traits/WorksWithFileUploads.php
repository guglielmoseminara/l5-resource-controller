<?php

namespace RafflesArgentina\ResourceController\Traits;

use Lang;
use Storage;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait WorksWithFileUploads
{
    /**
     * Upload request files.
     *
     * @param \Illuminate\Http\Request            $request      The request object
     * @param \Illuminate\Database\Eloquent\Model $model        The eloquent model.
     * @param string|null                         $relativePath The file uploads relative path.
     *
     * @return void
     */
    public function uploadFiles(Request $request, Model $model, $relativePath = null)
    {
        if (!$relativePath) {
            $relativePath = $this->setRelativePath();
        }
        $storagePath = $this->getStoragePath($relativePath);

        $files = $request->files;
        foreach ($files as $relationName => $uploadedFiles) {
            $this->_checkFile2RelationExists($model, $relationName);
            $relation = $model->{$relationName}();

            foreach ($uploadedFiles as $id => $uploadedFile) {
                if ($uploadedFile->isValid()) {
                    $extension = $uploadedFile->guessExtension();
                    $filename = str_random().'.'.$extension;
                    $uploadedFile->move($storagePath, $filename);
                    $relativeLocation = $relativePath.$filename;
                    $this->updateOrCreateFileRelation($model, $relation, $id, $relativeLocation);
                }
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
    public function getStoragePath($relativePath)
    {
        return Storage::disk()->getDriver()->getAdapter()->getPathPrefix().$relativePath;
    }

    /**
     * Set relative path.
     *
     * @return string
     */
    public function setRelativePath()
    {
        return 'uploads/';
    }

    /**
     * Update or create file relation with file location.
     *
     * @param \Illuminate\Database\Eloquent\Model              $model            The eloquent model.
     * @param \Illuminate\Database\Eloquent\Relations\Relation $relation         The eloquent relation.
     * @param string                                           $id               The id for the existent or new relation.
     * @param string                                           $relativeLocation The uploaded file relative location.
     *
     * @return void
     */
    public function updateOrCreateFileRelation(Model $model, Relation $relation, $id, $relativeLocation)
    {
        $column = $this->getLocationColumn();
        $related = $relation->getRelated();

        switch (true) {
        case $relation instanceof BelongsTo:
            if (!$relation->first()) {
                $relation->associate($related->create([$column => $relativeLocation]));
                $model->save();
            } else {
                $relation->update([$column => $relativeLocation]);
            }
            break;
        case $relation instanceof BelongsToMany:
            $relation->sync($id, [$column => $relativeLocation]);
            break;
        default:
            $relation->updateOrCreate(['id' => $id], [$column => $relativeLocation]);
        }
    }

    /**
     * Get location column.
     *
     * @return string
     */
    public function getLocationColumn()
    {
        return 'location';
    }

    /**
     * Throw an exception if request file is not named after an existent relation.
     *
     * @param \Illuminate\Database\Eloquent\Model $model        The eloquent model.
     * @param string                              $relationName The eloquent relation name.
     *
     * @throws \Symfony\Component\HttpFoundation\File\Exception\UploadException
     *
     * @return void
     */
    private function _checkFile2RelationExists(Model $model, $relationName)
    {
        if (!method_exists($model, $relationName) || !$model->{$relationName}() instanceof Relation) {
            if (Lang::has('resource-controller.file2relationinexistent')) {
                $message = trans('resource-controller.file2relationinexistent', ['relationName' => $relationName]);
            } else {
                $message = "Request file '{$relationName}' is not named after an existent relation.";
            }

            throw new UploadException($message);
        }
    }
}
