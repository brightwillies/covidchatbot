<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class AskCitizenship extends Conversation
{

    public function askCitizenship()
    {

        $question = Question::create("Are you in the United States
         or a U.S. territory right now?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Yes')->value('yes'),
                Button::create('No')->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'yes') {
                    $this->ask('Where in the United States or in which U.S. territory are you currently located?', function (Answer $answer) {
                        $this->firstname = $answer->getText();
                        $this->say('Nice to meet you American');
                        $this->askIfOnBehalf();
                    });
                } else {

                    $this->say('Please check with your Ministry of Health or local health department for additional information and guidelines about COVID-19 in your location.');
                    $this->askIfOnBehalf();
                }
            }
        });
    }

    public function askIfOnBehalf()
    {
        $question = Question::create("Are you answering for
            yourself or someone else?")->addButtons([
            Button::create('Myself')->value('yes'),
            Button::create('Someone')->value('no'),
        ]);
        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'yes') {
                    $this->say('Yes');
                    $this->askOfAge(' your');
                } else {
                    $this->askOfAge("the person's");
                }
            }
        });
    }
    public function askOfAge(string $person)
    {

        $question = Question::create("what  is" . $person . " age ?")->addButtons([
            Button::create('2-4 years')->value('1'),
            Button::create('5-9')->value('9'),
            Button::create('10-12')->value('10'),
            Button::create('13-17')->value('13'),
            Button::create('18-29')->value('18'),
            Button::create('30-39')->value('30'),
            Button::create('40-49')->value('40'),
            Button::create('50-59')->value('50'),
            Button::create('60-69')->value('60'),
            Button::create('70-79')->value('70'),
            Button::create('80+')->value('80'),
        ]);
        $this->ask($question, function (Answer $answer, $person) {
            if ($person == 'your') {
                $newPerson = 'your';
            } else {
                $newPerson = "the person's";
            }
            if ($answer->isInteractiveMessageReply()) {

                $this->askGender($newPerson);
                // if ($answer->getValue() === 1) {
                //     $this->askGender($person);
                // } else {
                //     $this->askGender($person);
                // }
            }
        });
    }
    /**
     * Start the conversation.
     *
     * @return mixed
     */


    public function askGender(string $person)
    {
        $question = Question::create("what  is" . $person . " gender ?")->addButtons([
            Button::create('male')->value('male'),
            Button::create('female')->value('female'),
            Button::create('other')->value('other'),
        ]);
        $this->ask(
            $question,
            function (Answer $answer) {
                if ($answer->isInteractiveMessageReply()) {
                    $this->askSymptomsStatus();
                }
            }
        );
    }


    public function askSymptomsStatus()
    {
        $question = Question::create("Do you (they) have any of these life-threatening symptoms? \n
• Bluish lips or face \n
• Severe and constant pain or pressure in the chest\n
• Extreme difficulty breathing (such as gasping for air,\n
 being unable to talk without catching your (their) breath, \n
  severe wheezing, nostrils flaring)\n
• New disorientation (acting confused)\n
• Unconscious or very difficult to wake up\n
• Slurred speech or difficulty speaking (new or worsening)\n
• New or worsening seizures\n
• Signs of low blood pressure (too weak to stand, dizziness,\n
lightheaded, feeling cold, pale, clammy skin)\n
• Dehydration (dry lips and mouth, not urinating much, sunken\n
eyes)")->addButtons([
            Button::create('yes')->value('yes'),
            Button::create('no')->value('no'),
        ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'yes') {
                    $this->say('Urgent medical attention may be needed. Please call 911 or go to the Emergency
                     Department');
                } else {
                    $this->areYouFeelingSick();
                }
            }
        });
    }

    public function areYouFeelingSick()
    {
        $question = Question::create(" Are you (they) feeling sick?")->addButtons([
            Button::create('yes')->value('yes'),
            Button::create('no')->value('no'),
        ]);
        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'yes') {
                    $this->askCovidSymptoms();
                } else {
                    $this->say('Go to Adult - Asymptomatic');
                }
            }
        });
    }

    public function askCovidSymptoms()
    {
        $question = Question::create("In the two weeks before you (they) felt sick, did you (they) care
for or have close contact (within 6 feet of an infected person for a
cumulative total of 15 minutes in a 24-hour period) with someone
with symptoms of COVID-19, tested for COVID-19, or diagnosed
with COVID-19?

")->addButtons([
            Button::create('yes')->value('yes'),
            Button::create('no')->value('no'),
        ]);
        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->askQuestion31();
            }
        });
    }

    public function askQuestion31()
    {
        $question = Question::create(". In the last 10 days, have you (they) tested
       positive for coronavirus?")->addButtons([
            Button::create('Yes, tested positive')->value('positive'),
            Button::create('No, tested negative')->value('negative'),
            Button::create('No, waiting for results')->value('waiting'),
            Button::create('No, not tested')->value('no_test'),
        ]);
        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $testStatus = $answer->getValue();
                if (
                    $answer->getValue() === 'positive' ||
                    $answer->getValue() === 'waiting'
                ) {
                    $this->bot->startConversation(new HighLevelSymptomsConversation($testStatus));
                } else {
                    $this->askOfAge("move to no new set");
                }
            }
        });
    }
    public function run()
    {
        //
        $this->askCitizenship();
    }
}
