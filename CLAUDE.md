# img-navi-bundle — Contao 5 Bundle

## Projekt-Überblick
Image Navigation Bundle für Contao 5.
Vendor: Mailwurm, Namespace: `Mailwurm\ImgNaviBundle`.

## Paket-Identität
- **Packagist**: `mailwurm/img-navi-bundle`
- **PHP-Namespace**: `Mailwurm\ImgNaviBundle`
- **Contao-Kompatibilität**: Contao 5.x (Symfony-Bundle-Struktur)
- **Git-Tag für Packagist**: nach Push eines Tags `vX.Y.Z` automatisch

## Tech-Stack
- PHP 8.2+, Symfony Bundle
- Contao 5.x (DCA, Templates, Content Elements)
- DDEV für lokale Entwicklung (Contao 5.7-Instanz unter `~/contao57/`)
- Composer für Dependency Management

## Verzeichnisstruktur
```
img-navi/
├── src/
│   ├── Controller/          # Content Element Controller
│   ├── ContaoManager/       # Plugin.php für Contao Manager
│   ├── DependencyInjection/ # Bundle-Konfiguration
│   └── Resources/
│       ├── config/          # services.yaml
│       ├── public/          # Assets (CSS, JS)
│       └── contao/
│           └── templates/   # Contao-Templates
├── contao/
│   └── dca/                 # DCA-Definitionen
├── composer.json
├── CHANGELOG.md
├── TODO.md
└── README.md
```

## Lokale Entwicklung (DDEV)
```bash
# Bundle-Änderungen in DDEV-Vendor synchronisieren (Symlinks funktionieren nicht!)
~/contao57/sync-bundle.sh

# DDEV-Umgebung
cd ~/contao57
ddev start
ddev exec php vendor/bin/contao-console cache:clear
```

**Wichtig:** Symlinks im DDEV-Container funktionieren wegen WSL2-Beschränkungen
nicht. Immer das rsync-Sync-Script verwenden (`~/contao57/sync-bundle.sh`).

## Versionierung & Release-Workflow
```bash
# 1. Code-Änderungen committen
git add . && git commit -m "feat: beschreibung"

# 2. Version taggen
git tag v5.0.0

# 3. Auf GitHub pushen (Packagist registriert automatisch)
git push origin main --tags
```

## Bekannte Eigenheiten (geerbt von belegungsplan-bundle)
- Template-Namen müssen exakt zum Contao-Naming-Convention passen
- Nach Bundle-Änderungen: Cache leeren + `sync-bundle.sh` ausführen
- Contao 5 ist Twig-basiert, HTML5-Templates werden noch unterstützt
- Custom Widget-Typen müssen explizit als Service registriert werden
- DCA-Paletten müssen vollständig sein, sonst kein Render im Backend

---

Release-Historie: siehe CHANGELOG.md.
Offene Punkte: siehe TODO.md.
