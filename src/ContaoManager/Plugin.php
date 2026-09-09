<?php

declare(strict_types=1);

/*
 * This file is part of the img-navi bundle.
 *
 * (c) Mathias Ebert
 *
 * @license LGPL-3.0-or-later
 */

namespace Tonsinn\ImgNaviBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Tonsinn\ImgNaviBundle\TonsinnImgNaviBundle;

/**
 * Plugin for the Contao Manager.
 */
class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(TonsinnImgNaviBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
