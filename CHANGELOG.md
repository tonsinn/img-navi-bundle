# Changelog

### v1.0.0 (09.09.2026)

#### Allgemein
- Erstveröffentlichung für Contao 5.3+ und Contao 6 (reine Twig-Templates)
- Getestet gegen Contao 5.7.13 und Contao 6.0.0 unter PHP 8.5
- Inhaltselement „Bildnavigation“ (`img_navi`) als Rahmenelement mit 2–6 verschachtelten
  Elementen „Bildnavigation-Panel“ (`img_navi_item`)

#### Anzeige-Einstellungen
- Anordnung horizontal (Standard) oder vertikal
- Konfigurierbare Gesamthöhe in px, vh oder rem (Standard 600px)
- Anpassung ohne Template-Override über CSS-Custom-Properties (`--imgnav-*`)
- Optional ein Panel bereits beim Laden geöffnet; beim Überfahren eines anderen
  wechselt die Anzeige, beim Verlassen kehrt sie zum Startpanel zurück
- Optional bleibt das zuletzt geöffnete Panel offen, wenn die Maus die
  Navigation verlässt
- Die Überschrift im zugeklappten Zustand wahlweise senkrecht gedreht
  (Standard) oder waagerecht mit Zeilenumbruch im sichtbaren Streifen
- In der gestapelten Ansicht (unter 768px) bricht die Überschrift ebenfalls um,
  statt seitlich aus dem Panel zu laufen und abgeschnitten zu werden
- Rahmen um jedes Panel mit einstellbarer Breite (Standard 2px) und Farbe
  (Standard Weiß); Breite 0 schaltet ihn ab
- Einstellbare Farbe für Überschriften und für Buttons; die Hover-Farbe der
  Buttons wird daraus abgeleitet

#### Bedienung
- Aufklappen per Hover auf Geräten mit Zeiger (Flex-Grow-Animation)
- Tap-Bedienung auf Touch-Geräten: erster Tap klappt auf, zweiter folgt dem Link
- Tastaturbedienung über den Button (`:focus-within`), Escape schließt
- Unter 768px immer gestapelte Darstellung
- Berücksichtigt `prefers-reduced-motion`

#### Darstellung
- Das Bild füllt die volle Panelhöhe und behält seine Proportionen; das Panel wirkt
  als Fenster darauf, sodass beim Aufklappen mehr vom Bild sichtbar wird

#### Performance
- Bild-Preload (`<link rel="preload" as="image">`) für alle Panels im `<head>`
- `loading="eager"`, `decoding="async"`, `fetchpriority="high"` für das erste Panel

---

## Claude-Code-Session-Historie

### 2026-09-09
- Projekt angelegt: Grundstruktur des Bundles erstellt
- Vorbild-Animation analysiert und in `docs/vorbild.md` dokumentiert
- Bildnavigation implementiert (Controller, DCA, Twig, CSS, JS, Sprachdateien)
- Umstellung auf Vendor `tonsinn`, Release v1.0.0

---

## Aktueller Stand (09.09.2026, Teil 1)

Das Bundle wurde von einem leeren Skelett zu einer vollständigen, in zwei
Contao-Versionen verifizierten Erweiterung ausgebaut und dabei auf den Vendor
`tonsinn` umgestellt.

Umgesetzt wurden zwei Inhaltselemente: `img_navi` als Rahmenelement, das über
`nestedFragments` mit `allowedTypes` 2–6 verschachtelte Panels aufnimmt, und
`img_navi_item` als einzelnes Panel. Das Panel nutzt ausschließlich Core-Felder
von `tl_content` und legt deshalb keine eigenen Spalten an; nur der Wrapper
bringt `imgNaviLayout`, `imgNaviHeight`, `imgNaviInitial` und `imgNaviSticky`
mit. Die Animation läuft über `flex-grow`-Transitions, das Bild wird per
`<link rel="preload" as="image">` im `<head>` vorgeladen.

Die Verifikation lief auf zwei echten Instanzen: `img-navi.tonsinn.de`
(Contao 5.7.13) und `tao6.tonsinn.de` (Contao 6.0.0), beide unter PHP 8.5 via
`keyhelp-php85`. Geprüft wurden Registrierung, Migration, Frontend-Rendering,
Hover-/Tap-/Tastaturbedienung, gestapelte Darstellung unter 768px,
`prefers-reduced-motion`, der 6er-Cap, die Übereinstimmung von Preload-URLs mit
den tatsächlich geladenen Bildern sowie im Backend Palette, Pflichtfeldlogik,
Typbeschränkung der Kind-Elemente und der Editor-Hinweis. Die Contao-6-Instanz
wurde nach dem Test vollständig zurückgebaut (Paket, Path-Repo, Spalten,
Testinhalte, `composer.json`).

Drei Fehler kamen erst durch diese Tests ans Licht und wurden behoben: die
fehlende `.twig-root`-Markerdatei (Templates wurden sonst als
`@Contao/img_navi.html.twig` statt `@Contao/content_element/img_navi.html.twig`
registriert und vom Controller nie gefunden), die Versionsbeschränkung
`contao/core-bundle: ^5.3`, die Contao 6 ausschloss, und das Bild-Rendering, das
mit `width/height: 100%` plus `object-fit: cover` bei jeder Panelbreite neu
zuschnitt statt das Panel als Fenster auf das Bild wirken zu lassen.

Zuletzt kamen die beiden Öffnungsoptionen hinzu. Dafür wanderte die
Hover-Steuerung ins Skript, weil CSS sich den zuletzt überfahrenen Zustand nicht
merken kann.

### Geänderte/erstellte Dateien — Session 09.09.2026 (Teil 1)
- `composer.json` — Vendor `tonsinn`, `contao/core-bundle: ^5.3 || ^6.0`, PHP ^8.2
- `src/TonsinnImgNaviBundle.php` — neu, `getPath()`-Override für Root-`contao/`+`public/`
- `src/DependencyInjection/TonsinnImgNaviExtension.php` — umbenannt, lädt `config/services.yaml`
- `src/ContaoManager/Plugin.php` — auf neue Bundle-Klasse umgestellt
- `src/Controller/ContentElement/ImgNaviController.php` — neu, Wrapper inkl. 6er-Cap, `initial`, `sticky`
- `src/Controller/ContentElement/ImgNaviItemController.php` — neu, Panel (Figure + Link)
- `src/EventListener/DataContainer/ImgNaviItemListener.php` — neu, Pflichtfeldlogik pro Typ
- `contao/dca/tl_content.php` — neu, zwei Paletten + vier Wrapper-Felder
- `contao/templates/.twig-root` — neu, ohne sie greift die Namespace-Auflösung nicht
- `contao/templates/content_element/img_navi.html.twig` — neu, Wrapper
- `contao/templates/content_element/img_navi_item.html.twig` — neu, Panel inkl. Preload
- `contao/languages/{de,en}/{default,tl_content}.xlf` — neu, Labels und Hinweistexte
- `public/css/img-navi.css` — neu, Animation, Fenster-Logik, Fallback-Hover
- `public/js/img-navi.js` — neu, Hover-/Tap-/Tastatursteuerung, Startpanel, Sticky
- `public/img-navi.svg` — neu, Logo für den Contao Manager
- `config/services.yaml` — von `src/Resources/config/` verschoben
- `bin/{sync-img-navi,remote-install,remote-uninstall}.sh` — neu, Deploy und Rückbau
- `testinstall.env.example` — neu, Schema für die Zugangsdaten
- `.gitignore` / `.gitattributes` — `testinstall.env` ausgeschlossen, Dist verschlankt
- `.github/workflows/ci.yml` — neu, PHP-Lint-Matrix 8.2–8.5 + XLIFF-Prüfung
- `README.md`, `CHANGELOG.md`, `TODO.md`, `CLAUDE.md`, `LICENSE`, `docs/vorbild.md`
- gelöscht: `src/ImgNaviBundle.php`, `src/DependencyInjection/ImgNaviExtension.php`, `src/Resources/`, `tests/`

### Entscheidung — Teil 1
- **Vendor `tonsinn` statt `mailwurm`**, analog zum Schwester-Bundle
  `tonsinn/belegungsplan-bundle` — gleiche Konventionen, gleicher Autor.
- **Verschachtelte Elemente statt `rowWizard`** für die Panels: der `rowWizard`
  kann keine Dateiauswahl mit Bildgröße pro Zeile, und `nestedFragments` ist der
  Weg, den Contao 5.3+ und 6 für Wrapper-Elemente vorsehen.
- **Panels nutzen ausschließlich Core-Felder** (`singleSRC`, `size`, `headline`,
  `text`, `url`, …) — kein einziges eigenes Feld, dadurch keine Spalten und
  vertraute Beschriftungen für Redakteure.
- **Bild als Fenster statt Zuschnitt:** das Bild behält Proportionen und volle
  Panelhöhe, das Panel zeigt einen Ausschnitt. Nur so entsteht das „Aufziehen"
  des Vorbilds; `object-fit: cover` auf Panelgröße skalierte stattdessen um.
- **Hover-Steuerung im JavaScript** statt in CSS, weil sich weder ein vorab
  geöffnetes Panel noch das Offenbleiben nach dem Verlassen in CSS abbilden
  lassen. Die `:hover`-Regeln bleiben als Fallback ohne JavaScript erhalten und
  werden über `data-imgnav-init` abgeschaltet, sobald das Skript übernimmt.
- **Strukturelle CSS-Regeln zweistufig** (`.imgnav .imgnav__media img`), weil
  Theme-Regeln gleicher Spezifität sonst über die Quellreihenfolge gewinnen.

---


---

## Aktueller Stand (09.09.2026, Teil 2)

Das Bundle hat drei weitere Gestaltungsoptionen bekommen und ist anschließend
als `v1.0.0` veröffentlicht worden.

Neu ist erstens die Ausrichtung der Panel-Überschrift im zugeklappten Zustand:
wahlweise senkrecht gedreht wie bisher oder waagerecht mit Zeilenumbruch im
sichtbaren Streifen. Beim Nachmessen fiel auf, dass die Beschriftung in der
gestapelten Ansicht unter 768px seit jeher `white-space: nowrap` hatte und lange
Überschriften seitlich aus dem Panel liefen — gemessen 528px in einem 333px
breiten Panel. Das ist unabhängig von der neuen Option behoben.

Zweitens rahmen einstellbare Breite und Farbe jedes Panel ein, drittens lassen
sich Überschriften und Buttons einfärben. Die Hover-Farbe der Buttons wird im
Controller aus der gewählten Farbe abgeleitet, damit dafür kein zweites Feld
nötig ist. Alle Werte gehen als CSS-Custom-Properties an den Wrapper; leere
Felder lässt `HtmlAttributes::addStyle` weg, sodass der Standard aus dem
Stylesheet greift. Auf Wunsch wurde der Vorgabewert der Rahmenfarbe später von
`ffffff` auf leer geändert, damit alle drei Farbfelder einheitlich sind.

Beim Ausrollen der Farboptionen legte ein Twig-Kommentar innerhalb des
`{% set %}`-Ausdrucks das Frontend lahm; der Fehler fiel erst beim Seitenaufruf
auf. Daraufhin läuft `lint:twig` jetzt in beiden Deploy-Skripten nach
`cache:clear` und bricht vor der Migration ab.

Die Veröffentlichung erfolgte nach einer Prüfung der gesamten Historie auf
Zugangsdaten (Passwörter, Host, Benutzer, Datenbankname aus `testinstall.env`
gegen alle 13 Commits — keine Treffer, `testinstall.env` nie committet). Für
HTTPS war kein Credential-Helper eingerichtet, es existierte aber ein
GitHub-SSH-Key samt `~/.ssh/config`-Eintrag; der Remote läuft deshalb über SSH.
Nach dem Push wurde gegengeprüft: Composer löst `v1.0.0` gegen eine echte
Contao-5.7-Anforderung auf, und nach dem Nachziehen des Packagist-Suchindex
findet die typgefilterte Suche (`type=contao-bundle`), wie sie der Contao
Manager verwendet, genau einen Treffer.

### Geänderte/erstellte Dateien — Session 09.09.2026 (Teil 2)
- `src/Controller/ContentElement/ImgNaviController.php` — `imgNaviLabel`,
  Rahmen- und Farbwerte, Helfer `getColor()`, `getBorderWidth()`, `darken()`
- `contao/dca/tl_content.php` — fünf neue Felder, zweite Legende
  `imgnavi_style_legend`
- `contao/templates/content_element/img_navi.html.twig` — Modifier-Klasse für
  die Beschriftung, Custom Properties für Rahmen und Farben
- `public/css/img-navi.css` — waagerechte Beschriftung, Umbruch in der
  gestapelten Ansicht, Rahmen am Panel, `--imgnav-title-color`
- `contao/languages/{de,en}/tl_content.xlf` — Labels und Hilfetexte der fünf Felder
- `bin/remote-install.sh`, `bin/sync-img-navi.sh` — `lint:twig` vor der Migration
- `composer.json` — Description und Keywords auf Englisch
- `README.md`, `CHANGELOG.md`, `TODO.md`, `CLAUDE.md`, `HINWEISE.md` — Doku
- Git: Tag `v1.0.0`, Remote `git@github.com:tonsinn/img-navi-bundle.git`

### Entscheidung — Teil 2
- **Erstveröffentlichung als `v1.0.0` statt `v5.0.0`.** Die geerbte Konvention
  (Version folgt der Contao-Hauptversion) passt nicht, weil das Bundle Contao 5
  und 6 gleichzeitig unterstützt — eine „5" im Tag wäre irreführend.
- **Der Rahmen sitzt am Panel, nicht am Bild.** Das Bild ist breiter als der
  sichtbare Ausschnitt; ein Rahmen daran läge größtenteils außerhalb.
  `box-sizing: border-box` hält ihn innerhalb der Panelbreite.
- **Die Hover-Farbe der Buttons wird in PHP berechnet**, nicht per `color-mix()`
  in CSS: dort wäre der Wert auf älteren Browsern ungültig und die Hover-Farbe
  fiele ganz aus.
- **Die Option für die Beschriftung ist auf die horizontale Anordnung ab 768px
  begrenzt.** Ohne diese Eingrenzung hätte sie die vertikale und die gestapelte
  Ansicht überschrieben, wo die Beschriftung ohnehin waagerecht steht.
- **Description und Keywords englisch, README und Backend-Labels deutsch.**
  Packagist und GitHub sind ein internationales Publikum, die Redaktionsoberfläche
  nicht.
- **Remote über SSH statt HTTPS**, weil kein Credential-Helper eingerichtet war
  und ein HTTPS-Push interaktiv nach einem Token gefragt hätte.

## Abgeschlossene Punkte (Archiv)

- ~~Bundle-Konzept definieren~~ erledigt (Teil 1, siehe `docs/vorbild.md`)
- ~~Content-Elemente `img_navi` und `img_navi_item` anlegen~~ erledigt (Teil 1)
- ~~DCA-Definitionen erstellen~~ erledigt (Teil 1)
- ~~Frontend-Templates (Twig) erstellen~~ erledigt (Teil 1)
- ~~CSS/JS-Assets~~ erledigt (Teil 1)
- ~~Test gegen Contao 5.7.13 und Contao 6.0.0~~ erledigt (Teil 1)
- ~~Release veröffentlichen (GitHub, Tag, Packagist)~~ erledigt (Teil 2)

---

## Unveröffentlicht

- Die im Backend gewählte Farbe für Überschriften setzt sich jetzt auch gegen
  Überschriften-Regeln des Themes durch. Zuvor wirkte sie nur auf die
  Beschriftung im zugeklappten Panel (ein `span`), während die Überschrift des
  aufgeklappten Panels (`h1`–`h6`) von Theme-Regeln wie `.main-content h2`
  überschrieben wurde.
