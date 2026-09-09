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
use Contao\CoreBundle\Fragment\Reference\ContentElementReference;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Wrapper element of the image navigation. It renders between MIN_ITEMS and
 * MAX_ITEMS nested "img_navi_item" panels side by side (or stacked) and passes
 * the panel index, the panel count and the layout down to each child fragment.
 */
#[AsContentElement(
    self::TYPE,
    category: 'miscellaneous',
    nestedFragments: ['allowedTypes' => [ImgNaviItemController::TYPE]],
)]
class ImgNaviController extends AbstractContentElementController
{
    public const TYPE = 'img_navi';

    public const MIN_ITEMS = 2;

    public const MAX_ITEMS = 6;

    public const LAYOUTS = ['horizontal', 'vertical'];

    /**
     * Orientation of the panel headline while the panel is collapsed.
     */
    public const LABEL_LAYOUTS = ['vertical', 'horizontal'];

    public const HEIGHT_UNITS = ['px', 'vh', 'rem'];

    public const DEFAULT_HEIGHT = '600px';

    /**
     * Panel numbers selectable as the initially opened panel.
     */
    public const INITIAL_OPTIONS = ['1', '2', '3', '4', '5', '6'];

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        /** @var array<ContentElementReference> $references */
        $references = $template->get('nested_fragments');
        $total = \count($references);

        // Never render more than MAX_ITEMS panels
        $references = \array_slice($references, 0, self::MAX_ITEMS);
        $count = \count($references);

        $layout = \in_array($model->imgNaviLayout, self::LAYOUTS, true) ? $model->imgNaviLayout : self::LAYOUTS[0];

        $labelLayout = \in_array($model->imgNaviLabel, self::LABEL_LAYOUTS, true)
            ? $model->imgNaviLabel
            : self::LABEL_LAYOUTS[0];

        // Panel that is already open when the page loads (0 = none). Numbers
        // beyond the actual panel count are ignored rather than silently clamped.
        $initial = (int) $model->imgNaviInitial;

        if ($initial < 1 || $initial > $count) {
            $initial = 0;
        }

        // Pass the panel context down to the children (available as "properties.imgnav")
        foreach ($references as $index => $reference) {
            $properties = $reference->attributes['templateProperties'] ?? [];
            $properties['imgnav'] = [
                'index' => $index,
                'count' => $count,
                'layout' => $layout,
                'initial' => $initial,
            ];

            $reference->attributes['templateProperties'] = $properties;
        }

        $template->set('nested_fragments', $references);
        $template->set('layout', $layout);
        $template->set('label_layout', $labelLayout);
        $template->set('height', $this->getHeight($model->imgNaviHeight));
        $template->set('count', $count);
        $template->set('initial', $initial);
        $template->set('sticky', (bool) $model->imgNaviSticky);
        $template->set('count_total', $total);
        $template->set('count_valid', $total >= self::MIN_ITEMS && $total <= self::MAX_ITEMS);
        $template->set('min_items', self::MIN_ITEMS);
        $template->set('max_items', self::MAX_ITEMS);

        return $template->getResponse();
    }

    /**
     * Turns the serialized inputUnit value into a CSS length such as "600px".
     */
    private function getHeight(mixed $value): string
    {
        $data = StringUtil::deserialize($value, true);
        $number = (int) ($data['value'] ?? 0);
        $unit = (string) ($data['unit'] ?? '');

        if ($number < 1 || !\in_array($unit, self::HEIGHT_UNITS, true)) {
            return self::DEFAULT_HEIGHT;
        }

        return $number.$unit;
    }
}
