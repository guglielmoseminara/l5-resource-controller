<?php

namespace RafflesArgentina\ResourceController\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

use RafflesArgentina\ResourceController\Exceptions\ResourceControllerException;

interface ResourceControllerInterface
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request The request object.
     *
     * @return mixed
     */
    public function index(Request $request);

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request The request object.
     *
     * @return mixed
     */
    public function create(Request $request);

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The request object.
     *
     * @throws ResourceControllerException
     *
     * @return mixed
     */
    public function store(Request $request);

    /**
     * Display the specified resource.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @return mixed
     */
    public function show(Request $request, $key);

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @return mixed
     */
    public function edit(Request $request, $key);

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @throws ResourceControllerException
     *
     * @return mixed
     */
    public function update(Request $request, $key);

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request The request object.
     * @param string  $key     The model key.
     *
     * @throws ResourceControllerException
     *
     * @return mixed
     */
    public function destroy(Request $request, $key);

    /**
     * Get named route for the specified action.
     *
     * @param string $action The action.
     *
     * @return string
     */
    public function getRouteName($action);

    /**
     * Validate rules from a FormRequest instance.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validateRules();

    /**
     * Throw an exception if the view doesn't exist.
     *
     * @param string $view The view.
     *
     * @throws ResourceControllerException
     *
     * @return void
     */
    public function checkViewExists($view);

    /**
     * Get view location for the specified action.
     *
     * @param string $action The action.
     *
     * @return string
     */
    public function getViewLocation($action);

    /**
     * Get redirection route.
     *
     * @return string
     */
    public function getRedirectionRoute();

    /**
     * Get the FormRequest instance.
     *
     * @return mixed
     */
    public function getFormRequestInstance();

    /**
     * Redirect back with errors.
     *
     * @param \Illuminate\Validation\Validator $validator The validator instance.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectBackWithErrors($validator);

    /**
     * Return a valid 200 Success json response.
     *
     * @param string $message The response message.
     * @param array  $data    The passed data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validSuccessJsonResponse($message = 'Success', $data = []);

    /**
     * Return a valid 404 Not found json response.
     *
     * @param string $message The response message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validNotFoundJsonResponse($message = 'Not found');

    /**
     * Return a valid 422 Unprocessable entity json response.
     *
     * @param MessageBag $errors  The message bag errors.
     * @param string     $message The response message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validUnprocessableEntityJsonResponse(MessageBag $errors, $message = 'Unprocessable Entity');
}
