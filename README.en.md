# Contao Image Navigation (img-navi-bundle)

*[Deutsch](README.md) | English*

Animated image navigation for Contao 5 and 6: several image panels sit side
by side; hovering the targeted panel expands it and reveals a heading, short
text, and a button.

## Installation

Search for `tonsinn/img-navi-bundle` in the Contao Manager and install it, or
on the console:

```bash
composer require tonsinn/img-navi-bundle
```

Afterwards, **update the database** (Contao Manager → Maintenance, or
`vendor/bin/contao-console contao:migrate`). The bundle adds two new columns
to `tl_content`.

## Requirements

- PHP 8.2 to 8.5
- Contao 5.3 or newer, including Contao 6 (tested with 5.7.13 and 6.0.0)
- Works with both Twig page layouts and the classic `fe_page`

## Usage

1. In the article, create a new content element of type **Image navigation**
   (group "Miscellaneous elements") and set the layout, height, and opening
   behavior there.
2. In the element list, open the **Child elements** operation on the frame
   element and create 2 to 6 elements of type **Image navigation panel**
   there.

### Fields of the image navigation

| Field | Meaning |
|---|---|
| Layout | Panels side by side (horizontal) or stacked (vertical) |
| Height | Total height in px, vh, or rem (default 600px) |
| Heading in collapsed state | Rotated vertically (default) or horizontal with line wrapping inside the visible strip. Only takes effect for horizontal layout from 768px upward — stacked panels show the heading horizontally and wrapping regardless |
| Initially opened panel | Number of the panel that is already open on load. Hovering over another panel switches the display there; leaving it returns here. Empty = all panels start closed |
| Keep panel open | The last opened panel stays open when the mouse leaves the navigation |
| Border width | Width of the border around each panel in pixels (default 2, `0` = no border) |
| Border color | Color of the border. Empty = default (white) |
| Heading color | Applies to the heading in the expanded panel **and** the label in the collapsed state. A color chosen here also overrides the theme's heading rules. Empty = default (white) |
| Button color | Background color of the buttons; the hover color is derived from it (20% darker). Empty = default (blue) |

Both options can be combined: with a start panel *and* "keep open," exactly
one panel is always open — the chosen one on load, then whichever was last
targeted.

### Fields of a panel

| Field | Meaning |
|---|---|
| Heading | Title in the expanded panel and label in the collapsed state (required) |
| Source file / image size | Background image of the panel; the image size is required (see Performance) |
| Override metadata | Alt text and image title; *image link* and *caption* are not used here |
| Text | Short text in the expanded panel (editor) – one to two sentences |
| Link address | Target of the button; without an address, no button is output |
| Link text | Label of the button, empty = "Learn more" |
| Title attribute | `title` attribute of the button |

More than six panels are not rendered in the frontend; in the backend, the
preview shows a notice if the number is outside 2 to 6.

## Behavior

| Situation | Behavior |
|---|---|
| Mouse / trackpad | Hover expands the panel, the others shrink |
| Mouse leaves | All close — or return to the start panel, or stay open (depending on setting) |
| Touch device | First tap expands, second tap on the button follows the link; tapping elsewhere closes |
| Keyboard | Tab to the button expands the panel, Escape closes it (or returns to the start panel) |
| Narrower than 768px | Panels always stacked, tap expands |
| `prefers-reduced-motion` | Transitions are disabled |

### Without JavaScript

The opening is controlled by the included script, because a pre-opened panel
and staying open cannot be represented in pure CSS. If JavaScript is
disabled, a CSS fallback kicks in: hover opens the panel, leaving closes it
again. Both options then have no effect, but the navigation is still fully
usable.

## Customization

Appearance and animation can be controlled without a template override via
CSS custom properties, for example in the theme stylesheet:

```css
.imgnav {
    --imgnav-grow: 4;
    --imgnav-btn-bg: #c8102e;
    --imgnav-gradient: linear-gradient(to top, rgba(0,0,0,.9), transparent);
}
```

| Property | Default | Meaning |
|---|---|---|
| `--imgnav-grow` | `3` | `flex-grow` of the expanded panel |
| `--imgnav-shrink` | `0.6` | `flex-grow` of the remaining panels |
| `--imgnav-duration` | `0.5s` | Duration of the expand/collapse animation |
| `--imgnav-content-delay` | `0.15s` | Delay until the content fades in |
| `--imgnav-collapsed` | `5rem` | Panel height when collapsed (< 768px) |
| `--imgnav-expanded-mobile` | `22rem` | Panel height when expanded (< 768px) |
| `--imgnav-padding` | `2rem` | Inner padding of the content |
| `--imgnav-dim` | `rgba(0,0,0,.3)` | Dimming in the collapsed state |
| `--imgnav-gradient` | gradient | Overlay in the expanded state |
| `--imgnav-label-size` | `0.9rem` | Font size of the label in the collapsed state |
| `--imgnav-label-inset` | `0.75rem` | Side spacing of the horizontal label from the panel edge |
| `--imgnav-title-size` | `1.5rem` | Font size of the heading |
| `--imgnav-text-lines` | `4` | Maximum number of lines of the short text |
| `--imgnav-border-width` / `--imgnav-border-color` | `2px` / `#fff` | Border around each panel |
| `--imgnav-title-color` | same as `--imgnav-color` | Color of the heading and label |

On heading color: the panel heading is an `h1`–`h6` and is therefore picked
up by theme rules such as `.main-content h2 { color: … }`. As long as no
color is chosen in the backend, the default prevails over such general
rules, but the theme can override it specifically — for example with
`.imgnav .imgnav__panel .imgnav__title { color: … }`. Once a color has been
chosen in the backend, it takes precedence over all theme rules.
| `--imgnav-btn-bg` / `--imgnav-btn-bg-hover` / `--imgnav-btn-color` | blue / dark blue / white | Button background, hover background, text color |

The colors set in the backend are output as custom properties on the
wrapper; an empty field omits the property, so the default applies. The text
color of the buttons has no dedicated field — it can be adjusted via
`--imgnav-btn-color` if a light button background is chosen.

For further changes, the templates `content_element/img_navi.html.twig` and
`content_element/img_navi_item.html.twig` can be overridden in the project's
`templates/` directory.

## Performance

The panel images are preloaded via `<link rel="preload" as="image">` in the
`<head>` so nothing loads while expanding. That's why the **image size is a
required field**: without it, the unscaled original image would be
preloaded.

### Choosing the image size

The image always fills the full panel height and keeps its proportions; the
panel acts as a window onto it. Expanding therefore reveals *more of the
image*, rather than re-cropping it.

So that the expanding panel never runs past the image, the image should be
wider than the widest expanded panel. The rendered image width results from
panel height × aspect ratio: at 600px height and 16:9, that's roughly
1070px, which is enough for content areas up to about 1700px. For wider
layouts or more panels, a flatter aspect ratio (around 21:9) is recommended.

If the image is too narrow, it falls back to being cropped to the panel
width — the display stays correct, but the expand effect is weaker.

Recommended: **1600 × 900 in "crop" mode** without additional pixel
densities.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).
