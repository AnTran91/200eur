<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Controller\Admin\Traits\DashboardTrait;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;

use App\Entity\Order;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Security("is_granted('ROLE_SUPER_ADMIN') || is_granted('ROLE_AGENCY_MANAGER') || is_granted('ROLE_DELIVERY_TIME_MANAGER') || is_granted('ROLE_HOLIDAYS_MANAGER') ||
is_granted('ROLE_INVOICE_MANAGER') || is_granted('ROLE_NETWORK_MANAGER') ||
is_granted('ROLE_ORDER_MANAGER') || is_granted('ROLE_PRODUCTION_MANAGER') ||
is_granted('ROLE_RETOUCH_MANAGER') || is_granted('ROLE_TRANSACTION_MANAGER') || is_granted('ROLE_USER_MANAGER')")
 */
class DashboardController extends Controller
{
    use ControllerTrait, DashboardTrait;

    private $orderStatusOptions;

    public function __construct(array $orderStatusOptions)
    {
        $this->orderStatusOptions = $orderStatusOptions;
    }
	
	/**
	 * @Route("/dashboard/{app}", requirements={"app": "%application_type_options_validation%"}, defaults={"app": "%application_type_options_defaults%"}, name="dashboard")
	 *
	 * @param string $app
	 * @param OrderRepository $orderRepository
	 * @param UserRepository $userRepository
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function index(string $app, OrderRepository $orderRepository, UserRepository $userRepository)
    {
	    list($yearProfit, $monthProfit, $weekProfit) = $this->getProfit($app, $orderRepository);
	    list($todayQuery, $weekQuery, $declinedQuery) = $this->getOrderDetails($app, $orderRepository);

        return $this->render('admin/dashboard/index.html.twig', [
           'todayDeliveredOrders' => $todayQuery,
           'thisWeekDeliveredOrders' => $weekQuery,
           'declinedOrders' => $declinedQuery,
           'yearProfit' => $yearProfit,
           'monthProfit' => $monthProfit,
           'weekProfit' => $weekProfit,
           'user_statistics' => $this->getUserStats ($userRepository),
           'order_statistics' => $this->getOrderStats($app, $orderRepository),
           'profit_statistics' => $this->getProfitStats($app, $orderRepository)
          ]);
    }
}
