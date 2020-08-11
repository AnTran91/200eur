<?php


namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ControllerTrait;
use App\Utils\UUID;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;

/**
 * @Route("user")
 * @Security("is_granted('ROLE_USER_MANAGER') or is_granted('ROLE_SUPER_ADMIN')")
 */
class UserActionController extends Controller
{
	use ControllerTrait;
	
	/**
	 * @Route(path="/generate/{id}/api/token", name="generate_api_token", methods={"GET"})
	 *
	 * @param Request $request
	 * @param User $user
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function regenerateToken(Request $request, User $user){
		if (!$this->isCsrfTokenValid('generate_api_token'.$user->getId(), $request->query->get('_token'))){
			throw new AccessDeniedHttpException('The CSRF token is invalid.');
		}
		
		$em = $this->getDoctrine()->getManager();
		
		do {
			$uniqueApiToken = UUID::v4();
		} while (!is_null($em->getRepository(User::class)->findOneBy(['apiToken' => $uniqueApiToken])));
		$user->setApiToken($uniqueApiToken);
		
		$em->flush();
		
		$this->addFlash('user_modified', $this->trans('admin.user.user_modified', [], 'admin'));
		return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_user_index')));
	}
	
	/**
	 * @Route(path="/generate/{id}/direcotry", name="generate_client_directory", methods={"GET"}, requirements={"id"="\d+"})
	 *
	 * @param Request $request
	 * @param User $user
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function createUserDir(Request $request, User $user)
	{
		if (!$this->isCsrfTokenValid('generate_client_directory'.$user->getId(), $request->query->get('_token'))){
			throw new AccessDeniedHttpException('The CSRF token is invalid.');
		}
		$user->setUserDirectory($this->createUniqueUserDir($user));
		$this->getDoctrine()->getManager()->flush();
		
		$this->addFlash('user_modified', $this->trans('admin.user.user_modified', [], 'admin'));
		return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_user_index')));
	}
	
	/**
	 * @Route("/clean/tmp/{id}", name="admin_user_clear_tmp_dir", methods="GET")
	 *
	 * @param Request $request
	 * @param User $user
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function clearUserTempDir(Request $request, User $user)
	{
		if (!$this->isCsrfTokenValid('clear_client_tmp_directory'.$user->getId(), $request->query->get('_token'))){
			throw new AccessDeniedHttpException('The CSRF token is invalid.');
		}
		
		$this->getDirHandler()->cleanUp($user->getUserDirectory());
		
		$this->addFlash('user_modified', $this->trans('admin.user.user_modified', [], 'admin'));
		return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_user_index')));
	}
}