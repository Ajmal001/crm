<?php

namespace Oro\Bundle\MagentoBundle\Test\Unit\Handler;

use Symfony\Component\HttpFoundation\Request;

use Oro\Bundle\IntegrationBundle\Manager\TypesRegistry;
use Oro\Bundle\MagentoBundle\Provider\ConnectorChoicesProvider;
use Oro\Bundle\MagentoBundle\Provider\TransportEntityProvider;
use Oro\Bundle\MagentoBundle\Provider\WebsiteChoicesProvider;
use Oro\Bundle\MagentoBundle\Provider\Transport\MagentoTransportInterface;
use Oro\Bundle\MagentoBundle\Entity\MagentoSoapTransport;

use Oro\Bundle\MagentoBundle\Handler\TransportHandler;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TransportHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var TransportHandler */
    protected $transportHandler;

    /** @var  TypesRegistry|\PHPUnit_Framework_MockObject_MockObject */
    protected $typesRegistry;

    /** @var  TransportEntityProvider|\PHPUnit_Framework_MockObject_MockObject */
    protected $transportEntityProvider;

    /** @var  WebsiteChoicesProvider|\PHPUnit_Framework_MockObject_MockObject */
    protected $websiteChoicesProvider;

    /** @var ConnectorChoicesProvider|\PHPUnit_Framework_MockObject_MockObject  */
    protected $connectorChoicesProvider;

    /** @var  Request|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /** @var  MagentoTransportInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $magentoTransport;

    /** @var  MagentoSoapTransport|\PHPUnit_Framework_MockObject_MockObject */
    protected $transportEntity;

    public function setUp()
    {
        $this->typesRegistry            = $this->createMock(TypesRegistry::class);
        $this->transportEntityProvider  = $this->createMock(TransportEntityProvider::class);
        $this->websiteChoicesProvider   = $this->createMock(WebsiteChoicesProvider::class);
        $this->connectorChoicesProvider = $this->createMock(ConnectorChoicesProvider::class);
        $this->magentoTransport         = $this->createMock(MagentoTransportInterface::class);
        $this->request                  = $this->createMock(Request::class);

        $this->transportEntity          = $this->createMock(MagentoSoapTransport::class);

        $this->transportHandler = new TransportHandler(
            $this->typesRegistry,
            $this->transportEntityProvider,
            $this->websiteChoicesProvider,
            $this->connectorChoicesProvider,
            $this->request
        );
    }

    public function tearDown()
    {
        parent::tearDown();

        unset(
            $this->typesRegistry,
            $this->transportEntityProvider,
            $this->websiteChoicesProvider,
            $this->connectorChoicesProvider,
            $this->magentoTransport,
            $this->request,
            $this->transportHandler
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetCheckResponse()
    {
        $this->request
            ->expects($this->atLeastOnce())
            ->method('get')
            ->will(
                $this->returnCallback(
                    function ($argument) {
                        switch ($argument) {
                            case TransportHandler::TRANSPORT_TYPE:
                                return 'magento_soap';
                            case TransportHandler::INTEGRATION_TYPE:
                                return 'magento';
                            case TransportEntityProvider::ENTITY_ID:
                                return 1;
                            default:
                                return null;
                        }
                    }
                )
            );

        $this->connectorChoicesProvider
             ->expects($this->once())
             ->method('getAllowedConnectorsChoices')
             ->willReturn(['test' => 'test']);

        $this->websiteChoicesProvider
             ->expects($this->once())
             ->method('formatWebsiteChoices')
             ->willReturn([
                 [
                     'id' => -1,
                     'label' => null
                 ]
             ]);

        $this->transportEntityProvider
             ->expects($this->once())
             ->method('getTransportEntityByRequest')
             ->willReturn($this->transportEntity);

        $this->magentoTransport
             ->expects($this->once())
             ->method('init')
             ->willReturn($this->returnSelf());

        $this->magentoTransport
             ->expects($this->once())
             ->method('getExtensionVersion')
             ->willReturn('1.0.1');

        $this->magentoTransport
             ->expects($this->once())
             ->method('getMagentoVersion')
             ->willReturn('1.9.3.1');

        $this->magentoTransport
             ->expects($this->once())
             ->method('getRequiredExtensionVersion')
             ->willReturn('1.0.1');

        $this->magentoTransport
            ->expects($this->once())
            ->method('isSupportedExtensionVersion')
            ->willReturn(true);

        $this->magentoTransport
             ->expects($this->once())
             ->method('getAdminUrl')
             ->willReturn('/admin');

        $this->typesRegistry
             ->expects($this->once())
             ->method('getTransportType')
             ->willReturn($this->magentoTransport);

        $response = $this->transportHandler->getCheckResponse();

        $expected = [
            'success' => true,
            'websites' => [
                [
                    'id' => -1,
                    'label' => null
                ]
            ],
            'isExtensionInstalled' => true,
            'magentoVersion' => '1.9.3.1',
            'extensionVersion' => '1.0.1',
            'requiredExtensionVersion' => '1.0.1',
            'isSupportedVersion' => true,
            'connectors' => [
                'test' => 'test'
            ],
            'adminUrl' => '/admin',
        ];

        $this->assertEquals($expected, $response);
    }
}
