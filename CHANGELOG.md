# Changelog

### v1.0.3 (17.09.2026)

#### Sonstiges
- `composer.json`: `homepage`-Feld auf `https://img-navi.tonsinn.de` gesetzt,
  damit Packagist und der Contao Manager unter „Mehr" einen Link zur
  Projekt-Website zeigen
- README auf Englisch ergänzt (`README.en.md`), beide Sprachversionen
  verlinken sich gegenseitig

### v1.0.2 (10.09.2026)

#### Sonstiges
- Logo (`public/img-navi.svg`) auf ein 360×360-Raster neu gezeichnet: feinere
  Striche und ein Bildmotiv im geöffneten Panel. Die bisherige Fassung war auf
  13 px ausgelegt; hochskaliert wirkten die Striche klobig, und der Button
  überlappte den Rahmen.

### v1.0.1 (09.09.2026)

#### Behoben
- Die im Backend gewählte Farbe für Überschriften setzt sich jetzt auch gegen
  Überschriften-Regeln des Themes durch. Zuvor wirkte sie nur auf die
  Beschriftung im zugeklappten Panel (ein `span`), während die Überschrift des
  aufgeklappten Panels (`h1`–`h6`) von Theme-Regeln wie `.main-content h2`
  überschrieben wurde.

#### Sonstiges
- Keyword `bildnavigation` ergänzt, damit das Paket auch bei deutscher Suche
  auf Packagist gefunden wird
- `public/img-navi.svg` durch das Icon aus dem package-metadata-Eintrag
  ersetzt, damit Contao Manager und Packagist dasselbe Bild zeigen

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


---

## Aktueller Stand (09.09.2026, Teil 3)

Nach der Erstveröffentlichung kamen der Eintrag im Contao Manager, zwei
Fehlerbehebungen und daraus das Release v1.0.1.

Für den Contao Manager entstand ein Eintrag im package-metadata-Repository.
Der erste Logo-Entwurf — eine farbige Kachel — war am Hausstil vorbei: die
Vorlage `Belegungsplan.svg` ist eine einfarbige Strichgrafik in `#91979c` ohne
Hintergrundplatte. Das neue Icon zeigt ein aufgeklapptes Panel mit
Überschriftenleiste, Textzeile und umrandetem Button neben drei schmalen
Streifen und ist mit 437 Bytes ein Viertel so groß wie die Vorlage. Beim
Aufsetzen des Pull Requests fielen zwei Dinge auf: der vorhandene Klon des
Metadaten-Repos war 76 Commits alt und sein `origin` zeigte auf
`contao/package-metadata` statt auf den Fork — ein Push wäre also am falschen
Ziel gelandet. Da der Linter dort mit aspell prüft, wurden zwei vermeidbare
Wörter umformuliert und nur der Produktname `Bildnavigation` in die deutsche
Wortliste aufgenommen, dem Muster von `Belegungsplan` und `Buchnavigation`
folgend. PR #788 läuft grün.

Zwei Fehler kamen aus dem Betrieb. Erstens brach im Contao Manager der Wechsel
von `@dev` auf `^1.0` ab: das Path-Repository aus `bin/remote-install.sh` ist in
Composer canonical und hat Vorrang vor Packagist, liefert aber nur `dev-main`.
Zweitens blieb die im Backend gewählte Farbe für Überschriften im aufgeklappten
Panel wirkungslos, während sie im zugeklappten wirkte. Der Unterschied war der
Schlüssel: die eingeklappte Beschriftung ist ein `span`, die Überschrift ein
`h1`–`h6` und damit von Theme-Regeln wie `.main-content h2` erfasst, die
spezifischer sind als eine einstufige Klasse. Der Fehler wurde erst reproduziert
— das Demo-Theme setzt gar keine Überschriftfarben — und dann zweistufig
behoben: erhöhte Spezifität für den Standard, `!important` über eine
Modifier-Klasse nur bei ausdrücklich gewählter Farbe. Verifiziert in vier
Konstellationen gegen Klassen- und ID-Selektoren.

Daraus wurde v1.0.1, zusammen mit den beiden zurückgestellten Punkten Keyword
und Logo. Packagist zog per Hook sofort nach; die Testinstallation wurde auf
v1.0.1 aktualisiert und der Fix dort in der echten Release-Version gegengeprüft,
nicht nur im zuvor handgepatchten `vendor/`-Verzeichnis.

### Geänderte/erstellte Dateien — Session 09.09.2026 (Teil 3)
- `public/css/img-navi.css` — Überschriftfarbe dreistufig plus
  `!important`-Regel unter `.imgnav--title-color`
- `contao/templates/content_element/img_navi.html.twig` — Modifier-Klasse
  `imgnav--title-color`, wenn eine Farbe gesetzt ist
- `public/img-navi.svg` — durch die Strichgrafik ersetzt (594 → 437 Bytes)
- `composer.json` — Keyword `bildnavigation`
- `bin/remote-install.sh` — Warnhinweis zum Path-Repository im Kopf
- `README.md` — Verhalten der Überschriftfarbe gegenüber Themes
- `HINWEISE.md` — Path-Repo-Vorrang, zweistufige Überschriftfarbe
- `CHANGELOG.md`, `TODO.md`, `CLAUDE.md` — Release v1.0.1 und Pflege
- ausserhalb des Repos: `~/package-metadata` (Fork synchronisiert, Branch
  `add-tonsinn-img-navi-bundle`, PR #788) und `Desktop/img-navi.svg`

### Entscheidung — Teil 3
- **Logo als einfarbige Strichgrafik** statt farbiger Kachel — der Hausstil
  ergibt sich aus `Belegungsplan.svg`, nicht aus allgemeinen Icon-Konventionen.
- **Nur `Bildnavigation` in die Linter-Wortliste**, die beiden anderen
  Risikowörter stattdessen umformuliert. Je weniger Zeilen in einer geteilten
  Datei, desto geringer die Reibung im Review.
- **Überschriftfarbe zweistufig statt pauschal `!important`.** Ohne gewählte
  Farbe bleibt der Standard vom Theme überschreibbar; eine ausdrückliche Wahl
  im Backend gewinnt dagegen auch gegen ID-Selektoren. Pauschales `!important`
  hätte Themes die Anpassung ganz verwehrt.
- **Korrektur direkt ins `vendor/`-Paket gespielt**, nicht über
  `bin/remote-install.sh` — das Skript hätte das Path-Repository wieder
  eingetragen und die Instanz von `^1.0` zurück auf `dev-main` geworfen.
- **Erst v1.0.1, als etwas Inhaltliches anstand.** Keyword und Logo allein
  waren kein Release wert; zusammen mit dem Fix schon.


---

## Aktueller Stand (10.09.2026, Teil 4)

Das Logo war auf 13 px ausgelegt (`viewBox 0 0 64 64`, Striche mit 4,7 % der
Kantenlänge). Hochskaliert auf 360 px wirkten die Striche klobig, und ein
Vorschau-Vergleich zeigte, dass der Button unten links den Rahmen überlappte —
bei 13 px unsichtbar. Es wurde auf einem 360er-Raster neu gezeichnet: 10er-Strich
und zusätzlich ein Bildmotiv (Berg und Sonne) im geöffneten Panel, das erst bei
dieser Größe lesbar ist. Geprüft im Vergleich bei 360, 64 und 32 px.

Die neue Datei ging in den noch offenen Metadaten-PR #788 (zweiter Commit,
Linter erneut grün) und ins Bundle als Release v1.0.2; Packagist zog nach etwa
20 Sekunden nach. Die Testinstallation bleibt auf v1.0.1, weil das Logo im
Frontend nicht erscheint.

### Geänderte/erstellte Dateien — Session 10.09.2026 (Teil 4)
- `public/img-navi.svg` — 360er-Raster, 554 Bytes
- `CHANGELOG.md` — Abschnitt v1.0.2
- `CLAUDE.md`, `HINWEISE.md`, `TODO.md` — Pflege
- ausserhalb: `~/package-metadata` Branch `add-tonsinn-img-navi-bundle`
  (Commit `ee3f5d6`), `Desktop/img-navi.svg`

### Entscheidung — Teil 4
- **Neu zeichnen statt nur `width/height` ändern.** SVG skaliert verlustfrei,
  aber Proportionen und Detailgrad waren auf Winzgröße abgestimmt.
- **PR aktualisieren, solange er offen ist** — nach dem Merge wäre ein zweiter
  PR nötig gewesen.
- **Sofort v1.0.2** auf Wunsch, obwohl nur ein Logo betroffen ist.


---

## Aktueller Stand (10.09.2026, Teil 5)

Auf Wunsch wurden alle Hinweise auf die Website entfernt, die als Vorlage für
die Animation diente. Die README-Einleitung ist neu gefasst und nennt jetzt
Contao 5 und 6, die Überschrift heißt „Contao Bildnavigation“; in
`docs/vorbild.md` sind Quellenangabe und URL entfernt. Eine Suche über das Repo
findet keinen Verweis mehr.

Bestehen bleibt der Verweis in der veröffentlichten Git-Historie (ältere Stände
von README und `docs/vorbild.md`) sowie lokal in `.claude/settings.local.json`,
die nicht versioniert ist.

### Geänderte/erstellte Dateien — Session 10.09.2026 (Teil 5)
- `README.md` — Einleitung und Überschrift ohne Website-Verweis
- `docs/vorbild.md` — Quellenangabe entfernt, Überschrift neutral
- `HINWEISE.md`, `CLAUDE.md` — Pflege

### Entscheidung — Teil 5
- **Git-Historie nicht umgeschrieben.** Das hätte ein Verschieben der
  veröffentlichten Tags v1.0.0 bis v1.0.2 erfordert, auf die Packagist und
  bestehende Installationen angewiesen sind.
- **Dateiname `docs/vorbild.md` beibehalten**, weil CLAUDE.md und die
  CHANGELOG-Historie darauf verweisen; der Name nennt die Website nicht.

## Aktueller Stand (17.09.2026, Teil 6)

Auf Wunsch wurde eine englische README ergänzt: `README.en.md` ist eine
vollständige Übersetzung von `README.md`, beide Dateien verlinken sich
gegenseitig über einen Sprachumschalter direkt unter der Überschrift.

### Geänderte/erstellte Dateien — Session 17.09.2026 (Teil 6)
- `README.en.md` — neu, vollständige englische Übersetzung
- `README.md` — Sprachumschalter-Link ergänzt

### Entscheidung — Teil 6
- **README jetzt zweisprachig, Backend-Labels bleiben deutsch.** Widerruft die
  Entscheidung aus Teil 2 („README und Backend-Labels deutsch"): Packagist und
  GitHub haben doch ein internationales Publikum, das von einer englischen
  README profitiert; die Redaktionsoberfläche (DCA-Labels, XLIFF) bleibt davon
  unberührt und weiterhin deutsch.

## Abgeschlossene Punkte (Archiv)

- ~~Bundle-Konzept definieren~~ erledigt (Teil 1, siehe `docs/vorbild.md`)
- ~~Content-Elemente `img_navi` und `img_navi_item` anlegen~~ erledigt (Teil 1)
- ~~DCA-Definitionen erstellen~~ erledigt (Teil 1)
- ~~Frontend-Templates (Twig) erstellen~~ erledigt (Teil 1)
- ~~CSS/JS-Assets~~ erledigt (Teil 1)
- ~~Test gegen Contao 5.7.13 und Contao 6.0.0~~ erledigt (Teil 1)
- ~~Release veröffentlichen (GitHub, Tag, Packagist)~~ erledigt (Teil 2)
- ~~Keyword `bildnavigation` und Logo-Austausch~~ erledigt (mit v1.0.1)
- ~~Metadaten-Eintrag für den Contao Manager (Logo, de/en, PR #788)~~ erledigt (Teil 3)
- ~~Logo für 360×360 optimieren~~ erledigt (Teil 4)
- ~~Website-Verweise aus README und Doku entfernen~~ erledigt (Teil 5)
- ~~Metadaten-PR #788 gemerged, Logo/Titel/Beschreibung im Contao Manager
  bestätigt~~ erledigt (Teil 6)
- ~~Homepage-Link (`composer.json`, v1.0.3) im Contao Manager als
  „Projektwebsite" unter „Mehr" bestätigt~~ erledigt (Teil 6)
