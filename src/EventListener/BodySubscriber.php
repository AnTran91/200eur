<?php

namespace App\EventListener;

use App\Controller\Immosquare\RequestFilter\APIFilterInterface;
use App\Exception\ApiProblem;
use App\Exception\ApiProblemException;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * BodySubscriber containe the logic that you want to be executed before your controllers.
 * In order to fire this event you need to implements BodyFilterInterface.
 *
 * @internal
 */
class BodySubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $configs;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param array                 $bodyListenerConfigs
     * @param SerializerInterface   $serializer
     */
    public function __construct(array $bodyListenerConfigs, SerializerInterface $serializer)
    {
        $this->configs = $bodyListenerConfigs;
        $this->serializer = $serializer;
    }

    /**
    * This method will hold the logic that validate the Body.
    *
    * @param FilterControllerEvent $event
    * @return void
    *
    * @throws AccessDeniedHttpException if the Content-Type not valid.
    */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof APIFilterInterface) {
            $request = $event->getRequest();
            $content = $request->getContent();

            // Content-Type accepted 'application/json' or 'application/xml'
            $mimetypes = $this->getMimeTypes();
            $contentType = $request->headers->get('Content-Type');

            if (!in_array(strtolower($contentType), array_keys($mimetypes))) {
                $this->throwInvalidRequestBodyFormat(sprintf("Error Processing Request, Content-Type %s is not accepted.", $contentType));
            }
            $request->setFormat($mimetypes[$contentType], $contentType);

            try {
                // decode the body content
                $data = $this->serializer->decode($content, $request->getContentType());
	
	            // test if body content is valid array
	            if (!is_array($data) || empty($data)) {
		            $this->throwInvalidRequestBodyFormat("The request body is empty.");
	            }
	
	            $request->request = new ParameterBag($data);

            } catch (NotEncodableValueException $e) {
                $this->throwInvalidRequestBodyFormat("The request body is malformed.");
            }
        }
    }

    /**
    * Return accepted mimeTypes
    *
    * @return array|null
    */
    private function getMimeTypes()
    {
        $formats = array();
        foreach ($this->configs['accepted_formats'] as $format => $mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $formats[strtolower($mimeType)] = strtolower($format);
            }
        }
        return $formats;
    }

    private function throwInvalidRequestBodyFormat(string $msg)
    {
        $apiProblem = new ApiProblem(
            400,
            ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
        );
        $apiProblem->set('error', $msg);

        throw new ApiProblemException($apiProblem);
    }

    /**
    * {@inheritDoc}
    */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(
              array('onKernelController', -10)
            ),
        );
    }
}
