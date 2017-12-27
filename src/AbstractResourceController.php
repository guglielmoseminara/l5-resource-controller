<?php

namespace RafflesArgentina\ResourceController;

use Lang;
use Validator;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\View;

use RafflesArgentina\ResourceController\Contracts\ResourceControllerInterface;
use RafflesArgentina\ResourceController\Exceptions\ResourceControllerException;

abstract class AbstractResourceController extends BaseController
                                          implements ResourceControllerInterface
{
    /**
     * Set alias for named routes.
     *
     * @var string|null
     */
    protected $alias;

    /**
     * Set views vendor location prefix.
     *
     * @var string|null
     */
    protected $module;

    /**
     * Set prefix for named routes.
     *
     * @var string|null
     */
    protected $prefix;

    /**
     * The Repository class to instantiate.
     *
     * @var string
     */
    protected $repository;

    /**
     * The FormRequest class to instantiate.
     *
     * @var string|null
     */
    protected $formRequest;

    /**
     * Set routes resource name.
     *
     * @var string
     */
    protected $resourceName;

    /**
     * Define if model uses SoftDeletes;
     *
     * @var boolean
     */
    protected $useSoftDeletes;

    /**
     * Set the info flash message key.
     *
     * @var string|null
     */
    protected $infoFlashMessageKey = 'rafflesargentina.status.info';

    /**
     * Set the error flash message key.
     *
     * @var string|null
     */
    protected $errorFlashMessageKey = 'rafflesargentina.status.error';

    /**
     * Set the success flash message key.
     *
     * @var string|null
     */
    protected $successFlashMessageKey = 'rafflesargentina.status.success';

    /**
     * Set the warning flash message key.
     *
     * @var string|null
     */
    protected $warningFlashMessageKey = 'rafflesargentina.status.warning';

    public function __construct()
    {
        $this->_checkRepositoryProperty();
        $this->_checkResourceNameProperty();
        $this->_formatRouteNameAndViewPathModifiers();

        $this->repository = app()->make($this->repository);
    }

    /**
     * Get full route from action, alias and resource name.
     *
     * @param string $action The route action.
     *
     * @return string
     */
    public function getRouteName($action)
    {
        return $this->alias.$this->resourceName.$action;
    }

    /**
     * Validate rules from a FormRequest class.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validateRules()
    {
        $input = request()->all();
        $rules = [];
        $messages = [];

        if ($this->formRequest) {
            $this->formRequest = new $this->formRequest;
            $rules = $this->formRequest->rules();
            $messages = $this->formRequest->messages();
        }

        return Validator::make($input, $rules, $messages);
    }

    /**
     * Throw an exception if the view doesn't exist.
     *
     * @param string $view The view.
     *
     * @throws \RafflesArgentina\ResourceController\Exceptions\ResourceControllerException
     *
     * @return void
     */
    public function checkViewExists($view)
    {
        if (!View::exists($view)) {
            if (Lang::has('resource-controller.viewnotfound')) {
                $message = trans('resource-controller.viewnotfound', ['view' => '$view']);
            } else {
                $message = 'Requested page couldn\'t be loaded because the view file is missing: '.$view;
            }

            throw new ResourceControllerException($message);
        }
    }

    /**
     * Get view location from vendor prefix, route action and resource name.
     *
     * @param string $action The route action.
     *
     * @return string
     */
    public function getViewLocation($action)
    {
        if (request()->ajax()) {
            return $this->module.$this->resourceName.'ajax.'.$action;
        }

        return $this->module.$this->resourceName.$action;
    }

    /**
     * Set redirection route.
     *
     * @return string
     */
    public function redirectionRoute()
    {
        return $this->getRouteName('index');
    }

    /**
     * Get the FormRequest instance.
     *
     * @return \Illuminate\Foundation\Http\FormRequest
     */
    public function getFormRequestInstance()
    {
        if (!$this->formRequest) {
            return new \Illuminate\Foundation\Http\FormRequest;
        }
        
        return app()->make($this->formRequest);
    }

    /**
     * Redirect back with errors if validator fails.
     *
     * @param \Illuminate\Validation\Validator $validator The validator instance.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectBackWithErrors($validator)
    {
        if (request()->wantsJson()) {
            return $this->validUnprocessableEntityJsonResponse($validator->errors());
        }

        return back()->withErrors($validator)->withInput();
    }

    /**
     * Return a valid 200 Success json response.
     *
     * @param string $message The response message.
     *
     * @return \Illuminate\Http\Response
     */
    public function validSuccessJsonResponse($message = 'Success')
    {
        return response()->json(
            [
                'code' => '200',
                'message' => $message,
                'errors' => [],
                'redirect' => route($this->redirectionRoute()),
            ], 200, [], JSON_PRETTY_PRINT
        );
    }

    /**
     * Return a valid 404 Not found json response.
     *
     * @param string $message The response message.
     *
     * @return \Illuminate\Http\Response
     */
    public function validNotFoundJsonResponse($message = 'Not found')
    {
        return response()->json(
            [
                'code' => '404',
                'message' => $message,
                'errors' => [],
                'redirect' => route($this->redirectionRoute()),
            ], 404, [], JSON_PRETTY_PRINT
        );
    }

    /**
     * Return a valid 422 Unprocessable entity json response.
     *
     * @param \Illuminate\Support\MessageBag $errors  The message bag errors.
     * @param string                         $message The response message.
     *
     * @return \Illuminate\Http\Response
     */
    public function validUnprocessableEntityJsonResponse(MessageBag $errors, $message = 'Unprocessable Entity')
    {
        return response()->json(
            [
                'code' => '422',
                'message' => $message,
                'errors' => $errors,
                'redirect' => route($this->redirectionRoute()),
            ], 422, [], JSON_PRETTY_PRINT
        );
    }

    /**
     * Format named route and view path modifiers.
     *
     * @return void
     */
    private function _formatRouteNameAndViewPathModifiers()
    {
        if ($this->alias) {
            $this->alias .= '.';
        }

        if ($this->module) {
            $this->module .= '::';
        }

        if ($this->prefix) {
            $this->prefix .= '.';
        }

        if ($this->resourceName) {
            $this->resourceName .= '.';
        }
    }

    /**
     * Throw an exception if repository property is not set.
     *
     * @throws \RafflesArgentina\ResourceController\Exceptions\ResourceControllerException
     *
     * @return void
     */
    private function _checkRepositoryProperty()
    {
        if (!$this->repository) {
            if (Lang::has('resource-controller.propertynotset')) {
                $message = trans('resource-controller.propertynotset', ['property' => '$repository']);
            } else {
                $message = '$repository property must be set.';
            }

            throw new ResourceControllerException($message);
        }
    }

    /**
     * Throw an exception if resourceName property is not set.
     *
     * @throws \RafflesArgentina\ResourceController\Exceptions\ResourceControllerException
     *
     * @return void
     */
    private function _checkResourceNameProperty()
    {
        if (!$this->resourceName) {
            if (Lang::has('resource-controller.propertynotset')) {
                $message = trans('resource-controller.propertynotset', ['property' => '$resourceName']);
            } else {
                $message = '$resourceName property must be set.';
            }

            throw new ResourceControllerException($message);
        }
    }
}