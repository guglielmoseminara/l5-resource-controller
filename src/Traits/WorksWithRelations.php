<?php

namespace RafflesArgentina\ResourceController\Traits;

use Lang;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\{HasOne, MorphOne, BelongsTo};
use Illuminate\Database\Eloquent\Relations\{HasMany, MorphMany, MorphToMany, BelongsToMany};
use Illuminate\Database\Eloquent\MassAssignmentException;

trait WorksWithRelations
{
    /**
     * Update or create relations handling array type request data.
     *
     * @param Request $request The Request object.
     * @param Model   $model   The eloquent model.
     *
     * @return void
     */
    public function updateOrCreateRelations(Request $request, Model $model)
    {
        $data = $request->all();
        foreach ($data as $relationName => $fillables) {
            if (is_array($request->{$relationName}) && !$request->hasFile($relationName)) {
                $this->_checkRelationExists($model, $relationName);

                $relation = $model->{$relationName}();
                $this->handleRelations($fillables, $model, $relation);
            }
        }
    }

    /**
     * Handle relations.
     *
     * @param array    $fillables The relation fillables.
     * @param Model    $model     The eloquent model.
     * @param Relation $relation  The eloquent relation.
     *
     * @return void
     */
    protected function handleRelations(array $fillables, Model $model, Relation $relation)
    {
        switch (true) {
        case $relation instanceof HasOne || $relation instanceof MorphOne:
            $this->updateOrCreateHasOne($fillables, $model, $relation);
            break;
        case $relation instanceof BelongsTo:
            $this->updateOrCreateBelongsToOne($fillables, $model, $relation);
            break;
        case $relation instanceof HasMany || $relation instanceof MorphMany:
            $this->updateOrCreateHasMany($fillables, $model, $relation);
            break;
        case $relation instanceof BelongsToMany || $relation instanceof MorphToMany:
            $this->updateOrCreateBelongsToMany($fillables, $model, $relation);
            break;
        }
    }

    /**
     * HasOne relation updateOrCreate logic.
     *
     * @param array    $fillables The relation fillables.
     * @param Model    $model     The eloquent model.
     * @param Relation $relation  The eloquent relation.
     *
     * @return void
     */
    protected function updateOrCreateHasOne(array $fillables, Model $model, Relation $relation)
    {
        if (!$relation->first()) {
            $relation->create($fillables);
        } else {
            $relation->update($fillables);
        }
    }

    /**
     * BelongsToOne relation updateOrCreate logic.
     *
     * @param array    $fillables The relation fillables.
     * @param Model    $model     The eloquent model.
     * @param Relation $relation  The eloquent relation.
     *
     * @return void
     */
    protected function updateOrCreateBelongsToOne(array $fillables, Model $model, Relation $relation)
    {
        $related = $relation->getRelated();

        if (!$relation->first()) {
            $relation->associate($related->create($fillables));
            $model->save();
        } else {
            $relation->update($fillables);
        }
    }

    /**
     * HasMany relation updateOrCreate logic.
     *
     * @param array    $fillables The relation fillables.
     * @param Model    $model     The eloquent model.
     * @param Relation $relation  The eloquent relation.
     *
     * @return void
     */
    protected function updateOrCreateHasMany(array $fillables, Model $model, Relation $relation)
    {
        foreach ($fillables as $id => $fields) {
            $relation->updateOrCreate(['id' => $id], $fields);
        }
    }

    /**
     * BelongsToMany relation updateOrCreate logic.
     *
     * @param array    $fillables The relation fillables.
     * @param Model    $model     The eloquent model.
     * @param Relation $relation  The eloquent relation.
     *
     * @return void
     */
    protected function updateOrCreateBelongsToMany(array $fillables, Model $model, Relation $relation)
    {
        $related = $relation->getRelated();

        foreach ($fillables as $id => $fields) {
            $related->updateOrCreate(['id' => $id], $fields);
        }

        $relation->sync(array_keys($fillables));
    }

    /**
     * Throw an exception if array type request data is not named after an existent Eloquent relation.
     *
     * @param Model  $model        The eloquent model.
     * @param string $relationName The eloquent relation name.
     *
     * @throws MassAssignmentException
     *
     * @return void
     */
    private function _checkRelationExists(Model $model, string $relationName)
    {
        if (!method_exists($model, $relationName) || !$model->{$relationName}() instanceof Relation) {
            if (Lang::has('resource-controller.data2relationinexistent')) {
                $message = trans('resource-controller.data2relationinexistent', ['relationName' => $relationName]);
            } else {
                $message = "Array type request data '{$relationName}' must be named after an existent relation.";
            }

            throw new MassAssignmentException($message);
        }
    }
}
