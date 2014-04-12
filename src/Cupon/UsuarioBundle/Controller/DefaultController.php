<?php

namespace Cupon\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Cupon\UsuarioBundle\Entity\Usuario;
use Cupon\UsuarioBundle\Form\Frontend\UsuarioType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
 
class DefaultController extends Controller
{
    public function defaultAction()
    {
        $usuario = new Usuario();
        $encoder = $this->get('security.encoder_factory')
                        ->getEncoder($usuario);
 
        $password = $encoder->encodePassword(
            'la-contraseña-en-claro',
            $usuario->getSalt()
        );
 
        $usuario->setPassword($password);
    }
    
    public function comprasAction()
    {
        $usuario_id = 1;
 
        $em = $this->getDoctrine()->getManager();
        $compras = $em->getRepository('UsuarioBundle:Usuario')
                      ->findTodasLasCompras($usuario_id);
 
        return $this->render('UsuarioBundle:Default:compras.html.twig', array(
            'compras'  => $compras
        ));
    }
    
    public function loginAction()
    {
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
 
        $error = $peticion->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
 
        return $this->render('UsuarioBundle:Default:login.html.twig', array(
            'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
            'error'         => $error
        ));
    }
    
    public function cajaLoginAction()
    {
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
 
        $error = $peticion->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
 
        return $this->render('UsuarioBundle:Default:cajaLogin.html.twig', array(
            'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
            'error'         => $error
        ));
    }
    
    public function registroAction()
    {
        $peticion = $this->getRequest();

        $usuario = new Usuario();
        $formulario = $this->createForm(new UsuarioType(), $usuario);

        $formulario->handleRequest($peticion);

        if ($formulario->isValid()) {
            $encoder = $this->get('security.encoder_factory')
                            ->getEncoder($usuario);
            $usuario->setSalt(md5(time()));
            $passwordCodificado = $encoder->encodePassword(
                $usuario->getPassword(),
                $usuario->getSalt()
            );
            $usuario->setPassword($passwordCodificado);

            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();

            $this->get('session')->getFlashBag()->add('info',
                '¡Enhorabuena! Te has registrado correctamente en Cupon'
            );
            
            $token = new UsernamePasswordToken(
              $usuario,
              $usuario->getPassword(),
              'frontend',
              $usuario->getRoles()
            );
            $this->container->get('security.context')->setToken($token);
 
            return $this->redirect($this->generateUrl('portada', array(
                'ciudad' => $usuario->getCiudad()->getSlug()
            )));
        }

        return $this->render(
            'UsuarioBundle:Default:registro.html.twig',
            array('formulario' => $formulario->createView())
        );
    }
    
    public function perfilAction()
    {
        $peticion = $this->getRequest();
        $usuario = $this->get('security.context')->getToken()->getUser();
        $formulario = $this->createForm(new UsuarioType(), $usuario);
 
        $passwordOriginal = $formulario->getData()->getPassword();
        $formulario->handleRequest($peticion);
 
        if ($formulario->isValid()) {
            if (null == $usuario->getPassword()) {
                $usuario->setPassword($passwordOriginal);
            } else {
                $encoder = $this->get('security.encoder_factory')
                                ->getEncoder($usuario);
                $passwordCodificado = $encoder->encodePassword(
                    $usuario->getPassword(),
                    $usuario->getSalt()
                );
                $usuario->setPassword($passwordCodificado);
            }
 
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();
 
            $this->get('session')->getFlashBag()->add('info',
                'Los datos de tu perfil se han actualizado correctamente'
            );
 
            return $this->redirect($this->generateUrl('usuario_perfil'));
        }
 
        return $this->render('UsuarioBundle:Default:perfil.html.twig', array(
            'usuario'    => $usuario,
            'formulario' => $formulario->createView()
        ));
    }
}
