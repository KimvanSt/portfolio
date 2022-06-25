<?php

require_once('Card.php');
require_once('Blackjack.php');
require_once('Deck.php');

class Player
{
    # Name and hand are available outside this scope so they can be
    # used in the other classes' functions.
    public string $name;
    public array $hand = [];

    # Add a card to the player's hand
    public function addCard(Card $card)
    {
        array_push($this->hand, $card);
    }

    # When a player gets created, they get two cards added to their hand
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    # Showing the player's hand
    public function showHand(): string
    {
        $currenthand = $this->hand;
        $result = "$this->name has: ";
        foreach ($currenthand as $card) {
            $result .= "{$card->show()} ";
        }
        return $result;
    }
}
