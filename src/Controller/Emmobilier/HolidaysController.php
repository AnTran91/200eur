<?php

namespace App\Controller\Emmobilier;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Response;

use App\Repository\HolidaysRepository;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("holidays")
 * @Security("is_granted('ROLE_EMMOBILIER_USER')")
 */
class HolidaysController extends Controller
{
	/**
	 * @Route("/", name="holidays_list", options = { "expose" = true }, methods="GET")
	 *
	 * @param HolidaysRepository $holidaysRepository
	 * @return Response
	 */
    public function index(HolidaysRepository $holidaysRepository): Response
    {
        return $this->json($holidaysRepository->findByMonthIterval(2));
    }
}
