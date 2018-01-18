<?php

namespace RafflesArgentina\ResourceController\Traits;

use Lang;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait WorksWithRelations
{
    /**
     * Store related models treating array type request data as Eloquent relations.
     *
     * @param \Illuminate\Http\Request            $request The request object.
     * @param \Illuminate\Database\Eloquent\Model $model   The eloquent model.
     *
     * @return void
     */
    public function storeRelatedModels(Request $request, Model $model)
    {
        $data = $request->all();
        foreach ($data as $relationName => $fillables) {
            if (is_array($request->{$relationName}) && !$request->hasFile($relationName)) {
                $this->_checkData2RelationExists($model, $relationName);
                $relation = $model->{$relationName}();
                $related = $relation->getRelated();

                switch (true) {
                case $relation instanceof HasOne:
                    $relation->create($fillables);
                    break;
                case $relation instanceof BelongsTo:
                    $related = $related->create($fillables);
                    $relation->associate($related);
                    $model->save();
                    break;
                case $relation instanceof HasMany:
                    foreach ($fillables as $id => $fillables) {
                        $relation->firstOrCreate(['id' => $id], $fillables);
                    }
                    break;
                case $relation instanceof BelongsToMany:
                    $relation->attach(array_keys($fillables));
                    break;
                }
            }
        }
    }

    /**
     * Update related models treating array type request data as Eloquent relations.
     *
     * @param \Illuminate\Http\Request            $request The request object
     * @param \Illuminate\Database\Eloquent\Model $model   The eloquent model.
     *
     * @return void
     */
    public function updateRelatedModels(Request $request, Model $model)
    {
        $data = $request->all();
        foreach ($data as $relationName => $fillables) {
            if (is_array($request->{$relationName}) && !$request->hasFile($relationName)) {
                $this->_checkData2RelationExists($model, $relationName);
                $relation = $model->{$relationName}();
                $related = $relation->getRelated();

                switch (true) {
                case $relation instanceof HasOne:
                    if (!$relation->first()) {
                        $relation->create($fillables);
                    } else {
                        $relation->update($fillables);
                    }
                    break;
                case $relation instanceof BelongsTo:
                    if (!$relation->first()) {
                        $relation->associate($related->create($fillables));
                        $model->save();
                    } else {
                        $relation->update($fillables);
                    }
                    break;
                case $relation instanceof HasMany:
                    foreach ($fillables as $id => $fillables) {
                        $relation->updateOrCreate(['id' => $id], $fillables);
                    }
                    break;
                case $relation instanceof BelongsToMany:
                    $relation->sync(array_keys($fillables));
                    break;
                }
            }
        }
    }

    /**
     * Throw an exception if array type request data is not named after an existent Eloquent relation.
     *
     * @param \Illuminate\Database\Eloquent\Model $model        The eloquent model.
     * @param string                              $relationName The eloquent relation name.
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     *
     * @return void
     */
    private function _checkData2RelationExists(Model $model, string $relationName)
    {
        if (!method_exists($model, $relationName) || !$model->{$relationName}() instanceof Relation) {
            if (Lang::has('resource-controller.data2relationinexistent')) {
                $message = trans('resource-controller.data2relationinexistent', ['relationName' => $relationName]);
            } else {
                $message = "Array type request data '{$relationName}' is not named after an existent relation.";
            }

            throw new MassAssignmentException($message);
        }
    }
}
