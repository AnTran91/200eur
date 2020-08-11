<?php

/**
 * (c) Sfari Rami <rami2sfari@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Emmobilier\Traits;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use App\PaymentServiceProvider\SystemPayProviderInterface;

/**
 * Add SystemPay features needed in controllers.
 */
trait SystemPayControllerTrait
{
    /**
     * @var SystemPayProviderInterface
     */
    private $systempay;

    /**
     * SystemPayControllerTrait Construct
     *
     * @param SystemPayProviderInterface $systempay
     */
    public function __construct(SystemPayProviderInterface $systempay)
    {
        $this->systempay = $systempay;
    }
	
	/**
	 * Pay with systempay form
	 *
	 * @param array $options
	 * @return Response A Response instance
	 */
    public function sendPaymentRequest(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureSystemPayOptions($resolver);

        $this->systempay->prepareTransaction($resolver->resolve($options));

        return $this->render('emmobilier/systempay/redirection_to_gateway.html.twig', [
          'fields' => $this->systempay->getRequest()
        ]);
    }

    /**
     * Verify the payment with systempay form
     *
     * @param array $request
     * @param string $locale
     * @return array
     */
    public function paymentVerification(array $request, string $locale)
    {
        return $this->systempay->responseHandler($request, $locale);
    }

    /**
     * configure OptionsResolver
     *
     * @param OptionsResolver $resolver
     */
    public function configureSystemPayOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'amount' => null,
            'reference' => null,
            'customer' => null,
            'validationURL' => null,
            'orderID' => null,
            'shopURL' => $this->generateUrl('order_list', [], UrlGeneratorInterface::ABSOLUTE_URL)
        ));

        $resolver->setAllowedTypes('amount', ['null', 'float']);
        $resolver->setAllowedTypes('reference', ['null', 'string']);
        $resolver->setAllowedTypes('customer', ['null', '\App\Entity\User']);
        $resolver->setAllowedTypes('orderID', ['null', 'string']);
        $resolver->setAllowedTypes('shopURL', ['null', 'string']);
        $resolver->setAllowedTypes('validationURL', ['null', 'string']);

        $resolver->setRequired(['amount', 'reference', 'customer', 'validationURL', 'orderID']);
    }
}
