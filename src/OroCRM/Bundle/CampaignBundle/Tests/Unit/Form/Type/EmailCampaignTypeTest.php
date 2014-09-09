<?php

namespace OroCRM\Bundle\CampaignBundle\Tests\Unit\Form\Type;

use OroCRM\Bundle\CampaignBundle\Form\Type\EmailCampaignType;

class EmailCampaignTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var EmailCampaignType */
    protected $type;

    protected function setUp()
    {
        $subscriber = $this
            ->getMockBuilder('Oro\Bundle\EmailBundle\Form\EventListener\BuildTemplateFormSubscriber')
            ->disableOriginalConstructor()
            ->getMock();

        $this->type = new EmailCampaignType($subscriber);
    }

    protected function tearDown()
    {
        unset($this->type);
    }

    public function testAddEntityFields()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->exactly(8))
            ->method('add')
            ->with($this->isType('string'), $this->isType('string'))
            ->will($this->returnSelf());

        $this->type->buildForm($builder, []);
    }

    public function testName()
    {
        $typeName = $this->type->getName();
        $this->assertInternalType('string', $typeName);
        $this->assertSame('orocrm_email_campaign', $typeName);
    }

    public function testSetDefaultOptions()
    {
        $resolver = $this->getMock('Symfony\Component\OptionsResolver\OptionsResolverInterface');
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => 'OroCRM\Bundle\CampaignBundle\Entity\EmailCampaign']);

        $this->type->setDefaultOptions($resolver);
    }

    public function testAddProvider()
    {
        $subscriber = $this->getMock('Symfony\Component\EventDispatcher\EventSubscriberInterface');

        $this->type->addSubscriber($subscriber);
    }
}
