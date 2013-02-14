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
use Symfony\Component\Form\FormBuilder;

use UJM\ExoBundle\Entity\Question;
use UJM\ExoBundle\Form\QuestionType;

use UJM\ExoBundle\Entity\InteractionQCM;
use UJM\ExoBundle\Form\InteractionQCMType;

use UJM\ExoBundle\Entity\InteractionGraphic;
use UJM\ExoBundle\Form\InteractionGraphicType;

use UJM\ExoBundle\Entity\InteractionOpen;
use UJM\ExoBundle\Form\InteractionOpenType;

use UJM\ExoBundle\Entity\InteractionHole;
use UJM\ExoBundle\Form\InteractionHoleType;

use UJM\ExoBundle\Entity\Interaction;

use UJM\ExoBundle\Entity\Response;
use UJM\ExoBundle\Form\ResponseType;



/**
 * Question controller.
 *
 */
class QuestionController extends Controller
{
    /**
     * Lists the User's Question entities.
     *
     */
    public function indexAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $uid = $user->getId();

        /*$questions = $this->getDoctrine()
                               ->getEntityManager()
                               ->getRepository('UJMExoBundle:Question')
                               ->getQuestionsUser($uid);*/
        $interactions = $this->getDoctrine()
                               ->getEntityManager()
                               ->getRepository('UJMExoBundle:Interaction')
                               ->getUserInteraction($uid);

        $questionWithResponse = array();
        $em = $this->getDoctrine()->getEntityManager();
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

        //var_dump($questionWithResponse);

        return $this->render('UJMExoBundle:Question:index.html.twig', array(
            'interactions'         => $interactions,
            'questionWithResponse' => $questionWithResponse
        ));
    }

    /**
     * Finds and displays a Question entity.
     *
     */
    public function showAction($id)
    {
        $question = $this->controlUserQuestion($id);

        if(count($question) > 0)
        {
            $interaction = $this->getDoctrine()
                                   ->getEntityManager()
                                   ->getRepository('UJMExoBundle:Interaction')
                                   ->getInteraction($id);

            $type_inter = $interaction[0]->getType();

            switch ($type_inter)
            {
                case "InteractionQCM":

                    $response = new Response();
                    $interactionQCM = $this->getDoctrine()
                                   ->getEntityManager()
                                   ->getRepository('UJMExoBundle:InteractionQCM')
                                   ->getInteractionQCM($interaction[0]->getId());

                    if($interactionQCM[0]->getShuffle())
                    {
                        $interactionQCM[0]->shuffleChoices();
                    }
                    else
                    {
                        $interactionQCM[0]->sortChoices();
                    }

                    $form   = $this->createForm(new ResponseType(), $response);

                    return $this->render('UJMExoBundle:InteractionQCM:paper.html.twig', array('interactionQCM' => $interactionQCM[0], 'form'   => $form->createView()));

                    break;

                case "InteractionGraphic":

                    break;

                case "InteractionHole":

                    break;

                case "InteractionOpen":

                    break;
            }
        }
        else
        {
            return $this->redirect($this->generateUrl('question'));
        }

    }

    /**
     * Displays a form to create a new Question entity.
     *
     */
    public function newAction()
    {
        $entity = new Question();
        $form   = $this->createForm(new QuestionType(), $entity);

        return $this->render('UJMExoBundle:Question:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to create a new Question entity with interaction.
     *
     */
    public function newwithqiAction($exoID)
    {
        return $this->render('UJMExoBundle:Question:new_QI.html.twig', array('exoID' => $exoID));
    }

    /**
     * Creates a new Question entity.
     *
     */
    public function createAction()
    {
        $entity  = new Question();
        $request = $this->getRequest();
        $form    = $this->createForm(new QuestionType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('question_show', array('id' => $entity->getId())));
            
        }

        return $this->render('UJMExoBundle:Question:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Question entity.
     *
     */
    public function editAction($id)
    {
        $question = $this->controlUserQuestion($id);

        if(count($question) > 0)
        {
            $interaction = $this->getDoctrine()
                                   ->getEntityManager()
                                   ->getRepository('UJMExoBundle:Interaction')
                                   ->getInteraction($id);

            $type_inter = $interaction[0]->getType();

            $nbResponses = 0;
            $em = $this->getDoctrine()->getEntityManager();
            $response = $em->getRepository('UJMExoBundle:Response')->findBy(array('interaction' => $interaction[0]->getId()));
            $nbResponses = count($response);

            switch ($type_inter)
            {
                case "InteractionQCM":

                    $interactionQCM = $this->getDoctrine()
                                           ->getEntityManager()
                                           ->getRepository('UJMExoBundle:InteractionQCM')
                                           ->getInteractionQCM($interaction[0]->getId());
                    //apel fonction qui trie
                    $interactionQCM[0]->sortChoices();

                    $editForm = $this->createForm(new InteractionQCMType($this->container->get('security.context')->getToken()->getUser()), $interactionQCM[0]);
                    $deleteForm = $this->createDeleteForm($interactionQCM[0]->getId());

                    return $this->render('UJMExoBundle:InteractionQCM:edit.html.twig', array(
                        'entity'      => $interactionQCM[0],
                        'edit_form'   => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                        'nbResponses' => $nbResponses
                    ));
                    break;

                case "InteractionGraphic":

                    break;

                case "InteractionHole":
                    $interactionHole = $this->getDoctrine()
                                            ->getEntityManager()
                                            ->getRepository('UJMExoBundle:InteractionHole')
                                            ->getInteractionHole($interaction[0]->getId());
                    
                    $editForm = $this->createForm(new InteractionHoleType($this->container->get('security.context')->getToken()->getUser()), $interactionHole[0]);
                    $deleteForm = $this->createDeleteForm($interactionHole[0]->getId());
                    
                    return $this->render('UJMExoBundle:InteractionHole:edit.html.twig', array(
                        'entity'      => $interactionHole[0],
                        'edit_form'   => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                        'nbResponses' => $nbResponses
                    ));

                    break;

                case "InteractionOpen":

                    break;
            }
        }
        else
        {
            return $this->redirect($this->generateUrl('question'));
        }
        
    }

    /**
     * Edits an existing Question entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('UJMExoBundle:Question')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Question entity.');
        }

        $editForm   = $this->createForm(new QuestionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('question_edit', array('id' => $id)));
        }

        return $this->render('UJMExoBundle:Question:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Question entity.
     *
     */
    public function deleteAction($id)
    {
        $question = $this->controlUserQuestion($id);

        if(count($question) > 0)
        {
            $em = $this->getDoctrine()->getEntityManager();

            $eq = $this->getDoctrine()
                       ->getEntityManager()
                       ->getRepository('UJMExoBundle:ExerciseQuestion')
                       ->getExercises($id);

            foreach($eq as $e)
            {
                $em->remove($e);
            }

            $em->flush();
   
            $interaction = $this->getDoctrine()
                                ->getEntityManager()
                                ->getRepository('UJMExoBundle:Interaction')
                                ->getInteraction($id);

            $type_inter = $interaction[0]->getType();

            switch ($type_inter)
            {
                case "InteractionQCM":
                    $interactionQCM = $this->getDoctrine()
                                           ->getEntityManager()
                                           ->getRepository('UJMExoBundle:InteractionQCM')
                                           ->getInteractionQCM($interaction[0]->getId());
                    return $this->forward('UJMExoBundle:InteractionQCM:delete', array('id' => $interactionQCM[0]->getId()));

                    break;

                case "InteractionGraphic":

                    break;

                case "InteractionHole":
                    $interactionHole = $this->getDoctrine()
                                           ->getEntityManager()
                                           ->getRepository('UJMExoBundle:InteractionHole')
                                           ->getInteractionHole($interaction[0]->getId());
                    return $this->forward('UJMExoBundle:InteractionHole:delete', array('id' => $interactionHole[0]->getId()));
                    
                    break;

                case "InteractionOpen":

                    break;
           }

        }

    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Displays the rigth form when a teatcher wants to create a new Question (JS)
     *
     */
    public function choixFormTypeAction()
    {

        $request = $this->container->get('request');

        if($request->isXmlHttpRequest())
        {
            $val_type = 0;

            $val_type = $request->request->get('indice_type');
            $exoID = $request->request->get('exercise');

            if($val_type != 0)
            {
                //index 1=Hole Question
                if($val_type == 1)
                {
                    $entity = new InteractionHole();
                    $form   = $this->createForm(new InteractionHoleType($this->container->get('security.context')->getToken()->getUser()), $entity);
                    return $this->container->get('templating')->renderResponse('UJMExoBundle:InteractionHole:new.html.twig', array(
                        'exoID'  => $exoID,
                        'entity' => $entity,
                        'form'   => $form->createView()
                    )
                    );
                }                
                
                //index 1=QCM Question
                if($val_type == 2)
                {
                    $entity = new InteractionQCM();
                    $form   = $this->createForm(new InteractionQCMType($this->container->get('security.context')->getToken()->getUser()), $entity);
                    return $this->container->get('templating')->renderResponse('UJMExoBundle:InteractionQCM:new.html.twig', array(
                        'exoID'  => $exoID,
                        'entity' => $entity,
                        'form'   => $form->createView()
                    )
                    );
                }                
                
                //index 1=Graphic Question
                if($val_type == 3)
                {
                    $entity = new InteractionGraphic();
                    $form   = $this->createForm(new InteractionGraphicType($this->container->get('security.context')->getToken()->getUser()), $entity);
                    return $this->container->get('templating')->renderResponse('UJMExoBundle:InteractionGraphic:new.html.twig', array(
                        'exoID'  => $exoID,
                        'entity' => $entity,
                        'form'   => $form->createView()
                    )
                    );
                }
                
                //index 1=Open Question
                if($val_type == 4)
                {
                    $entity = new InteractionOpen();
                    $form   = $this->createForm(new InteractionOpenType($this->container->get('security.context')->getToken()->getUser()), $entity);
                    return $this->container->get('templating')->renderResponse('UJMExoBundle:InteractionOpen:new.html.twig', array(
                        'exoID'  => $exoID,
                        'entity' => $entity,
                        'form'   => $form->createView()
                    )
                    );
                }                
                
            }
            else
            {
                
            }          
        } 
          
    }

    /**
     * To control the User's rights to this Question
     *
     */
    private function controlUserQuestion($questionID)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $question = $this->getDoctrine()
                      ->getEntityManager()
                      ->getRepository('UJMExoBundle:Question')
                      ->getControlOwnerQuestion($user->getId(), $questionID);

        return $question;
    }
    
}
