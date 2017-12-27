<?php

namespace RafflesArgentina\ResourceController\Contracts;

use Illuminate\Support\MessageBag;

interface ResourceControllerInterface
{
    /**
     * Get full route from action, alias and resource name.
     *
     * @param string $action The route action name.
     *
     * @return string
     */
    public function getRouteName($action);

    /**
     * Validate rules from a FormRequest class.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validateRules();

    /**
     * Throw an exception if the view doesn't exist.
     *
     * @param string $view The view.
     *
     * @throws \RafflesArgentina\ResourceController\Exceptions\ResourceControllerException
     *
     * @return void
     */
    public function checkViewExists($view);

    /**
     * Get view location from vendor prefix, route action and resource name.
     *
     * @param string $action The route action.
     *
     * @return string
     */
    public function getViewLocation($action);

    /**
     * Set redirection route.
     *
     * @return string
     */
    public function redirectionRoute();

    /**
     * Get the FormRequest instance.
     *
     * @return \Illuminate\Foundation\Http\FormRequest
     */
    public function getFormRequestInstance();

    /**
     * Redirect back with errors if validator fails.
     *
     * @param \Illuminate\Validation\Validator $validator The validator instance.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectBackWithErrors($validator);

    /**
     * Return a valid 200 Success json response.
     *
     * @param string $message The response message.
     *
     * @return \Illuminate\Http\Response
     */
    public function validSuccessJsonResponse($message = 'Success');

    /**
     * Return a valid 404 Not found json response.
     *
     * @param string $message The response message.
     *
     * @return \Illuminate\Http\Response
     */
    public function validNotFoundJsonResponse($message = 'Not found');

    /**
     * Return a valid 422 Unprocessable entity json response.
     *
     * @param \Illuminate\Support\MessageBag $errors  The message bag errors.
     * @param string                         $message The response message.
     *
     * @return \Illuminate\Http\Response
     */
    public function validUnprocessableEntityJsonResponse(MessageBag $errors, $message = 'Unprocessable Entity');
}
