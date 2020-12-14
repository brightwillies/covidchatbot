<?php

use App\Http\Controllers\BotManController;
use App\Conversations\AskForConsentConversation;
// use BotMan\BotMan\Messages\Incoming\Answer;
// use BotMan\BotMan\Messages\Outgoing\Question;
// use BotMan\BotMan\Messages\Outgoing\Actions\Button;
// use BotMan\BotMan\Messages\Conversations\Conversation;

$botman = resolve('botman');



$botman->hears('hello', function ($bot) {
    $bot->reply('Great to have me assist you.');
    $bot->startConversation(new  AskForConsentConversation());
});
$botman->hears('hi', function ($bot) {
    $bot->reply('Great to have me assist you.');
    $bot->startConversation(new  AskForConsentConversation());
});




$botman->fallback(function ($bot) {
    $bot->reply('Sorry, I did not understand');
});
$botman->hears('Start conversation', BotManController::class . '@startConversation');
