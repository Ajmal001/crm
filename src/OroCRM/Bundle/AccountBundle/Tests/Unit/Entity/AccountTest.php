<?php

namespace OroCRM\Bundle\AccountBundle\Tests\Unit\Entity;

use OroCRM\Bundle\AccountBundle\Entity\Account;
use OroCRM\Bundle\ContactBundle\Entity\Contact;

use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

class AccountTest extends \PHPUnit_Framework_TestCase
{
    public function testGettersSetters()
    {
        $entity = new Account();
        $entity->setName('Test');

        $this->assertEquals('Test', $entity->getName());
        $this->assertEquals('Test', (string)$entity);

        $organization = new Organization();
        $this->assertNull($entity->getOrganization());
        $entity->setOrganization($organization);
        $this->assertSame($organization, $entity->getOrganization());

    }

    public function testBeforeSave()
    {
        $entity = new Account();
        $entity->beforeSave();
        $this->assertInstanceOf('\DateTime', $entity->getCreatedAt());
    }

    public function testDoPreUpdate()
    {
        $entity = new Account();
        $entity->doPreUpdate();
        $this->assertInstanceOf('\DateTime', $entity->getUpdatedAt());
    }

    public function testAddContact()
    {
        $account = new Account();
        $account->setId(1);

        $contact = new Contact();
        $contact->setId(2);

        $this->assertEmpty($account->getContacts()->toArray());

        $account->addContact($contact);
        $actualContacts = $account->getContacts()->toArray();
        $this->assertCount(1, $actualContacts);
        $this->assertEquals($contact, current($actualContacts));
    }

    public function testAddContactShouldSetDefaultContactIfNotSetAlready()
    {
        $account = new Account();

        // guards
        $this->assertTrue($account->getContacts()->isEmpty());
        $this->assertNull($account->getDefaultContact());

        $newContact = new Contact();
        $account->addContact($newContact);
        $this->assertCount(1, $account->getContacts());
        $this->assertSame($newContact, $account->getContacts()->first());
        $this->assertSame($newContact, $account->getDefaultContact());
    }

    public function testAddContactsShouldNotOverwriteExistingDefaultContact()
    {
        $account = new Account();
        $existingContact = new Contact();
        $account->addContact($existingContact);

        // guard
        $this->assertSame($existingContact, $account->getDefaultContact());

        $account->addContact(new Contact());
        $this->assertSame($existingContact, $account->getDefaultContact());
    }

    public function testRemoveContact()
    {
        $account = new Account();
        $account->setId(1);

        $contact = new Contact();
        $contact->setId(2);

        $account->addContact($contact);
        $this->assertCount(1, $account->getContacts()->toArray());

        $account->removeContact($contact);
        $this->assertEmpty($account->getContacts()->toArray());
    }

    public function testOwners()
    {
        $entity = new Account();
        $user = new User();

        $this->assertEmpty($entity->getOwner());

        $entity->setOwner($user);

        $this->assertEquals($user, $entity->getOwner());
    }

    public function testGetEmail()
    {
        $account = new Account();
        $contact = $this->getMockBuilder('OroCRM\Bundle\ContactBundle\Entity\Contact')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertNull($account->getEmail());

        $account->setDefaultContact($contact);
        $contact->expects($this->once())
            ->method('getEmail')
            ->will($this->returnValue('email@example.com'));
        $this->assertEquals('email@example.com', $account->getEmail());
    }
}
