<?php

namespace App\Controller\Admin\Traits;

use App\Entity\User;
use App\Handlers\FileHandler;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Form\Form;
use Symfony\Component\EventDispatcher\Event;

use Doctrine\ORM\Query;
use App\Handlers\PaginatorHandler;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

/**
 * Extra features needed in controllers.
 */
trait ControllerTrait
{
    /**
     * @var PaginatorHandler
     */
    private $paginator;

    /**
     * @var FileHandler
     */
    private $dirHandler;

    /**
     * ControllerTrait constructor.
     *
     * @param PaginatorHandler $paginate
     * @param FileHandler $fileHandler
     */
    public function __construct(PaginatorHandler $paginate, FileHandler $fileHandler)
    {
        $this->paginator = $paginate;
        $this->dirHandler = $fileHandler;
    }

    /**
     * Get Error Messages From the Form with keys.
     *
     * @param Form  $form    The cuurent form that contains errors
     *
     * @return array The array of errors
     */
    public function getErrorMessages(Form $form): array
    {
        return \App\Utils\Tools::getErrorMessages($form);
    }

    /**
     * Get Error Messages From the Form without keys.
     *
     * @param Form  $form    The cuurent form that contains errors
     *
     * @return array The array of errors
     */
    public function getErrorMessage(Form $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            foreach ($form[$child->getName()]->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
        }
        return $errors;
    }

	/**
	 * Dynamic Pagination with entity class and extra entity to be use.
	 *
	 * @param array|null    $requestParams     An array of GET and POST parameters
	 * @param string        $entityClass       For the message
	 * @param array         $extraColumns      Add extra field (used for query optimization)
	 * @param array         $options
	 *
	 * @return SlidingPagination The Paginate
	 * @throws \Exception
	 */
    public function dynamicPaginator(?array $requestParams, string $entityClass, array $extraColumns = array(), array $options = array()): SlidingPagination
    {
        return $this->paginator->dynamicPaginatorHandler($requestParams, $entityClass, $extraColumns, $options);
    }

    /**
     * Basic Pagination with the given query.
     *
     * @param array|null    $requestParams       array of params
     * @param Query         $query               Database query
     * @return SlidingPagination The Paginator
     */
    public function basicPaginator(?array $requestParams, Query $query): SlidingPagination
    {
        return $this->paginator->paginatorHandler($requestParams, $query);
    }

    /**
     * Translates the given message.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     *
     * @return string The translated string
     */
    public function trans(string $id, array $parameters = array(), ?string $domain = null): ?string
    {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param int         $number     The number to use to find the indice of the message
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     *
     * @return string The translated string
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null): ?string
    {
        return $this->container->get('translator')->transChoice($id, $number, $parameters, $domain);
    }

	/**
	 * Retrieves a FilterCollection instance from the given ObjectManager.
	 *
	 * @throws \Gedmo\Exception\InvalidArgumentException
	 * @return mixed
	 */
	public function getFilters()
	{
		$om = $this->container->get('doctrine.orm.default_entity_manager');

		if (is_callable(array($om, 'getFilters'))) {
			return $om->getFilters();
		} else {
			if (is_callable(array($om, 'getFilterCollection'))) {
				return $om->getFilterCollection();
			}
		}
		throw new \Gedmo\Exception\InvalidArgumentException("ObjectManager does not support filters");
	}

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param string $eventName The name of the event to dispatch. The name of
     *                          the event is the name of the method that is
     *                          invoked on listeners.
     * @param Event  $event     The event to pass to the event handlers/listeners
     *                          If not supplied, an empty Event instance is created
     *
     * @return Event
     */
    public function dispatch($eventName, Event $event = null): ?Event
    {
        return $this->container->get('event_dispatcher')->dispatch($eventName, $event);
    }

    /**
     * @return FileHandler
     */
    public function getDirHandler(): FileHandler
    {
        return $this->dirHandler;
    }

	/**
	 * Create unique dir for client.
	 *
	 * @param User $user
	 * @return string
	 */
	public function createUniqueUserDir(User $user): string
	{
		/** @var ManagerRegistry $em */
		$em = $this->getDoctrine()->getManager();

		if (!empty($user->getFirstName())){
			$username = $user->getFirstName();
		}else{
			list($username) = explode('@', $user->getEmail());
		}

		$userUniqueDir = $this->getDirHandler()->getUserUniqueDir($username);
		while (!is_null($em->getRepository(User::class)->findOneBy(['userDirectory' => $userUniqueDir])))
		{
			$userUniqueDir = $this->getDirHandler()->getUserUniqueDir($username);
		}

		return $userUniqueDir;
	}

    /**
     * Change number of items will be displayed.
     *
     * @param $itemsPerPage
     */
	public function changeItemsPerPage($itemsPerPage) {
	    $this->paginator->setItemsPerPage($itemsPerPage);
    }
}
