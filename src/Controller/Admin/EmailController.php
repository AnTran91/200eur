<?php
namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use App\Handlers\MailerHandler;
use App\Form\Admin\MailerType;


/**
 * @Route("send/mail")
 * @Security("is_granted('ROLE_USER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class EmailController extends Controller
{
	use ControllerTrait;
	
	/**
	 * @Route("/", name="admin_email_manually_send", methods="GET|POST")
     *
	 * @param Request $request
	 * @param MailerHandler $mailer
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 */
    public function send(Request $request, MailerHandler $mailer)
    {
        $form = $this->createForm(MailerType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $email = $form->getData();
            $mailer->sendSimpleMail($email['to'], $email['subject'], $email['content']);
	
	        $this->addFlash('flash_msg_success', $this->trans('admin.email.msg', [], 'admin'));
	        
	        return $this->redirectToRoute('admin_email_manually_send');
        }

        return $this->render('admin/email/show.html.twig', ['form' => $form->createView()]);
    }
}