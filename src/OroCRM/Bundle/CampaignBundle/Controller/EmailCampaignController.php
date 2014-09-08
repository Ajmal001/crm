<?php

namespace OroCRM\Bundle\CampaignBundle\Controller;

use OroCRM\Bundle\CampaignBundle\Entity\EmailCampaign;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

/**
 * @Route("/campaign/email")
 */
class EmailCampaignController extends Controller
{
    /**
     * @Route("/", name="orocrm_email_campaign_index")
     * @AclAncestor("orocrm_email_campaign_view")
     * @Template
     */
    public function indexAction()
    {
        return [
            'entity_class' => $this->container->getParameter('orocrm_campaign.email_campaign.entity.class')
        ];
    }

    /**
     * Create email campaign
     *
     * @Route("/create", name="orocrm_email_campaign_create")
     * @Template("OroCRMCampaignBundle:EmailCampaign:update.html.twig")
     * @Acl(
     *      id="orocrm_email_campaign_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="OroCRMCampaignBundle:EmailCampaign"
     * )
     */
    public function createAction()
    {
        return $this->update(new EmailCampaign());
    }

    /**
     * Edit email campaign
     *
     * @Route("/update/{id}", name="orocrm_email_campaign_update", requirements={"id"="\d+"}, defaults={"id"=0})
     * @Template
     * @Acl(
     *      id="orocrm_email_campaign_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="OroCRMCampaignBundle:EmailCampaign"
     * )
     * @param EmailCampaign $entity
     * @return array
     */
    public function updateAction(EmailCampaign $entity)
    {
        return $this->update($entity);
    }

    /**
     * View email campaign
     *
     * @Route("/view/{id}", name="orocrm_email_campaign_view", requirements={"id"="\d+"})
     * @Acl(
     *      id="orocrm_email_campaign_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="OroCRMCampaignBundle:EmailCampaign"
     * )
     * @Template
     * @param EmailCampaign $entity
     * @return array
     */
    public function viewAction(EmailCampaign $entity)
    {
        return ['entity' => $entity];
    }

    /**
     * Process save email campaign entity
     *
     * @param EmailCampaign $entity
     * @return array
     */
    protected function update(EmailCampaign $entity)
    {
        return $this->get('oro_form.model.update_handler')->handleUpdate(
            $entity,
            $this->get('orocrm_campaign.email_campaign.form'),
            function (EmailCampaign $entity) {
                return array(
                    'route' => 'orocrm_email_contact_update',
                    'parameters' => array('id' => $entity->getId())
                );
            },
            function (EmailCampaign $entity) {
                return array(
                    'route' => 'orocrm_email_campaign_view',
                    'parameters' => array('id' => $entity->getId())
                );
            },
            $this->get('translator')->trans('orocrm.campaign.emailcampaign.controller.saved.message')
        );
    }

    /**
     * @Route("/send/{id}", name="orocrm_email_campaign_send", requirements={"id"="\d+"})
     * @Acl(
     *      id="orocrm_email_campaign_send",
     *      type="action",
     *      label="Send campaign emails",
     *      group_name=""
     * )
     *
     * @param EmailCampaign $entity
     * @return array
     */
    public function sendAction(EmailCampaign $entity)
    {
        $emailTransport = $this->get('orocrm_campaign.transport.email');
        $sender = $this->get('orocrm_campaign.email_campaign.sender');
        $sender->setTransport($emailTransport);
        $sender->send($entity);

        return $this->redirect(
            $this->generateUrl(
                'orocrm_email_campaign_view',
                ['id' => $entity->getId()]
            )
        );
    }
}
