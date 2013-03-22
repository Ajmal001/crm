<?php

namespace Oro\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Entity\Status;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/status")
 */
class StatusController extends Controller
{
    /**
     * @Route("/", name="oro_user_status_list", defaults={"limit"=10})
     * @Template
     */
    public function indexAction()
    {
        return array(
            'user' => $this->getUser(),
            'statuses' => $this->get('knp_paginator')->paginate(
                $this->getUser()->getStatuses(),
                $this->getRequest()->get('page', 1),
                $this->getRequest()->get('limit')
            )

        );
    }

    /**
     * @Route("/status-form", name="oro_user_status_form")
     * @Template()
     */
    public function statusFormAction()
    {
        return array(
            'form' => $this->get('oro_user.form.status')->createView(),
        );
    }

    /**
     * @Route("/create", name="oro_user_status_create")
     * @Template()
     */
    public function addAction()
    {
        $result = false;
        if ($this->get('oro_user.form.handler.status')->process($this->getUser(),  new Status(), true)) {
           $result = true;
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new Response((string)$result);

        } elseif ($result) {
            $this->get('session')->getFlashBag()->add('success', 'Status saved');

            return $this->redirect($this->generateUrl('oro_user_status_list'));
        }

        return array(
            'form' => $this->get('oro_user.form.status')->createView(),
        );
    }

    /**
     * @Route("/remove/{id}", name="oro_user_status_remove", requirements={"id"="\d+"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Status $status)
    {
        if ($this->get('oro_user.status_manager')->deleteStatus($this->getUser(), $status, true)) {
            $this->get('session')->getFlashBag()->add('success', 'Status deleted');
        } else {
            $this->get('session')->getFlashBag()->add('success', 'Status was not deleted');
        }

        return $this->redirect($this->generateUrl('oro_user_status_list'));
    }

    /**
     * @Route("/set-current/{id}", name="oro_user_status_set_current", requirements={"id"="\d+"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setCurrentStatus(Status $status)
    {
        $this->get('oro_user.status_manager')->setCurrentStatus($this->getUser(), $status, true);
        $this->get('session')->getFlashBag()->add('success', 'Status set');

        return $this->redirect($this->generateUrl('oro_user_status_list'));
    }
}