<?php

namespace App\Controller\Emmobilier;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TranslateController extends Controller
{
	/**
	 * @Route("/translate" , name="translate_index", methods="GET")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
    public function translatorAction(Request $request)
    {
        $lang = $request->get('lang' , $this->getParameter('default_locale'));

        if (in_array($lang, $this->getParameter('locale_supported'))) {
            $session = $request->getSession();
            $session->set('_locale', $lang);
        }

        return $this->redirect($request->headers->get('referer', $this->generateUrl('order_list')));
    }
}
