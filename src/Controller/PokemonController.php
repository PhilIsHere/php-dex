<?php


namespace App\Controller;

use App\Entity\Pokemon;
use App\Entity\PokemonTypes;
use App\Form\PokemonFormType;
use App\Repository\PokemonTypesRepository;
use App\Service\IApiCall;
use App\Service\ServiceGetPokemonApi;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class PokemonController extends AbstractController {
    private RequestStack $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack) {
        $this->requestStack = $requestStack;
    }

    /*
     * Auf der GET Route dürfen nur angezeigte Elemente genutzt werden, keine speichernde elemente.
     */
    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     * Zeigt alle bisher angelegten Pokemon an.
     */
    #[Route('/pokemon', name: 'post_pokemon', methods: 'GET')]
    public function pkmnname(Request $request, ManagerRegistry $doctrine, IApiCall $getPokemonApi): Response {
        $session = $this->requestStack->getSession();
        $pkmnDb = $doctrine->getRepository(Pokemon::class);
        $form = $this->createForm(PokemonFormType::class);
        $form->handleRequest($request);
        try {
            if ($session->get('pokedexId')) {
                $cmpPokemon = $getPokemonApi->getPokemon((int)$session->get('pokedexId'));
                //ToDO
                $session->remove('pokedexId');
            }

            if (!$pkmnDb) {
                $session->getMetadataBag()->setName('Datenbank leer');
            }
        } catch (Exception $exception) {
            $session->getMetadataBag()->setName($exception);
        } finally {
            return $this->render('pokemon/pokemon_get_and_create.html.twig', [
                'allPkmn' => $pkmnDb->findAll(),
                'controller_name' => 'PokemonController',
                'pokemonForm' => $form->createView(),
            ]);
        }
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     * Erstellt eine Eingabemaske wo der User ein Pokemon anlegen kann.
     * Es wird in der Datenbank gespeichert und der Name des zuletzt angelegten Pokemon wird in der Session vermerkt.
     */
    //Eine POST Route hat nie ein eigenes Template, sondern ein redirect!
    #[Route('/pokemon', name: 'get_pokemon', methods: 'POST')]
    public function index(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response {
        $session = $this->requestStack->getSession();
        $listOfPokemon = $doctrine->getRepository(Pokemon::class)->findAll();
        $form = $this->createForm(PokemonFormType::class, new Pokemon());

        $form->handleRequest($request);
        $errors = $validator->validate($form);
        if ($form->isSubmitted() && $form->isValid()) {
            $session->remove('pokedexId');
            /** @var Pokemon $task */
            $task = $form->getData();
            if ($form->has('pokemonType')) {
                $typeName = $form->get('pokemonType')->getData();
                foreach ($typeName as $type) {
                    $task->addPokemonType($type);
                }
            }
            $pkmnManager = $doctrine->getManager();
            $pkmnManager->persist($task);
            $session->set('pokedexId', $task->getPokedexId());
            $pkmnManager->flush();
            return $this->redirect('/pokemon?pokemonid=' . (int)$session->get('pokedexId'));

        }
        return $this->render('pokemon/pokemon_get_and_create.html.twig', [
            'allPkmn' => $listOfPokemon,
            'message' => 'Dein Pokemon wurde erfolgreich angelegt!',
            'controller_name' => 'PokemonController',
            'pokemonForm' => $form->createView(),
        ]);
    }
}
