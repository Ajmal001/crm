<?php

namespace OroCRM\Bundle\SalesBundle\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;
use OroCRM\Bundle\SalesBundle\Entity\Opportunity;
use OroCRM\Bundle\SalesBundle\Entity\OpportunityStatus;

class OpportunityFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return 'OroCRM\Bundle\SalesBundle\Entity\Opportunity';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->getEntityData('Jerry Coleman');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity($key)
    {
        return new Opportunity();
    }

    /**
     * @param string      $key
     * @param Opportunity $entity
     */
    public function fillEntityData($key, $entity)
    {
        $userRepo     = $this->templateManager->getEntityRepository('Oro\Bundle\UserBundle\Entity\User');
        $customerRepo = $this->templateManager->getEntityRepository('OroCRM\Bundle\SalesBundle\Entity\B2bCustomer');
        $contactRepo  = $this->templateManager->getEntityRepository('OroCRM\Bundle\ContactBundle\Entity\Contact');
        $leadRepo     = $this->templateManager->getEntityRepository('OroCRM\Bundle\SalesBundle\Entity\Lead');

        switch ($key) {
            case 'Jerry Coleman':
                $entity->setName('Oro Inc. Opportunity Name');
                $entity->setCustomer($customerRepo->getEntity('Coleman'));
                $entity->setCreatedAt(new \DateTime());
                $entity->setUpdatedAt(new \DateTime());
                $entity->setOwner($userRepo->getEntity('John Doo'));
                $entity->setBudgetAmount(1000000);
                $entity->setContact($contactRepo->getEntity('Jerry Coleman'));
                $entity->setLead($leadRepo->getEntity('Jerry Coleman'));
                $entity->setStatus(new OpportunityStatus('In Progress'));

                return;
        }

        parent::fillEntityData($key, $entity);
    }
}
