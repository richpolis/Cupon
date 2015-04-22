<?php

// src/Cupon/BackendBundle/Controller/CiudadController.php
namespace Cupon\BackendBundle\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cupon\CiudadBundle\Entity\Ciudad;
use Cupon\BackendBundle\Form\CiudadType;
 
class CiudadController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ciudades = $em->getRepository('CiudadBundle:Ciudad')->findAll();
 
        return $this->render('BackendBundle:Ciudad:index.html.twig', array(
            'ciudades' => $ciudades
        ));
    }
 
    public function verAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $ciudad = $em->getRepository('CiudadBundle:Ciudad')->find($id);
 
        if (!$ciudad) {
            throw $this->createNotFoundException('No existe esa ciudad');
        }
 
        return $this->render('BackendBundle:Ciudad:ver.html.twig', array(
            'ciudad' => $ciudad,
        ));
    }
 
    public function crearAction()
    {
        $request = $this->getRequest();
 
        $ciudad = new Ciudad();
        $formulario = $this->createForm(new CiudadType(), $ciudad);
 
        $form->handleRequest($request);
 
        if ($formulario->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ciudad);
            $em->flush();
 
            return $this->redirect($this->generateUrl('backend_ciudad'));
        }
 
        return $this->render('BackendBundle:Ciudad:crear.html.twig', array(
            'formulario' => $formulario->createView()
        ));
    }
 
    public function actualizarAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $ciudad = $em->getRepository('CiudadBundle:Ciudad')->find($id);
 
        if (!$ciudad) {
            throw $this->createNotFoundException('No existe esa ciudad');
        }
 
        $formulario = $this->createForm(new CiudadType(), $ciudad);
 
        $request = $this->getRequest();
        $form->handleRequest($request);
 
        if ($formulario->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ciudad);
            $em->flush();
 
            return $this->redirect($this->generateUrl('backend_ciudad'));
        }
 
        return $this->render('BackendBundle:Ciudad:actualizar.html.twig', array(
            'formulario' => $formulario->createView(),
            'ciudad'     => $ciudad
        ));
    }
 
    public function borrarAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $ciudad = $em->getRepository('CiudadBundle:Ciudad')->find($id);
 
        if (!$ciudad) {
            throw $this->createNotFoundException('No existe esa ciudad');
        }
 
        $em->remove($ciudad);
        $em->flush();
 
        return $this->redirect($this->generateUrl('backend_ciudad'));
    }
}