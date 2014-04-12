<?php

namespace Cupon\CiudadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    public function cambiarAction($ciudad)
    {
        return new RedirectResponse($this->generateUrl(
            'portada',
            array('ciudad' => $ciudad)
        ));
    }
    
    /**
     * Busca todas las ciudades disponibles en la base de datos y pasa la lista
     * a una plantilla muy sencilla que simplemente muestra una lista desplegable
     * para seleccionar la ciudad activa.
     *
     * @param string $ciudad El slug de la ciudad seleccionada
     */
    public function listaCiudadesAction($ciudad)
    {
        $em = $this->getDoctrine()->getManager();
        $ciudades = $em->getRepository('CiudadBundle:Ciudad')->findAll();
 
        return $this->render('CiudadBundle:Default:listaCiudades.html.twig', array(
            'ciudadActual' => $ciudad,
            'ciudades'     => $ciudades,
        ));
    }
    
    public function recientesAction($ciudad)
    {
        $em = $this->getDoctrine()->getManager();

        $formato = $this->get('request')->getRequestFormat();
 
        $ciudad = $em->getRepository('CiudadBundle:Ciudad')
                     ->findOneBySlug($ciudad);
        $cercanas = $em->getRepository('CiudadBundle:Ciudad')
                       ->findCercanas($ciudad->getId());
        $ofertas  = $em->getRepository('OfertaBundle:Oferta')
                       ->findRecientes($ciudad->getId());
 
        return $this->render('CiudadBundle:Default:recientes.'.$formato.'.twig',
            array(
                'ciudad'   => $ciudad,
                'cercanas' => $cercanas,
                'ofertas'  => $ofertas
            )
        );
    }
}
