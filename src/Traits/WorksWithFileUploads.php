<?php

namespace RafflesArgentina\ResourceController\Traits;

use Lang;
use Storage;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

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

        $data = $request->attributes->all();

        $fileBag = $request->files;
        foreach ($fileBag->all() as $name => $parameters) {
            $this->_checkFileRelationExists($model, $name);
            $relation = $model->{$name}();

            foreach ($parameters as $index => $file) {
                if (!$file->isValid()) {
                    throw new UploadException($file->getError());
                }

                $filename = $this->getFilename($file);
                $destination = $this->getStoragePath($relativePath);
                $this->moveUploadedFile($file, $filename, $destination);

                $location = $relativePath.$filename;

                if (count($fileBag->get($name)) > 1) {
                    $data[$name][$index] = [$this->getLocationColumn() => $location];
                } else {
                    $data[$name][$this->getLocationColumn()] = $location;
                }

                $request->merge($data);
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
        $filename = str_random().'.'.$extension;

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
