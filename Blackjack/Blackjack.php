<?php

class Blackjack
{
    # Using the 'score' function from Card class to determine the score. Returning
    # Busted, a type of Win, or the regular score as a string. 
    public function scoreHand(array $hand): string
    {
        $value = 0;
        foreach ($hand as $card) {
            $value += $card->score();
        }
        if ($value > 21) {
            $result = "Oh no, " . strval($value) . "! Busted!";
        } else if (count($hand) == 2 && $value == 21) {
            $result = "Blackjack!! You win!";
        } else if (count($hand) >= 5 && $value < 21) {
            $result = "Five Card Charlie, you win!";
        } else if ($value == 21) {
            $result = "Twenty-One, you win!";
        } else {
            $result = strval($value) . " points.";
        }
        return $result;
    }
}
