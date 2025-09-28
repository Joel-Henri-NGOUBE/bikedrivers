<?php

namespace App\State;

use App\Repository\UserRepository;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Offers;
use App\Entity\RequiredDocuments;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Exception;

class DenyNotOwnerActionsProcessor implements ProcessorInterface
{
    public function __construct(private JWTTokenManagerInterface $jwtManager, private TokenStorageInterface $tokenStorageInterface, private UserRepository $userRepository)
    {
    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): RequiredDocuments|Offers
    {
        // dd($data);
        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

        $authenticatedUserId = $this->userRepository->findOneByMailField($decodedJwtToken["username"])->getId();

        if(in_array('user_id', array_keys($uriVariables))){
            
            if(!($uriVariables["user_id"] == $authenticatedUserId || in_array("ROLE_ADMIN", $decodedJwtToken["roles"]))){
                throw new Exception("You are not allowed to act on someone else's data");
            }
            
        }
        else if($data instanceof RequiredDocuments){

            $correspondingUserId = $data->getOffer()->getVehicle()->getUser()->getId();

            if(!($correspondingUserId == $authenticatedUserId || in_array("ROLE_ADMIN", $decodedJwtToken["roles"]))){
                throw new Exception("You are not allowed to act on someone else's data");
            }
            
        }

        return $data;
        
    }
}
