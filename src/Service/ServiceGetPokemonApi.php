<?php

namespace App\Service;

use App\Entity\Pokemon;
use App\Entity\PokemonTypes;
use App\Factory\PokemonFactory;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;


class ServiceGetPokemonApi implements IApiCall {
    const BASE_URL = "https://pokeapi.co/api/v2/";

    /**
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $em
     */
    public function __construct(private readonly LoggerInterface $logger, private readonly EntityManagerInterface $em) {
    }

    /**
     * @param int $pkmnId The Pokedex Number of the Pokemon >0 && <=905
     * @return Pokemon|null
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws InvalidArgumentException
     */
    public function getPokemon(int $pkmnId): ?Pokemon {
        $apiResponse = [];
        try {
            $cache = new FilesystemAdapter();
            $apiResponse = $cache->get('pkmnObject', function (ItemInterface $item) use ($pkmnId) {
                $httpClient = HttpClient::create();
                $item->expiresAfter(1800);
                return $httpClient->request('GET', self::BASE_URL . 'pokemon/' . $pkmnId);
            });
            $apiResponse = $apiResponse->toArray();
        } catch (Exception $exception) {
            $this->logger->critical('Exception in file: ' . __FILE__);
            $this->logger->critical($exception->getMessage());
        }
        return PokemonFactory::createPokemonByApi($apiResponse, $this->em);
    }

    /**
     * @param int $id
     * @return PokemonTypes
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getPokemonType(int $id): PokemonTypes {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', self::BASE_URL . 'type/' . $id)->toArray();
        $pokemonType = new PokemonTypes();
        $pokemonType->setTypeName(ucfirst($response['name']));
        return $pokemonType;
    }
}