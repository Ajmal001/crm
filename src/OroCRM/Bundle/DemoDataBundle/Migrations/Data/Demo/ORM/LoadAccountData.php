<?php
namespace OroCRM\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use OroCRM\Bundle\AccountBundle\Entity\Account;

class LoadAccountData extends AbstractDemoFixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'OroCRM\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM\LoadUsersData'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle  = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'dictionaries' . DIRECTORY_SEPARATOR . "accounts.csv", "r");
        $headers = fgetcsv($handle, 1000, ",");

        $companies = [];

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $data = array_combine($headers, array_values($data));

            if (!isset($companies[$data['Company']])) {
                $account = new Account();
                $account->setName($data['Company']);
                $account->setOwner($this->getRandomUser());

                $this->em->persist($account);

                $companies[$data['Company']] = $data['Company'];
            }
        }
        fclose($handle);

        $this->em->flush();
    }
}
