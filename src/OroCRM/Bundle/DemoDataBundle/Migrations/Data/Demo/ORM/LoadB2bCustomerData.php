<?php

namespace OroCRM\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Oro\Bundle\AddressBundle\Entity\Address;

use OroCRM\Bundle\ChannelBundle\Entity\Channel;
use OroCRM\Bundle\SalesBundle\Entity\B2bCustomer;

class LoadB2bCustomerData extends AbstractDemoFixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'OroCRM\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM\LoadUsersData',
            'OroCRM\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM\LoadAccountData',
            'OroCRM\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM\LoadChannelData',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle  = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'dictionaries' . DIRECTORY_SEPARATOR . "accounts.csv", "r");
        $headers = fgetcsv($handle, 1000, ",");

        $companies          = [];
        $customersPersisted = 0;
        $channel            = $this->getChannel();

        while (($data = fgetcsv($handle, 1000, ",")) !== false && $customersPersisted < 25) {
            $data = array_combine($headers, array_values($data));

            if (!isset($companies[$data['Company']])) {
                $customer = $this->createCustomer($data, $channel);

                $this->em->persist($customer);

                $companies[$data['Company']] = $data['Company'];
                $customersPersisted++;
            }
        }
        fclose($handle);

        $this->em->flush();
    }

    /**
     * @param array   $data
     * @param Channel $channel
     *
     * @return B2bCustomer
     */
    protected function createCustomer($data, Channel $channel = null)
    {
        $address  = new Address();
        $customer = new B2bCustomer();

        $customer->setName($data['Company']);
        $customer->setOwner($this->getRandomUserReference());

        $address->setCity($data['City']);
        $address->setStreet($data['StreetAddress']);
        $address->setPostalCode($data['ZipCode']);
        $address->setFirstName($data['GivenName']);
        $address->setLastName($data['Surname']);

        $address->setCountry($this->getCountryReference($data['Country']));
        $address->setRegion($this->getRegionReference($data['Country'], $data['State']));

        $customer->setShippingAddress($address);
        $customer->setBillingAddress(clone $address);

        if ($channel) {
            $customer->setDataChannel($channel);
        }

        return $customer;
    }

    /**
     * @return null|Channel
     */
    private function getChannel()
    {
        if ($this->hasReference('default_channel')) {
            return $this->getReference('default_channel');
        } else {
            return $this->em->getRepository('OroCRMChannelBundle:Channel')->createQueryBuilder('c')
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();
        }
    }
}
