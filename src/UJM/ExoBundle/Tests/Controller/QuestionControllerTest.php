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

namespace UJM\ExoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuestionControllerTest extends WebTestCase
{
    /*
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/question/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'question[field_name]'  => 'Test',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertTrue($crawler->filter('td:contains("Test")')->count() > 0);

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Edit')->form(array(
            'question[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertTrue($crawler->filter('[value="Foo"]')->count() > 0);

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }
    */
    
    public function testNewwithqiAction()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'miloudb',
            'PHP_AUTH_PW'   => 'toto',
        ));

        $crawler = $client->request('GET', '/crud/question/newWithQI');

        //$this->assertEquals(1, $crawler->filter('select')->count());
        //$client->getResponse()->getContent();
        //$this->assertEquals('Question creation', $blog->slugify('A Day With Symfony2'));
        //$this->assertEquals(1, $crawler->filter('h1:contains("Question creation")')->count());
        
        
        //$this->assertTrue(1 === 1);
        //$this->assertTrue(1 === 2);
        //$this->assertEquals(6, $crawler->filter('img')->count());
        //$this->assertEquals(1, $crawler->filter('h1:contains("Question creation)')->count());
        
        //Il y a un H1 qui contient "Question creation"
        $this->assertEquals(1, $crawler->filter('h1:contains("Question creation")')->count());
        
        //Il y a 1 select 
        $this->assertEquals(1, $crawler->filter('select')->count());
        
        //Il y a 5 option, qui sont "Blancs, QCM, Graphique, Ouverte",
        $this->assertEquals(5, $crawler->filter('select option')->count());
        
        //le 3eme élément des options est QCM
        $this->assertEquals('QCM', $crawler->filterXpath('//option[3]')->text());
        
        
        
        //séléction d'un type de question - à partir d'ici on traite que les nouveaux éléments bring back bu ajax
        
        $crawler = $client->request(
        'POST', 
        '/Question/choixFormType', 
        array(
            'val_type' => 'QCM'
            ), 
            array(), 
            array('HTTP_X-Requested-With' => 'XMLHttpRequest')
        );
        
        //$this->assertEquals(3, $crawler->filter('select')->count());
        //$this->assertEquals(1, $crawler->filter('h1:contains("Créer une nouvelle catégorie")')->count());
        
        /*$crawler = $client->request('GET', '/foo/', array(), array(), array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ));*/
        
        /*$this->assertEquals('QCM', $crawler->filterXpath('//div[1]')->text());
        
        //$this->assertTrue($crawler->filter('html:contains("ordre")')->count() == 0);
        //$this->assertEquals(5, $crawler->filter('ordre')->count());
        
        //$crawler->checkElement('input[id="ujm_exobundle_interactionqcmtype_interaction_question_title"][name=ujm_exobundle_interactionqcmtype[interaction][question][title]]', true);
        $this->assertEquals(1, $crawler->filter('form')->count());
        $this->assertEquals(7, $crawler->filter('input')->count());
        $this->assertEquals(7, $crawler->filter('table')->count());
        
*/
        
        //$this->assertEquals('QCM', $crawler->filterXpath('//div[@id]')->text());
        
        //select all div wich they have un id
        $this->assertEquals(13, $crawler->filterXpath('//div[@id]')->count());
        
        //select all div
        $this->assertEquals(28, $crawler->filter('div')->count());
        
        //$this->assertEquals('wsdf', $crawler->filterXpath('//div[7]/*')->text());
        
        //$this->assertEquals('1', $crawler->filterXpath('//select[1]/option[3]')->text());
        $this->assertEquals(2, $crawler->filterXpath('//select')->count());
        
        //$this->assertEquals(2, $crawler->filter('#ujm_exobundle_interactionqcmtype_interaction_question_title')->count());
        
        
        //remplir le formulaire et l'envoyer
        /*$form = $crawler->selectButton('button_submit')->form(array(
            '#title'  => 'question 50',
            '#ujm_exobundle_interactionqcmtype_interaction_invite' => 'what',
        ));*/
        
        $form = $crawler->selectButton('button_submit')->form();     
            $form['ujm_exobundle_interactionqcmtype[interaction][question][title]'] = 'question 50';
            $form['ujm_exobundle_interactionqcmtype[interaction][invite]'] = 'what';
            $form['ujm_exobundle_interactionqcmtype[choices][0][label]'] = 'xxxxx';
            $form['ujm_exobundle_interactionqcmtype[choices][0][rightResponse]']->tick();
            $form['ujm_exobundle_interactionqcmtype[choices][1][label]'] = 'zzzzz';
            $form['ujm_exobundle_interactionqcmtype[scoreRightResponse]'] = '2';  
        $crawler = $client->submit($form);
               
        
    }
    
}