<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxServerProcessor;

/**
 * @covers oxServerProcessor
 */
class ServerProcessorTest extends \OxidTestCase
{
    public function testNodeInformationNotUpdatedIfNotNeed()
    {
        /** @var \OxidEsales\Eshop\Core\ApplicationServer $oNode */
        $oNode = $this->getMock('\OxidEsales\Eshop\Core\ApplicationServer');
        // Test that check is called with object got from server node manager.
        $oNode->expects($this->any())->method('needToUpdate')->will($this->returnValue(false));

        /** @var \OxidEsales\Eshop\Core\Service\ApplicationServerService $oApplicationServerService */
        $oApplicationServerService = $this->getApplicationServerServiceMock($oNode);

        $oUtilsServer = $this->getMock('oxUtilsServer');
        $oUtilsDate = $this->getMock('oxUtilsDate');

        $oServerNodesProcessor = new oxServerProcessor($oApplicationServerService, $oUtilsServer, $oUtilsDate);
        $oServerNodesProcessor->process();
    }

    public function providerNodeInformationUpdatedWhenNeed()
    {
        $sCurrentTime = '14000000000000';
        $sIP = '192.168.1.7';
        $sServerId = 'a45sdas5d4as564d56asd4';

        $oNode = oxNew('oxApplicationServer');
        $oNode->setId($sServerId);
        $oNode->setIp($sIP);
        $oNode->setTimestamp($sCurrentTime);
        $oNode->setIsValid();

        $oNodeFrontend = clone($oNode);
        $oNodeFrontend->setLastFrontendUsage($sCurrentTime);

        $oNodeAdmin = clone($oNode);
        $oNodeAdmin->setLastAdminUsage($sCurrentTime);

        return array(
            array(false, $oNodeFrontend, $sServerId, $sCurrentTime, $sIP),
            array(true, $oNodeAdmin, $sServerId, $sCurrentTime, $sIP),
        );
    }

    /**
     * @param bool                $blAdmin
     * @param oxApplicationServer $oExpectedNode
     * @param string              $sServerId
     * @param string              $sCurrentTime
     * @param string              $sIP
     *
     * @dataProvider providerNodeInformationUpdatedWhenNeed
     */
    public function testNodeInformationUpdatedWhenNeed($blAdmin, $oExpectedNode, $sServerId, $sCurrentTime, $sIP)
    {
        $this->setAdminMode($blAdmin);

        /** @var \OxidEsales\Eshop\Core\ApplicationServer $oNode */
        $oNode = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);

        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $databaseProvider = oxNew(\OxidEsales\Eshop\Core\DatabaseProvider::class);
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
        $service = $this->getMock(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,
            array("loadAppServer", "saveAppServer"),
            array($appServerDao, \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime()));
        // Test that processor do not update node information if not needed.
        $service->expects($this->any())->method('loadAppServer')->will($this->returnValue($oNode));
        $service->expects($this->any())->method('saveAppServer')->with($this->equalTo($oExpectedNode));

        $oUtilsServer = $this->getMock('oxUtilsServer');
        $oUtilsServer->expects($this->any())->method('getServerIp')->will($this->returnValue($sIP));
        $oUtilsServer->expects($this->any())->method('getServerNodeId')->will($this->returnValue($sServerId));

        $oUtilsDate = $this->getMock('oxUtilsDate');
        $oUtilsDate->expects($this->any())->method('getTime')->will($this->returnValue($sCurrentTime));

        $oServerNodesProcessor = new oxServerProcessor($service, $oUtilsServer, $oUtilsDate);
        $oServerNodesProcessor->process();
    }

    /**
     * @param array $appServerList An array of application servers to return.
     *
     * @return \OxidEsales\Eshop\Core\Service\ApplicationServerService
     */
    private function getApplicationServerServiceMock($oNode)
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $databaseProvider = oxNew(\OxidEsales\Eshop\Core\DatabaseProvider::class);
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
        $service = $this->getMock(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,
            array("loadAppServer", "saveAppServer"),
            array($appServerDao, \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime()));
        // Test that processor do not update node information if not needed.
        $service->expects($this->any())->method('loadAppServer')->will($this->returnValue($oNode));
        $service->expects($this->any())->method('saveAppServer');

        return $service;
    }

}
