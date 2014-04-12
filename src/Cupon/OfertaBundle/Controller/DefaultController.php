<?php

namespace Cupon\OfertaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function portadaAction($ciudad)
    {
        $em = $this->getDoctrine()->getManager();
 
        $oferta = $em->getRepository('OfertaBundle:Oferta')
                ->findOfertaDelDia($ciudad);
 
        if (!$oferta) {
            throw $this->createNotFoundException(
                'No se ha encontrado la oferta del día'
            );
        }

        return $this->render('OfertaBundle:Default:portada.html.twig', array(
            'oferta' => $oferta,
            'ciudad' => $ciudad,
        ));
    }
    
    public function ayudaAction()
    {
        return new Response('Ayuda');
    }
    
    public function ofertaAction($ciudad, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $oferta = $em->getRepository('OfertaBundle:Oferta')
                     ->findOferta($ciudad, $slug);
        $relacionadas = $em->getRepository('OfertaBundle:Oferta')
                           ->findRelacionadas($ciudad);
 
        return $this->render('OfertaBundle:Default:detalle.html.twig', array(
            'oferta'       => $oferta,
            'relacionadas' => $relacionadas
        ));
    }
}
