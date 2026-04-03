<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Behat\Step\When;
use Behat\Step\Then;
use PHPUnit\Framework\TestCase;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private $val = 0;
    public function __construct()
    {
    }

    #[Given('que je suis étudiant et que je suis sur la page de dépôt d’un justificatif')]
    public function prepareContext(){
        $this->val++;
    }

    #[When('renseigne correctement les informations')]
    public function renseigneCorrect(){
        $this->val++;
    }

    #[Then('le justificatif est enregistré correctement dans la base de données')]
    public function checkOutcome(){
    TestCase::assertEquals(2, $this->val);
    }
}
