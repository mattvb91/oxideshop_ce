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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Http;

/**
 * Testing that the .htaccess rules are as expected.
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Http
 */
class HtAccessTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * Assure, that we get an HTTP code 301 for redirected file extensions.
     */
    public function testHtAccessRewrite301ForRedirectedFileExtensions()
    {
        $response = $this->callCurl('/file.someExtension');

        $this->assertHttpCode301($response, 'All files with a not defined extension are redirected with code "301 Moved Permanently"');
    }

    /**
     * Assure, that we get no HTTP code 301 for not redirected file extensions.
     *
     * @dataProvider dataProviderTestHtAccessRewriteForFileExtensions
     *
     * @param string $fileExtension The extension of the file we want to check right now.
     * @param string $message       The message we want to show, if the cURL response is a 301.
     */
    public function testHtAccessRewrite301ForNotRedirectedFileExtensions($fileExtension, $message)
    {
        $response = $this->callCurl('/file.' . $fileExtension);

        $this->assertNoHttpCode301($message, $response);
    }

    public function dataProviderTestHtAccessRewriteForFileExtensions()
    {
        return [
            ['html', 'html files are not redirected with 301'],
            ['jpg', 'jpg files are not redirected with 301'],
            ['jpeg', 'jpeg files are not redirected with 301'],
            ['css', 'css files are not redirected with 301'],
            ['pdf', 'pdf files are not redirected with 301'],
            ['doc', 'doc files are not redirected with 301'],
            ['gif', 'gif files are not redirected with 301'],
            ['png', 'png files are not redirected with 301'],
            ['js', 'js files are not redirected with 301'],
            ['htc', 'htc files are not redirected with 301'],
            ['svg', 'svg files are not redirected with 301'],
            ['HTML', 'HTML FILES ARE NOT REDIRECTED WITH 301'],
            ['JPG', 'JPG FILES ARE NOT REDIRECTED WITH 301'],
            ['JPEG', 'JPEG FILES ARE NOT REDIRECTED WITH 301'],
            ['CSS', 'CSS FILES ARE NOT REDIRECTED WITH 301'],
            ['PDF', 'PDF FILES ARE NOT REDIRECTED WITH 301'],
            ['DOC', 'DOC FILES ARE NOT REDIRECTED WITH 301'],
            ['GIF', 'GIF FILES ARE NOT REDIRECTED WITH 301'],
            ['PNG', 'PNG FILES ARE NOT REDIRECTED WITH 301'],
            ['JS', 'JS FILES ARE NOT REDIRECTED WITH 301'],
            ['HTC', 'HTC FILES ARE NOT REDIRECTED WITH 301'],
            ['SVG', 'SVG FILES ARE NOT REDIRECTED WITH 301'],
        ];
    }

    /**
     * Call an OXID eShop file URL over the shell cURL command. Assure, that the cURL command didn't failed.
     *
     * @param string $fileUrlPart The URL part pointing to the file we want to get over cURL.
     *
     * @return string The response of the cURL call.
     */
    protected function callCurl($fileUrlPart)
    {
        $url = $this->getConfig()->getShopUrl(1) . $fileUrlPart;
        $command = 'curl -I -s ' . $url;
        $response = shell_exec($command);

        $this->assertNotNull($response, 'This command failed to execute: ' . $command);

        return $response;
    }

    /**
     * Assure, that the given cURL response is a HTTP code 301.
     *
     * @param string $response The response of the cURL call.
     * @param string $message  The message we show, if the given response is not a HTTP code 301.
     */
    protected function assertHttpCode301($response, $message)
    {
        $this->assertContains('301 Moved Permanently', $response, $message);
    }

    /**
     * Assure, that the given cURL response isn't a HTTP code 301.
     *
     * @param string $response The response of the cURL call.
     * @param string $message  The message we show, if the given response is a HTTP code 301.
     */
    protected function assertNoHttpCode301($response, $message)
    {
        $this->assertNotContains('301 Moved Permanently', $response, $message);
    }
}
