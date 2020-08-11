<?php

namespace App\Controller\Emmobilier;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Entity\Announcement;

class SecurityController extends Controller
{
    private $tokenManager;

    public function __construct(CsrfTokenManagerInterface $tokenManager = null)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route(path="login", name="emmobilier_security_login")
     */
    public function loginAction(Request $request)
    {
        /** @var $session Session */
        $session = $request->getSession();
        $announcement = $this->getDoctrine()->getManager()->getRepository(Announcement::class)->find(1);
        $now = new \DateTime('now');
        $show = false;
        $title = '';
        $content = '';
        if ($announcement != null){
            if ($now >= $announcement->getStartDate() && $now->format('Y-m-d H:i:s') <= $announcement->getEndDate()->format('Y-m-d 23:59:59') && $announcement->getEnabled()){
                $title = $announcement->getTitle();
                $content = $announcement->getBody();
                $show = true;
            }
        }

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;

        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
            'title' => $title,
            'content' => $content,
            'show' => $show
        ));
    }

    /**
     * @Route(path="/login_check", name="emmobilier_security_check")
     */
    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    /**
     * @Route(path="logout", name="emmobilier_security_logout")
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return Response
     */
    protected function renderLogin(array $data)
    {
        return $this->render('bundles/FOSUserBundle/Security/login.html.twig', $data);
    }
}
