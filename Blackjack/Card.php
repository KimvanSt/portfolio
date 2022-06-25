<?php

class Card
{
    #Suit and value are only needed in this class
    private string $suit = "No suit selected";
    private string $value = "No value selected";

    #Checking if the suit matches available suits, and value matches available
    #values
    private function validateSuit($cardSuit)
    {
        $availableSuits = ['♣', '♤', '♦', '♥'];
        if (!in_array($cardSuit, $availableSuits)) {
            throw new InvalidArgumentException("Dit is geen geldige kleur!");
        }
    }

    private function validateValue($cardValue)
    {
        $availableValues = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'B', 'V', 'K', 'A'];
        if (!in_array($cardValue, $availableValues)) {
            throw new InvalidArgumentException("Dit is geen geldige waarde!");
        }
    }

    # On making a card, a suit and value is accepted and processed into this card
    public function __construct($suit, $value)
    {
        $this->suit = $suit;
        $this->value = $value;
        try {
            $this->validateSuit($suit);
            $this->validateValue($value);
        } catch (InvalidArgumentException $ie) {
            echo "$suit $value kunnen wij niet verwerken. " . $ie->getMessage() . PHP_EOL;
            exit();
        }
    }

    # Showing the card combines the current suit and value and returns them as a string
    public function show(): string
    {
        $resultSymbol = $this->suit;
        $resultValue = $this->value;
        $result = $resultSymbol . $resultValue;
        return $result;
    }

    # Scoring the card combines the numeric value and the value of an image into
    # the final score
    public function score(): int
    {
        $score = 0;
        $cardvalues = [
            "B" => 10,
            "V" => 10,
            "K" => 10,
            "A" => 11
        ];
        if (is_numeric($this->value)) {
            $score += intval($this->value);
        } else {
            $score += $cardvalues[$this->value];
        }
        return $score;
    }
}
