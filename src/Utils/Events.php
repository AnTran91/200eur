<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Utils;

/**
 * Contains all events dispatched by an Application.
 */
final class Events
{
    /**
     * This event is invoked on order creation.
     * It is called here @see \App\EventSubscriber\EmmobilierOrderEventSubscriber.
     * And @see \App\Events\OrderEvent class is passed.
     *
     * @var string
     */
    public const ON_SAVE_ORDER = 'on_save_order';

    /**
     * This event is invoked on monthly order creation.
     * It is called here @see \App\EventSubscriber\EmmobilierOrderEventSubscriber.
     * And @see \App\Events\OrderEvent class is passed.
     *
     * @var string
     */
    public const ON_SAVE_MONTHLY_ORDER = 'on_save_monthly_order';

    /**
     * This event is invoked on monthly invoice creation.
     * It is called here @see \App\EventSubscriber\EmmobilierOrderEventSubscriber.
     * And @see \App\Events\OrderEvent class is passed.
     *
     * @var string
     */
    public const ON_CREATE_MONTHLY_INVOICE = 'on_create_monthly_invoice';

    /**
     * This event is invoked on order creation.
     * It is called here @see \App\EventSubscriber\EmmobilierOrderEventSubscriber.
     * And @see \App\Events\OrderEvent class is passed.
     *
     * @var string
     */
    public const ON_UPDATE_INVOICE = 'on_update_invoice';

    /**
     * This event is invoked on order creation.
     * It is called here @see \App\EventSubscriber\EmmobilierOrderEventSubscriber.
     * And @see \App\Events\OrderEvent class is passed.
     *
     * @var string
     */
    public const ON_ADD_ADDITIONAL_INVOICE = 'on_add_additional_invoice';

    /**
     * This event is invoked on order creation.
     * It is called here @see \App\EventSubscriber\EmmobilierOrderEventSubscriber.
     * And @see \App\Events\OrderEvent class is passed.
     *
     * @var string
     */
    public const ON_SAVE_ORDER_WHEN_DISCONNECT = 'on_save_order_when_disconnect';

    /**
     * This event is invoked on invoice creation.
     * It is called here @see \App\EventSubscriber\EmmobilierOrderEventSubscriber.
     * And @see \App\Events\OrderEvent class is passed.
     *
     * @var string
     */
    public const ON_PAY_ORDER_BY_TRANSACTION = 'on_create_invoice';

    /**
     * This event is invoked on order creation with wallet.
     * It is called here @see \App\EventSubscriber\EmmobilierOrderEventSubscriber.
     * And @see \App\Events\OrderEvent class is passed.
     *
     * @var string
     */
    public const ON_PAY_ORDER_BY_WALLET = 'on_create_invoice_by_wallet';

    /**
     * This event is invoked on order creation with promo.
     * It is called here @see \App\EventSubscriber\EmmobilierOrderEventSubscriber.
     * And @see \App\Events\OrderEvent class is passed.
     *
     * @var string
     */
    public const ON_FREE_ORDER = 'on_free_order';

    /**
     * This event is invoked when file is uploaded.
     * It is called here @see \App\EventSubscriber\UploadEventSubscriber.
     * And @see \App\Events\UploadEvent class is passed.
     *
     * @var string
     */
    public const ON_UPLOAD = 'on_upload';

    /**
     * This event is invoked when finished file is uploaded.
     * It is called here @see \App\EventSubscriber\UploadEventSubscriber.
     * And @see \App\Events\UploadEvent class is passed.
     *
     * @var string
     */
    public const ON_UPLOAD_FINISHED_PICTURE = 'on_upload_finished_picture';

    /**
     * This event is invoked when gif file is uploaded.
     * It is called here @see \App\EventSubscriber\UploadEventSubscriber.
     * And @see \App\Events\UploadEvent class is passed.
     *
     * @var string
     */
    public const ON_UPLOAD_GIF_PICTURE = 'on_upload_gif_picture';

    /**
     * This event is invoked when MP4 file is uploaded.
     * It is called here @see \App\EventSubscriber\UploadEventSubscriber.
     * And @see \App\Events\UploadEvent class is passed.
     *
     * @var string
     */
    public const ON_UPLOAD_MP4_FILE = 'on_upload_mp4_file';

    /**
     * This event is invoked when painted file is uploaded.
     * It is called here @see \App\EventSubscriber\PaintedFileUploadSubscriber.
     * And @see \App\Events\PaintedFileUploadEvent class is passed.
     *
     * @var string
     */
    public const ON_UPLOAD_PAINTED_PICTURE = 'on_upload_painted_picture';

    /**
     * This event is invoked when finished file is uploaded.
     * It is called here @see \App\EventSubscriber\UploadEventSubscriber.
     * And @see \App\Events\UploadEvent class is passed.
     *
     * @var string
     */
    public const ON_DELETE_FINISHED_PICTURE = 'on_delete_finished_picture';

    /**
     * This event is invoked when chunked file is uploaded.
     * It is called here @see \App\EventSubscriber\UploadEventSubscriber.
     * And @see \App\Events\UploadEvent class is passed.
     *
     * @var string
     */
    public const ON_CHUNK_UPLOAD = 'on_chunk_upload';

    /**
     * This event is invoked when the params is applyed to the picture.
     * It is called here @see \App\EventSubscriber\UploadEventSubscriber.
     * And @see \App\Events\UploadEvent class is passed.
     *
     * @var string
     */
    public const ON_UPDATE_PICTURE_SETTING = 'on_update_picture_setting';

    /**
     * This event is invoked when the params is applyed to the picture.
     * It is called here @see \App\EventSubscriber\UploadEventSubscriber.
     * And @see \App\Events\UploadEvent class is passed.
     *
     * @var string
     */
    public const ON_SESSION_SAVE_PICTURE_SETTING = 'on_save_picture_setting';

    /**
     * This event is invoked when file is deleted.
     * It is called here @see \App\EventSubscriber\UploadEventSubscriber.
     * And @see \App\Events\UploadEvent class is passed.
     *
     * @var string
     */
    public const ON_DELETE_UPLOADED_FILE = 'on_delete_uploaded_file';

    /**
     * This event is invoked when saved file is deleted.
     * It is called here @see \App\EventSubscriber\UploadEventSubscriber.
     * And @see \App\Events\UploadEvent class is passed.
     *
     * @var string
     */
    public const ON_DELETE_SAVED_FILE = 'on_delete_saved_file';

    /**
     * This event is invoked when mutiple file is deleted.
     * It is called here @see \App\EventSubscriber\UploadEventSubscriber.
     * And @see \App\Events\UploadEvent class is passed.
     *
     * @var string
     */
    public const ON_DELETE_MULTIPLE_UPLOADED_FILE = 'on_delete_uploaded_multiple_file';

    /**
    * This event is invoked when the status of an order is updated to suspended
    * It will send an email to the production to stop working on this order.
    *
    * @var string
    */
    public const ON_PENDING_ORDER = 'on_pending_order';

    /**
    * This event is invoked when the status of an order is updated to finished
    * It will send an email to the customer to check the order.
    *
    * @var string
    */
    public const ON_FINISHED_ORDER = 'on_finished_order';

    /**
     * This event is invoked when an invoice is missing.
     * It is called here @see \App\EventSubscriber\InvoiceEventSubscriber.
     * And @see \App\Events\MissingInvoicePdfEvent class is passed.
     *
     * @var string
     */
    public const ON_MISSING_INVOICE_PDF = 'on_missing_invoice_pdf';

    /**
     * This event is invoked when param is saved.
     * It is called here @see \App\EventSubscriber\ChunkedFileUploadSubscriber.
     * And @see \App\Events\ParamEvent class is passed.
     *
     * @var string
     */
    public const ON_SAVE_PARAMS = 'on_save_params';

    /**
     * This event is invoked when updated one param.
     * It is called here @see \App\EventSubscriber\ChunkedFileUploadSubscriber.
     * And @see \App\Events\ParamEvent class is passed.
     *
     * @var string
     */
    public const ON_UPDATE_ONE_PARAM = 'on_update_one_params';

    /**
     * This event is invoked when an invoice is missing.
     * It is called here @see \App\EventSubscriber\ChunkedFileUploadSubscriber.
     * And @see \App\Events\ParamEvent class is passed.
     *
     * @var string
     */
    public const ON_UPDATE_PARAMS = 'on_update_params';

    /**
     * This event is invoked when an invoice is missing.
     * It is called here @see \App\EventSubscriber\Order\ImmosqureOrderEventSubscriber.
     * And @see \App\Events\OrderEvent class is passed.
     *
     * @var string
     */
    public const ON_CREATE_IMMOSQUARE_ORDER = 'on_create_immosquare_order';

    /**
     * This event is invoked on recharge to wallet.
     * It is called here @see \App\EventSubscriber\EmmobilierOrderEventSubscriber.
     * And @see \App\Events\OrderEvent class is passed.
     *
     * @var string
     */
    public const ON_RECHARGE_TO_WALLET = 'on_recharge_to_wallet';
}
