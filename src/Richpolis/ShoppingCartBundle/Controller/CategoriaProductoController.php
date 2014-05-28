<?php

namespace Richpolis\ShoppingCartBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\ShoppingCartBundle\Entity\CategoriaProducto;
use Richpolis\ShoppingCartBundle\Form\CategoriaProductoType;

/**
 * CategoriaProducto controller.
 *
 * @Route("/categoriaproducto")
 */
class CategoriaProductoController extends Controller
{

    /**
     * Lists all CategoriaProducto entities.
     *
     * @Route("/", name="categoriaproducto")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('RichpolisShoppingCartBundle:CategoriaProducto')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new CategoriaProducto entity.
     *
     * @Route("/", name="categoriaproducto_create")
     * @Method("POST")
     * @Template("RichpolisShoppingCartBundle:CategoriaProducto:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new CategoriaProducto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categoriaproducto_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a CategoriaProducto entity.
    *
    * @param CategoriaProducto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(CategoriaProducto $entity)
    {
        $form = $this->createForm(new CategoriaProductoType(), $entity, array(
            'action' => $this->generateUrl('categoriaproducto_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new CategoriaProducto entity.
     *
     * @Route("/new", name="categoriaproducto_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new CategoriaProducto();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a CategoriaProducto entity.
     *
     * @Route("/{id}", name="categoriaproducto_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RichpolisShoppingCartBundle:CategoriaProducto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriaProducto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CategoriaProducto entity.
     *
     * @Route("/{id}/edit", name="categoriaproducto_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RichpolisShoppingCartBundle:CategoriaProducto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriaProducto entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a CategoriaProducto entity.
    *
    * @param CategoriaProducto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CategoriaProducto $entity)
    {
        $form = $this->createForm(new CategoriaProductoType(), $entity, array(
            'action' => $this->generateUrl('categoriaproducto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing CategoriaProducto entity.
     *
     * @Route("/{id}", name="categoriaproducto_update")
     * @Method("PUT")
     * @Template("RichpolisShoppingCartBundle:CategoriaProducto:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('RichpolisShoppingCartBundle:CategoriaProducto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriaProducto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('categoriaproducto_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a CategoriaProducto entity.
     *
     * @Route("/{id}", name="categoriaproducto_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('RichpolisShoppingCartBundle:CategoriaProducto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CategoriaProducto entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('categoriaproducto'));
    }

    /**
     * Creates a form to delete a CategoriaProducto entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('categoriaproducto_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
