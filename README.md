# Contao 5 Bildnavigation (img-navi-bundle)

Animierte Bildnavigation für Contao 5 nach dem Vorbild von
[vr-rheinahreifel.de](https://www.vr-rheinahreifel.de): mehrere Bild-Panels liegen
nebeneinander, das angesteuerte Panel klappt auf und zeigt Überschrift, Kurztext
und einen Button.

## Installation

Im Contao Manager nach `tonsinn/img-navi-bundle` suchen und installieren, oder
auf der Konsole:

```bash
composer require tonsinn/img-navi-bundle
```

Danach die **Datenbank aktualisieren** (Contao Manager → Wartung, oder
`vendor/bin/contao-console contao:migrate`). Das Bundle legt zwei neue Spalten in
`tl_content` an.

## Anforderungen

- PHP 8.2 bis 8.5
- Contao 5.3 oder neuer, einschließlich Contao 6 (getestet mit 5.7.13 und 6.0.0)
- Funktioniert sowohl mit Twig-Seitenlayouts als auch mit klassischem `fe_page`

## Verwendung

1. Im Artikel ein neues Inhaltselement vom Typ **Bildnavigation** anlegen
   (Gruppe „Verschiedene Elemente“) und dort Anordnung, Höhe und das
   Öffnungsverhalten festlegen.
2. In der Elementliste beim Rahmenelement die Operation **Kind-Elemente** öffnen
   und dort 2 bis 6 Elemente vom Typ **Bildnavigation-Panel** anlegen.

### Felder der Bildnavigation

| Feld | Bedeutung |
|---|---|
| Anordnung | Panels nebeneinander (horizontal) oder untereinander (vertikal) |
| Höhe | Gesamthöhe in px, vh oder rem (Standard 600px) |
| Überschrift im zugeklappten Zustand | Senkrecht gedreht (Standard) oder waagerecht mit Zeilenumbruch innerhalb des sichtbaren Streifens. Wirkt nur bei horizontaler Anordnung ab 768px — gestapelte Panels zeigen die Überschrift ohnehin waagerecht und umbrechend |
| Zunächst geöffnetes Panel | Nummer des Panels, das beim Laden bereits offen ist. Beim Überfahren eines anderen Panels wechselt die Anzeige dorthin, beim Verlassen kehrt sie hierher zurück. Leer = alle Panels starten geschlossen |
| Panel geöffnet lassen | Das zuletzt geöffnete Panel bleibt offen, wenn die Maus die Navigation verlässt |
| Rahmenbreite | Breite des Rahmens um jedes Panel in Pixeln (Standard 2, `0` = kein Rahmen) |
| Rahmenfarbe | Farbe des Rahmens. Leer = Standard (Weiß) |
| Farbe der Überschriften | Gilt für die Überschrift im aufgeklappten Panel **und** die Beschriftung im zugeklappten Zustand. Eine hier gewählte Farbe setzt sich auch gegen Überschriften-Regeln des Themes durch. Leer = Standard (Weiß) |
| Farbe der Buttons | Hintergrundfarbe der Buttons; die Farbe beim Überfahren wird daraus abgeleitet (20 % dunkler). Leer = Standard (Blau) |

Beide Optionen lassen sich kombinieren: mit einem Startpanel *und* „geöffnet
lassen“ ist immer genau ein Panel offen — beim Laden das gewählte, danach das
zuletzt angesteuerte.

### Felder eines Panels

| Feld | Bedeutung |
|---|---|
| Überschrift | Titel im aufgeklappten Panel und Beschriftung im eingeklappten Zustand (Pflichtfeld) |
| Quelldatei / Bildgröße | Hintergrundbild des Panels; die Bildgröße ist Pflicht (siehe Performance) |
| Metadaten überschreiben | Alternativtext und Bildtitel; *Bild-Link* und *Bildunterschrift* werden hier nicht verwendet |
| Text | Kurztext im aufgeklappten Panel (Editor) – ein bis zwei Sätze |
| Link-Adresse | Ziel des Buttons; ohne Adresse wird kein Button ausgegeben |
| Linktext | Beschriftung des Buttons, leer = „Mehr erfahren“ |
| Titel-Attribut | `title`-Attribut des Buttons |

Mehr als sechs Panels werden im Frontend nicht ausgegeben; im Backend erscheint
in der Vorschau ein Hinweis, wenn die Anzahl außerhalb von 2 bis 6 liegt.

## Verhalten

| Situation | Verhalten |
|---|---|
| Maus / Trackpad | Hover klappt das Panel auf, die übrigen schrumpfen |
| Maus verlässt | Alle schließen — oder zurück zum Startpanel, bzw. offen bleiben (je nach Einstellung) |
| Touch-Gerät | Erster Tap klappt auf, zweiter Tap auf den Button folgt dem Link; Tap daneben schließt |
| Tastatur | Tab auf den Button klappt das Panel auf, Escape schließt (bzw. kehrt zum Startpanel zurück) |
| Schmaler als 768px | Panels immer untereinander, Tap klappt auf |
| `prefers-reduced-motion` | Übergänge werden abgeschaltet |

### Ohne JavaScript

Das Öffnen wird vom mitgelieferten Skript gesteuert, weil sich ein vorab
geöffnetes Panel und das Offenbleiben in reinem CSS nicht abbilden lassen.
Ist JavaScript deaktiviert, greift ein CSS-Fallback: Hover öffnet das Panel,
beim Verlassen schließt es wieder. Die beiden Optionen bleiben dann ohne
Wirkung, die Navigation ist aber voll benutzbar.

## Anpassung

Aussehen und Animation lassen sich ohne Template-Override über CSS-Custom-Properties
steuern, zum Beispiel im Theme-Stylesheet:

```css
.imgnav {
    --imgnav-grow: 4;
    --imgnav-btn-bg: #c8102e;
    --imgnav-gradient: linear-gradient(to top, rgba(0,0,0,.9), transparent);
}
```

| Property | Standard | Bedeutung |
|---|---|---|
| `--imgnav-grow` | `3` | `flex-grow` des aufgeklappten Panels |
| `--imgnav-shrink` | `0.6` | `flex-grow` der übrigen Panels |
| `--imgnav-duration` | `0.5s` | Dauer der Auf-/Zuklapp-Animation |
| `--imgnav-content-delay` | `0.15s` | Verzögerung, bis der Inhalt eingeblendet wird |
| `--imgnav-collapsed` | `5rem` | Panelhöhe eingeklappt (< 768px) |
| `--imgnav-expanded-mobile` | `22rem` | Panelhöhe aufgeklappt (< 768px) |
| `--imgnav-padding` | `2rem` | Innenabstand des Inhalts |
| `--imgnav-dim` | `rgba(0,0,0,.3)` | Abdunklung im eingeklappten Zustand |
| `--imgnav-gradient` | Verlauf | Overlay im aufgeklappten Zustand |
| `--imgnav-label-size` | `0.9rem` | Schriftgröße der Beschriftung im zugeklappten Zustand |
| `--imgnav-label-inset` | `0.75rem` | Seitlicher Abstand der waagerechten Beschriftung zum Panelrand |
| `--imgnav-title-size` | `1.5rem` | Schriftgröße der Überschrift |
| `--imgnav-text-lines` | `4` | Maximale Zeilenzahl des Kurztextes |
| `--imgnav-border-width` / `--imgnav-border-color` | `2px` / `#fff` | Rahmen um jedes Panel |
| `--imgnav-title-color` | wie `--imgnav-color` | Farbe von Überschrift und Beschriftung |

Zur Überschriftfarbe: Die Panel-Überschrift ist ein `h1`–`h6` und wird deshalb
von Theme-Regeln wie `.main-content h2 { color: … }` erfasst. Solange im Backend
keine Farbe gewählt ist, setzt sich der Standard gegen solche allgemeinen Regeln
durch, lässt sich aber vom Theme gezielt überschreiben — etwa mit
`.imgnav .imgnav__panel .imgnav__title { color: … }`. Sobald im Backend eine
Farbe gewählt wurde, hat diese Vorrang vor allen Theme-Regeln.
| `--imgnav-btn-bg` / `--imgnav-btn-bg-hover` / `--imgnav-btn-color` | Blau / Dunkelblau / Weiß | Button-Hintergrund, Hintergrund beim Überfahren, Textfarbe |

Die im Backend gesetzten Farben werden als Custom Properties am Wrapper
ausgegeben; ein leeres Feld lässt die Property weg, sodass der Standard greift.
Die Textfarbe der Buttons hat kein eigenes Feld — sie lässt sich über
`--imgnav-btn-color` anpassen, falls ein heller Button-Hintergrund gewählt wird.

Für weitergehende Änderungen können die Templates
`content_element/img_navi.html.twig` und `content_element/img_navi_item.html.twig`
im Projektverzeichnis `templates/` überschrieben werden.

## Performance

Die Panel-Bilder werden über `<link rel="preload" as="image">` im `<head>`
vorgeladen, damit beim Aufklappen nichts nachlädt. Deshalb ist die **Bildgröße ein
Pflichtfeld**: ohne sie würde das unskalierte Originalbild vorgeladen.

### Wahl der Bildgröße

Das Bild füllt immer die volle Panelhöhe und behält seine Proportionen; das Panel
wirkt als Fenster darauf. Beim Aufklappen wird deshalb *mehr vom Bild sichtbar*,
statt das Bild neu zuzuschneiden.

Damit das aufgehende Panel nie über das Bild hinausläuft, sollte das Bild breiter
sein als das breiteste aufgeklappte Panel. Die gerenderte Bildbreite ergibt sich aus
Panelhöhe × Seitenverhältnis: bei 600px Höhe und 16:9 sind das rund 1070px, was für
Inhaltsbereiche bis etwa 1700px ausreicht. Für breitere Layouts oder mehr Panels
empfiehlt sich ein flacheres Seitenverhältnis (etwa 21:9).

Ist das Bild zu schmal, wird es als Rückfallebene auf die Panelbreite beschnitten —
die Darstellung bleibt korrekt, der Aufzieh-Effekt fällt aber schwächer aus.

Empfohlen: **1600 × 900 im Modus „crop“** ohne zusätzliche Pixeldichten.

## Changelog

Siehe [CHANGELOG.md](CHANGELOG.md).
