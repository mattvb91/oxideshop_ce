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

/**
 * Metadata version
 */
$sMetadataVersion = '1.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'virtualnamespace_extending_3_classes',
    'title'        => 'Test extending 3 shop classes',
    'description'  => 'Module testing extending 3 shop classes',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend'       => array(
        \OxidEsales\Eshop\Application\Model\Article::class => \virtualnamespace_extending_3_classes\MyArticle::class,
        \OxidEsales\Eshop\Application\Model\Order::class => \virtualnamespace_extending_3_classes\MyOrder::class,
        \OxidEsales\Eshop\Application\Model\User::class => \virtualnamespace_extending_3_classes\MyUser::class,
    )
);
