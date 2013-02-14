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

use UJM\ExoBundle\Entity\InteractionGraphic;
use UJM\ExoBundle\Form\InteractionGraphicType;

/**
 * InteractionGraphic controller.
 *
 */
class InteractionGraphicController extends Controller
{
    /**
     * Lists all InteractionGraphic entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('UJMExoBundle:InteractionGraphic')->findAll();

        return $this->render('UJMExoBundle:InteractionGraphic:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a InteractionGraphic entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('UJMExoBundle:InteractionGraphic')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InteractionGraphic entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UJMExoBundle:InteractionGraphic:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new InteractionGraphic entity.
     *
     */
    public function newAction()
    {
        $entity = new InteractionGraphic();
        $form   = $this->createForm(new InteractionGraphicType(), $entity);

        return $this->render('UJMExoBundle:InteractionGraphic:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new InteractionGraphic entity.
     *
     */
    public function createAction()
    {
        $user=$this->container->get('security.context')->getToken()->getUser();
        
        $interGraph  = new InteractionGraphic();
        $request = $this->getRequest();
        $form    = $this->createForm(new InteractionGraphicType($user), $interGraph);
        $form->bindRequest($request);

        $interGraph->getInteraction()->getQuestion()->setDateCreate(new \Datetime());
        $interGraph->getInteraction()->getQuestion()->setUser($user);
        $interGraph->getInteraction()->setType('InteractionGraphic');
        
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($interGraph);
            $em->persist($interGraph->getInteraction()->getQuestion());
            $em->persist($interGraph->getInteraction());
            $em->flush();

            return $this->redirect($this->generateUrl('question'));
            
        }

        return $this->render('UJMExoBundle:InteractionGraphic:new.html.twig', array(
            'interGraph' => $interGraph,
            'form'   => $form->createView()
        ));
    }
 
    /**
     * Displays a form to edit an existing InteractionGraphic entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('UJMExoBundle:InteractionGraphic')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InteractionGraphic entity.');
        }

        $editForm = $this->createForm(new InteractionGraphicType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UJMExoBundle:InteractionGraphic:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing InteractionGraphic entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('UJMExoBundle:InteractionGraphic')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InteractionGraphic entity.');
        }

        $editForm   = $this->createForm(new InteractionGraphicType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('interactiongraphic_edit', array('id' => $id)));
        }

        return $this->render('UJMExoBundle:InteractionGraphic:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a InteractionGraphic entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('UJMExoBundle:InteractionGraphic')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InteractionGraphic entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('interactiongraphic'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
