<?php

require_once 'Blackjack.php';
require_once 'Card.php';
require_once 'Dealer.php';
require_once 'Deck.php';
require_once 'Player.php';

$dealer = new Dealer(new Blackjack(), new Deck());
# Asking the player how many people are playing, and what the player names are. 
# Adding those new players.
$playernum = intval(readline("How many players? "));
for ($i = 0; $i < $playernum; $i++) {
    $playername = readline("What's this player's name? ");
    $dealer->addPlayer($playername);
}
$dealer->playGame();
