<?php

namespace UJM\ExoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use UJM\ExoBundle\Entity\WordResponse;
use UJM\ExoBundle\Form\WordResponseType;

/**
 * WordResponse controller.
 *
 */
class WordResponseController extends Controller
{
    /**
     * Lists all WordResponse entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('UJMExoBundle:WordResponse')->findAll();

        return $this->render('UJMExoBundle:WordResponse:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a WordResponse entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UJMExoBundle:WordResponse')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WordResponse entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UJMExoBundle:WordResponse:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new WordResponse entity.
     *
     */
    public function newAction()
    {
        $entity = new WordResponse();
        $form   = $this->createForm(new WordResponseType(), $entity);

        return $this->render('UJMExoBundle:WordResponse:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new WordResponse entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new WordResponse();
        $form = $this->createForm(new WordResponseType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('wordresponse_show', array('id' => $entity->getId())));
        }

        return $this->render('UJMExoBundle:WordResponse:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing WordResponse entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UJMExoBundle:WordResponse')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WordResponse entity.');
        }

        $editForm = $this->createForm(new WordResponseType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UJMExoBundle:WordResponse:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing WordResponse entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UJMExoBundle:WordResponse')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WordResponse entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new WordResponseType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('wordresponse_edit', array('id' => $id)));
        }

        return $this->render('UJMExoBundle:WordResponse:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a WordResponse entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UJMExoBundle:WordResponse')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find WordResponse entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('wordresponse'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
