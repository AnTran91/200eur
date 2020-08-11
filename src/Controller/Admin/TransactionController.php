<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Form\Admin\Filters\TransactionFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Transaction;

/**
 * @Route("transaction")
 * @Security("is_granted('ROLE_TRANSACTION_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class TransactionController extends Controller
{
    use ControllerTrait;
	
	/**
	 * @Route("/", name="admin_transaction_index", methods="GET")
	 *
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
    public function index(Request $request): Response
    {
        $transactions = $this->dynamicPaginator([
            'filter' => $request->query->get('filter', []),
            'search' => $request->query->get('search', null),
            'page' => $request->query->get('page', 1)
        ], Transaction::class, ['client']);

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('admin/transaction/_list.html.twig', ['transactions' => $transactions]),
                'page' => $transactions->getPaginationData()['current'],
                'params' => $transactions->getParams(),
                'total_page' => $transactions->getPaginationData()['pageCount']
            ]);
        }

        return $this->render('admin/transaction/index.html.twig', [
            'transactions' => $transactions,
            'filter_form' => $this->createForm(TransactionFilterType::class)->createView()
        ]);
    }
	
	/**
	 * @Route("/{id}", name="admin_transaction_show", methods="GET")
	 *
	 * @param Transaction $transaction
	 * @return Response
	 */
    public function show(Transaction $transaction): Response
    {
        return $this->render('admin/transaction/show.html.twig', ['transaction' => $transaction]);
    }
	
	/**
	 * @Route("/{id}", name="admin_transaction_delete", methods="DELETE")
	 *
	 * @param Request $request
	 * @param Transaction $transaction
	 * @return Response
	 */
    public function delete(Request $request, Transaction $transaction): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transaction->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction);
            $em->flush();
        }

        return $this->redirectToRoute('admin_transaction_index');
    }
}
