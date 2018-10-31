<?php

namespace RafflesArgentina\ResourceController\Exceptions;

class ResourceControllerException extends \Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request The request object.
     *
     * @return mixed
     */
    public function render($request)
    {
        if ($request->wantsJson()) {
            return $this->validInternalServerErrorJsonResponse($this->message);
        } else {
            return redirect()->back()->with(['rafflesargentina.status.error' => $this->message]);
        }
    }

    /**
     * Return a valid 500 Internal Server Error json response.
     *
     * @param string $message The response message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validInternalServerErrorJsonResponse($message = 'Error')
    {
        return response()->json(
            [
                'exception' => class_basename($this),
                'file' => basename($this->getFile()),
                'line' => $this->getLine(),
                'message' => $this->getMessage(),
                'trace' => $this->getTrace(),
            ], 500, [], JSON_PRETTY_PRINT
        );
    }
}
