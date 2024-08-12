<?php

namespace App\Factory;


use App\Entity\Pokemon;
use App\Entity\PokemonTypes;
use Doctrine\ORM\EntityManagerInterface;

class PokemonFactory {
    private function __construct() {
    }

    /**
     * @param array $response
     * @param EntityManagerInterface $em
     * @return Pokemon The response from API. CAUTITION: Height*10 because PokeAPI gives the height in decimeter
     */
    public static function createPokemonByApi(array $response, EntityManagerInterface $em): Pokemon {
        $apiPokemon = new Pokemon();
        $apiPokemon->setName(ucfirst($response['name']));
        $apiPokemon->setHeight($response['height'] * 10);
        $apiPokemon->setPokedexId($response['id']);
        $pokemonTypeRepo = $em->getRepository(PokemonTypes::class);
        foreach ($response['types'] as $key => $type) {
            $localType = $pokemonTypeRepo->findOneBy(array('typeName' => ucfirst($response['types'][$key]['type']['name'])));
            $apiPokemon->addPokemonType($localType);
        }
        return $apiPokemon;
    }
}