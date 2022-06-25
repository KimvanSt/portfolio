<?php

require_once('Card.php');

class Deck
{
    # The array will contain all the cards in a deck
    private array $cards = [];

    # This loop combines each suit with all of the values and puts the resulting
    # card into the Deck
    public function __construct()
    {
        $availableSuits = ['♣', '♤', '♦', '♥'];
        $availableValues = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'B', 'V', 'K', 'A'];
        foreach ($availableSuits as $suit) {
            foreach ($availableValues as $value) {
                $card = new Card($suit, $value);
                array_push($this->cards, $card);
            }
        }
    }

    # This function shuffles the array and takes out the top Card object.
    public function drawCard(): Card
    {
        try {
            if (count($this->cards) == 0) {
                throw new Exception("No more cards!");
            }
            shuffle($this->cards);
            $carddrawn = array_shift($this->cards);
            return $carddrawn;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }
}
