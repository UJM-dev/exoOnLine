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

namespace UJM\ExoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use UJM\ExoBundle\Form\ExerciseType;
use UJM\ExoBundle\Form\ExerciseHandler;
use UJM\ExoBundle\Entity\Exercise;
use UJM\ExoBundle\Entity\ExerciseQuestion;
use UJM\ExoBundle\Entity\Paper;
use UJM\ExoBundle\Entity\Response;
use UJM\ExoBundle\Entity\Interaction;

/**
 * Exercise controller.
 *
 */
class ExerciseController extends Controller
{
    
    /**
     * Lists User's Exercise entities.
     *
     */
    public function indexAction()
    {
        // On appelle le service security.context, les services sont gérés par un conteneur (container) et fonctionnent comme des singletons
        $user = $this->container->get('security.context')->getToken()->getUser();

        // Pouvoir récupérer facilement le login de l'utilisateur connecté est utile pour le plugin image de tinymce qui n'a oas accès aux méthodes symfony
        $php_session = $this->getRequest()->getSession();
        $php_session->set('ext_username', $user->getUsername());
        
        $uid = $user->getId();

        $liste_subscriptions = $this->getDoctrine()
                               ->getEntityManager()
                               ->getRepository('UJMExoBundle:Subscription')
                               ->getExercisesUser($uid);

        return $this->render('UJMExoBundle:Exercise:index.html.twig', array(
            'liste_subscriptions' => $liste_subscriptions)
        );
    }

    /**
     * Finds and displays a Exercise entity if the User is enrolled.
     *
     */
    public function showAction($id)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $allowToCompose = 0;
        $subscription = $this->controlSubscription($id);
        
        if(count($subscription) > 0)
        {
            $em = $this->getDoctrine()->getEntityManager();

            $exercise = $em->getRepository('UJMExoBundle:Exercise')->find($id);

            if (!$exercise) {
                throw $this->createNotFoundException('Unable to find Exercise entity.');
            }

            $deleteForm = $this->createDeleteForm($id);

            if ( ($this->controlDate($subscription, $exercise) === true) && ($this->controlMaxAttemps($exercise, $user, $subscription) === true) )
            {
                $allowToCompose = 1;
            }

            return $this->render('UJMExoBundle:Exercise:show.html.twig', array(
                'entity'         => $exercise,
                'delete_form'    => $deleteForm->createView(),
                'subscription'   => $subscription[0],
                'allowToCompose' => $allowToCompose

            ));
        }
        else
        {
            return $this->redirect($this->generateUrl('exercise'));
        }
    }

    /**
     * Displays a form to create a new Exercise entity.
     *
     */
    public function newAction()
    {
        $entity = new Exercise();
        $entity->setNbQuestion(0);
        $entity->setDuration(0);
        $entity->setMaxAttemps(0);
        // \ pour instancier un objet du namespace global et non pas de l'actuel
        $entity->setStartDate(new \Datetime());
        $entity->setEndDate(new \Datetime());
        $entity->setDateCorrection(new \Datetime());
        
        $form   = $this->createForm(new ExerciseType(), $entity);

        return $this->render('UJMExoBundle:Exercise:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Exercise entity.
     *
     */
    public function createAction()
    {
        $exercise  = new Exercise();
        $form    = $this->createForm(new ExerciseType(), $exercise);

        $formHandler = new ExerciseHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager(), $this->container->get('security.context')->getToken()->getUser());

        if( $formHandler->process() )
        {

            return $this->redirect($this->generateUrl('exercise_show', array('id' => $exercise->getId())));
            
        }

        return $this->render('UJMExoBundle:Exercise:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Exercise entity.
     *
     */
    public function editAction($id)
    {
        $subscription = $this->controlSubscription($id);

        if((count($subscription) > 0) and ($subscription[0]->getAdmin() == 1))
        {
            $em = $this->getDoctrine()->getEntityManager();

            $entity = $em->getRepository('UJMExoBundle:Exercise')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Exercise entity.');
            }

            $editForm = $this->createForm(new ExerciseType(), $entity);
            $deleteForm = $this->createDeleteForm($id);

            return $this->render('UJMExoBundle:Exercise:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));
        }
        else
        {
            return $this->redirect($this->generateUrl('exercise'));
        }
    }

    /**
     * Edits an existing Exercise entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('UJMExoBundle:Exercise')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Exercise entity.');
        }

        $editForm   = $this->createForm(new ExerciseType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            //return $this->redirect($this->generateUrl('exercise_edit', array('id' => $id)));
            // on retourne vers la liste des exercices
            return $this->redirect($this->generateUrl('exercise'));
            
        }

        return $this->render('UJMExoBundle:Exercise:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Exercise entity.
     *
     */
    public function deleteAction($id)
    {
        $subscription = $this->controlSubscription($id);

        if((count($subscription) > 0) and ($subscription[0]->getAdmin() == 1))
        {
            $form = $this->createDeleteForm($id);
            $request = $this->getRequest();

            $form->bindRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $entity = $em->getRepository('UJMExoBundle:Exercise')->find($id);

                if (!$entity) {
                    throw $this->createNotFoundException('Unable to find Exercise entity.');
                }

                $subscriptions = $em->getRepository('UJMExoBundle:Subscription')->findBy(array('exercise' => $id));
                foreach($subscriptions as $sub)
                {
                    $em->remove($sub);
                }

                $eqs = $em->getRepository('UJMExoBundle:ExerciseQuestion')->findBy(array('exercise' => $id));
                foreach($eqs as $eq)
                {
                    $em->remove($eq);
                }

                $papers = $em->getRepository('UJMExoBundle:Paper')->findBy(array('exercise' => $id));
                foreach($papers as $paper)
                {

                    $lhps = $em->getRepository('UJMExoBundle:LinkHintPaper')->findBy(array('paper' => $paper->getId()));
                    foreach($lhps as $lhp)
                    {
                        $em->remove($lhp);
                    }

                    $responses = $em->getRepository('UJMExoBundle:Response')->findBy(array('paper' => $paper->getId()));
                    foreach($responses as $response)
                    {
                        $em->remove($response);
                    }

                    $em->remove($paper);
                }

                $em->remove($entity);
                $em->flush();
            }
        }
        return $this->redirect($this->generateUrl('exercise'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    

    /**
     * Finds and displays a Question entity to this Exercise.
     *
     */
    public function showQuestionsAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $subscription = $this->controlSubscription($id);

        if((count($subscription) > 0) and ($subscription[0]->getAdmin() == 1))
        {
            $interactions = $this->getDoctrine()
                                   ->getEntityManager()
                                   ->getRepository('UJMExoBundle:Interaction')
                                   ->getExerciseInteraction($em, $id, 0);

            $questionWithResponse = array();
            foreach($interactions as $interaction)
            {
                $response = $em->getRepository('UJMExoBundle:Response')->findBy(array('interaction' => $interaction->getId()));
                if (count($response) > 0)
                {
                    $questionWithResponse[] = 1;
                }
                else
                {
                    $questionWithResponse[] = 0;
                }
            }

            return $this->render('UJMExoBundle:Question:exerciseQuestion.html.twig', array(
                'interactions'         => $interactions,
                'exerciseID'           => $id,
                'questionWithResponse' => $questionWithResponse
            ));
        }
        else
        {
            return $this->redirect($this->generateUrl('exercise'));
        }
   }    

   /**
     *To import in this Exercise a Question of the User's bank.
     *
     */
    public function importQuestionAction($exoID)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $uid = $user->getId();

        $subscription = $this->controlSubscription($exoID);

        if((count($subscription) > 0) and ($subscription[0]->getAdmin() == 1))
        {
            /*$questions = $this->getDoctrine()
                                   ->getEntityManager()
                                   ->getRepository('UJMExoBundle:Question')
                                   ->getQuestionsUser($uid);*/
            $interactions = $this->getDoctrine()
                                   ->getEntityManager()
                                   ->getRepository('UJMExoBundle:Interaction')
                                   ->getUserInteractionImport($this->getDoctrine()->getEntityManager(), $uid, $exoID);

            return $this->render('UJMExoBundle:Question:import.html.twig', array(
                'interactions' => $interactions,
                'exoID'   => $exoID
            ));
        }
        else
        {
            return $this->redirect($this->generateUrl('exercise'));
        }
    }

    /**
     * To record the Question's import.
     *
     */
    public function importValidateAction($exoID, $qid)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $question = $this->getDoctrine()
                      ->getEntityManager()
                      ->getRepository('UJMExoBundle:Question')
                      ->getControlOwnerQuestion($user->getId(), $qid);

        if (count($question) >0 )
        {
            $em = $this->getDoctrine()->getEntityManager();

            $exo = $em->getRepository('UJMExoBundle:Exercise')->find($exoID);
            $question = $em->getRepository('UJMExoBundle:Question')->find($qid);

            $EQ = new ExerciseQuestion($exo, $question);

            $dql = 'SELECT max(eq.ordre) FROM UJM\ExoBundle\Entity\ExerciseQuestion eq '
                 . 'WHERE eq.exercise='.$exoID;
            $query = $em->createQuery($dql);
            $maxOrdre = $query->getResult();

            $EQ->setOrdre((int)$maxOrdre[0][1]+1);
            $em->persist($EQ);

            $em->flush();

            return $this->redirect($this->generateUrl('exercise_questions', array('id' => $exoID)));
        }
        else
        {
            return $this->redirect($this->generateUrl('exercise_import_question', array('exoID' => $exoID)));
        }
    }

    /**
     * Delete the Question of the exercise.
     *
     */
    public function deleteQuestionAction($exoID, $qid)
    {

        $subscription = $this->controlSubscription($exoID);

        if((count($subscription) > 0) and ($subscription[0]->getAdmin() == 1))
        {
            $em = $this->getDoctrine()->getEntityManager();
            $EQ = $em->getRepository('UJMExoBundle:ExerciseQuestion')->findOneBy(array('exercise' => $exoID, 'question' => $qid));
            $em->remove($EQ);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('exercise_questions', array('id' => $exoID)));
    }

    /**
     * To create a paper in order to take an assessment
     *
     */
    public function exercisePaperAction($id)
    {
        //$response = '';
        $user = $this->container->get('security.context')->getToken()->getUser();
        $uid = $user->getId();

        $subscription = $this->controlSubscription($id);
        $em = $this->getDoctrine()->getEntityManager();
        $exercise = $em->getRepository('UJMExoBundle:Exercise')->find($id);

        if ($this->controlDate($subscription, $exercise) === true)
        {
            $session = $this->getRequest()->getSession();
            $orderInter = '';
            $tabOrderInter = array();

            $dql = 'SELECT max(p.num_paper) FROM UJM\ExoBundle\Entity\Paper p '
                 . 'WHERE p.exercise='.$id.' AND p.user='.$uid;
            $query = $em->createQuery($dql);
            $maxNumPaper = $query->getResult();

            //Verify if it exists a not finished paper
            $paper = $this->getDoctrine()
                          ->getEntityManager()
                          ->getRepository('UJMExoBundle:Paper')
                          ->getPaper($user->getId(), $id);

            //if not exist a paper no finished
            if (count($paper) == 0)
            {
                if ($this->controlMaxAttemps($exercise, $user, $subscription) === false)
                {
                    return $this->redirect($this->generateUrl('exercise_show', array('id' => $id)));
                }
                
                $paper = new Paper();
                $paper->setNumPaper((int)$maxNumPaper[0][1]+1);
                $paper->setExercise($exercise);
                $paper->setUser($user);
                $paper->setStart(new \Datetime());
                $paper->setArchive(0);
                $paper->setInterupt(0);

                $interactions = $this->getDoctrine()
                                     ->getEntityManager()
                                     ->getRepository('UJMExoBundle:Interaction')
                                     ->getExerciseInteraction($this->getDoctrine()->getEntityManager(), $id, $exercise->getShuffle(), $exercise->getNbQuestion());


                foreach($interactions as $interaction)
                {
                    $orderInter = $orderInter.$interaction->getId().';';
                    $tabOrderInter[] = $interaction->getId();
                }

                $paper->setOrdreQuestion($orderInter);
                $em->persist($paper);
                $em->flush();
            }
            else
            {
                $paper = $paper[0];
                $tabOrderInter = explode(';',$paper->getOrdreQuestion());
                unset($tabOrderInter[count($tabOrderInter)-1]);
                $interactions[0] = $em->getRepository('UJMExoBundle:Interaction')->find($tabOrderInter[0]);
            }

            $session->set('tabOrderInter', $tabOrderInter);
            $session->set('paper', $paper->getId());
            $session->set('exerciseID', $id);

            $type_inter = $interactions[0]->getType();

            //To display selectioned question
            return $this->displayQuestion(1, $interactions[0], $type_inter, $exercise->getDispButtonInterrupt());
        }
        else
        {
            return $this->redirect($this->generateUrl('exercise_show', array('id' => $id)));
        }
    }

    /**
     * To navigate in the Questions of the assessment
     *
     */
    public function exercisePaperNavAction()
    {
        $response = '';
        $em = $this->getDoctrine()->getEntityManager();
        $session = $this->getRequest()->getSession();
        $request = $this->getRequest();
        $type_inter_to_recorded = $request->get('typeInteraction');

        $tabOrderInter = $session->get('tabOrderInter');
                
        //To record response
        $exerciseSer = $this->container->get('UJM_Exo.exerciseServices');
        $ip = $exerciseSer->getIP();
        $interactionToValidatedID = $request->get('interactionToValidated');
        $response = $this->getDoctrine()
                         ->getEntityManager()
                         ->getRepository('UJMExoBundle:Response')
                         ->getAlreadyResponded($session->get('paper'), $interactionToValidatedID);

        switch ($type_inter_to_recorded)
        {
            case "InteractionQCM":
                $res = $exerciseSer->responseQCM($request, $session->get('paper'));
                break;

            case "InteractionGraphic":

                break;

            case "InteractionHole":

                break;

            case "InteractionOpen":

                break;
        }

        if (count($response) == 0)
        {
            //INSERT Response
            $response = new Response();
            $response->setNbTries(1);
            $response->setPaper($em->getRepository('UJMExoBundle:Paper')->find($session->get('paper')));
            $response->setInteraction($em->getRepository('UJMExoBundle:Interaction')->find($interactionToValidatedID));//var_dump($em->getRepository('UJMExoBundle:Interaction')->find($interactionToValidatedID));die('ok');
        }
        else
        {
            //UPDATE Response
            $response = $response[0];
            $response->setNbTries($response->getNbTries() + 1);
        }

        $response->setIp($ip);
        $score = explode('/',$res['score']);
        $response->setMark($score[0]);
        $response->setResponse($res['response']);


        $em->persist($response);
        $em->flush();

        //To display selectioned question
        $numQuestionToDisplayed = $request->get('numQuestionToDisplayed');
        
        if($numQuestionToDisplayed == 'finish')
        {
            return $this->finishExercise();
        }
        elseif($numQuestionToDisplayed == 'interupt')
        {
            return $this->interuptExercise();
        }
        else
        {
            $interactionToDisplayedID = $tabOrderInter[$numQuestionToDisplayed - 1];
            $interactionToDisplay = $em->getRepository('UJMExoBundle:Interaction')->find($interactionToDisplayedID);
            $type_inter_to_displayed = $interactionToDisplay->getType();

            return $this->displayQuestion($numQuestionToDisplayed, $interactionToDisplay, $type_inter_to_displayed, $response->getPaper()->getExercise()->getDispButtonInterrupt());
        }

        
    }

    /**
     * Finds and displays the question selectionned by the User in an assesment
     *
     */
    private function displayQuestion($numQuestionToDisplayed, $interactionToDisplay, $type_inter_to_displayed, $dispButtonInterrupt)
    {
        $session = $this->getRequest()->getSession();
        $tabOrderInter = $session->get('tabOrderInter');
        
        switch ($type_inter_to_displayed)
        {
            case "InteractionQCM":

                $interactionToDisplayed = $this->getDoctrine()
                                               ->getEntityManager()
                                               ->getRepository('UJMExoBundle:InteractionQCM')
                                               ->getInteractionQCM($interactionToDisplay->getId());

                if($interactionToDisplayed[0]->getShuffle())
                {
                    $interactionToDisplayed[0]->shuffleChoices();
                }
                else
                {
                    $interactionToDisplayed[0]->sortChoices();
                }

                $responseGiven = $this->getDoctrine()
                             ->getEntityManager()
                             ->getRepository('UJMExoBundle:Response')
                             ->getAlreadyResponded($session->get('paper'), $interactionToDisplay->getId());
                
                if(count($responseGiven) > 0)
                {
                    $responseGiven = $responseGiven[0]->getResponse();
                }
                else
                {
                    $responseGiven = '';
                }

                break;

            case "InteractionGraphic":

                break;

            case "InteractionHole":

                break;

            case "InteractionOpen":

                break;
        }

        return $this->render('UJMExoBundle:Exercise:paper.html.twig', array(
            'tabOrderInter'          => $tabOrderInter,
            'interactionToDisplayed' => $interactionToDisplayed[0],
            'interactionType'        => $type_inter_to_displayed,
            'numQ'                   => $numQuestionToDisplayed,
            'paper'                  => $session->get('paper'),
            'response'               => $responseGiven,
            'dispButtonInterrupt'    => $dispButtonInterrupt
        ));
    }

    /**
     * To finish an assessment
     *
     */
    private function finishExercise()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $session = $this->getRequest()->getSession();
        
        $paper = $em->getRepository('UJMExoBundle:Paper')->find($session->get('paper'));
        $paper->setInterupt(0);
        $paper->setEnd(new \Datetime());
        $em->persist($paper);
        $em->flush();

        return $this->forward('UJMExoBundle:Paper:show', array('id' => $paper->getId()));
        
    }

    /**
     * To interupt an assessment
     *
     */
    private function interuptExercise()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $session = $this->getRequest()->getSession();

        $paper = $em->getRepository('UJMExoBundle:Paper')->find($session->get('paper'));
        $paper->setInterupt(1);
        $em->persist($paper);
        $em->flush();

        return $this->redirect($this->generateUrl('exercise_show', array('id' => $paper->getExercise()->getId())));

    }

    /**
     * To control the subscription
     *
     */
    private function controlSubscription($exoID)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $subscription = $this->getDoctrine()
                      ->getEntityManager()
                      ->getRepository('UJMExoBundle:Subscription')
                      ->getControlExerciseEnroll($user->getId(), $exoID);

        return $subscription;
    }

    /**
     * The user must be registered and (the dates must be good or the user must to be admin for the exercise)
     *
     */
    private function controlDate($subscription, $exercise)
    {
        if ( (count($subscription) > 0) && ( ( ($exercise->getStartDate()->format('Y-m-d H:i:s') <= date('Y-m-d H:i:s')) && ( ($exercise->getUseDateEnd() == 0) || ($exercise->getEndDate()->format('Y-m-d H:i:s') >= date('Y-m-d H:i:s')) ) ) || ($subscription[0]->getAdmin() == 1) ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * To control the max attemps
     *
     */
    private function controlMaxAttemps($exercise, $user, $subscription)
    {
        if ( ($subscription[0]->getAdmin() != 1) && ($exercise->getMaxAttemps() > 0) && ($exercise->getMaxAttemps() <= $this->container->get('UJM_Exo.exerciseServices')->getNbPaper($user->getId(), $exercise->getId())) )
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}
