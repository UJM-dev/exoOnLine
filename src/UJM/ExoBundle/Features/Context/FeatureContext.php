<?php

/**
 * ExoOnLine
 * Copyright or © or Copr. Université Jean Monnet (France), 2012
 * dsi.dev@univ-st-etienne.fr
 *
 * This software is a computer program whose purpose is to [describe
 * functionalities and technical features of your software].
 *
 * This software is governed by the CeCILL license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
*/

namespace UJM\ExoBundle\Features\Context;

use Behat\BehatBundle\Context\BehatContext,
    Behat\BehatBundle\Context\MinkContext;
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
   require_once 'PHPUnit/Autoload.php';
   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Feature context.
 */
class FeatureContext extends MinkContext 
{
    
    private $page;
    private $question_title = "question de test 15";
    private $hint1 = "indice1";
    private $penalite_hint1 = 1;
    private $score = 3;
    private $score2 = 0;
    private $choice1 = "ch1";
    private $choice2 = "ch2";
    private $choice3 = "ch3";

    /**
      * @Given /^Given I am authenticated$/
      */
    public function givenIAmAuthenticated()
    {
        $this->page = $this->getSession()->getPage();
        $username_field = $this->page->findById('username');
        $password_field = $this->page->findField('password');
        $button_submit = $this->page->findById('_submit');
        $username_field->setValue('toto');
        $password_field->setValue('toto');
        $button_submit->press();
    }

    /**
      * @Given /^Given I createQuestion$/
      */
    public function givenICreateQuestion()
    {
        $link_newQ = $this->page->findLink('Create a new entry');
        $link_newQ->click();
    }

    /**
      * @Given /^Given I verifyTable$/
      */
    public function givenIverifyTable()
    {             
        
        //click sur ajouter_hint et vérifier le nombre de choix        
        $this->page->findLink('add_hint')->click();
        
        //vérifier l'existence de la table hint
        assertTrue($this->page->hasTable('newTable2'));
        assertTrue($this->page->hasField('ujm_exobundle_interactionqcmtype_interaction_hints_0_value'));            
        
        //vérifier l'existence de la table choices
        assertTrue($this->page->hasTable('newTable'));                       
        
        //click sur ajouter_choix et vérifier le nombre de choix
        $this->page->findLink('add_choice')->click();
        assertTrue($this->page->hasField('ujm_exobundle_interactionqcmtype_choices_0_label'));
        assertTrue($this->page->hasField('ujm_exobundle_interactionqcmtype_choices_1_label'));
        assertTrue($this->page->hasField('ujm_exobundle_interactionqcmtype_choices_2_label'));
    }
    
        
    /**
      * @Given /^Given I createNewQ$/
      */
    public function givenIcreateNewQ()
    {
        //$this->page = $this->getSession()->getPage();
        $titleQ = $this->page->findById('ujm_exobundle_interactionqcmtype_interaction_question_title');
        $scoreRep = $this->page->findById('ujm_exobundle_interactionqcmtype_scoreRightResponse');
        $scoreBadRep = $this->page->findById('ujm_exobundle_interactionqcmtype_scoreRightResponse');
        $scoreBadRep = $this->page->findById('ujm_exobundle_interactionqcmtype_scoreFalseResponse');
        $choice1 = $this->page->findField('ujm_exobundle_interactionqcmtype_choices_0_label');
        $choice2 = $this->page->findField('ujm_exobundle_interactionqcmtype_choices_1_label');
        $choice3 = $this->page->findField('ujm_exobundle_interactionqcmtype_choices_2_label');
        $hint1 = $this->page->findField('ujm_exobundle_interactionqcmtype_interaction_hints_0_value');
        $hint1_penalite = $this->page->findField('ujm_exobundle_interactionqcmtype_interaction_hints_0_penality'); 
        $correctRep = $this->page->findField('ujm_exobundle_interactionqcmtype_choices_1_rightResponse');

        $titleQ->setValue($this->question_title);
        $this->getMink()->getSession()->getDriver()->executeScript("$(document).ready(function(){tinyMCE.execCommand('mceFocus',false,'ujm_exobundle_interactionqcmtype_interaction_invite');}) ");
        $this->getMink()->getSession()->getDriver()->executeScript("$(document).ready(function(){tinyMCE.activeEditor.setContent('uiouiouio');})");
        $hint1->setValue($this->hint1);
        $hint1_penalite->setValue($this->penalite_hint1); 
        $scoreRep->setValue($this->score);
        $scoreBadRep->setValue($this->score2);
        $choice1->setValue($this->choice1);
        $choice2->setValue($this->choice2);
        $choice3->setValue($this->choice3);
        
        $correctRep->check();       
        
        $button_submit = $this->page->findById('button_submit');
        $button_submit->click();
    } 

    /**
      * @Given /^Given I passeTheQ/
      */
    public function givenIpasseTheQ()
    {
        //$this->page = $this->getSession()->getPage();
        $pass_newQ = $this->page->findLink($this->question_title);
        $pass_newQ->click();
        
        $this->page->findField($this->choice2)->check();      
    }
    
    /**
      * @Given /^Given I verifyScore/
      */
    public function givenIverifyScore()
    {
        //$this->page = $this->getSession()->getPage();
        $this->page->findButton('submit_response')->click();
 
        assertEquals($this->page->findById('score')->getText(), $this->score."/".$this->score);
    } 
}