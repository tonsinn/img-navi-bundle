# TODO

Erledigte Punkte stehen im Archiv am Ende von CHANGELOG.md.

## Offen

1. **Release veröffentlichen** — GitHub-Remote fehlt noch. Sobald die URL
   vorliegt: `extra.logo` in `composer.json` gegen das tatsächliche Repo
   abgleichen (steht auf `tonsinn/img-navi-bundle`), `git remote add origin
   <URL>`, `main` und Tag `v1.0.0` pushen, danach Packagist-Eintrag +
   GitHub-Hook (macht der Nutzer).
2. **Eigener Voter:** `img_navi_item` außerhalb einer `img_navi` im Backend
   ausblenden. Aktuell lässt sich ein Panel auch direkt im Artikel anlegen; es
   rendert dann ohne Wrapper-CSS als Einzel-Panel.
3. **`load_callback` für die Panel-Überschrift**, der leere Werte auf `h3`
   vorbelegt. Der SQL-Default ist `h2`; ein per-Typ-Default in der DCA greift
   bei Bestandsdatensätzen nicht.
4. **PHPUnit-Smoke-Test** für die beiden Controller.
5. **Kompatibilität mit dem Template-Studio** prüfen.
6. **Kontrast der eingeklappten Labels:** auf hellen Motiven sind die weißen
   Labels schwer lesbar. Prüfen, ob `--imgnav-dim` als Standard erhöht werden
   sollte oder ein Textschatten-Preset sinnvoll ist.
7. **Testinstallation zurückbauen**, wenn sie nicht mehr gebraucht wird:
   `bin/remote-uninstall.sh` auf `img-navi.tonsinn.de`.
