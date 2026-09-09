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

    public const MAX_BORDER_WIDTH = 40;

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

        // Rahmen und Farben werden als CSS-Custom-Properties am Wrapper gesetzt.
        // Ein leerer Wert lässt die Property weg, sodass der Standard aus
        // img-navi.css greift (HtmlAttributes::addStyle entfernt leere Werte).
        $template->set('border_width', $this->getBorderWidth($model->imgNaviBorderWidth));
        $template->set('border_color', $this->getColor($model->imgNaviBorderColor));
        $template->set('headline_color', $this->getColor($model->imgNaviHeadlineColor));
        $buttonColor = $this->getColor($model->imgNaviButtonColor);
        $template->set('button_color', $buttonColor);
        $template->set('button_color_hover', $this->darken($buttonColor, 0.8));
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
     * Turns a hex value stored without a leading hash into a CSS color.
     * Returns an empty string for anything that is not a valid hex color, so
     * the stylesheet default stays in place.
     */
    private function getColor(mixed $value): string
    {
        $hex = ltrim(trim((string) $value), '#');

        if (!preg_match('/^(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $hex)) {
            return '';
        }

        return '#'.$hex;
    }

    /**
     * Derives the hover colour from the button colour so editors only have to
     * pick one. Computed here rather than with color-mix() in CSS, which would
     * silently produce an invalid value on older browsers.
     */
    private function darken(string $color, float $factor): string
    {
        if ('' === $color) {
            return '';
        }

        $hex = ltrim($color, '#');

        if (3 === \strlen($hex)) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $channels = array_map(
            static fn (string $part): int => max(0, min(255, (int) round(hexdec($part) * $factor))),
            str_split($hex, 2),
        );

        return vsprintf('#%02x%02x%02x', $channels);
    }

    /**
     * Border width in pixels; 0 disables the border.
     */
    private function getBorderWidth(mixed $value): string
    {
        if ('' === trim((string) $value)) {
            return '';
        }

        $width = (int) $value;

        if ($width < 0) {
            $width = 0;
        } elseif ($width > self::MAX_BORDER_WIDTH) {
            $width = self::MAX_BORDER_WIDTH;
        }

        return $width.'px';
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
