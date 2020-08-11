<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;

use App\Entity\ApplicationType;

use App\Entity\Order;
use App\Entity\Invoice;

use App\Entity\Promo;
use App\Entity\PictureCounter;
use App\Entity\PictureDiscount;

use App\Entity\Organization;
use App\Entity\Agency;
use App\Entity\Network;

use Twig\TwigTest;

class EntityTest extends AbstractExtension
{
    /**
     * @return array
     */
    public function getTests(): array
    {
        return [
            // orders tests
            new TwigTest('order_has_mail_to_send', function (Order $order) {
                return in_array($order->getOrderStatus(), [Order::SEND_TO_CLIENT, Order::AWAITING_FOR_CLIENT_RESPONSE, Order::DELIVERY_SHORT_TIME_READY, Order::DECLINED_BY_PRODUCTION]);
            }),
            new TwigTest('waiting_for_payment', function (Order $order) {
                return in_array($order->getOrderStatus(), [Order::ERROR_CB, Order::AWAITING_FOR_PAYMENT, Order::AWAITING_FOR_COMPLETION_BY_CLIENT]);
            }),
            new TwigTest('processing', function (Order $order) {
                return !in_array($order->getOrderStatus(), [Order::ERROR_CB, Order::AWAITING_FOR_PAYMENT, Order::DECLINED_BY_PRODUCTION, Order::AWAITING_FOR_COMPLETION_BY_CLIENT]);
            }),
	        new TwigTest('in_production', function (Order $order) {
		        return in_array($order->getOrderStatus(), [Order::IN_PRODUCTION, Order::PARTIALLY_COMPLETED]);
	        }),
            new TwigTest('contained_errors', function (Order $order) {
                return in_array($order->getOrderStatus(), [Order::ERROR_CB, Order::DECLINED_BY_PRODUCTION]);
            }),
            new TwigTest('not_valid', function (Order $order) {
                return in_array($order->getOrderStatus(), [Order::DECLINED_BY_PRODUCTION]);
            }),
            new TwigTest('valid', function (Order $order) {
                return in_array($order->getOrderStatus(), [Order::DELIVERY_SHORT_TIME_READY, Order::PARTIALLY_COMPLETED, Order::SEND_TO_CLIENT]);
            }),
            new TwigTest('completed', function (Order $order) {
                return in_array($order->getOrderStatus(), [Order::COMPLETED]);
            }),
            new TwigTest('awaiting_for_client_response', function (Order $order) {
                return in_array($order->getOrderStatus(), [Order::AWAITING_FOR_CLIENT_RESPONSE]);
            }),

            // invoice tests
            new TwigTest('monthly_per_user', function (Invoice $invoice) {
                return in_array($invoice->getType(), [Invoice::MONTHLY_PER_USER]);
            }),
            new TwigTest('monthly_per_organization', function (Invoice $invoice) {
                return in_array($invoice->getType(), [Invoice::MONTHLY_PER_ORGANIZATION]);
            }),
            new TwigTest('ordinary', function (Invoice $invoice) {
                return in_array($invoice->getType(), [Invoice::ORDINARY]);
            }),
            new TwigTest('additional', function (Invoice $invoice) {
                return in_array($invoice->getType(), [Invoice::ADDITIONAL]);
            }),

            // promo tests
            new TwigTest('promo_with_discount', function (Promo $promo) {
                return $promo instanceof PictureDiscount;
            }),
            new TwigTest('promo_with_counter', function (Promo $promo) {
                return $promo instanceof PictureCounter;
            }),
            new TwigTest('promo_with_specific_client', function (Promo $promo) {
                return $promo->getPromoType() == Promo::ASSIGN_TO_SPECIFIC_CUSTOMERS;
            }),
            new TwigTest('promo_with_organization', function (Promo $promo) {
                return in_array($promo->getPromoType(), [Promo::ASSIGN_TO_NETWORK, Promo::ASSIGN_TO_AGENCY]);
            }),

            // organization tests
            new TwigTest('agency', function (Organization $organization) {
                return $organization instanceof Agency;
            }),
            new TwigTest('network', function (Organization $organization) {
                return $organization instanceof Network;
            }),

            // Applications tests
            new TwigTest('application_immosquare', function (ApplicationType $applicationType) {
                return $applicationType->getAppType() == ApplicationType::IMMOSQUARE_TYPE;
            }),
            new TwigTest('application_emmobilier', function (ApplicationType $applicationType) {
                return $applicationType->getAppType() == ApplicationType::EMMOBILIER_TYPE;
            }),
      ];
    }
}
