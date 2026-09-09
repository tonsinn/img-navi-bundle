# Changelog

### v5.0.0 (09.09.2026)

#### Allgemein
- Erstveröffentlichung für Contao 5.3+ und Contao 6 (reine Twig-Templates)
- Getestet gegen Contao 5.7.13 und Contao 6.0.0 unter PHP 8.5
- Inhaltselement „Bildnavigation“ (`img_navi`) als Rahmenelement mit 2–6 verschachtelten
  Elementen „Bildnavigation-Panel“ (`img_navi_item`)

#### Anzeige-Einstellungen
- Anordnung horizontal (Standard) oder vertikal
- Konfigurierbare Gesamthöhe in px, vh oder rem (Standard 600px)
- Anpassung ohne Template-Override über CSS-Custom-Properties (`--imgnav-*`)

#### Bedienung
- Aufklappen per Hover auf Geräten mit Zeiger (Flex-Grow-Animation)
- Tap-Bedienung auf Touch-Geräten: erster Tap klappt auf, zweiter folgt dem Link
- Tastaturbedienung über den Button (`:focus-within`), Escape schließt
- Unter 768px immer gestapelte Darstellung
- Berücksichtigt `prefers-reduced-motion`

#### Performance
- Bild-Preload (`<link rel="preload" as="image">`) für alle Panels im `<head>`
- `loading="eager"`, `decoding="async"`, `fetchpriority="high"` für das erste Panel

---

## Claude-Code-Session-Historie

### 2026-09-09
- Projekt angelegt: Grundstruktur des Bundles erstellt
- Vorbild-Animation analysiert und in `docs/vorbild.md` dokumentiert
- Bildnavigation implementiert (Controller, DCA, Twig, CSS, JS, Sprachdateien)
- Umstellung auf Vendor `tonsinn`, Release v5.0.0
