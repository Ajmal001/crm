<?php

namespace OroCRM\Bundle\ChannelBundle\Tests\Unit\Entity;

class ChannelIdentityTest extends AbstractEntityTestCase
{
    /**
     * {@inheritDoc}
     */
    public function getEntityFQCN()
    {
        return 'OroCRM\Bundle\ChannelBundle\Entity\ChannelIdentity';
    }

    /**
     * {@inheritDoc}
     */
    public function getDataProvider()
    {
        $name       = 'Some name';
        $account    = $this->getMock('Oro\Bundle\AccountBundle\Entity\Account');
        $contact    = $this->getMock('Oro\Bundle\ContactBundle\Entity\Contact');
        $channel    = $this->getMock('Oro\Bundle\ChannelBundle\Entity\Channel');
        $owner      = $this->getMock('Oro\Bundle\UserBundle\Entity\User');

        $someDateTime     = new \DateTime();

        return [
            'name'      => ['name', $name, $name],
            'owner'     => ['owner', $owner, $owner],
            'account'   => ['account', $account, $account],
            'contact'   => ['contact', $contact, $contact],
            'channel'   => ['channel', $channel, $channel],
            'createdAt' => ['createdAt', $someDateTime, $someDateTime],
            'updatedAt' => ['updatedAt', $someDateTime, $someDateTime]
        ];
    }

    public function testPrePersist()
    {
        $this->assertNull($this->entity->getCreatedAt());

        $this->entity->prePersist();

        $this->assertInstanceOf('DateTime', $this->entity->getCreatedAt());
        $this->assertLessThan(3, $this->entity->getCreatedAt()->diff(new \DateTime())->s);
    }

    public function testPreUpdate()
    {
        $this->assertNull($this->entity->getUpdatedAt());

        $this->entity->preUpdate();

        $this->assertInstanceOf('DateTime', $this->entity->getUpdatedAt());
        $this->assertLessThan(3, $this->entity->getUpdatedAt()->diff(new \DateTime())->s);
    }
}
