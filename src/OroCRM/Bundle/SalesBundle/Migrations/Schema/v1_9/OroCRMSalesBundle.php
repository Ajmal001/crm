<?php

namespace OroCRM\Bundle\SalesBundle\Migrations\Schema\v1_9;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\SecurityBundle\Migrations\Schema\UpdateOwnershipTypeQuery;

class OroCRMSalesBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        self::addOrganization($schema);
        //Add organization fields to ownership entity config
        $queries->addQuery(
            new UpdateOwnershipTypeQuery(
                'OroCRM\Bundle\SalesBundle\Entity\Lead',
                [
                    'organization_field_name' => 'organization',
                    'organization_column_name' => 'organization_id'
                ]
            )
        );
        //Add organization fields to ownership entity config
        $queries->addQuery(
            new UpdateOwnershipTypeQuery(
                'OroCRM\Bundle\SalesBundle\Entity\Opportunity',
                [
                    'organization_field_name' => 'organization',
                    'organization_column_name' => 'organization_id'
                ]
            )
        );
    }

    /**
     * Adds organization_id field
     *
     * @param Schema $schema
     */
    public static function addOrganization(Schema $schema)
    {
        $table = $schema->getTable('orocrm_sales_lead');
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addIndex(['organization_id'], 'IDX_73DB463332C8A3DE', []);
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );

        $table = $schema->getTable('orocrm_sales_opportunity');
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addIndex(['organization_id'], 'IDX_C0FE4AAC32C8A3DE', []);
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }
}
