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

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations\Prefix,
    FOS\RestBundle\Controller\Annotations\NamePrefix,
    FOS\RestBundle\Controller\Annotations\View,
    FOS\RestBundle\View\RouteRedirectView,
    FOS\RestBundle\View\View AS FOSView;

use UJM\ExoBundle\Entity\InteractionQCM;
use UJM\ExoBundle\Entity\Document;
use UJM\ExoBundle\Form\InteractionQCMType;
use UJM\ExoBundle\Form\InteractionQCMHandler;

/**
 * WSRest controller.
 * To create REST WS
 *
 */
class WSRestController extends Controller
{

    /**
     * WS test
     *
     */
    public function testTest_wsAction()
    {
	//la route GET test_ws/test sera générée automatiquement
        return 'ok';
    }

    /**
     * To add a document with the plugin advimage with tinyMCEBundle
     *
     */
    public function postDocumentAddAction()
    {
        //on poste les données label,url, type, login
        //le login permet de lier le doc à un utilisateur mais aussi de vérifier que le login correspond bien à l'utilisateur connecté.

        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            //var_dump($this->container->get('router'));die();
            $user_dir = './bundles/ujmexo/users_documents/'.$this->container->get('security.context')->getToken()->getUser()->getUsername();
            //echo $user_dir;die();
            
            if (!is_dir('./bundles/ujmexo/users_documents/'))
            {
                mkdir('./bundles/ujmexo/users_documents/');
            }
            
            if (!is_dir($user_dir))
            {
                $dirs = array('audio','images','media','video');
                mkdir($user_dir);
                foreach ($dirs as $dir)
                {
                    mkdir($user_dir.'/'.$dir);
                }
            }

            if((isset($_FILES['picture'])) && ($_FILES['picture'] != ''))
            {
                 $file = basename($_FILES['picture']['name']);
                 move_uploaded_file($_FILES['picture']['tmp_name'], $user_dir.'/images/'. $file);

                 $em = $this->getDoctrine()->getEntityManager();
                 $document = new Document();

                 $document->setLabel($_POST['label']);
                 $document->setUrl($user_dir.'/images/'. $file);
                 $document->setType(strrchr($file, '.'));
                 $document->setUser($this->container->get('security.context')->getToken()->getUser());

                 $em->persist($document);

                 $em->flush();
            }

            return $this->redirect($_SERVER["HTTP_REFERER"]);
        }
        else
        {
            return 'Not authorized';
        }
        

    }

    /**
     * Provide the form's elements to create a QCM
     *
     */
    public function postTest_wsQcmNewAction()
    {
        //post test_ws/qcm/new
	$request = $this->getRequest();
        $link = $request->get('link');
	$action = $request->get('action');
        $loadJQuery = $request->get('loadJQuery');

        $entity = new InteractionQCM();
        $form   = $this->createForm(new InteractionQCMType($this->container->get('security.context')->getToken()->getUser()), $entity);
       
        return $this->container->get('templating')->renderResponse('UJMExoBundle:InteractionQCM:rest_new.html.twig', array(
           'entity'     => $entity,
           'form'       => $form->createView(),
           'action'     => $action,
           'link'       => $link,
           'loadJQuery' => $loadJQuery
        )
        );


    }

    /**
     * To record a QCM
     *
     */
    public function postTest_wsQcmAddAction($exoID = -1)
    {
        //post test_ws/qcm/add

        $interQCM  = new InteractionQCM();
        $form    = $this->createForm(new InteractionQCMType($this->container->get('security.context')->getToken()->getUser()), $interQCM);

        $formHandler = new InteractionQCMHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager(), $this->container->get('security.context')->getToken()->getUser(), $exoID);

        if( $formHandler->processAdd() )
        {
            return $this->redirect($this->get('request')->get('link'));
        }

        return 'ko';
    }

    /**
     * Lists the User's Exercise entities.
     *
     */
    public function postTest_wsExerciseAction()
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

        return $this->render('UJMExoBundle:Exercise:rest_index.html.twig', array(
            'liste_subscriptions' => $liste_subscriptions)
        );
    }

    /**
     * Lists the User's Question entities.
     *
     */
    public function postTest_wsQuestionAction()
    {
        // On appelle le service security.context, les services sont gérés par un conteneur (container) et fonctionnent comme des singletons
        $user = $this->container->get('security.context')->getToken()->getUser();

        // Pouvoir récupérer facilement le login de l'utilisateur connecté est utile pour le plugin image de tinymce qui n'a oas accès aux méthodes symfony
        $php_session = $this->getRequest()->getSession();
        $php_session->set('ext_username', $user->getUsername());

        $uid = $user->getId();

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

        return $this->render('UJMExoBundle:Question:rest_index.html.twig', array(
            'interactions'         => $interactions,
            'questionWithResponse' => $questionWithResponse
        ));
    }
}
