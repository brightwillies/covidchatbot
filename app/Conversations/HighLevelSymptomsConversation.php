<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class HighLevelSymptomsConversation extends Conversation
{

    public function __construct(string $testResponse)
    {

        $this->testResponse = $testResponse;
    }

    public function checkHighDegreeSymptoms()
    {

        $covidTestResponse = $this->testResponse;
        // $this->say($responseAnswer);
        $question = Question::create(" Do you (they) have any of the following? (Check any)")
            ->fallback('Unable to ask question')

            ->addButtons([
                Button::create('Fever or feeling feverish (such as chills, sweating)')->value('primary'),
                Button::create('Cough')->value('primary'),
                Button::create('Mild or moderate difficulty breathing')->value('primary'),
                Button::create('Sore throat')->value('secondary'),
                Button::create('Muscle aches or body aches')->value('secondary'),
                Button::create('Vomiting or diarrhea')->value('secondary'),
                Button::create('New loss of taste or smell')->value('secondary'),
                Button::create('Congestion or runny nose')->value('secondary'),
                Button::create('Other symptoms')->value('other_symptoms'),

            ]);

        $this->ask($question, function (Answer $answer) {


            if ($answer->isInteractiveMessageReply()) {
                $symptomTypeResponseAnswer = $answer->getValue();
                $this->say('ok');

                switch ($symptomTypeResponseAnswer) {
                    case 'primary':
                        $question = Question::create("Do you (they) live in a long-term care facility,
                                nursing home, or homeless shelter ?")
                            ->fallback('Unable to ask question')

                            ->addButtons([
                                Button::create('Yes')->value('yes'),
                                Button::create('No')->value('no'),
                            ]);

                        $this->ask($question, function (Answer $answer) {


                            if ($answer->isInteractiveMessageReply()) {
                                if ($answer->getValue() === 'yes') {
                                    //answered yes
                                    $covidTestResponse = $this->testResponse;
                                    switch ($covidTestResponse) {
                                        case 'positive':
                                            $this->say("Contact a medical provider in the care
                                        center, nursing home, or homeless shelter where
                                  you (they) live.");
                                            $this->say("f you (they) test positive for SARS-CoV-2
                            infection, stay home");
                                            $this->say("No further COVID-19 testing needed at this
                         time unless recommended by a provider");
                                            break;

                                        case 'negative':
                                            $this->say("Contact a medical provider in the care
                            center, nursing home, or homeless shelter where
                            you (they) live");
                                            $this->say("If you (they) test negative for SARS-CoV-
                        2 infection");
                                            $this->say("If you (they) have been in close contact
                        with someone with confirmed COVID-19");
                                            $this->say("Further COVID-19 testing may not be needed
                        at this time, unless recommended by a provider");
                                            break;
                                        case 'waiting':
                                            $this->say("Contact a medical provider in the care
                            center, nursing home, or homeless shelter where
                            you (they) live");
                                            $this->say("While waiting for your (their) results,
                            isolate at home:");
                                            $this->say("If you (they) have been in close contact
                            with someone with confirmed COVID-19");

                                            break;

                                        case 'no_test':
                                            $this->say("Contact a medical provider in the care
                            center, nursing home, or homeless shelter where
                            you (they) live.");
                                            $this->say("If you do not get tested, you (they)
                            should:  If you (they) have been in close contact
                            with someone with confirmed COVID-19");
                                            $this->say("Sounds like you (they) may have symptoms of
                            COVID-19. You (they) should get tested for COVID-
                            19");
                                            break;

                                        default:
                                            # code...
                                            break;
                                    }
                                } elseif ($answer->getValue() === 'no') {
                                    // answered no

                                    $question = Question::create(" In the last two weeks, have you (they) worked or
                                            volunteered in a healthcare facility or as a first
                                            responder? Healthcare facilities include a hospital,
                                            medical or dental clinic, long-term care facility, or
                                            nursing home")
                                        ->addButtons([
                                            Button::create('Yes')->value('yes'),
                                            Button::create('No')->value('no'),
                                        ]);

                                    $this->ask($question, function (Answer $answer) {

                                        if ($answer->isInteractiveMessageReply()) {
                                            if ($answer->getValue() === 'yes') {

                                                $covidTestResponse = $this->testResponse;
                                                // $this->say($covidTestResponse);

                                                switch ($covidTestResponse) {
                                                    case 'positive':
                                                        $this->say("Stay home (keep them home) and take
                                                        care of yourself (them). Call your (their) medical
                                                     provider if you (they) get worse");
                                                        $this->say("Contact the occupational health provider
                                                       at your workplace immediately");
                                                        $this->say("No further COVID-19 testing needed at this
                                                        time unless recommended by a provider");
                                                        break;
                                                    case 'negative':
                                                        $this->say("Stay home (keep them home) and take
                                                        care of yourself (them). Call your (their) medical
                                                     provider if you (they) get worse");
                                                        $this->say("Contact the occupational health provider
                                                       at your workplace immediately");
                                                        $this->say("If you (they) test negative for SARS-CoV-
                                                2 infection");
                                                        $this->say("If you (they) have been in close contact
                                             with someone with confirmed COVID-19");
                                                        $this->say("Further COVID-19 testing may not be needed
                                                 at this time, unless recommended by a provider");
                                                        break;
                                                    case 'waiting':
                                                        $this->say("Stay home (keep them home) and take
                                                care of yourself (them). Call your (their) medical
                                               provider if you (they) get worse");
                                                        $this->say("Contact the occupational health provider
                                              at your workplace immediately.");
                                                        $this->say("While waiting for your (their) results,
                                               isolate at home");
                                                        $this->say("If you (they) have been in close contact
                                                         with someone with confirmed COVID-19");
                                                        # code...
                                                        break;

                                                    case 'no_test':
                                                        $this->say("Stay home (keep them home) and take
                                                        care of yourself (them). Call your (their) medical
                                                     provider if you (they) get worse");
                                                        $this->say("Contact the occupational health provider
                                                       at your workplace immediately");
                                                        $this->say("<If you do not get tested, you (they)
                                                          should");
                                                        $this->say("If you (they) have been in close contact
                                                     with someone with confirmed COVID-19:");

                                                        $this->say("Sounds like you (they) may have symptoms of
                                                      COVID-19. You (they) should get tested for COVID-
                                                      19");
                                                        break;

                                                    default:
                                                        # code...
                                                        break;
                                                }
                                                // to switch statement
                                                // $this->say('print covid test');

                                            } else {
                                                //answered no
                                                $question = Question::create("Do any of these apply to you (them)? (check any)")
                                                    ->addButtons([
                                                        Button::create(' Chronic lung disease, such as moderate to severe asthma, COPD (chronic obstructive
                                                        pulmonary disease), cystic fibrosis, or pulmonary fibrosis')->value('yes'),
                                                        Button::create('  Serious heart condition, such as heart failure, cardiomyopathy, heart attack, or blocked
                                                        arteries to the heart')->value('yes'),
                                                        Button::create(' Weakened immune system or taking medications that may cause immune suppression')->value('yes'),
                                                        Button::create('Obesity')->value('yes'),
                                                        Button::create('Diabetes, chronic kidney disease, or liver disease')->value('yes'),
                                                        Button::create('High blood pressure')->value('yes'),
                                                        Button::create(' Cancer')->value('yes'),
                                                        Button::create('HIV')->value('yes'),
                                                        Button::create('Blood disorder, such as sickle cell disease or thalassemia')->value('yes'),
                                                        Button::create('Cerebrovascular disease or neurologic condition, such as stroke or dementia')->value('yes'),
                                                        Button::create('Smoking or vaping')->value('yes'),
                                                        Button::create('Pregnancy** If female/other gender is selected and age is ≥12 and <60 years, then
                                                         include question on pregnancy')->value('yes'),
                                                        Button::create('none')->value('no'),

                                                    ]);
                                                $this->ask($question, function (Answer $answer) {
                                                    if ($answer->isInteractiveMessageReply()) {
                                                        if ($answer->getValue() === 'yes') {
                                                            $covidTestResponse = $this->testResponse;
                                                            switch ($covidTestResponse) {
                                                                case 'positive':
                                                                    $this->say("Call a medical provider.");
                                                                    $this->say("If you (they) test positive for SARS-CoV-2
                                                                infection, stay home.");
                                                                    $this->say("No further COVID-19 testing needed at this
                                                                   time unless recommended by a provider");

                                                                    break;
                                                                case 'negative':
                                                                    $this->say("Call a medical provider.");
                                                                    $this->say("If you (they) test positive for SARS-CoV-2
                                                                infection, stay home.");
                                                                    $this->say("Further COVID-19 testing may not be needed
                                                          at this time, unless recommended by a provider");


                                                                    break;
                                                                case 'pending':
                                                                    $this->say("Call a medical provider.");
                                                                    $this->say("While waiting for your (their) results,
                                                                isolate at home");
                                                                    $this->say("f you (they) have been in close contact
                                                                    with someone with confirmed COVID-19:");

                                                                    break;
                                                                case 'no_test':
                                                                    $this->say("Call a medical provider.");
                                                                    $this->say("If you do not get tested, you (they)
                                                                    shou");
                                                                    $this->say("If you (they) have been in close contact
                                                                    with someone with confirmed COVID-19");
                                                                    $this->say("Sounds like you (they) may have symptoms of
                                                             COVID-19. You (they) should get tested for COVID-
                                                                        19");


                                                                    break;

                                                                default:
                                                                    # code...
                                                                    break;
                                                            }
                                                        } elseif ($answer->getValue === 'none') {

                                                            $covidTestResponse = $this->testResponse;
                                                            switch ($covidTestResponse) {
                                                                case 'positive':
                                                                    $this->say("Call a medical provider.");
                                                                    $this->say("If you (they) test positive for SARS-CoV-2
                                                                infection, stay home.");
                                                                    $this->say("No further COVID-19 testing needed at this
                                                                   time unless recommended by a provider");

                                                                    break;
                                                                case 'negative':
                                                                    $this->say("Call a medical provider.");
                                                                    $this->say("If you (they) test positive for SARS-CoV-2
                                                                infection, stay home.");
                                                                    $this->say("Further COVID-19 testing may not be needed
                                                          at this time, unless recommended by a provider");


                                                                    break;
                                                                case 'pending':
                                                                    $this->say("Call a medical provider.");
                                                                    $this->say("While waiting for your (their) results,
                                                                isolate at home");
                                                                    $this->say("f you (they) have been in close contact
                                                                    with someone with confirmed COVID-19:");

                                                                    break;
                                                                case 'no_test':
                                                                    $this->say("Call a medical provider.");
                                                                    $this->say("If you do not get tested, you (they)
                                                                    shou");
                                                                    $this->say("If you (they) have been in close contact
                                                                    with someone with confirmed COVID-19");
                                                                    $this->say("Sounds like you (they) may have symptoms of
                                                             COVID-19. You (they) should get tested for COVID-
                                                                        19");


                                                                    break;

                                                                default:
                                                                    # code...
                                                                    break;
                                                            }
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                    });
                                }
                            }
                        });
                        break;

                    case 'secondary':

                        $question = Question::create(" Do you (they) live in a long-term care facility,
                            nursing home, or homeless shelter?
                        ")->addButtons([
                            Button::create('yes')->value('yes'),
                            Button::create('no')->value('no'),
                        ]);
                        $this->ask($question, function (Answer $answer) {
                            if ($answer->isInteractiveMessageReply()) {
                                if ($answer->getValue() === 'yes') {

                                    $covidTestResponse = $this->testResponse;
                                    switch ($covidTestResponse) {
                                        case 'positive':
                                            $this->say("Contact a medical provider in the care
                            center, nursing home, or homeless shelter where
                            you (they) live");
                                            $this->say("If you (they) test positive for SARS-CoV-2
                            infection, stay home");
                                            $this->say("No further COVID-19 testing needed at this
                                    time unless recommended by a provider");

                                            break;
                                        case 'negative':
                                            $this->say("Contact a medical provider in the care
                                    center, nursing home, or homeless shelter where
                                        you (they) live");
                                            $this->say("If you (they) test negative for SARS-CoV-
                                                2 infection");
                                            $this->say("If you (they) have been in close contact
                                                with someone with confirmed COVID-19");
                                            $this->say("Further COVID-19 testing may not be needed
                                            at this time, unless recommended by a provider");

                                            break;
                                        case 'pending':
                                            $this->say("Contact a medical provider in the care
                                        center, nursing home, or homeless shelter where
                                        you (they) live.");
                                            $this->say("While waiting for your (their) results,
                                        isolate at home");

                                            $this->say("If you (they) have been in close contact
                                with someone with confirmed COVID-19");

                                            break;
                                        case 'no_test':
                                            $this->say("Contact a medical provider in the care
                               center, nursing home, or homeless shelter where
                              you (they) live");
                                            $this->say("If you do not get tested, you (they)
                                                should");


                                            $this->say("If you (they) have been in close contact
                                                        with someone with confirmed COVID-19");
                                            $this->say("ounds like you (they) may have symptoms of
                                        COVID-19. You (they) should get tested for COVID-
                                        19");
                                            break;

                                        default:
                                            # code...
                                            break;
                                    }
                                } else {
                                    //answered no
                                    $question = Question::create("n the last two weeks, have you (they) worked or
volunteered in a healthcare facility or as a first
responder? Healthcare facilities include a hospital,
medical or dental clinic, long-term care facility, or
nursing home.")->addButtons([
                                        Button::create('yes')->value('yes'),
                                        Button::create('no')->value('no'),
                                    ]);
                                    $this->ask($question, function (Answer $answer) {
                                        if ($answer->isInteractiveMessageReply()) {
                                            if ($answer->getValue() === 'yes') {
                                                $this->say('No ');
                                            } else {
                                                $this->say('Yes ');
                                            }
                                        }
                                    });
                                }
                            }
                        });

                        break;

                    case 'other_symptoms':
                        $this->say(" Sorry you are (or your child is) feeling sick.
                            Stay home (or Keep your child home) and monitor
                            your (or your child’s) symptoms. Call your (or your
                          child’s) medical provider if you get worse");
                        break;

                    default:
                        # code...
                        break;
                }
            }
        });
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        //
        $this->checkHighDegreeSymptoms();
    }
}
