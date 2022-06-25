<?php

require_once('Blackjack.php');

class Dealer
{
    private Blackjack $blackjack;
    private Deck $deck;
    # Making an array for the dealer's hand
    private array $dealerhand = [];
    # Three arrays: one for our player objects and an array for the players still in the game and the players who have passed
    private array $players = [];
    private array $ingame = [];
    private array $passed = [];

    # On constructing the dealer gets the blackjack and Deck objects
    public function __construct(Blackjack $blackjack, Deck $deck)
    {
        $this->blackjack = $blackjack;
        $this->deck = $deck;
        $card1 = $deck->drawCard();
        $card2 = $deck->drawCard();
        array_push($this->dealerhand, $card1);
        array_push($this->dealerhand, $card2);
    }

    # Adds a player to our Player arrays with the name as the key and the object as the value. The object is created
    # in the Player class.
    public function addPlayer(string $name)
    {
        $player = new Player($name);
        $deck = $this->deck;
        $card1 = $deck->drawCard();
        $card2 = $deck->drawCard();
        $player->addCard($card1);
        $player->addCard($card2);
        ($this->players)[$player->name] = $player;
    }

    public function playGame()
    {
        # Making a new array which starts as an array with player names. This array will
        # keep track of which player is still in the game
        foreach (($this->players) as $player) {
            $name = $player->name;
            array_push($this->ingame, $name);
        }
        echo "The game will start." . PHP_EOL;

        # While there are 1 or more players still in the game, players will have their turn. A new 
        # variable 'myturn' points to the player object in our array. This player will take a turn with the current
        # deck. If the player folds or gets busted, the player will be removed from 'ingame'. 
        while (count($this->ingame) >= 1) {
            $this->dealerTurn();
            foreach ($this->ingame as $player) {
                $myturn = ($this->players)[$player];
                $turn = $this->playerTurn($myturn, $this->deck);
                if (!$turn) {
                    $this->ingame = \array_diff($this->ingame, [$player]);
                }
            }
        }
        # When there are no more players 'ingame', the game will end and the winner will be determined.
        $this->endGame($this->players);
    }

    # Each turn starts with a Dealer Turn. The Dealer's hand will be scored (string) and valued (int). The hand will be shown.
    private function dealerTurn()
    {
        $dealerScore = ($this->blackjack)->scoreHand($this->dealerhand);
        $scoreValue = $this->getDealerScore();
        echo PHP_EOL . "Dealer has: " . PHP_EOL;
        foreach ($this->dealerhand as $card) {
            echo $card->show() . " ";
        }
        echo PHP_EOL;
        # Using the String-score, the game will determine if it has to end (ends when Dealer busts or has blackjack).
        # If the game doesn't end, and the dealer has 16 or less points, he has to draw a new card. 
        # The new card is shown and the game determines once again if it has to end.
        $this->didDealerWin($dealerScore);
        if ($scoreValue <= 16) {
            $newCard = ($this->deck)->drawCard();
            array_push($this->dealerhand, $newCard);
            echo PHP_EOL . "Dealer drew " . PHP_EOL . $newCard->show() . PHP_EOL;
            $dealerScore = ($this->blackjack)->scoreHand($this->dealerhand);
            $this->didDealerWin($dealerScore);
        }
    }

    # This function takes the string from the ScoreHand function, and checks if it returns a numeric score first
    # (for example: '15 points' instead of 'Blackjack!'). If so, it translates those numbers into the int value. 
    private function getDealerScore(): int
    {
        $score = ($this->blackjack)->scoreHand($this->dealerhand);
        if (is_numeric(substr($score, 0, 2))) {
            return intval(substr($score, 0, 2));
        } else {
            return 0;
        }
    }

    # This function retreives the string from the ScoreHand function, and checks for
    # the returned text: is it a win/bust or is it a score? On a win/bust it will end the game,
    # on a score it will simply echo the score. 
    private function didDealerWin($score)
    {
        $end = 0;
        if ($this->getWinner($score) == "win") {
            echo $score . PHP_EOL . "Dealer wins!" . PHP_EOL;
            $end = 1;
        } else if ($this->getWinner($score) == "out") {
            echo $score . PHP_EOL . "Dealer is busted. All remaining players won!" . PHP_EOL;
            foreach ($this->ingame as $player) {
                echo $player . PHP_EOL;
            }
            foreach ($this->passed as $player) {
                echo $player . PHP_EOL;
            }
            $end = 1;
        } else {
            echo $score . PHP_EOL;
        }
        if ($end == 1) {
            exit();
        }
    }

    # Asking the player if they want to hit or pass, returning false on Pass
    private function hitOrPass(): bool
    {
        $action = readline("Hit (H) or Pass (P)? H/P : ");
        if ($action == "H") {
            return true;
        } else if ($action == "P") {
            return false;
        }
    }

    # Checking if the score contains 'win' or 'busted', so the player can be
    # kicked out or the game stopped
    private function getWinner(string $string): string
    {
        if (str_contains($string, 'win')) {
            return "win";
        } else if (str_contains($string, 'Busted')) {
            return "out";
        } else {
            return "continue";
        }
    }

    # Start of the turn, announcing a turn, showing the player's cards, returning
    # a Win, a Busted, or the Score depending on the player's hand
    private function turnStart(Player $player): string
    {
        $announcer = "";
        $name = $player->name;
        $announcer .= PHP_EOL . "It's $name's turn." . PHP_EOL;
        $announcer .= $player->showHand() . PHP_EOL;
        $score = ($this->blackjack)->scoreHand($player->hand);
        if ($this->getWinner($score) == "win") {
            $announcer .= $score . PHP_EOL . "$name wins!" . PHP_EOL;
        } else if ($this->getWinner($score) == "out") {
            $announcer .= $score . PHP_EOL . "Sorry $name, you're out." . PHP_EOL;
        } else {
            $announcer .= $score . PHP_EOL;
        }
        return $announcer;
    }

    # After the turn start of the previous function, if this player does not get points,
    # Checking if they won - The game will end. If they didn't they got busted, and 'false'
    # is returned. Otherwise, they get the option to Hit (True) or Pass (False).
    public function playerTurn(Player $player, Deck $deck): bool
    {
        $startTurn = $this->turnStart($player);
        echo $startTurn;
        if (!str_contains($startTurn, 'points')) {
            if (str_contains($startTurn, 'wins')) {
                exit();
            } else {
                return false;
            }
        } else {
            $next = $this->hitOrPass();
            if ($next) {
                $newCard = $deck->drawCard();
                $player->addCard($newCard);
                return true;
            } else {
                array_push($this->passed, $player->name);
                return false;
            }
        }
    }

    # This function ends the game. 
    private function endGame(array $players)
    {
        # A new array for final scores. The final score is calculated and pushed into the array with
        # name as key and final score as value. 
        echo PHP_EOL . "The final scores were: " . PHP_EOL;
        $finalScores = [];
        foreach ($players as $player) {
            ${"score$player->name"} = $this->finalScore($player);
            echo $player->name . ": " . ${"score$player->name"} . PHP_EOL;
            $finalScores[$player->name] = ${"score$player->name"};
        }
        # The score of the dealer is calculated and entered into the array. 
        $dealerscore = $this->getDealerScore();
        $finalScores['Dealer'] = $dealerscore;
        echo "Dealer: $dealerscore" . PHP_EOL;
        $this->determineWinner($finalScores);
    }

    # Calculating the final score - Scoring the current player hand. If there are
    # points in it it means the player wasn't busted, so they get the points. Otherwise,
    # the points are 0. 
    private function finalScore(Player $player): int
    {
        $score = ($this->blackjack)->scoreHand($player->hand);
        if (is_numeric(substr($score, 0, 2))) {
            return intval(substr($score, 0, 2));
        } else {
            return 0;
        }
    }

    # The scores in the array are used to check who has the highest score and who is the winner. 
    private function determineWinner(array $scores)
    {
        # The loop calculates who the winner is and congratulates them. 
        $winner = "Nobody";
        $winnerscore = 0;
        foreach ($scores as $player => $score) {
            if ($score > $winnerscore) {
                $winner = $player . ", you win!";
                $winnerscore = $score;
            } else if ($score == $winnerscore) {
                $winner .= " And $player, you win!";
            }
        }
        echo "$winner The winning score was $winnerscore.";
    }
}
