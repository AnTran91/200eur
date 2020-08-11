<?php

namespace App\Controller\script;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Entity\User;

class updateUserScript extends Controller
{
	/**
	 * @Route("/update_user", name="update_user", methods={"GET", "POST"})
	 *
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
    public function updateUsers()
    {	
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();
        // dump($users);
        foreach ($users as $key => $user) {
            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();
            $user->setFirstName($lastName);
            $user->setLastName($firstName);
            // dump($lastName); exit;
        }
        $em->flush();
		print_r('Updated done!'); exit;
		return $this->redirectToRoute('order_list');
    }
}