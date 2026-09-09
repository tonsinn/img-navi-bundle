<?php

declare(strict_types=1);

/*
 * This file is part of the img-navi bundle.
 *
 * (c) Mathias Ebert
 *
 * @license LGPL-3.0-or-later
 */

use Tonsinn\ImgNaviBundle\Controller\ContentElement\ImgNaviController;
use Tonsinn\ImgNaviBundle\Controller\ContentElement\ImgNaviItemController;

/*
 * Palettes
 *
 * The element types themselves are registered automatically through the
 * #[AsContentElement] attributes, so only the palettes and the two wrapper
 * fields have to be declared here.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes'][ImgNaviController::TYPE] =
    '{type_legend},type,headline,title;'
    .'{imgnavi_legend},imgNaviLayout,imgNaviHeight,imgNaviInitial,imgNaviSticky;'
    .'{template_legend:hide},customTpl;'
    .'{protected_legend:hide},protected;'
    .'{expert_legend:hide},cssID;'
    .'{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes'][ImgNaviItemController::TYPE] =
    '{type_legend},type,headline,title;'
    .'{image_legend},singleSRC,size,overwriteMeta;'
    .'{text_legend},text;'
    .'{link_legend},url,target,linkTitle,titleText;'
    .'{template_legend:hide},customTpl;'
    .'{protected_legend:hide},protected;'
    .'{expert_legend:hide},cssID;'
    .'{invisible_legend:hide},invisible,start,stop';

/*
 * Fields
 *
 * Only the wrapper needs its own columns; the panel reuses core fields
 * (singleSRC, size, overwriteMeta, headline, text, url, target, linkTitle,
 * titleText) and therefore does not add any columns.
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['imgNaviLayout'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['imgNaviLayout'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => ImgNaviController::LAYOUTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['imgNaviLayoutOptions'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => "varchar(16) COLLATE ascii_bin NOT NULL default 'horizontal'",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['imgNaviInitial'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['imgNaviInitial'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => ImgNaviController::INITIAL_OPTIONS,
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql' => "smallint(5) unsigned NOT NULL default 0",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['imgNaviSticky'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['imgNaviSticky'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['imgNaviHeight'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['imgNaviHeight'],
    'exclude' => true,
    'inputType' => 'inputUnit',
    'options' => ImgNaviController::HEIGHT_UNITS,
    'eval' => ['rgxp' => 'natural', 'maxlength' => 5, 'tl_class' => 'w50'],
    'sql' => "varchar(64) NOT NULL default 'a:2:{s:5:\"value\";s:3:\"600\";s:4:\"unit\";s:2:\"px\";}'",
];
