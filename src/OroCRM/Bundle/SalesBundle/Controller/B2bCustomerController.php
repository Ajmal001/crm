<?php

namespace OroCRM\Bundle\SalesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use OroCRM\Bundle\SalesBundle\Entity\B2bCustomer;

/**
 * @Route("/b2bcustomer")
 */
class B2bCustomerController extends Controller
{
    /**
     * @Route("/", name="orocrm_sales_b2bcustomer_index")
     * @Template
     * @AclAncestor("orocrm_sales_b2bcustomer_view")
     */
    public function indexAction()
    {
        return [
            'entity_class' => $this->container->getParameter('orocrm_sales.b2bcustomer.entity.class')
        ];
    }

    /**
     * @Route("/view/{id}", name="orocrm_sales_b2bcustomer_view", requirements={"id"="\d+"})
     * @Template
     * @Acl(
     *      id="orocrm_sales_b2bcustomer_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="OroCRMSalesBundle:B2bCustomer"
     * )
     */
    public function viewAction(B2bCustomer $customer)
    {
        return [
            'entity' => $customer
        ];
    }

    /**
     * @Route("/widget/info/{id}", name="orocrm_salses_b2bcustomer_widget_info", requirements={"id"="\d+"})
     * @AclAncestor("orocrm_sales_b2bcustomer_view")
     * @Template()
     */
    public function infoAction(B2bCustomer $customer)
    {
        return [
            'entity' => $customer
        ];
    }

    /**
     * @Route("/widget/b2bcustomer-leads/{id}", name="orocrm_sales_b2bcustomer_widget_leads", requirements={"id"="\d+"})
     * @AclAncestor("orocrm_sales_lead_view")
     * @Template
     */
    public function b2bCustomerLeadsAction(B2bCustomer $customer)
    {
        return [
            'entity' => $customer
        ];
    }

    /**
     * Create b2bcustomer form
     *
     * @Route("/create", name="orocrm_b2bcustomer_create")
     * @Acl(
     *      id="orocrm_b2bcustomer_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="OroCRMSalesBundle:B2bCustomer"
     * )
     * @Template("OroCRMSalesBundle:B2bCustomer:update.html.twig")
     */
    public function createAction()
    {
        return $this->update(new B2bCustomer());
    }

    /**
     * @param B2bCustomer $entity
     * @return array
     */
    protected function update(B2bCustomer $entity = null)
    {
        return $this->get('oro_form.model.update_handler')->handleUpdate(
            $entity,
            $this->get('orocrm_sales.B2bCustomer.form'),
            function (B2bCustomer $entity) {
                return array(
                    'route' => 'orocrm_sales_b2bcustomer_update',
                    'parameters' => array('id' => $entity->getId())
                );
            },
            function (B2bCustomer $entity) {
                return array(
                    'route' => 'orocrm_sales_b2bCustomer_view',
                    'parameters' => array('id' => $entity->getId())
                );
            },
            $this->get('translator')->trans('orocrm.sales.controller.b2bcustomer.saved.message'),
            $this->get('orocrm_sales.B2bCustomer.form.handler')
        );
    }

    /**
     * Update user form
     * @Route("/update/{id}", name="orocrm_sales_b2bcustomer_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     *      id="orocrm_sales_b2bcustomer_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="OroCRMSalesBundle:B2bCustomer"
     * )
     */
    public function updateAction(B2bCustomer $entity)
    {
        return $this->update($entity);
    }

    /**
     * @Route(
     *      "/widget/b2bcustomer-opportunities/{id}",
     *      name="orocrm_sales_b2bcustomer_widget_opportunities",
     *      requirements={"id"="\d+"}
     * )
     * @AclAncestor("orocrm_sales_opportunity_view")
     * @Template
     */
    public function b2bCustomerOpportunitiesAction(B2bCustomer $customer)
    {
        return [
            'entity' => $customer
        ];
    }

    /**
     * @Route(
     *      "/widget/leads/{id}",
     *      name="orocrm_sales_widget_leads_info",
     *      requirements={"id"="\d+"},
     *      defaults={"id"=0}
     * )
     * @AclAncestor("orocrm_b2bcustomer_view")
     * @Template()
     */
    public function leadsInfoAction(B2bCustomer $b2bCustomer = null)
    {
        return [
            'b2bCustomer' => $b2bCustomer
        ];
    }

    /**
     * @Route(
     *      "/widget/opportunities/{id}",
     *      name="orocrm_sales_widget_opportunities_info",
     *      requirements={"id"="\d+"},
     *      defaults={"id"=0}
     * )
     * @AclAncestor("orocrm_b2bcustomer_view")
     * @Template()
     */
    public function opportunitiesInfoAction(B2bCustomer $b2bCustomer = null)
    {
        return [
            'b2bCustomer' => $b2bCustomer
        ];
    }
}
