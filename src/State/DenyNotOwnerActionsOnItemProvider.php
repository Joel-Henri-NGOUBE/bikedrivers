<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class DenyNotOwnerActionsOnItemProvider implements ProviderInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager, 
        private TokenStorageInterface $tokenStorageInterface, 
        private UserRepository $userRepository,
        #[Autowire(service: "api_platform.doctrine.orm.state.item_provider")]
        private ProviderInterface $providerInterface
        )
    {
    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

        $authenticatedUserId = $this->userRepository->findOneByMailField($decodedJwtToken["username"])->getId();

        if(in_array('user_id', array_keys($uriVariables))){
            
            if(!($uriVariables["user_id"] == $authenticatedUserId || in_array("ROLE_ADMIN", $decodedJwtToken["roles"]))){
                throw new Exception("You are not allowed to act on someone else's data");
            }

        }

        return $this->providerInterface->provide($operation, $uriVariables, $context);
    }
}
