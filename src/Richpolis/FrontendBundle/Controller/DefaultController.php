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
        
        $inicio = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina'=>'inicio'));
        
        return array(
            'pagina'=>$inicio,
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
        $nosotros = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina'=>'nosotros'));
        return array(
            'nosotros'=>$nosotros
        );
    }
    
    /**
     * @Route("/servicios", name="frontend_servicios")
     * @Template()
     */
    public function serviciosAction()
    {
        $em = $this->getDoctrine()->getManager();
        $autobuses = $em->getRepository('PublicacionesBundle:Servicio')
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
        return array();
    }

    /**
     * @Route("/maldivino/express", name="frontend_maldivino_express")
     * @Template()
     */
    public function expressAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $express = $em->getRepository('PaginasBundle:Pagina')
                ->findOneBy(array('pagina'=>'express'));
        
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
        
        if ($request->getMethod() == 'POST') {
            
            $form->handleRequest($request);

            if ($form->isValid()) {
                $datos=$form->getData();
                
                $em = $this->getDoctrine()->getManager();
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
        
        return $this->render('FrontendBundle:Default:contacto.html.twig',array(
              'form' => $form->createView(),
              'ok'=>$ok,
              'error'=>$error,
              'mensaje'=>$mensaje,
        ));
    }

    /**
     * @Route("/cotizador", name="frontend_cotizador")
     * @Method({"GET", "POST"})
     * @Template("FrontendBundle:Default:cotizador.html.twig")
     */
    public function cotizadorAction() {
        $cotizador = new Cotizador();
        $form = $this->createForm(new CotizadorType(), $cotizador);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            
            $form->handleRequest($request);

            if ($form->isValid()) {
                $datos=$form->getData();
                
                $em = $this->getDoctrine()->getManager();
                $configuracion = $em->getRepository('BackendBundle:Configuraciones')
                                ->findOneBy(array('slug'=>'email-cotizador'));
                
                $message = \Swift_Message::newInstance()
                        ->setSubject('Solicitud de cotización')
                        ->setFrom($datos->getEmail())
                        ->setTo($configuracion->getTexto())
                        ->setBody($this->renderView('FrontendBundle:Default:cotizadorEmail.html.twig', array('datos' => $datos)), 'text/html');
                $this->get('mailer')->send($message);

                // Redirige - Esto es importante para prevenir que el usuario
                // reenvíe el formulario si actualiza la página
                $ok=true;
                $error=false;
                $mensaje="La solicitud de cotización se ha enviado";
                $cotizador = new Cotizador();
                $form = $this->createForm(new CotizadorType(), $cotizador);
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
        
        return array(
              'form' => $form->createView(),
              'ok'=>$ok,
              'error'=>$error,
              'mensaje'=>$mensaje,
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
}