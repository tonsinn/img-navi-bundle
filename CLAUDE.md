# img-navi-bundle — Contao 5 Bundle

## Projekt-Überblick
Animierte Bildnavigation als Inhaltselement für Contao 5.
Vendor: Tonsinn, Namespace: `Tonsinn\ImgNaviBundle`.

## Paket-Identität
- **Packagist**: `tonsinn/img-navi-bundle`
- **PHP-Namespace**: `Tonsinn\ImgNaviBundle`
- **Bundle-Klasse**: `TonsinnImgNaviBundle` (Asset-Pfad `bundles/tonsinnimgnavi/`)
- **Contao-Kompatibilität**: Contao 5.3+ und Contao 6 (getestet: 5.7.13, 6.0.0)
- **Git-Tag für Packagist**: nach Push eines Tags `vX.Y.Z` automatisch

## Elemente
- `img_navi` — Rahmenelement, rendert 2–6 verschachtelte Panels (`nestedFragments`)
- `img_navi_item` — einzelnes Panel; nutzt ausschließlich Core-Felder von `tl_content`

## Tech-Stack
- PHP 8.2–8.5, Symfony Bundle
- Contao 5.3+ / 6.x (DCA, Twig-Templates, Content Elements)
- DDEV für lokale Entwicklung (Contao 5.7 unter `~/contao57/`)
- Remote-Testserver über `testinstall.env` (nicht im Git!)

## Verzeichnisstruktur
```
img-navi/
├── bin/                     # Sync- und Deploy-Skripte (nicht im Dist-Paket)
├── config/services.yaml
├── contao/
│   ├── dca/tl_content.php
│   ├── languages/{de,en}/   # XLIFF
│   └── templates/content_element/
├── docs/vorbild.md          # Analyse der Vorbild-Animation
├── public/                  # CSS, JS, Logo (Asset-Root des Bundles)
└── src/
    ├── TonsinnImgNaviBundle.php   # getPath() -> Bundle-Root
    ├── ContaoManager/Plugin.php
    ├── Controller/ContentElement/
    ├── DependencyInjection/
    └── EventListener/DataContainer/
```

**Wichtig:** `contao/` und `public/` liegen im Bundle-Root (nicht unter
`src/Resources/`). Das funktioniert nur wegen des `getPath()`-Overrides in
`TonsinnImgNaviBundle`.

## Lokale Entwicklung (DDEV)
```bash
# Bundle in die DDEV-Instanz synchronisieren, Cache leeren, Assets verlinken
bin/sync-img-navi.sh            # --migrate ergänzen, wenn sich die DCA geändert hat

cd ~/contao57 && ddev start
```

## Remote-Test
```bash
cp testinstall.env.example testinstall.env   # ausfüllen, steht in .gitignore
bin/remote-install.sh                        # rsync + composer + migrate
bin/remote-uninstall.sh                      # Rückbau
```
Auf dem KeyHelp-Server läuft PHP 8.5 über `keyhelp-php85`. Composer und
`contao-console` dort **immer** über diese Binary aufrufen — das System-`php`
ist älter (8.3). Die PHP-Version des vHosts muss ebenfalls 8.5 sein, sonst
schlägt `vendor/composer/platform_check.php` im Frontend fehl.

## Versionierung & Release-Workflow
```bash
git add . && git commit -m "feat: beschreibung"
git tag v5.0.0
git push origin main --tags     # Packagist zieht per Hook nach
```

## Bekannte Eigenheiten

Die dauerhaften Regeln (Contao-API, Bundle-Struktur, Deployment-Reihenfolge,
Frontend-Invarianten) stehen gesammelt in **HINWEISE.md** — vor Änderungen an
Templates, CSS/JS oder den Deploy-Skripten dort nachlesen.

Die drei häufigsten Stolpersteine:
- `cache:clear` muss VOR `contao:migrate` laufen, sonst fehlen neue Spalten
- `contao/templates/.twig-root` muss existieren, sonst werden Templates unter
  falschem Namespace registriert
- Auf dem Testserver immer `keyhelp-php85` statt `php`/`composer` aufrufen

## Letzter Stand

Das Bundle ist vollständig implementiert und auf zwei Instanzen verifiziert:
Contao 5.7.13 (`img-navi.tonsinn.de`) und Contao 6.0.0 (`tao6.tonsinn.de`),
beide unter PHP 8.5. Die Contao-6-Instanz wurde nach dem Test zurückgebaut, die
5.7-Testinstallation läuft weiter.

Vier Commits liegen lokal auf `main` und sind **noch nicht gepusht** — es ist
kein Git-Remote konfiguriert. Nächster Schritt: GitHub-Remote-URL vom Nutzer
einholen, `extra.logo` in `composer.json` gegen das tatsächliche Repo abgleichen
(steht auf `tonsinn/img-navi-bundle`), dann `main` und Tag `v5.0.0` pushen; der
Packagist-Eintrag erfolgt durch den Nutzer.

Details: siehe CHANGELOG.md, Teil 1. Offene Punkte: siehe TODO.md.

---

Release-Historie: siehe CHANGELOG.md.
Offene Punkte: siehe TODO.md.
Dauerhafte Invarianten: siehe HINWEISE.md.
