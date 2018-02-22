<?php

namespace RafflesArgentina\ResourceController;

use DB;

use Illuminate\Http\Request;

use RafflesArgentina\ResourceController\Traits\WorksWithRelations;
use RafflesArgentina\ResourceController\Traits\WorksWithFileUploads;
use RafflesArgentina\ResourceController\Traits\FormatsResponseMessages;
use RafflesArgentina\ResourceController\Exceptions\ResourceControllerException;

class ResourceController extends AbstractResourceController
{
    use WorksWithRelations, WorksWithFileUploads, FormatsResponseMessages;

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request The request object
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->getFormRequestInstance();

        $items = $this->useSoftDeletes ? $this->repository->withTrashed()->paginate()
                                       : $this->repository->paginate();

        if ($request->wantsJson()) {
            return response()->json($items, 200, [], JSON_PRETTY_PRINT);
        }

        $view = $this->getViewLocation(__FUNCTION__);
        $this->checkViewExists($view);

        return response()->view($view, compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \lluminate\Http\Request $request The request object.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return $this->validNotFoundJsonResponse();
        }

        $model = new $this->repository->model;

        $view = $this->getViewLocation(__FUNCTION__);
        $this->checkViewExists($view);

        return response()->view($view, compact('model'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request The request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \RafflesArgentina\ResourceController\Exceptions\RepositoryException
     */
    public function store(Request $request)
    {
        $this->getFormRequestInstance();

        DB::beginTransaction();

        try {
            $instance = $this->repository->create($request->all());
            $model = $instance[1];
            $number = $model->{$model->getRouteKeyName()};
            $this->updateOrCreateRelations($request, $model);
            $this->uploadFiles($request, $model);
        } catch (\Exception $e) {
            DB::rollback();

            $message = $this->storeFailedMessage($e->getMessage());
            throw new ResourceControllerException($message);
        }

        DB::commit();

        $message = $this->storeSuccessfulMessage($number);

        if ($request->wantsJson()) {
            return $this->validSuccessJsonResponse($message);
        }

        return redirect()->route($this->redirectionRoute())
                         ->with($this->successFlashMessageKey, $message);
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param int                      $id      The model id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $model = $this->useSoftDeletes ? $this->repository->withTrashed()->where($this->repository->model->getRouteKeyName(), $id)->first()
                                       : $this->repository->findBy($this->repository->model->getRouteKeyName(), $id);

        if (!$model) {
            if ($request->wantsJson()) {
                return $this->validNotFoundJsonResponse();
            }
            abort(404);
        }

        if ($request->wantsJson()) {
            return response()->json($model, 200, [], JSON_PRETTY_PRINT);
        }

        $view = $this->getViewLocation(__FUNCTION__);
        $this->checkViewExists($view);

        return response()->view($view, compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param int                      $id      The model id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->wantsJson()) {
            return $this->validNotFoundJsonResponse();
        }

        $model = $this->useSoftDeletes ? $this->repository->withTrashed()->where($this->repository->model->getRouteKeyName(), $id)->first()
                                       : $this->repository->findBy($this->repository->model->getRouteKeyName(), $id);

        if (!$model) {
            abort(404);
        }

        $view = $this->getViewLocation(__FUNCTION__);
        $this->checkViewExists($view);

        return response()->view($view, compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param int                      $id      The model id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->getFormRequestInstance();

        $model = $this->useSoftDeletes ? $this->repository->withTrashed()->where($this->repository->model->getRouteKeyName(), $id)->first()
                                       : $this->repository->findBy($this->repository->model->getRouteKeyName(), $id);

        if (!$model) {
            if ($request->wantsJson()) {
                return $this->validNotFoundJsonResponse();
            }
            abort(404);
        }

        DB::beginTransaction();
       
        try { 
            $instance = $this->repository->update($model, $request->all());
            $model = $instance[1];
            $this->updateOrCreateRelations($request, $model);
            $this->uploadFiles($request, $model);
        } catch (\Exception $e) {
            DB::rollback();

            $message = $this->updateFailedMessage($id, $e->getMessage());
            throw new ResourceControllerException($message);
        }

        DB::commit();

        $message = $this->updateSuccessfulMessage($id);

        if ($request->wantsJson()) {
            return $this->validSuccessJsonResponse($message);
        }

        return redirect()->route($this->redirectionRoute())
                         ->with($this->successFlashMessageKey, $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param int                      $id      The model id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->getFormRequestInstance();

        $model = $this->useSoftDeletes ? $this->repository->withTrashed()->where($this->repository->model->getRouteKeyName(), $id)->first()
                                       : $this->repository->findBy($this->repository->model->getRouteKeyName(), $id);

        if (!$model) {
            if ($request->wantsJson()) {
                return $this->validNotFoundJsonResponse();
            }
            abort(404);
        }

        DB::beginTransaction();

        try {
            $this->repository->delete($model);
        } catch (\Exception $e) {
            DB::rollback();

            $message = $this->destroyFailedMessage($id, $e->getMessage());
            throw new ResourceControllerException($message);
        }

        DB::commit();

        $message = $this->destroySuccessfulMessage($id);

        if ($request->wantsJson()) {
            return $this->validSuccessJsonResponse($message);
        }

        return redirect()->route($this->redirectionRoute())
                         ->with($this->infoFlashMessageKey, $message);
    }
}
