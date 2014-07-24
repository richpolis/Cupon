<?php

// src/Cupon/TiendaBundle/Controller/ExtranetController.php
namespace Cupon\TiendaBundle\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Cupon\TiendaBundle\Form\Extranet\TiendaType;
use Cupon\OfertaBundle\Entity\Oferta;
use Cupon\OfertaBundle\Form\Extranet\OfertaType;
 
class ExtranetController extends Controller
{
    public function loginAction()
    {
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
 
        $error = $peticion->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
 
        return $this->render('TiendaBundle:Extranet:login.html.twig', array(
            'error' => $error
        ));
    }

    public function portadaAction()
    {
        $em = $this->getDoctrine()->getManager();
 
        $tienda = $this->get('security.context')->getToken()->getUser();
        $ofertas = $em->getRepository('TiendaBundle:Tienda')
                      ->findOfertasRecientes($tienda->getId());
 
        return $this->render('TiendaBundle:Extranet:portada.html.twig', array(
            'ofertas' => $ofertas
        ));
    }

    public function ofertaVentasAction($id)
    {
        $em = $this->getDoctrine()->getManager();
 
        $ventas = $em->getRepository('OfertaBundle:Oferta')
                     ->findVentasByOferta($id);
 
        return $this->render('TiendaBundle:Extranet:ventas.html.twig', array(
            'oferta' => $ventas[0]->getOferta(),
            'ventas' => $ventas
        ));
    }

    public function perfilAction()
    {
        $peticion = $this->getRequest();
 
        $tienda = $this->get('security.context')->getToken()->getUser();
        $formulario = $this->createForm(new TiendaType(), $tienda);
 
        $passwordOriginal = $formulario->getData()->getPassword();
 
        $formulario->handleRequest($peticion);
 
        if ($formulario->isValid()) {
            if (null == $tienda->getPassword()) {
                // La tienda no cambia su contraseña, utilizar la original
                $tienda->setPassword($passwordOriginal);
            } else {
                // La tienda cambia su contraseña, codificar su valor
                $encoder = $this->get('security.encoder_factory')
                                ->getEncoder($tienda);
                $passwordCodificado = $encoder->encodePassword(
                    $tienda->getPassword(),
                    $tienda->getSalt()
                );
                $tienda->setPassword($passwordCodificado);
            }
 
            $em = $this->getDoctrine()->getManager();
            $em->persist($tienda);
            $em->flush();
 
            $this->get('session')->getFlashBag()->add('info',
                'Los datos de tu perfil se han actualizado correctamente'
            );
 
            return $this->redirect(
                $this->generateUrl('extranet_portada')
            );
        }
 
        return $this->render('TiendaBundle:Extranet:perfil.html.twig', array(
            'tienda'     => $tienda,
            'formulario' => $formulario->createView()
        ));
    }

    public function ofertaNuevaAction()
    {
        $peticion = $this->getRequest();
 
        $oferta = new Oferta();
        $formulario = $this->createForm(new OfertaType(), $oferta);
 
        $formulario->handleRequest($peticion);
 
        if ($formulario->isValid()) {
            $tienda = $this->get('security.context')->getToken()->getUser();
 
            $oferta->setCompras(0);
            $oferta->setTienda($tienda);
            $oferta->setCiudad($tienda->getCiudad());

            $oferta->subirFoto(
                $this->container->getParameter('cupon.directorio.imagenes')
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($oferta);
            $em->flush();
 
            return $this->redirect($this->generateUrl('extranet_portada'));
        }
 
       return $this->render('TiendaBundle:Extranet:formulario.html.twig', 
            array(
                'accion'     => 'crear',
                'formulario' => $formulario->createView()
            )
        );
    }

    public function ofertaEditarAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $oferta = $em->getRepository('OfertaBundle:Oferta')->find($id);
 
        if (!$oferta) {
            throw $this->createNotFoundException('La oferta no existe');
        }
 
        $contexto = $this->get('security.context');
        if (false === $contexto->isGranted('ROLE_EDITAR_OFERTA', $oferta)) {
            throw new AccessDeniedException();
        }
 
        if ($oferta->getRevisada()) {
            $this->get('session')->getFlashBag()->add('error',
                'La oferta no se puede modificar porque ya ha sido revisada'
            );
 
            return $this->redirect($this->generateUrl('extranet_portada'));
        }
 
        $$peticion = $this->getRequest();
        $formulario = $this->createForm(new OfertaType(), $oferta);
 
        $rutaFotoOriginal = $formulario->getData()->getRutaFoto();
 
        $formulario->handleRequest($peticion);
 
        if ($formulario->isValid()) {
            if (null == $oferta->getFoto()) {
                // La foto original no se modifica, recuperar su ruta
                $oferta->setRutaFoto($rutaFotoOriginal);
            } else {
                // La foto de la oferta se ha modificado
                $directorioFotos = $this->container->getParameter(
                    'cupon.directorio.imagenes'
                );
 
                $oferta->subirFoto($directorioFotos);
 
                // Borrar la foto anterior
                unlink($directorioFotos.$fotoOriginal);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($oferta);
            $em->flush();
 
            return $this->redirect(
                $this->generateUrl('extranet_portada')
            );
        }
 
        return $this->render('TiendaBundle:Extranet:formulario.html.twig', 
            array(
                'accion'     => 'editar',
                'oferta'     => $oferta,
                'formulario' => $formulario->createView()
            )
        );
    }

}