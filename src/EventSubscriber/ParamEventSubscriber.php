<?php

namespace App\EventSubscriber;

use App\Entity\PictureDetails;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use App\Events\ParamEvent;
use App\Utils\Events;

use App\Handlers\ParamHandler;
use App\Handlers\OrderHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @see \App\Events\ParamEvent class.
 */
final class ParamEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ParamHandler
     */
    private $paramHandler;

    /**
     * @var OrderHandler
     */
    private $orderHandler;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Constructor
     *
     * @param ParamHandler $paramHandler
     * @param OrderHandler $orderHandler
     * @param EntityManagerInterface $em
     */
    public function __construct(ParamHandler $paramHandler, OrderHandler $orderHandler, EntityManagerInterface $em)
    {
        $this->paramHandler = $paramHandler;
        $this->orderHandler = $orderHandler;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
          Events::ON_SAVE_PARAMS => [
            array('doSaveParams', 0)
          ],
          Events::ON_UPDATE_ONE_PARAM => [
            array('doUpdateOneParam', 0)
          ],
          Events::ON_UPDATE_PARAMS => [
            array('doDeletePictureDetail', 0),
            array('doValidateAndUpdateParams', -10)
          ]
        ];
    }

    /**
     * Save params in the form
     *
     * @param ParamEvent $paramEvent
     */
    public function doSaveParams(ParamEvent $paramEvent)
    {
    	/** @var SessionInterface $session */
        $session = $paramEvent['request']->getSession();
        $retouchs = $session->get('retouchs', []);
        $params = $session->get('params', []);

        $retouchObjects = $this->em
                  ->getRepository(\App\Entity\Retouch::class)
                  ->findBy(['id' => $paramEvent['formData']['retouchs']]);
        
		/**
		 * @var array $validParams
		 * @var array $errors
		 */
        extract($this->paramHandler->handleParams($paramEvent['request'], $params[$paramEvent['pictureDir']] ?? [], $retouchObjects, [
              'userDir' => $paramEvent['userDir'],
              'pictureDir' => $paramEvent['pictureDir'],
              'orderUploadFolder' => null
        ]));

        $params[$paramEvent['pictureDir']] = $validParams;
        $retouchs[$paramEvent['pictureDir']] = $paramEvent['formData']['retouchs'];

        $session->set('retouchs', $retouchs);
        $session->set('params', $params);

        $paramEvent->setViolations($errors);
    }

    /**
     * Update one Param
     *
     * @param ParamEvent $paramEvent
     */
    public function doUpdateOneParam(ParamEvent $paramEvent)
    {
	    /**
	     * @var array $validParams
	     * @var array $errors
	     */
        // extract($this->paramHandler->handleParams($paramEvent['request'], [
        //   $paramEvent['pictureDetail']->getRetouch()->getId() => $paramEvent['pictureDetail']->getParam()->getElements()
        // ], [$paramEvent['pictureDetail']->getRetouch()], [
        //   'userDir' => $paramEvent['pictureDetail']->getPicture()->getOrder()->getClient(),
        //   'pictureDir' => $paramEvent['pictureDetail']->getPicture()->getPictureDirectory(),
        //   'orderUploadFolder' => $paramEvent['pictureDetail']->getPicture()->getOrder()->getUploadFolder()
        // ]));

        extract($this->paramHandler->handleParamsUpdate($paramEvent['request'], [
          $paramEvent['pictureDetail']->getRetouch()->getId() => $paramEvent['pictureDetail']->getParam()->getElements()
        ], [$paramEvent['retouch']], [
          'userDir' => $paramEvent['pictureDetail']->getPicture()->getOrder()->getClient(),
          'pictureDir' => $paramEvent['pictureDetail']->getPicture()->getPictureDirectory(),
          'orderUploadFolder' => $paramEvent['pictureDetail']->getPicture()->getOrder()->getUploadFolder()
        ]));

        // $paramEvent['pictureDetail']->getParam()->setElements($validParams[$paramEvent['pictureDetail']->getRetouch()->getId()]);
        $paramEvent['pictureDetail']->getParam()->setElements($validParams[$paramEvent['retouch']->getId()]);
        $this->em->flush();

        $paramEvent->setViolations($errors);
    }

    /**
     * delete none used picture details
     *
     * @param ParamEvent $paramEvent
     */
    public function doDeletePictureDetail(ParamEvent $paramEvent)
    {
    	/** @var PictureDetails $pictureDetail */
	    foreach ($paramEvent['picture']->getPictureDetail() as $pictureDetail) {
            if (!in_array($pictureDetail->getRetouch()->getId(), $paramEvent['formData']['retouchs'])) {
                $paramEvent['picture']->removePictureDetail($pictureDetail);
                $this->em->remove($pictureDetail);
            }
        }
        $this->em->flush();
    }
	
	/**
	 * Create params
	 *
	 * @param ParamEvent $paramEvent
	 * @throws \Doctrine\DBAL\ConnectionException
	 */
    public function doValidateAndUpdateParams(ParamEvent $paramEvent)
    {
        $oldParams = $paramEvent['picture']->getPictureDetail()->map(function (PictureDetails $pictureDetail) {
            return [$pictureDetail->getRetouch()->getId() => $pictureDetail->getParam()->getElements()];
        })->toArray();

        $retouchObjects = $this->em
              ->getRepository(\App\Entity\Retouch::class)
              ->findBy(['id' => $paramEvent['formData']['retouchs']]);
	
	    /**
	     * @var array $validParams
	     * @var array $errors
	     */
        extract($this->paramHandler->handleParams($paramEvent['request'], $oldParams, $retouchObjects, [
          'userDir' => $paramEvent['userDir'],
          'pictureDir' => $paramEvent['picture']->getPictureDirectory(),
          'orderUploadFolder' => $paramEvent['picture']->getOrder()->getUploadFolder()
        ]));

        $this->orderHandler->editAndUpdateOrder($retouchObjects, $validParams, $paramEvent['picture']);
    }
}
