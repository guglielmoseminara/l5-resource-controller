<?php

namespace RafflesArgentina\ResourceController\Traits;

use Lang;

trait FormatsResponseMessages
{
    /**
     * Format the store failed message.
     *
     * @return string
     */
    public function storeFailedMessage()
    {
        if (Lang::has('resource-controller.storefailed')) {
            return trans('resource-controller.storefailed');
        }

        return 'Operation failed while creating a new record.';
    }

    /**
     * Format the update failed message.
     *
     * @param string $number The model id.
     *
     * @return string
     */
    public function updateFailedMessage($number)
    {
        if (Lang::has('resource-controller.updatefailed')) {
            return trans(
                'resource-controller.updatefailed', [
                    'number' => $number
                ]
            );
        }

        return 'Operation failed while updating the record: '.$number;
    }

    /**
     * Format the destroy failed message.
     *
     * @param string $number The model id.
     *
     * @return string
     */
    public function destroyFailedMessage($number)
    {
        if (Lang::has('resource-controller.destroyfailed')) {
            return trans(
                'resource-controller.destroyfailed', [
                    'number' => $number
                ]
            );
        }

        return 'Operation failed while destroying the record: '.$number;
    }

    /**
     * Format the store successful message.
     *
     * @param string $number The model id.
     *
     * @return string
     */
    public function storeSuccessfulMessage($number)
    {
        if (Lang::has('resource-controller.storesuccessful')) {
            return trans(
                'resource-controller.storesuccessful', [
                    'number' => $number
                ]
            );
        }

        return 'Newly created record number: '.$number;
    }

    /**
     * Format the update successful message.
     *
     * @param string $number The model id.
     *
     * @return string
     */
    public function updateSuccessfulMessage($number)
    {
        if (Lang::has('resource-controller.updatesuccessful')) {
            return trans(
                'resource-controller.updatesuccessful', [
                    'number' => $number
                ]
            );
        }

        return 'Register successfully updated: '.$number;
    }

    /**
     * Format the destroy successful message.
     *
     * @param string $number The model id.
     *
     * @return string
     */
    public function destroySuccessfulMessage($number)
    {
        if (Lang::has('resource-controller.destroysuccessful')) {
            return trans(
                'resource-controller.destroysuccessful', [
                    'number' => $number
                ]
            );
        }

        return 'Register successfully deleted: '.$number;
    }
}
