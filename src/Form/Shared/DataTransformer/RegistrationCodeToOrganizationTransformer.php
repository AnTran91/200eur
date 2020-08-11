<?php
namespace App\Form\Shared\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;

class RegistrationCodeToOrganizationTransformer implements DataTransformerInterface
{
    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * Transforms an object (organization) to a string (number).
     *
     * @param  Organization|null $organization
     * @return string
     */
    public function transform($organization)
    {
        if (null === $organization || !$organization instanceOf Organization) {
            return '';
        }

        return $organization->getRegistrationCode();
    }

    /**
     * Transforms a string (number) to an object (organization).
     *
     * @param  string $registrationCode
     * @return Organization|null
     * @throws TransformationFailedException if object (organization) is not found.
     */
    public function reverseTransform($registrationCode)
    {
        // no registration code number? It's optional, so that's ok
        if (empty($registrationCode)) {
            return null;
        }

        $organization = $this->organizationRepository
            ->findOneBy(['registrationCode' => $registrationCode])
        ;

        if (!is_null($organization)) {
          return $organization;
        }

        throw new TransformationFailedException(sprintf(
            'This registration code is not valid!',
            $registrationCode
        ));
    }
}
