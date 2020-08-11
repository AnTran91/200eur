<?php


namespace App\Controller\Immosquare\Traits;

use App\Entity\Order;
use App\Exception\ApiProblem;
use App\Exception\ApiProblemException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

trait APIControllerTrait
{
    /**
     * @param $data
     * @param int $statusCode
     * @param string $contentType
     * @return Response
     */
    public function createResponse($data, int $statusCode, string $contentType): Response
    {
        $response = new Response();
        $response->setContent($data);
        $response->setStatusCode($statusCode);

        $response->headers->set('Accept-version', $this->getParameter('api_version'));
        $response->headers->set('Content-Type', sprintf("%s; charset=UTF-8", $contentType));
        $response->setCharset('UTF-8');

        return $response;
    }

    /**
     * @param FormInterface $form
     */
    public function throwApiProblemValidationException(FormInterface $form)
    {
        $errors = $this->getErrorMessages($form);

        $apiProblem = new ApiProblem(
            400,
            ApiProblem::TYPE_VALIDATION_ERROR
        );
        $apiProblem->set('errors', $errors);

        throw new ApiProblemException($apiProblem);
    }

    /**
     * @param Order $order
     */
    public function throwApiProblemOrderNotReadyException(Order $order)
    {
        $apiProblem = new ApiProblem(
            400,
            ApiProblem::TYPE_ORDER_NOT_READY_ERROR
        );
        $apiProblem->set('message', sprintf('Current status : %s', $this->trans($order->getOrderStatus(), [], 'admin')));

        throw new ApiProblemException($apiProblem);
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
     * Get Error Messages From the Form with keys.
     *
     * @param FormInterface $form The cuurent form that contains errors
     *
     * @return array The array of errors
     */
    public function getErrorMessages(FormInterface $form): array
    {
        return \App\Utils\Tools::getFromErrorMessages($form);
    }
}