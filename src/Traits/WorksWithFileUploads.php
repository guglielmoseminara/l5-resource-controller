<?php

namespace RafflesArgentina\ResourceController\Traits;

use Lang;
use Storage;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait WorksWithFileUploads
{
    /**
     * Upload request files and update or create relations handling array type request data.
     *
     * @param Request     $request      The Request object.
     * @param Model       $model        The eloquent model.
     * @param string|null $relativePath The file uploads relative path.
     *
     * @return Request
     */
    public function uploadFiles(Request $request, Model $model, $relativePath = null)
    {
        if (!$relativePath) {
            $relativePath = $this->getDefaultRelativePath();
        }

        $fileBag = $request->files;
        foreach ($fileBag->all() as $paramName => $uploadedFiles) {
            $attributes = $model->getAttributes();
            if (array_key_exists($paramName, $attributes)) {
                $this->handleNonMultipleFileUploads($model, $paramName, $uploadedFiles, $relativePath);
            } else {
                $this->handleMultipleFileUploads($request, $model, $paramName, $uploadedFiles, $relativePath);
            }
        }

        return $request;
    }

    /**
     * Get the name of the file.
     *
     * @param UploadedFile $uploadedFile The UploadedFile object.
     *
     * @return string
     */
    protected function getFilename(UploadedFile $uploadedFile)
    {
        $extension = $uploadedFile->guessExtension();
        $filename = Str::random().'.'.$extension;

        return $filename;
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
     * Move the uploaded file to specified filename and destination.
     *
     * @param UploadedFile $uploadedFile The UploadedFile object.
     * @param string       $filename     The name of the file.
     * @param string       $destination  The file destination.
     *
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    protected function moveUploadedFile($uploadedFile, $filename, $destination)
    {
        return $uploadedFile->move($destination, $filename);
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
     * Get default relative path.
     *
     * @return string
     */
    protected function getDefaultRelativePath()
    {
        return 'uploads/';
    }

    /**
     * Handle multiple file uploads.
     *
     * @param Request $request       The Request object.
     * @param Model   $model         The eloquent model.
     * @param string  $paramName     The name of the file param.
     * @param array   $uploadedFiles An array of UploadedFile objects.
     * @param string  $relativePath  The file uploads relative path.
     *
     * @return void
     */
    protected function handleMultipleFileUploads(Request $request, Model $model, $paramName, $uploadedFiles, $relativePath)
    {
        $this->_checkFileRelationExists($model, $paramName);

        $data = $request->attributes->all();
        $fileBag = $request->files;
        foreach ($uploadedFiles as $index => $uploadedFile) {
            if (!$uploadedFile->isValid()) {
                throw new UploadException($uploadedFile->getError());
            }

            $filename = $this->getFilename($uploadedFile);
            $storagePath = $this->getStoragePath($relativePath);
            $this->moveUploadedFile($uploadedFile, $filename, $storagePath);

            $location = $relativePath.$filename;

            if (count($fileBag->get($paramName)) > 1) {
                $data[$paramName][$index] = [$this->getLocationColumn() => $location];
            } else {
                $data[$paramName][$this->getLocationColumn()] = $location;
            }

            $request->merge($data);
        }
    }

    /**
     * Handle non-multiple file uploads.
     *
     * @param Model        $model        The eloquent model.
     * @param string       $paramName    The name of the file param.
     * @param UploadedFile $uploadedFile The UploadedFile object.
     * @param string       $relativePath The file uploads relative path.
     *
     * @return void
     */
    protected function handleNonMultipleFileUploads(Model $model, $paramName, $uploadedFile, $relativePath)
    {
        if (!$uploadedFile->isValid()) {
            throw new UploadException($uploadedFile->getError());
        }

        $filename = $this->getFilename($uploadedFile);
        $destination = $this->getStoragePath($relativePath);
        $this->moveUploadedFile($uploadedFile, $filename, $destination);

        $location = $relativePath.$filename;

        $model->{$paramName} = $location;
        $model->save();
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
        if ((!method_exists($model, $relationName) && !$model->{$relationName}() instanceof Relation)) {
            if (Lang::has('resource-controller.filerelationinexistent')) {
                $message = trans('resource-controller.filerelationinexistent', ['relationName' => $relationName]);
            } else {
                $message = "Request file '{$relationName}' is not named after an existent relation.";
            }
            throw new UploadException($message);
        }
    }
}
