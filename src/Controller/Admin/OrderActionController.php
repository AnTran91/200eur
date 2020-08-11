<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Entity\Invoice;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Handlers\OrderArchiveHandler;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use App\Events\OrderEvent;
use App\Events\MailerEvent;
use App\Utils\Events;

use App\Entity\Order;

use App\Handlers\OrderHandler;

/**
 * @Route("order")
 */
class OrderActionController extends Controller
{
    use ControllerTrait;
    
    /**
     * @Route("/order/send/mail/{id}/{token}", name="admin_order_send_mails", methods="GET")
     * @Entity("order", expr="repository.findOrderByID(id)")
     * @Security("is_granted('ROLE_ORDER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Order $order
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendMails(Order $order, string $token)
    {
        if (!$this->isCsrfTokenValid('send_mail'.$order->getId(), $token)) {
            throw new AccessDeniedException('The CSRF token is invalid.');
        }

        $event = new MailerEvent($order->getClient(), $order);
        $this->dispatch($order->getOrderStatus(), $event);

        $this->addFlash('flash_msg_success', $this->trans('admin.order.msg.send_mail', ['%status' => $this->trans($order->getOrderStatus(), [], 'admin')], 'admin'));
        return $this->redirectToRoute('admin_order_edit', ['id' => $order->getId()]);
    }
    
    /**
     * @Route("/order/detail/{id}/revert/{token}", name="admin_order_revert", methods="GET")
     * @Entity("order", expr="repository.findOrderByID(id)")
     * @Security("is_granted('ROLE_ORDER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Order $order
     * @param string $token
     * @param OrderHandler $orderHandler
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function revert(Order $order, string $token, OrderHandler $orderHandler)
    {
        if (!$this->isCsrfTokenValid('revert'.$order->getId(), $token)) {
            throw new AccessDeniedException('The CSRF token is invalid.');
        }

        $order = $orderHandler->revertOrder($order);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('flash_msg_success', $this->trans('admin.order.msg.edit', [], 'admin'));
        return $this->redirectToRoute('admin_order_edit', ['id' => $order->getId()]);
    }
    
    /**
     * @Route("/invoice/regenerate/{id}/{token}", name="admin_order_regenerate_invoice", methods="GET")
     * @Entity("order", expr="repository.findOrderByID(id)")
     * @Security("is_granted('ROLE_ORDER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Order $order
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function regenerateInvoice(Order $order, string $token)
    {
        if (!$this->isCsrfTokenValid('regenerate'.$order->getId(), $token)) {
            throw new AccessDeniedException('The CSRF token is invalid.');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $invoice = $order->findOneInvoiceByType([Invoice::MONTHLY_PER_USER]);
        if (!is_null($invoice)) {
            $invoice->calculatePrices();
            $entityManager->flush();
        }

        $event = new OrderEvent(null, null, $order);
        $this->dispatch(Events::ON_UPDATE_INVOICE, $event);

        $this->addFlash('flash_msg_success', $this->trans('admin.order.msg.edit', [], 'admin'));
        return $this->redirectToRoute('admin_order_edit', ['id' => $order->getId()]);
    }
    
    /**
     * @Route("/invoice/additional/{id}/{token}", name="admin_order_regenerate_additional_invoice", methods="GET")
     * @Entity("order", expr="repository.findOrderByID(id)")
     * @Security("is_granted('ROLE_ORDER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Order $order
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function additionalInvoice(Order $order, string $token)
    {
        if (!$this->isCsrfTokenValid('additional_invoice'.$order->getId(), $token)) {
            throw new AccessDeniedException('The CSRF token is invalid.');
        }

        $event = new OrderEvent(null, $order->getClient(), $order);
        $this->dispatch(Events::ON_ADD_ADDITIONAL_INVOICE, $event);

        $this->addFlash('flash_msg_success', $this->trans('admin.order.msg.edit', [], 'admin'));
        return $this->redirectToRoute('admin_order_show', ['id' => $order->getId()]);
    }
    
    /**
     * Downloads todo ZIP Action
     *
     * @Route("todo/zip/{id}", name="admin_order_todo_zip", methods="GET")
     * @Security("is_granted('ROLE_ORDER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Order $order
     * @param OrderArchiveHandler $orderArchiveHandler
     * @return BinaryFileResponse
     */
    public function downloadToDoZIP(Order $order, OrderArchiveHandler $orderArchiveHandler): BinaryFileResponse
    {
        return $this->file($orderArchiveHandler->createToDoArchive($order));
    }
    
    /**
     * Downloads done ZIP Action
     *
     * @Route("/done/zip/{id}", name="admin_order_done_zip", methods="GET")
     * @Security("is_granted('ROLE_ORDER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Order $order
     * @param OrderArchiveHandler $orderArchiveHandler
     * @return BinaryFileResponse
     */
    public function downloadDoneZIP(Order $order, OrderArchiveHandler $orderArchiveHandler): BinaryFileResponse
    {
        return $this->file($orderArchiveHandler->createDoneArchive($order));
    }
    
    /**
     * Immosquare Notification
     *
     * @Route(path="notify/{id}/{token}", name="immosquare_notify", requirements={"id"="\d+"}, methods={"GET"})
     *
     * @param Order $order
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws GuzzleException
     */
    public function notifyAPI(Order $order, string $token)
    {
        if (!$this->isCsrfTokenValid('notify'.$order->getId(), $token)) {
            throw new AccessDeniedException('The CSRF token is invalid.');
        }
        try{
            if (Order::COMPLETED !== $order->getOrderStatus()){
                $order->setOrderStatus(Order::COMPLETED);
                $this->getDoctrine()->getManager()->flush();
            }
            
            $notifyParams = $this->getParameter('nofify_immosquare_client');
            $client = new \GuzzleHttp\Client(['base_uri' => $notifyParams["base_url"], 'allow_redirects' => true]);
    
            $client->request("GET", (string) $order->getOrderNumber(), [
                'query' => $notifyParams["query"]
            ]);
            
            $this->addFlash('flash_msg_success', $this->trans('admin.order.msg.edit', [], 'admin'));
            
        }catch (ClientException $exception) {
            $responseBody = $exception->getResponse();
            
            $this->addFlash('flash_msg_success', json_decode($responseBody->getBody()->getContents())->errors);
        }
         
        return $this->redirectToRoute('admin_order_edit', ['id' => $order->getId()]);
    }
}
