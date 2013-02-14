<?php

namespace UJM\ExoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use UJM\ExoBundle\Entity\Hole;
use UJM\ExoBundle\Form\HoleType;

/**
 * Hole controller.
 *
 */
class HoleController extends Controller
{
    /**
     * Lists all Hole entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('UJMExoBundle:Hole')->findAll();

        return $this->render('UJMExoBundle:Hole:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Hole entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UJMExoBundle:Hole')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Hole entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UJMExoBundle:Hole:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Hole entity.
     *
     */
    public function newAction()
    {
        $entity = new Hole();
        $form   = $this->createForm(new HoleType(), $entity);

        return $this->render('UJMExoBundle:Hole:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Hole entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Hole();
        $form = $this->createForm(new HoleType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('hole_show', array('id' => $entity->getId())));
        }

        return $this->render('UJMExoBundle:Hole:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Hole entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UJMExoBundle:Hole')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Hole entity.');
        }

        $editForm = $this->createForm(new HoleType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('UJMExoBundle:Hole:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Hole entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UJMExoBundle:Hole')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Hole entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new HoleType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('hole_edit', array('id' => $id)));
        }

        return $this->render('UJMExoBundle:Hole:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Hole entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('UJMExoBundle:Hole')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Hole entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('hole'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
