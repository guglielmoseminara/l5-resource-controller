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
        $parameterBag = $request->request;
        foreach ($parameterBag->all() as $name => $attributes) {
            if (is_array($request->{$name})) {
                $this->_checkRelationExists($model, $name);

                $relation = $model->{$name}();
                $this->handleRelations($attributes, $model, $relation);
            }
        }
    }

    /**
     * Handle relations.
     *
     * @param array    $fillable The relation fillable.
     * @param Model    $model    The eloquent model.
     * @param Relation $relation The eloquent relation.
     *
     * @return void
     */
    protected function handleRelations(array $fillable, Model $model, Relation $relation)
    {
        switch (true) {
        case $relation instanceof HasOne || $relation instanceof MorphOne:
            $this->updateOrCreateHasOne($fillable, $model, $relation);
            break;
        case $relation instanceof BelongsTo:
            $this->updateOrCreateBelongsToOne($fillable, $model, $relation);
            break;
        case $relation instanceof HasMany || $relation instanceof MorphMany:
            $this->updateOrCreateHasMany($fillable, $model, $relation);
            break;
        case $relation instanceof BelongsToMany || $relation instanceof MorphToMany:
            $this->updateOrCreateBelongsToMany($fillable, $model, $relation);
            break;
        }
    }

    /**
     * HasOne relation updateOrCreate logic.
     *
     * @param array    $fillable The relation fillable.
     * @param Model    $model    The eloquent model.
     * @param Relation $relation The eloquent relation.
     *
     * @return void
     */
    protected function updateOrCreateHasOne(array $fillable, Model $model, Relation $relation)
    {
        if (!$relation->first()) {
            $relation->create($fillable);
        } else {
            $relation->update($fillable);
        }
    }

    /**
     * BelongsToOne relation updateOrCreate logic.
     *
     * @param array    $fillable The relation fillable.
     * @param Model    $model    The eloquent model.
     * @param Relation $relation The eloquent relation.
     *
     * @return void
     */
    protected function updateOrCreateBelongsToOne(array $fillable, Model $model, Relation $relation)
    {
        $related = $relation->getRelated();

        if (!$relation->first()) {
            $relation->associate($related->create($fillable));
            $model->save();
        } else {
            $relation->update($fillable);
        }
    }

    /**
     * HasMany relation updateOrCreate logic.
     *
     * @param array    $fillable The relation fillable.
     * @param Model    $model    The eloquent model.
     * @param Relation $relation The eloquent relation.
     *
     * @return void
     */
    protected function updateOrCreateHasMany(array $fillable, Model $model, Relation $relation)
    {
        foreach ($fillable as $fields) {
            $id = array_key_exists('id', $fields) ? $fields['id'] : '';
            $relation->updateOrCreate(['id' => $id], array_except($fields, ['id']));
        }
    }

    /**
     * BelongsToMany relation updateOrCreate logic.
     *
     * @param array    $fillable The relation fillable.
     * @param Model    $model    The eloquent model.
     * @param Relation $relation The eloquent relation.
     *
     * @return void
     */
    protected function updateOrCreateBelongsToMany(array $fillable, Model $model, Relation $relation)
    {
        $related = $relation->getRelated();

        $keys = [];
        foreach ($fillable as $fields) {
            $id = array_key_exists('id', $fields) ? $fields['id'] : '';
            $record = $related->updateOrCreate(['id' => $id], array_except($fields, ['id']));
            array_push($keys, $record->id);
        }

        $relation->sync($keys);
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
