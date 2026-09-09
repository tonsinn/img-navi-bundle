# Hinweise — dauerhafte Invarianten

Regeln, die beim Arbeiten an diesem Bundle immer gelten. Session-Details stehen
in CHANGELOG.md, offene Aufgaben in TODO.md.

## Contao-API (Contao-6-Tauglichkeit)

- **`FragmentTemplate` nur über `set()`, `get()`, `getResponse()`** ansprechen.
  Die Legacy-Magie (`$template->foo = …`) wirft bereits in Contao 5 und
  verschwindet in Contao 6.
- **Elementtypen werden über `#[AsContentElement]` registriert.** Die DCA liefert
  ausschließlich Palette und Felder; `$GLOBALS['TL_CTE']` niemals selbst setzen —
  das macht der `RegisterFragmentsPass` aus dem `category:`-Argument.
- **Verschachtelte Kinder** kommen als `nested_fragments` ins Template und werden
  mit `{{ content_element(reference) }}` ausgegeben. Es gibt kein
  `renderNested…()`; die Beschränkung erfolgt über
  `nestedFragments: ['allowedTypes' => […]]`.
- **DCA-Paletten müssen vollständig sein**, sonst rendert das Backend nicht.

## Bundle-Struktur

- **`contao/` und `public/` liegen im Bundle-Root**, nicht unter `src/Resources/`.
  Das funktioniert nur wegen des `getPath()`-Overrides in
  `TonsinnImgNaviBundle`, der auf `\dirname(__DIR__)` zeigt. Wird der Override
  entfernt, findet Contao weder Templates noch DCA.
- **`contao/templates/.twig-root` muss existieren.** Ohne diese leere
  Markerdatei scannt Contao nur die oberste Ebene und registriert Templates aus
  Unterordnern unter falschem Namen (`@Contao/img_navi.html.twig` statt
  `@Contao/content_element/img_navi.html.twig`) — der Controller findet sie dann
  nie. Die Datei ist leer und darf nicht durch `.gitignore` verschwinden.
- **Der Asset-Pfad heißt `bundles/tonsinnimgnavi/`**, abgeleitet aus dem
  Bundle-Klassennamen. Wird die Bundle-Klasse umbenannt, ändern sich alle
  `asset()`-Aufrufe in den Templates mit.

## Deployment

- **`cache:clear` läuft VOR `contao:migrate`.** `contao:migrate` liest die
  DCA-Definitionen aus dem Cache: mit einem alten Cache kennt es neu
  hinzugekommene Felder nicht und legt deren Spalten stillschweigend nicht an —
  meldet aber trotzdem „All migrations completed". Die Skripte in `bin/`
  berücksichtigen das; bei Handarbeit selbst darauf achten und anschließend
  die Spalten in `information_schema.COLUMNS` kontrollieren.
- **Auf dem KeyHelp-Testserver immer `keyhelp-php85` verwenden**, nie das nackte
  `php` oder `composer`. Das System-PHP ist älter (8.3); Composer würde die
  Abhängigkeiten gegen die falsche Version auflösen. Die PHP-Version des vHosts
  muss zur CLI passen, sonst schlägt `vendor/composer/platform_check.php` im
  Frontend fehl.
- **Das Path-Repository aus `bin/remote-install.sh` blockiert Versionswechsel.**
  Path-Repos sind in Composer *canonical* und haben Vorrang vor Packagist: sie
  liefern nur `dev-main`, und ein `^1.0` lässt sich dann nicht auflösen ("has
  higher repository priority"). Wer auf eine veröffentlichte Version wechseln
  will, muss das Repo erst entfernen:
  `composer config --unset repositories.imgnavi`, danach
  `composer require tonsinn/img-navi-bundle:^1.0`. Umgekehrt setzt ein erneuter
  Lauf von `bin/remote-install.sh` die Instanz wieder auf den lokalen
  Entwicklungsstand zurück — das ist gewollt, aber nichts, was man versehentlich
  auf einer Instanz tun will, die die Release-Version testen soll.
- **Assets werden dort als Kopien installiert**, nicht als Symlinks. Nach jeder
  Änderung an `public/` erneut `assets:install` ausführen, sonst liefert der
  Server das alte CSS/JS aus.
- **`testinstall.env` enthält Zugangsdaten und steht in `.gitignore`.** Nie
  committen, nie Werte in Ausgaben schreiben — nur `testinstall.env.example`
  wird versioniert.
- **Vor jedem Push in ein öffentliches Repo die gesamte Historie auf
  Zugangsdaten prüfen**, nicht nur den Arbeitsbaum:
  `git grep -I -l -F -- "<wert>" $(git rev-list --all)`. Aus einer
  veröffentlichten Historie lassen sich Geheimnisse praktisch nicht mehr
  entfernen.
- **Der Git-Remote läuft über SSH** (`git@github.com:tonsinn/img-navi-bundle.git`,
  Key `~/.ssh/id_ed25519_github` per `~/.ssh/config`). Für HTTPS ist kein
  Credential-Helper eingerichtet; ein HTTPS-Push bliebe an der Passwortabfrage
  hängen.

## Templates

- **Twig-Kommentare dürfen nicht innerhalb eines Tags stehen.** `{# … #}` mitten
  in einem `{% set … %}`-Ausdruck ist ein Syntaxfehler („Unclosed") und legt das
  Frontend lahm. Kommentare gehören vor oder hinter das Tag.
- **Vor dem Ausrollen `lint:twig` laufen lassen.** Die Skripte in `bin/` machen
  das nach `cache:clear` und brechen bei einem Fehler ab; von Hand:
  `vendor/bin/contao-console lint:twig <pfad>/contao/templates`.

## Frontend

- **Das Panelbild ist ein Fenster, kein Zuschnitt.** Das Bild behält seine
  Proportionen und füllt die volle Panelhöhe (`height:100%; width:auto;
  min-width:100%; max-width:none`); das Panel zeigt davon einen Ausschnitt. Wird
  daraus wieder `width/height:100%` mit `object-fit: cover`, skaliert das Bild
  bei jeder Panelbreite neu und der Aufzieh-Effekt des Vorbilds geht verloren.
- **Strukturelle CSS-Regeln brauchen zwei Ebenen** (`.imgnav .imgnav__media img`).
  Theme-Regeln wie `.main-content img { height:auto; max-width:100% }` haben
  dieselbe Spezifität wie eine einstufige Regel und gewinnen dann über die
  Quellreihenfolge, weil Theme-CSS nach dem Bundle-CSS geladen wird.
- **Ab `data-imgnav-init` steuert das Skript den Zustand.** Das JavaScript setzt
  dieses Attribut auf den Wrapper; die `:hover`-Regeln in `img-navi.css` sind
  darüber abgeschaltet und dienen nur noch als Fallback ohne JavaScript. Neue
  Zustandslogik gehört deshalb ins Skript (`.is-active` / `.has-active`), nicht
  in zusätzliche `:hover`-Regeln.
- **Die Beschriftung im zugeklappten Zustand muss in den sichtbaren Streifen
  passen.** Senkrecht gedreht (Standard) läuft sie mit `white-space: nowrap`;
  waagerecht — per Option ab 768px, in der gestapelten Ansicht immer — braucht
  sie eine Breitenbegrenzung plus `overflow-wrap: break-word` und
  `hyphens: auto`, sonst wird sie vom `overflow: hidden` des Panels
  abgeschnitten.
- **CSS/JS werden über `{% add … to stylesheets|body %}` eingebunden.** Im
  Backend gibt Contao diese Blöcke nicht aus — die Editor-Vorschau ist deshalb
  bewusst ohne Styles. Das ist Core-Verhalten, kein Fehler.
- **Der Rahmen sitzt am Panel, nicht am Bild.** Aus demselben Grund wie oben:
  das Bild ist breiter als der sichtbare Ausschnitt, ein Rahmen daran läge
  grösstenteils ausserhalb. `box-sizing: border-box` am Panel hält ihn
  innerhalb der Panelbreite, sodass das Layout unverändert bleibt.
- **Optionale Gestaltungswerte laufen über CSS-Custom-Properties am Wrapper.**
  `HtmlAttributes::addStyle` lässt Eigenschaften mit leerem Wert weg — der
  Controller gibt für nicht gesetzte Felder deshalb `''` zurück, und der
  Standard aus `img-navi.css` greift. Kein Fallback-Wert im Template
  nachbauen, das würde die Kaskade doppeln.
- **Panels nutzen ausschließlich Core-Felder von `tl_content`.** Neue Felder nur
  am Wrapper anlegen; sonst wachsen die Spalten der Kern-Tabelle für jedes Panel
  mit, ohne dass Redakteure etwas gewinnen.
