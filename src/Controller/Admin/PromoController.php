<?php

namespace App\Controller\Admin;


use App\Controller\Admin\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Promo;
use App\Entity\PictureDiscount;
use App\Entity\PictureCounter;

use App\Form\Admin\PictureDiscountType;
use App\Form\Admin\PictureCounterType;
use App\Form\Admin\Filters\PromoTypeFilter;

use App\Repository\PromoRepository;

/**
 * @Route("promo")
 * @Security("is_granted('ROLE_PROMO_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class PromoController extends Controller
{
	use ControllerTrait;
	
	/**
	 * @Route("/", name="admin_promo_index", methods="GET")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
	public function index(Request $request): Response
	{
		$promos = $this->dynamicPaginator([
			'filter' => $request->query->get('filter', []),
			'page' => $request->query->get('page', 1)
		], Promo::class);
		
		if ($request->isXmlHttpRequest()) {
			return $this->json([
				'html' => $this->renderView('admin/promo/_list.html.twig', ['promos' => $promos]),
				'page' => $promos->getPaginationData()['current'],
				'params' => $promos->getParams(),
				'total_page' => $promos->getPaginationData()['pageCount']
			]);
		}
		
		return $this->render('admin/promo/index.html.twig', [
			'filter_form' => $this->createForm(PromoTypeFilter::class, null, ['method' => 'GET'])->handleRequest($request)->createView(),
			'promos' => $promos
		]);
	}
	
	/**
	 * @Route("/{type}/new", requirements={"type"="counter|discount"}, defaults={"type"="counter"}, name="admin_promo_new", methods="GET|POST")
	 *
	 * @param Request $request
	 * @param string $type
	 * @return Response
	 */
	public function new(Request $request, string $type): Response
	{
		$promo = strcmp($type, "counter") === 0 ? new PictureCounter() : new PictureDiscount();
		$form = $this->createForm(strcmp($type, "counter") === 0 ? PictureCounterType::class : PictureDiscountType::class, $promo);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			
			$em->persist($promo);
			$em->flush();
			
			$this->addFlash('flash_msg_success', $this->trans('admin.promo.msg.new', [], 'admin'));
			return $this->redirectToRoute('admin_promo_index');
		}
		
		if (strcmp($type, "counter") === 0) {
			return $this->render(!$request->isXmlHttpRequest() ? 'admin/promo/new_picture_counter.html.twig' : 'admin/promo/_form_picture_counter.html.twig', ['promo' => $promo, 'form' => $form->createView()]);
		} else {
			return $this->render(!$request->isXmlHttpRequest() ? 'admin/promo/new_picture_discount.html.twig' : 'admin/promo/_form_picture_discount.html.twig', ['promo' => $promo, 'form' => $form->createView()]);
		}
	}
	
	/**
	 * @Route("/{id}", name="admin_promo_show", methods="GET")
	 *
	 * @param Promo $promo
	 * @param PromoRepository $promoRepository
	 * @return Response
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function show(Promo $promo, PromoRepository $promoRepository): Response
	{
		$statistics = array_map(function ($value) {
			/** @var \DateTimeInterface $date */
			$date = $value['date'];
			
			return [
				'dates' => $date->format('Y-m-d H:i:s'),
				'values' => (int)$value['imageNumber']
			];
		}, $promoRepository->findTheUseNumberAndImageNumberGroupedByDate($promo->getId()));
		
		/** @var int $useNumber */
		extract($promoRepository->findTheUseNumberByPromo($promo->getId()));
		
		return $this->render('admin/promo/show.html.twig', [
			'promo' => $promo,
			'numberOfUse' => $useNumber,
			'numberOfPictures' => array_sum(array_column($statistics, 'values')),
			'statistics' => $statistics
		]);
	}
	
	/**
	 * @Route("/{id}/edit", name="admin_promo_edit", methods="GET|POST")
	 *
	 * @param Request $request
	 * @param Promo $promo
	 * @return Response
	 */
	public function edit(Request $request, Promo $promo): Response
	{
		$form = $this->createForm($promo instanceof PictureDiscount ? PictureDiscountType::class : PictureCounterType::class, $promo);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			
			$em->persist($promo);
			$em->flush();
			
			$this->addFlash('flash_msg_success', $this->trans('admin.promo.msg.edit', [], 'admin'));
			return $this->redirectToRoute('admin_promo_edit', ['id' => $promo->getId()]);
		}
		
		if ($promo instanceof PictureCounter) {
			return $this->render(!$request->isXmlHttpRequest() ? 'admin/promo/edit_picture_counter.html.twig' : 'admin/promo/_form_picture_counter.html.twig', ['promo' => $promo, 'form' => $form->createView()]);
		} else {
			return $this->render(!$request->isXmlHttpRequest() ? 'admin/promo/edit_picture_discount.html.twig' : 'admin/promo/_form_picture_discount.html.twig', ['promo' => $promo, 'form' => $form->createView()]);
		}
	}
	
	/**
	 * @Route("/promo/expire", name="admin_promo_bulk_expire", methods="DELETE")
	 *
	 * @param Request $request
	 * @param PromoRepository $promoRepository
	 * @return Response
	 */
	public function delete(Request $request, PromoRepository $promoRepository): Response
	{
		$isAjax = $request->isXmlHttpRequest();
		
		$data = $request->request->get('data', []);
		$token = $request->request->get('token');
		
		if ($isAjax && !empty($data) && !empty($token)) {
			if (!$this->isCsrfTokenValid('multiselect_promo', $token)) {
				throw new AccessDeniedException('The CSRF token is invalid.');
			}
			
			if ($promoRepository->disableOrEnablePromosByIds($data)){
				return $this->json(['msg' => $this->trans('admin.promo.msg.edit', [], 'admin'), 'success' => true]);
			}
		}
		
		return $this->json(['msg' => $this->trans('admin.common.error_msg', [], 'admin'), 'success' => false]);
	}
}
