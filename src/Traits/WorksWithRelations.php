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
     * Update or create relations handling array type request data.
     *
     * @param \Illuminate\Http\Request            $request The Request object.
     * @param \Illuminate\Database\Eloquent\Model $model   The eloquent model.
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
     * @param array                                            $fillables The relation fillables.
     * @param \Illuminate\Database\Eloquent\Model              $model     The eloquent model.
     * @param \Illuminate\Database\Eloquent\Relations\Relation $relation  The eloquent relation.
     *
     * @return void
     */
    protected function handleRelations(array $fillables, Model $model, Relation $relation)
    {
        switch (true) {
        case $relation instanceof HasOne:
            $this->updateOrCreateHasOne($fillables, $model, $relation);
            break;
        case $relation instanceof BelongsTo:
            $this->updateOrCreateBelongsToOne($fillables, $model, $relation);
            break;
        case $relation instanceof HasMany:
            $this->updateOrCreateHasMany($fillables, $model, $relation);
            break;
        case $relation instanceof BelongsToMany:
            $this->updateOrCreateBelongsToMany($fillables, $model, $relation);
            break;
        }
    }

    /**
     * HasOne relation updateOrCreate logic.
     *
     * @param array                                            $fillables The relation fillables.
     * @param \Illuminate\Database\Eloquent\Model              $model     The eloquent model.
     * @param \Illuminate\Database\Eloquent\Relations\Relation $relation  The eloquent relation.
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
     * @param array                                            $fillables The relation fillables.
     * @param \Illuminate\Database\Eloquent\Model              $model     The eloquent model.
     * @param \Illuminate\Database\Eloquent\Relations\Relation $relation  The eloquent relation.
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
     * @param array                                            $fillables The relation fillables.
     * @param \Illuminate\Database\Eloquent\Model              $model     The eloquent model.
     * @param \Illuminate\Database\Eloquent\Relations\Relation $relation  The eloquent relation.
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
     * @param array                                            $fillables The relation fillables.
     * @param \Illuminate\Database\Eloquent\Model              $model     The eloquent model.
     * @param \Illuminate\Database\Eloquent\Relations\Relation $relation  The eloquent relation.
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
     * @param \Illuminate\Database\Eloquent\Model $model        The eloquent model.
     * @param string                              $relationName The eloquent relation name.
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     *
     * @return void
     */
    private function _checkRelationExists(Model $model, string $relationName)
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
