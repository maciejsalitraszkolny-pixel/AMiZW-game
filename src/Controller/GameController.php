<?php

declare(strict_types=1);


namespace App\Controller;


use App\Dictionary\ActionType;
use App\Helper\DmgHelper;
use App\ValueObject\Monster;
use App\ValueObject\Player;
use App\ValueObject\State;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/game')]
class GameController extends AbstractController
{
    #[Route('', name: 'game', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('state')) {
            $this->resetState($session);
        }

        return $this->render('game/index.html.twig', [
                'state' => $session->get('state')
        ]);
    }

    #[Route('/action/{actionName}', name: 'game_action', methods: ['POST'])]
    public function action(Request $req, string $actionName): Response
    {
        $session = $req->getSession();
        if (!$session->has('state'))
        {
            $this->resetState($session);
        }
        /** @var State $state */
        $state = $session->get('state');

        if ($state->isOver()) {
            return $this->redirectToRoute('game');
        }

        switch ($actionName) {

            case ActionType::ATTACK->value:
                $dmg = DmgHelper::calculateDamage(8,18);
                $state->getMonster()->takeDmg($dmg);
                break;


            case ActionType::HEAVY->value:
                $heavydmg = DmgHelper::calculateHeavyDamage(20,40);
                $state->getMonster()->heavyDmg($heavydmg);
                break;


            case ActionType::HEAL->value:
                $heal = 40;
                $state->getPlayer()->heal($heal);
                break;

            case ActionType::RUN->value:
                $run = DmgHelper::calculateRunDamage(0,5);
                $state->getPlayer()->run($run);

                break;



            default:
                // wrong action
                break;
        }

        if ($state->getMonster()->getHp() <= 0) {
            $state->nextWave();
            $state->setMonster($this->spawnMonster($state->getWave()));
            $session->set('state', $state);
            return $this->redirectToRoute('game');
        }


        if (!$state->isOver()) {
            $monsterDmg = DmgHelper::calculateDamage(6,14);
            $state->getPlayer()->takeDmg($monsterDmg);
            if ($state->getPlayer()->getHp() <= 0) {
                $state->getPlayer()->setHp(0);
                $state->endGame();
            }
        }

        $session->set('state', $state);
        return $this->redirectToRoute('game');
    }

    #[Route('/reset', name: 'game_reset', methods: ['POST'])]
    public function reset(Request $req): Response
    {
        $this->resetState($req->getSession());
        return $this->redirectToRoute('game');
    }

    private function resetState(Session $session): void
    {
        $session->set('state', new State(
            new Player(100),
            new Monster('Goblin', 50),
            1,
            0,
            3,
            false,
            [['['.date('H:i:s').']', 'Bitwa się zaczyna!']]
            )
        );
    }

    private function spawnMonster(int $wave): Monster
    {
        if ($wave % 3 === 0) {
            return new Monster('BOSS Troll', 120 + ($wave-3)*15);
        }
        if ($wave >= 2) {
            return new Monster('Ork', 80 + ($wave-2)*12);
        }
        return new Monster('Goblin', 50 + ($wave-1)*10);
    }
}
