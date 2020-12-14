<?php

namespace App\Conversations;

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use App\Conversations\AskCitizenship;

class AskForConsentConversation extends Conversation
{

    /**
     * First question
     */
    public function askForConsent()
    {

        $question = Question::create("Please consent to use the Self-Checker")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Yes l agree')->value('agreed'),
                Button::create('No l disagree')->value('disagreed'),
            ]);
        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {

           
                if ($answer->getValue() === 'agreed') {
                    $this->say("Thank you for that, let's begin");
                    $this->bot->startConversation(new AskCitizenship());
                } else {

                    $this->say('Your consent is required to use the Self-Checker');
                    $this->askForConsent();
                }
            }
        });
    }




    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askForConsent();
    }
}
