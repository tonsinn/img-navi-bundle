<?php

declare(strict_types=1);

/*
 * This file is part of the img-navi bundle.
 *
 * (c) Mathias Ebert
 *
 * @license LGPL-3.0-or-later
 */

namespace Tonsinn\ImgNaviBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Input;
use Contao\System;
use Tonsinn\ImgNaviBundle\Controller\ContentElement\ImgNaviItemController;

/**
 * Adjusts the reused core fields when editing an "img_navi_item" element:
 *
 *  - "text" and "url" are optional here (the core marks them mandatory for the
 *    text and hyperlink elements)
 *  - "headline" is mandatory, because it doubles as the collapsed panel label
 *  - "size" is mandatory, otherwise the unresized original image would be
 *    preloaded, which defeats the purpose of the preload hints
 *  - the file picker is limited to image files
 */
#[AsCallback(table: 'tl_content', target: 'config.onload')]
class ImgNaviItemListener
{
    public function __invoke(DataContainer|null $dc = null): void
    {
        if (!$dc || !$dc->id || 'edit' !== Input::get('act')) {
            return;
        }

        $record = $dc->getCurrentRecord();

        if (ImgNaviItemController::TYPE !== ($record['type'] ?? null)) {
            return;
        }

        $fields = &$GLOBALS['TL_DCA']['tl_content']['fields'];

        $fields['text']['eval']['mandatory'] = false;
        $fields['url']['eval']['mandatory'] = false;
        $fields['headline']['eval']['mandatory'] = true;
        $fields['size']['eval']['mandatory'] = true;

        $container = System::getContainer();

        if ($container->hasParameter('contao.image.valid_extensions')) {
            $extensions = (array) $container->getParameter('contao.image.valid_extensions');
            $fields['singleSRC']['eval']['extensions'] = implode(',', $extensions);
        }
    }
}
