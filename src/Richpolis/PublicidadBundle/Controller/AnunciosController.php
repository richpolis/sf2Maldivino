<?php

namespace Richpolis\PublicidadBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\PublicidadBundle\Entity\Anuncio;
use Richpolis\PublicidadBundle\Form\AnuncioType;
use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

/**
 * Anuncios controller.
 *
 * @Route("/anuncios")
 */
class AnunciosController extends Controller
{

    /**
     * Lists all Anuncios entities.
     *
     * @Route("/", name="anuncios")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PublicidadBundle:Anuncio')->getAnunciosActivos();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Anuncios entity.
     *
     * @Route("/", name="anuncios_create")
     * @Method("POST")
     * @Template("PublicidadBundle:Anuncios:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Anuncio();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('anuncios_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
    * Creates a form to create a Anuncios entity.
    *
    * @param Anuncio $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Anuncio $entity)
    {
        $form = $this->createForm(new AnuncioType(), $entity, array(
            'action' => $this->generateUrl('anuncios_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Anuncios entity.
     *
     * @Route("/new", name="anuncios_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Anuncio();
        $em = $this->getDoctrine()->getManager();
        $max = $em->getRepository('PublicidadBundle:Anuncio')->getMaxPosicion();
        if($max == null){
            $max=0;
        }
        $entity->setPosition($max+1);
        $form   = $this->createCreateForm($entity);
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Finds and displays a Anuncios entity.
     *
     * @Route("/{id}", name="anuncios_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicidadBundle:Anuncio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Anuncios entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Anuncios entity.
     *
     * @Route("/{id}/edit", name="anuncios_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicidadBundle:Anuncio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Anuncios entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores' => RpsStms::getErrorMessages($editForm)
        );
    }

    /**
    * Creates a form to edit a Anuncios entity.
    *
    * @param Anuncio $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Anuncio $entity)
    {
        $form = $this->createForm(new AnuncioType(), $entity, array(
            'action' => $this->generateUrl('anuncios_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Anuncios entity.
     *
     * @Route("/{id}", name="anuncios_update")
     * @Method("PUT")
     * @Template("PublicidadBundle:Anuncios:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicidadBundle:Anuncio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Anuncios entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('anuncios_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores'     => RpsStms::getErrorMessages($editForm)
        );
    }
    /**
     * Deletes a Anuncios entity.
     *
     * @Route("/{id}", name="anuncios_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        //$form->submit($request->request->all());
        $form->handleRequest($request);
        
        //if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PublicidadBundle:Anuncio')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Anuncios entity.');
            }

            $em->remove($entity);
            $em->flush();
        //}

        return $this->redirect($this->generateUrl('anuncios'));
    }

    /**
     * Creates a form to delete a Anuncios entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('anuncios_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
         /*->add('submit', 'submit', array(
                'label' => 'Eliminar',
                'attr'=>array(
                    'class'=>'btn btn-danger'
            )))*/
    }
	
    /**
     * Ordenar las posiciones de los autobuses.
     *
     * @Route("/ordenar/registros", name="anuncios_ordenar")
     * @Method("PATCH")
     */
    public function ordenarRegistrosAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $registro_order = $request->query->get('registro');
            $em = $this->getDoctrine()->getManager();
            $result['ok'] = true;
            foreach ($registro_order as $order => $id) {
                $registro = $em->getRepository('PublicidadBundle:Anuncio')->find($id);
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
