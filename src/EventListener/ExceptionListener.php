<?php

namespace App\EventListener;

use App\Exception\ApiProblemException;
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Serializer\SerializerInterface;


class ExceptionListener
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var array
     */
    private $configs;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $apiVersion;
	
	/**
	 * @param SerializerInterface $serializer
	 * @param LoggerInterface $logger
	 * @param array $exceptionListenerConfigs
	 * @param string $apiVersion
	 */
    public function __construct(SerializerInterface $serializer, LoggerInterface $logger, array $exceptionListenerConfigs, string $apiVersion)
    {
        $this->serializer = $serializer;
        $this->configs = $exceptionListenerConfigs;
        $this->logger = $logger;
        $this->apiVersion = $apiVersion;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();

        if (!$exception instanceof ApiProblemException) {
            return;
        }

        $format = $this->configs["response"]["format"];
        $contentType = $this->configs["response"]["content_type"];
        if ($format != $request->getRequestFormat() && in_array($request->getRequestFormat(), $this->configs["response"]["valid_format"])){
            $format = $request->getRequestFormat();
            $contentType = $request->getMimeType($request->getRequestFormat());
        }

        $this->setLogger($exception);

        // Send the modified response object to the event
        $event->setResponse($this->createResponse($exception, $format, $contentType));
    }

    private function createResponse(ApiProblemException $exception , $format, $contentType): Response
    {
        $data = $exception->getMessage();
        $statusCode = $exception->getStatusCode();

        if (method_exists($exception, 'getApiProblem') && !is_null($exception->getApiProblem())){
            $data = $exception->getApiProblem()->toArray();
            $statusCode = $exception->getApiProblem()->getStatusCode();
        }

        $response = new Response();
        // set message content
        $response->setContent($this->serializer->serialize($data, $format));
        $response->setStatusCode($statusCode);
        // Set the header content type
        $response->headers->set('Accept-version', $this->apiVersion);
        $response->headers->set('Content-Type', $contentType);

        return $response;
    }

    /**
     * Log the exception
     *
     * @param \Exception $exception
     */
    public function setLogger(\Exception $exception): void
    {
        $this->logger->error(sprintf('`%s` [caught exception]: throws `%s` (code `%s`) at `%s` line `%s`', get_class($exception), $exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine()));
    }
}