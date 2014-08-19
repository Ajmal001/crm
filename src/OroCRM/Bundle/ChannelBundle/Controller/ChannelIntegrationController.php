<?php

namespace OroCRM\Bundle\ChannelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\IntegrationBundle\Entity\Channel as Integration;

/**
 * @Route("/integration")
 */
class ChannelIntegrationController extends Controller
{
    /**
     * @Route("/create/{type}/{channelName}", requirements={"type"="\w+"}, name="orocrm_channel_integration_create")
     * @AclAncestor("oro_integration_create")
     * @Template("OroCRMChannelBundle:ChannelIntegration:update.html.twig")
     */
    public function createAction($type, $channelName)
    {
        $translator         = $this->get('translator');
        $integrationName    = $channelName . ' ' . $translator->trans('orocrm.channel.data_source.label');
        $integration        = new Integration();
        $integration->setType($type);
        $integration->setName($integrationName);

        return $this->update($integration);
    }

    /**
     * @Route("/update/{id}", requirements={"id"="\d+"}, name="orocrm_channel_integration_update")
     * @AclAncestor("oro_integration_update")
     * @Template()
     */
    public function updateAction(Integration $integration)
    {
        return $this->update($integration);
    }

    /**
     * @param Integration $integration
     *
     * @return array
     */
    protected function update(Integration $integration)
    {
        $handler = $this->get('orocrm_channel.channel_integration_form.handler');

        $data = null;
        if ($handler->process($integration)) {
            $data = $handler->getFormSubmittedData();
        }

        return [
            'form'        => $handler->getFormView(),
            'isSubmitted' => null !== $data,
            'savedData'   => $data
        ];
    }
}
