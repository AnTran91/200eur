<?php

namespace App\Controller\Admin\Traits;


use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;

/**
 * Extra features needed in Dashboard controllers.
 *
 * @method string trans(string $id, array $parameters = array(), ?string $domain = null)
 */
trait DashboardTrait
{
	/**
	 * Get Profit stats
	 *
	 * @param string $app
	 * @param OrderRepository $orderRepository
	 * @return array
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	protected function getProfit (string $app, OrderRepository $orderRepository): array
	{
		list ($startYear, $endYear) = $this->getThisYearInterval();
		list ($startMonth, $endMonth) = $this->getThisMonthInterval();
		list ($startWeek, $endWeek) = $this->getThisWeekInterval();
		
		$yearProfit = (int) $orderRepository->findByDateIntervalWithoutCanceled($startYear, $endYear, $app)['profit'];
		$monthProfit = (int) $orderRepository->findByDateIntervalWithoutCanceled($startMonth, $endMonth, $app)['profit'];
		$weekProfit = (int) $orderRepository->findByDateIntervalWithoutCanceled($startWeek, $endWeek, $app)['profit'];
		
		return [$yearProfit, $monthProfit, $weekProfit];
	}
	
	/**
	 * Get the latest information on orders
	 *
	 * @param string $app
	 * @param OrderRepository $orderRepository
	 * @return array
	 */
	protected function getOrderDetails(string $app, OrderRepository $orderRepository): array
	{
		list ($startWeek, $endWeek) = $this->getThisWeekInterval();
		
		// query for the orders that are completed today, used for the paginator
		$todayQuery = $orderRepository->findOrdersByStatusAndDate(Order::COMPLETED, (new \DateTime('now'))->format('Y-m-d'), $app);
		
		// query for the orders that are completed this week, used for the paginator
		$weekQuery = $orderRepository->findOrdersByStatusAndDateInterval(Order::COMPLETED, $startWeek, $endWeek, $app);
		
		// query for the orders that are declined, used for the paginator
		$declinedQuery = $orderRepository->findOrdersByStatus(Order::DECLINED_BY_PRODUCTION, $app);
		
		return [$todayQuery, $weekQuery, $declinedQuery];
	}
	
	/**
	 * Get Order stats
	 *
	 * @param string $app
	 * @param OrderRepository $orderRepository
	 * @return array
	 */
	protected function getOrderStats(string $app, OrderRepository $orderRepository)
	{
		return array_map(function ($value) {
			return [
				'name' => $this->trans($value['status'], [], 'admin'),
				'value' => (int) $value['countOrder']
			];
		}, $orderRepository->findAllGroupedByStatus($app));
	}
	
	/**
	 * Get Profit stats
	 *
	 * @param string $app
	 * @param OrderRepository $orderRepository
	 * @return array
	 */
	protected function getProfitStats(string $app, OrderRepository $orderRepository)
	{
		list ($startYear, $endYear) = $this->getThisYearInterval();
		
		$profitStatistics = array_map(function ($value) {
			/** @var \DateTime $date */
			$date = $value['date'];
			$value['date'] = $date->format('Y-m-d H:i:s');
			return $value;
		}, $orderRepository->findAllGroupedByDate($startYear, $endYear, $app));
		
		uasort($profitStatistics, function ($a, $b){
			return $a['date'] > $b['date'];
		});
		
		return $profitStatistics;
	}
	
	/**
	 * Get User availability stats
	 *
	 * @param UserRepository $userRepository
	 * @return array
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	protected function getUserStats (UserRepository $userRepository): array
	{
		list ($startYear, $endYear) = $this->getThisMonthInterval();
		list ($startMonth, $endMonth) = $this->getThisYearInterval();
		list ($startWeek, $endWeek) = $this->getThisWeekInterval();
		
		return [
			['name' => $this->trans('admin.dashboard.user_active_this_year', [], 'admin'),
				'value' => (int) $userRepository->findByDate($startYear, $endYear)['userNumber'],],
			
			['name' => $this->trans('admin.dashboard.user_active_this_month', [], 'admin'),
				'value' => (int) $userRepository->findByDate($startMonth, $endMonth)['userNumber'],],
			
			['name' => $this->trans('admin.dashboard.user_active_this_week', [], 'admin'),
				'value' => (int) $userRepository->findByDate($startWeek, $endWeek)['userNumber'],]
		];
	}
	
	/**
	 * Getting this month interval
	 *
	 * @return array
	 */
	protected function getThisMonthInterval(): array
	{
		return [
			new \DateTime(date('Y-m-01', strtotime(date('Y-m-d')))),
			new \DateTime(date('Y-m-t', strtotime(date('Y-m-d'))))
		];
	}
	
	/**
	 * Getting this year interval
	 *
	 * @return array
	 */
	protected function getThisYearInterval(): array
	{
		return [
			new \DateTime(date('Y-01-01', strtotime(date('Y-m-d')))),
			new \DateTime(date('Y-12-t', strtotime(date('Y-m-d'))))
		];
	}
	
	/**
	 * Getting this week interval
	 *
	 * @return array
	 */
	protected function getThisWeekInterval(): array
	{
		return [
			new \DateTime(date("Y-m-d", strtotime('monday this week'))),
			new \DateTime(date("Y-m-d", strtotime('friday this week')))
		];
	}
}