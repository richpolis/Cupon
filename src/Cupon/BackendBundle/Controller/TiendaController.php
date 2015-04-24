<?php

namespace Cupon\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cupon\TiendaBundle\Entity\Tienda;
use Cupon\TiendaBundle\Form\TiendaType;

/**
 * Tienda controller.
 *
 */
class TiendaController extends Controller
{

    /**
     * Lists all Tienda entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TiendaBundle:Tienda')->findAll();

        return $this->render('TiendaBundle:Tienda:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Tienda entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Tienda();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_tiendas_show', array('id' => $entity->getId())));
        }

        return $this->render('TiendaBundle:Tienda:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Tienda entity.
    *
    * @param Tienda $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Tienda $entity)
    {
        $form = $this->createForm(new TiendaType(), $entity, array(
            'action' => $this->generateUrl('backend_tiendas_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Tienda entity.
     *
     */
    public function newAction()
    {
        $entity = new Tienda();
        $form   = $this->createCreateForm($entity);

        return $this->render('TiendaBundle:Tienda:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Tienda entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TiendaBundle:Tienda')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tienda entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('TiendaBundle:Tienda:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Tienda entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TiendaBundle:Tienda')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tienda entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('TiendaBundle:Tienda:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Tienda entity.
    *
    * @param Tienda $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Tienda $entity)
    {
        $form = $this->createForm(new TiendaType(), $entity, array(
            'action' => $this->generateUrl('backend_tiendas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Tienda entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TiendaBundle:Tienda')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tienda entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_tiendas_edit', array('id' => $id)));
        }

        return $this->render('TiendaBundle:Tienda:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Tienda entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TiendaBundle:Tienda')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tienda entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_tiendas'));
    }

    /**
     * Creates a form to delete a Tienda entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_tiendas_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
