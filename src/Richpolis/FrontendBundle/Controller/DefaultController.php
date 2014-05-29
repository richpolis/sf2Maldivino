<?php

namespace Richpolis\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Richpolis\BackendBundle\Utils\Richsys as RpsStms;

use Richpolis\FrontendBundle\Entity\Contacto;
use Richpolis\FrontendBundle\Form\ContactoType;

use Richpolis\FrontendBundle\Entity\Cotizador;
use Richpolis\FrontendBundle\Form\CotizadorType;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $portada = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina'=>'portada'));
		
		$promociones = $em->getRepository('PublicidadBundle:Publicidad')->getPublicidadActivos();
		
		$anuncios = $em->getRepository('PublicidadBundle:Anuncio')->getAnunciosActivos();
		
        
        return array(
            'pagina'=>$portada,
			'promociones'=>$promociones,
			'anuncios'=>$anuncios,
        );
		        
    }

     private function getPublicacionesPorFilas($categorias){
        $arreglo = array();
        $largo = 0;
        $paginas = 0;
        $contPagina = 0;
        $cont=0;
        foreach($categorias as $categoria){
            $arreglo[$categoria->getSlug()]=array();
            $largo = count($categoria->getPublicaciones());
            $paginas = ceil($largo/3);
            $contPagina = 0;
            $arreglo[$categoria->getSlug()][$contPagina]=array();
            $cont=0;
            foreach($categoria->getPublicaciones() as $publicacion){
                $arreglo[$categoria->getSlug()][$contPagina][$cont++]=$publicacion;
                if($cont==3){
                    $cont=0;
                    $contPagina++;
                }
            }
        }
        return $arreglo;
    }
    
    
    /**
     * @Route("/quienes/somos", name="frontend_quienes_somos")
     * @Template()
     */
    public function quienesSomosAction()
    {
        $em = $this->getDoctrine()->getManager();
        $quienesSomos = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina'=>'quienes-somos'));
        return array(
            'pagina'=>$quienesSomos
        );
    }
    
    /**
     * @Route("/servicios", name="frontend_servicios")
     * @Template()
     */
    public function serviciosAction()
    {
        $em = $this->getDoctrine()->getManager();
        $servicios = $em->getRepository('PublicacionesBundle:Servicio')
                ->findActivos();
        return array(
            'servicios'=>$servicios,
        );
    }
    
    /**
     * @Route("/productos", name="frontend_productos")
     * @Template()
     */
    public function productosAction() 
    {
        $em = $this->getDoctrine()->getManager();
        $categorias = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->getCategoriaPublicacionActivas();
        return array(
          'categorias'=>$categorias,
          'productos'=>$categorias[0]->getPublicaciones(),
        );
    }

    
    /**
     * @Route("/maldivino/express", name="frontend_maldivino_express")
     * @Template()
     */
    public function expressAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $express = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina'=>'maldivino-express'));
        
        return array(
            'pagina'=>$express,
        );
    }
    
   
    
    /**
     * @Route("/contacto", name="frontend_contacto")
     * @Method({"GET", "POST"})
     */
    public function contactoAction(Request $request) {
        $contacto = new Contacto();
        $form = $this->createForm(new ContactoType(), $contacto);
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            
            $form->handleRequest($request);

            if ($form->isValid()) {
                $datos=$form->getData();
                
                $configuracion = $em->getRepository('BackendBundle:Configuraciones')
                                ->findOneBy(array('slug'=>'email-contacto'));
                
                $message = \Swift_Message::newInstance()
                        ->setSubject('Contacto desde pagina')
                        ->setFrom($datos->getEmail())
                        ->setTo($configuracion->getTexto())
                        ->setBody($this->renderView('FrontendBundle:Default:contactoEmail.html.twig', array('datos' => $datos)), 'text/html');
                $this->get('mailer')->send($message);

                // Redirige - Esto es importante para prevenir que el usuario
                // reenvíe el formulario si actualiza la página
                $ok=true;
                $error=false;
                $mensaje="Se ha enviado el mensaje";
                $contacto = new Contacto();
                $form = $this->createForm(new ContactoType(), $contacto);
                
                
            }else{
                $ok=false;
                $error=true;
                $mensaje="El mensaje no se ha podido enviar";
            }
        }else{
            $ok=false;
            $error=false;
            $mensaje="";
        }
        
        if($request->isXmlHttpRequest()){
            return $this->render('FrontendBundle:Default:formContacto.html.twig',array(
                'form' => $form->createView(),
                'ok'=>$ok,
                'error'=>$error,
                'mensaje'=>$mensaje,
            ));
        }
        
		
        
        $ubicacion = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina'=>'ubicacion'));
		$mapa = $em->getRepository('BackendBundle:Configuraciones')
                                ->findOneBy(array('slug'=>'mapa-ubicacion'));
		
        return $this->render('FrontendBundle:Default:contacto.html.twig',array(
              'form' => $form->createView(),
              'ok'=>$ok,
              'error'=>$error,
              'mensaje'=>$mensaje,
			  'pagina'=>$ubicacion,
			  'mapa'=>$mapa,
        ));
    }

    /**
     * @Route("/cotizador", name="frontend_cotizador")
     * @Method("GET")
     * @Template("FrontendBundle:Default:cotizador.html.twig")
     */
    public function cotizadorAction() {
        $em = $this->getDoctrine()->getManager();
        $categorias = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->getCategoriaPublicacionActivas();

        return array(
              'categorias'=>$categorias,
        );
    }
    
    /**
     * @Route("/api/categorias", name="get_categorias")
     * @Method({"GET"})
     */
    public function getCategoriasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categorias = $em->getRepository('PublicacionesBundle:CategoriaPublicacion')
                ->getCategoriasActivas();
        
        $resultados = $this->decodeCategorias($categorias);
        
        $response = new JsonResponse();
        $response->setData($resultados);
        return $response;
    }
    
    private function decodeCategorias($categorias){
        $arreglo = array();
        $cont = 0;
        $largo = count($categorias);
        $avalancheService = $this->get('imagine.cache.path.resolver');
        foreach($categorias as $categoria){
            $item = array(
              'id'=>$categoria->getId(),
              'nombre'=>$categoria->getNombre(),
              'position'=>$categoria->getPosition(),
              'isActive'=>$categoria->getIsActive(),
              'slug'=>$categoria->getSlug(),
              'publicaciones'=>array(),
            );
            $contPublicacion = 0;
            $publicaciones = array();
            foreach($categoria->getPublicaciones() as $publicacion){
                $publicaciones[$contPublicacion++]=array(
                  'botella'=>$publicacion->getTitulo(),
                  'descripcion'=>$publicacion->getDescripcion(),
                  'thumbnail'=>$avalancheService->getBrowserPath($publicacion->getWebPath(), 'botellas'),
                  'precio'=>$publicacion->getPrecio(),  
                );
            }
            $item['publicaciones']=$publicaciones;
            $arreglo[$cont++]= $item;
        }
        return $arreglo;
    }
    
    /**
     * @Route("/pie/pagina", name="frontend_pie_pagina")
     * @Method({"GET"})
     * @Template()
     */
    public function piePaginaAction(){
        $em = $this->getDoctrine()->getManager();
        $configuracion = $em->getRepository('BackendBundle:Configuraciones')
                                ->findOneBy(array('slug'=>'pie-pagina'));
        return array(
          'piePagina'=>$configuracion,  
        );
    }
	
	/**
     * @Route("/enviar/pedido", name="frontend_enviar_pedido")
     * @Method("POST")
     */
    public function enviarPedidoAction() {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
			$datos = $request->request->all();
                $configuracion = $em->getRepository('BackendBundle:Configuraciones')
                                ->findOneBy(array('slug'=>'email-cotizador'));
              
                $message = \Swift_Message::newInstance()
                        ->setSubject('Solicitud de cotización')
                        ->setFrom('noreply@maldivino.com')
                        ->setTo($configuracion->getTexto())
                        ->setBody($this->renderView('FrontendBundle:Default:cotizadorEmail.html.twig', array('datos' => $datos)), 'text/html');
                $this->get('mailer')->send($message);

                // Redirige - Esto es importante para prevenir que el usuario
                // reenvíe el formulario si actualiza la página
                $ok=true;
                $error=false;
                $mensaje="La solicitud de cotización se ha enviado";

        }else{
            $ok=false;
            $error=false;
            $mensaje="";
        }
		
		$response = new JsonResponse();
        $response->setData(array('enviado'=>$ok,'error'=>$error,'mensaje'=>$mensaje));
        return $response;

    }
}