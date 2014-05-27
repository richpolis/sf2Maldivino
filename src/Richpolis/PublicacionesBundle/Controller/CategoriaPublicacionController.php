<?php

namespace Richpolis\PublicacionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\PublicacionesBundle\Entity\CategoriaPublicacion;
use Richpolis\PublicacionesBundle\Form\CategoriaPublicacionType;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * CategoriaPublicacion controller.
 *
 * @Route("/categorias/publicaciones")
 */
class CategoriaPublicacionController extends Controller
{

    /**
     * Lists all CategoriaPublicacion entities.
     *
     * @Route("/", name="categorias_publicaciones")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new CategoriaPublicacion entity.
     *
     * @Route("/", name="categorias_publicaciones_create")
     * @Method("POST")
     * @Template("PublicacionesBundle:CategoriaPublicacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new CategoriaPublicacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categorias_publicaciones_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
    * Creates a form to create a CategoriaPublicacion entity.
    *
    * @param CategoriaPublicacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(CategoriaPublicacion $entity)
    {
        $form = $this->createForm(new CategoriaPublicacionType(), $entity, array(
            'action' => $this->generateUrl('categorias_publicaciones_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new CategoriaPublicacion entity.
     *
     * @Route("/new", name="categorias_publicaciones_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new CategoriaPublicacion();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'errores' => RpsStms::getErrorMessages($form)
        );
    }

    /**
     * Finds and displays a CategoriaPublicacion entity.
     *
     * @Route("/{id}", name="categorias_publicaciones_show", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriaPublicacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CategoriaPublicacion entity.
     *
     * @Route("/{id}/edit", name="categorias_publicaciones_edit", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriaPublicacion entity.');
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
    * Creates a form to edit a CategoriaPublicacion entity.
    *
    * @param CategoriaPublicacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CategoriaPublicacion $entity)
    {
        $form = $this->createForm(new CategoriaPublicacionType(), $entity, array(
            'action' => $this->generateUrl('categorias_publicaciones_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    /**
     * Edits an existing CategoriaPublicacion entity.
     *
     * @Route("/{id}", name="categorias_publicaciones_update", requirements={"id" = "\d+"})
     * @Method("PUT")
     * @Template("PublicacionesBundle:CategoriaPublicacion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriaPublicacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('categorias_publicaciones_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errores'     => RpsStms::getErrorMessages($editForm)
        );
    }
    
    /**
     * Deletes a CategoriaPublicacion entity.
     *
     * @Route("/{id}", name="categorias_publicaciones_delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CategoriaPublicacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('categorias_publicaciones'));
    }

    /**
     * Creates a form to delete a CategoriaPublicacion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('categorias_publicaciones_delete', array('id' => $id)))
            ->setMethod('DELETE')
            /*->add('submit', 'submit', array(
                'label' => 'Eliminar',
                'attr'=>array(
                    'class'=>'btn btn-danger'
            )))*/
            ->getForm()
        ;
    }
    
    /**
     * Ordenar las posiciones de las categorias publicaciones.
     *
     * @Route("/ordenar/registros", name="categorias_publicaciones_ordenar")
     * @Method("PATCH")
     */
    public function ordenarRegistrosAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $registro_order = $request->query->get('registro');
            $em = $this->getDoctrine()->getManager();
            $result['ok'] = true;
            foreach ($registro_order as $order => $id) {
                $registro = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')->find($id);
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
    
    /**
     * @Route("/exportar", name="categorias_publicaciones_exportar")
     * @Method({"GET", "POST"})
     */
    public function exportarAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            if($request->request->has('exportarTodos') && $request->request->get('exportarTodos')){
                $entities = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')->findAll();
            }elseif($request->request->has('inputDesde') && 
                    $request->request->has('inputHasta')){
                $entities = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                        ->findEntreFechas($request->request->get('inputDesde'),$request->request->get('inputHasta'));
            }
            $filename = "export_".date("Y_m_d").".xls"; 
            $response=$this->render('PublicacionesBundle:CategoriaPublicacion:tablaExportar.html.twig', array('entities'=>$entities)); 
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8'); 
            $response->headers->set('Content-Disposition', 'attachment; filename='.$filename); 
            $response->headers->set('Pragma', 'public'); 
            $response->headers->set('Cache-Control', 'maxage=1');
            return $response;
        }
        
            return $this->render("PublicacionesBundle:CategoriaPublicacion:exportar.html.twig");
    }
    
    /**
     * @Route("/importar", name="categorias_publicaciones_importar")
     * @Method({"GET", "POST"})
     */
    public function importarAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            
         
          return $this->redirect($this->generateUrl('categorias_publicaciones'));  
        }
        
        return $this->render("PublicacionesBundle:CategoriaPublicacion:importar.html.twig");
    }
}
