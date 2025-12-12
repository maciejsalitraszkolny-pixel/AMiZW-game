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
use App\Helper\PlayerHelper;

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
                'state' => $session->get('state'),
                "log" => ($session->get('state')->getLog()) 
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
        $actualHp = $state->getPlayer()->getHp();
        switch ($actionName) {
            case ActionType::ATTACK->value:
                $dmg = DmgHelper::calculateDmgWithLevel(8,18,$state->getPlayer()->getExp());
                $state->getMonster()->takeDmg($dmg);
                $state->addLog("Gracz zadaje potworowi obrażeń w rozmiarze $dmg.");
                break;

            case ActionType::HeavyAttack->value:
                $dmg = DmgHelper::calculateDmgWithLevel(18, 23,$state->getPlayer()->getExp());
                $state->getMonster()->takeDmg($dmg);
                $state->addLog("Gracz zadaje mocnej ataki potworowi w rozmiarze $dmg.");
                break;

            case ActionType::HEAL->value:
                $heal = 30;
                
                $state->addLog("Gracz odnawia swoje życie na $actualHp.");

                if($actualHp>=100){
                    break;
                }
                if($actualHp >= 70){
                    $state->getPlayer()->setHeal(100 - $actualHp);
                    break;
                }
                $state->getPlayer()->setHeal($heal);
                break;

            case ActionType::RUN->value:
                $state->getPlayer()->run();
                $state->addLog("Gracz biegnie tracąc $actualHp życia.");
                break;

            default:
                // wrong action
                break;
        }

        if ($state->getMonster()->getHp() <= 0) {
            $state->nextWave();
            $state->getPlayer()->addExperience($state->getMonster()->getExp());
            if (PlayerHelper::checkExperience($state->getPlayer()->getExp()))
            {
                $state->getPlayer()->levelUp();
            }
            $state->setMonster($this->spawnMonster($state->getWave()));
            $session->set('state', $state);
            return $this->redirectToRoute('game');
        }


        if (!$state->isOver()) {
            $monsterDmg = DmgHelper::calculateDamage(6,14);
            $state->getPlayer()->takeDmg($monsterDmg);
            $state->addLog("Potwór daje obrażenia $monsterDmg.");
            if ($state->getPlayer()->getHp() <= 0) {
                $state->getPlayer()->setHp(0);
                $state->endGame();
                $state->addLog("Koniec fali.");
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
            new Player(100, 1, 0),
            new Monster('Goblin', 50, 5),
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
            return new Monster('BOSS Troll', 120 + ($wave-3)*15,15*($wave/3));
        }
        if ($wave >= 2) {
            return new Monster('Ork', 80 + ($wave-2)*12,12*($wave/2));
        }
        return new Monster('Goblin', 50 + ($wave-1)*10,10*($wave/1));
    }
}
