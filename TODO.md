# TODO

Erledigte Punkte stehen im Archiv am Ende von CHANGELOG.md.

## Offen

1. **Eigener Voter:** `img_navi_item` außerhalb einer `img_navi` im Backend
   ausblenden. Aktuell lässt sich ein Panel auch direkt im Artikel anlegen; es
   rendert dann ohne Wrapper-CSS als Einzel-Panel.
2. **`load_callback` für die Panel-Überschrift**, der leere Werte auf `h3`
   vorbelegt. Der SQL-Default ist `h2`; ein per-Typ-Default in der DCA greift
   bei Bestandsdatensätzen nicht.
3. **PHPUnit-Smoke-Test** für die beiden Controller.
4. **Kompatibilität mit dem Template-Studio** prüfen.
5. **Kontrast der eingeklappten Labels:** auf hellen Motiven sind die weißen
   Labels schwer lesbar. Prüfen, ob `--imgnav-dim` als Standard erhöht werden
   sollte oder ein Textschatten-Preset sinnvoll ist.
6. **Testinstallation zurückbauen**, wenn sie nicht mehr gebraucht wird:
   `bin/remote-uninstall.sh` auf `img-navi.tonsinn.de`.
7. **Deutsches Keyword `bildnavigation`** in `composer.json` ergänzen. Eine
   Suche auf Deutsch findet das Paket derzeit nicht, weil Description und
   Keywords englisch sind. Greift erst mit einem neuen Tag, weil Packagist die
   Keywords je Version liest — beim nächsten inhaltlichen Release mitnehmen,
   nicht als eigenes Release.
8. **Textfarbe der Buttons** hat kein Backend-Feld, nur `--imgnav-btn-color`.
   Prüfen, ob ein Feld nötig ist, sobald jemand einen hellen Button-Hintergrund
   wählt und der weiße Text unlesbar wird.
9. **GitHub-Repo abrunden:** Description und Topics im *About*-Kasten setzen,
   Website auf die Packagist-URL. Vorschläge stehen in CHANGELOG.md, Teil 2.
