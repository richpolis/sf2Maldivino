<?php

namespace Richpolis\PublicidadBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\PublicidadBundle\Entity\Publicidad;
use Richpolis\PublicidadBundle\Form\PublicidadType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Publicidad controller.
 *
 * @Route("/publicidad")
 */
class PublicidadController extends Controller
{

    /**
     * Lists all Publicidad entities.
     *
     * @Route("/", name="publicidad")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PublicidadBundle:Publicidad')->getPublicidadActivos();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Publicidad entity.
     *
     * @Route("/", name="publicidad_create")
     * @Method("POST")
     * @Template("PublicidadBundle:Publicidad:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Publicidad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('publicidad_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
			'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
    * Creates a form to create a Publicidad entity.
    *
    * @param Publicidad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Publicidad $entity)
    {
        $form = $this->createForm(new PublicidadType(), $entity, array(
            'action' => $this->generateUrl('publicidad_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Publicidad entity.
     *
     * @Route("/new", name="publicidad_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Publicidad();
		
		$max = $this->getDoctrine()->getRepository('PublicidadBundle:Publicidad')
                ->getMaxPosicion();

        if (!is_null($max)) {
            $entity->setPosition($max + 1);
        } else {
            $entity->setPosition(1);
        }
		
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Finds and displays a Publicidad entity.
     *
     * @Route("/{id}", name="publicidad_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicidadBundle:Publicidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicidad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Publicidad entity.
     *
     * @Route("/{id}/edit", name="publicidad_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicidadBundle:Publicidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicidad entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'    => $entity,
            'form'   	=> $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
			'errores' => RpsStms::getErrorMessages($editForm)
        );
    }

    /**
    * Creates a form to edit a Publicidad entity.
    *
    * @param Publicidad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Publicidad $entity)
    {
        $form = $this->createForm(new PublicidadType(), $entity, array(
            'action' => $this->generateUrl('publicidad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Publicidad entity.
     *
     * @Route("/{id}", name="publicidad_update")
     * @Method("PUT")
     * @Template("PublicidadBundle:Publicidad:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicidadBundle:Publicidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicidad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('publicidad_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
	    'errores' => RpsStms::getErrorMessages($editForm)
        );
    }
    /**
     * Deletes a Publicidad entity.
     *
     * @Route("/{id}", name="publicidad_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PublicidadBundle:Publicidad')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Publicidad entity.');
            }

            $em->remove($entity);
            $em->flush();
        //}

        return $this->redirect($this->generateUrl('publicidad'));
    }

    /**
     * Creates a form to delete a Publicidad entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('publicidad_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
	
	/**
     * Ordenar las posiciones de los autobuses.
     *
     * @Route("/ordenar/registros", name="publicidad_ordenar")
     * @Method("PATCH")
     */
    public function ordenarRegistrosAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $registro_order = $request->query->get('registro');
            $em = $this->getDoctrine()->getManager();
            $result['ok'] = true;
            foreach ($registro_order as $order => $id) {
                $registro = $em->getRepository('PublicidadBundle:Publicidad')->find($id);
                if ($registro->getPosition() != ($order + 1)) {
                    try {
                        $registro->setPosition($order + 1);
                        $em->flush();
                    } catch (Exception $e) {
                        $result['mensaje'] = $e->getMessage();
                        $result['ok'] = false;
                    }
                }
            }

            $response = new \Symfony\Component\HttpFoundation\JsonResponse();
            $response->setData($result);
            return $response;
        } else {
            $response = new \Symfony\Component\HttpFoundation\JsonResponse();
            $response->setData(array('ok' => false));
            return $response;
        }
    }
}
