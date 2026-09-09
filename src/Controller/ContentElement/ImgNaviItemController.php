<?php

declare(strict_types=1);

/*
 * This file is part of the img-navi bundle.
 *
 * (c) Mathias Ebert
 *
 * @license LGPL-3.0-or-later
 */

namespace Tonsinn\ImgNaviBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\String\HtmlAttributes;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A single panel of the image navigation: background image, headline, rich text
 * and a call-to-action link. Only core tl_content fields are reused, so this
 * element does not add any database columns of its own.
 */
#[AsContentElement(self::TYPE, category: 'miscellaneous')]
class ImgNaviItemController extends AbstractContentElementController
{
    public const TYPE = 'img_navi_item';

    public function __construct(
        private readonly Studio $studio,
        private readonly InsertTagParser $insertTagParser,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        // The panel has its own call-to-action, so the image is rendered without
        // a link and without a lightbox (see the picture component in the template).
        $figure = $this->studio
            ->createFigureBuilder()
            ->fromUuid($model->singleSRC ?: '')
            ->setSize($model->size)
            ->setOverwriteMetadata($model->getOverwriteMetadata())
            ->buildIfResourceExists()
        ;

        $template->set('image', $figure);

        // Link handling follows the core hyperlink element
        $href = trim($this->insertTagParser->replaceInline($model->url ?? ''));

        if ('' !== $href && Validator::isRelativeUrl($href)) {
            $href = $request->getBasePath().'/'.$href;
        }

        $linkAttributes = new HtmlAttributes();

        if ('' !== $href) {
            $titleText = $this->insertTagParser->replaceInline($model->titleText ?? '');

            $linkAttributes
                ->set('href', $href)
                ->setIfExists('title', $titleText)
            ;

            if ($model->target) {
                $linkAttributes
                    ->set('target', '_blank')
                    ->set('rel', 'noreferrer noopener')
                ;
            }
        }

        $template->set('href', $href);
        $template->set('link_attributes', $linkAttributes);
        $template->set('link_text', (string) $model->linkTitle);
        $template->set('text', (string) $model->text);

        return $template->getResponse();
    }
}
