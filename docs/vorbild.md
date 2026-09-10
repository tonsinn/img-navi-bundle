# Analyse: Bildnavigation

**Element:** Vertikale Accordion-Image-Navigation (4 Panels, oberhalb des Folds)

---

## 1. Visuelles Konzept (Beobachtung)

Vier Bilder liegen **vertikal gestapelt** untereinander. Im Ruhezustand sind alle
Panels gleichmäßig schmal (nur ein schmaler Streifen ist sichtbar – Bild als
Hintergrund, ggf. kurzer Titel senkrecht oder abgeschnitten).

Bei **Mouse-Over** öffnet sich das aktive Panel auf seine volle Höhe/Breite,
während die übrigen Panels zusammengedrückt werden. Das geöffnete Panel zeigt:

- das Bild als vollflächigen Hintergrund (mit `background-size: cover`)
- einen **dunklen Gradient-Overlay** (unten nach oben), damit Text lesbar bleibt
- einen **Titel** (h2/h3)
- eine **kurze Beschreibung** (optional)
- einen **CTA-Button** (z. B. „Mehr erfahren")

---

## 2. HTML-Struktur

```html
<div class="img-navi">
  <div class="img-navi__panel" style="--panel-bg: url('bild1.jpg')">
    <div class="img-navi__content">
      <h3 class="img-navi__title">Titel Panel 1</h3>
      <p class="img-navi__text">Kurze Beschreibung zum Thema.</p>
      <a href="/seite1.html" class="img-navi__btn">Mehr erfahren</a>
    </div>
  </div>

  <div class="img-navi__panel" style="--panel-bg: url('bild2.jpg')">
    <div class="img-navi__content">
      <h3 class="img-navi__title">Titel Panel 2</h3>
      <p class="img-navi__text">Kurze Beschreibung zum Thema.</p>
      <a href="/seite2.html" class="img-navi__btn">Mehr erfahren</a>
    </div>
  </div>

  <!-- ... weitere Panels ... -->
</div>
```

**Variante mit Data-Attribut** (kein inline Style nötig):
```html
<div class="img-navi__panel" data-bg="/files/pfad/bild.jpg">
```
→ Per JS gesetzt: `panel.style.backgroundImage = 'url(' + panel.dataset.bg + ')'`

---

## 3. CSS-Konzept

### Kern-Mechanismus: Flexbox + `flex` Gewichtung

```css
/* Wrapper: vertikale Flex-Spalte */
.img-navi {
  display: flex;
  flex-direction: column;
  height: 600px;          /* fixe Gesamthöhe */
  overflow: hidden;
}

/* Panel im Ruhezustand: klein */
.img-navi__panel {
  flex: 1;                /* alle gleich groß */
  position: relative;
  background-size: cover;
  background-position: center;
  cursor: pointer;
  transition: flex 0.5s ease;
  overflow: hidden;
}

/* Aktives / gehovertes Panel: groß */
.img-navi:hover .img-navi__panel {
  flex: 0.3;              /* nicht-aktive schrumpfen */
}
.img-navi__panel:hover {
  flex: 3;                /* aktives wächst */
}
```

> **Schlüsseltrick:** `flex`-Wert bestimmt den proportionalen Anteil.
> Bei Hover ändert sich nur die Zahl — kein absolutes `height` nötig.
> Die `transition: flex` sorgt für die weiche Auf-/Zuklapp-Animation.

---

### Gradient-Overlay & Content-Sichtbarkeit

```css
/* Dunkler Gradient von unten */
.img-navi__panel::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to top,
    rgba(0, 0, 0, 0.75) 0%,
    rgba(0, 0, 0, 0.1) 60%,
    transparent 100%
  );
  transition: opacity 0.4s ease;
  opacity: 0;
}

.img-navi__panel:hover::after {
  opacity: 1;
}

/* Content (Titel, Text, Button) */
.img-navi__content {
  position: absolute;
  bottom: 2rem;
  left: 2rem;
  right: 2rem;
  z-index: 1;
  color: #fff;
  transform: translateY(1rem);
  opacity: 0;
  transition: opacity 0.4s ease 0.15s, transform 0.4s ease 0.15s;
}

.img-navi__panel:hover .img-navi__content {
  opacity: 1;
  transform: translateY(0);
}
```

> Der Inhalt erscheint mit einem leichten **Slide-up-Fade** (transform + opacity),
> verzögert um 150 ms, damit das Panel erst etwas aufgegangen ist.

---

### Schmaler Streifen im Ruhezustand (Fallback-Beschriftung)

Damit der Nutzer erkennt, welches Panel welches Thema hat, gibt es oft
einen **vertikalen Titel** im geschlossenen Zustand:

```css
.img-navi__label {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%) rotate(-90deg);
  color: #fff;
  font-size: 0.9rem;
  white-space: nowrap;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  opacity: 1;
  transition: opacity 0.2s ease;
}

.img-navi__panel:hover .img-navi__label {
  opacity: 0;   /* ausblenden wenn Panel offen */
}
```

---

### Button-Styling (CTA)

```css
.img-navi__btn {
  display: inline-block;
  margin-top: 1rem;
  padding: 0.6em 1.4em;
  background: var(--color-primary, #004a99);
  color: #fff;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 600;
  transition: background 0.2s;
}

.img-navi__btn:hover {
  background: var(--color-primary-dark, #003070);
}
```

---

## 4. Animations-Ablauf (Timeline)

| Zeit      | Ereignis |
|-----------|----------|
| t = 0 ms  | Maus betritt Panel |
| t = 0 ms  | `flex`-Transition startet (Panel öffnet sich, andere schließen) |
| t = 0 ms  | Gradient-Overlay beginnt einzublenden |
| t = 150 ms| Content beginnt einzublenden (slide-up) |
| t = 400 ms| Gradient vollständig sichtbar |
| t = 500 ms| Panel vollständig geöffnet, Content vollständig sichtbar |
| t = 0 ms* | Maus verlässt Panel → alles kehrt in Ausgangsposition zurück |

*Rücktransition läuft analog in gleicher Dauer.

---

## 5. Responsive-Verhalten

| Breakpoint       | Verhalten |
|------------------|-----------|
| Desktop (≥ 992px) | Horizontale Variante: Panels nebeneinander (flex-direction: row) |
| Tablet (≥ 768px)  | Vertikale Variante: Panels übereinander |
| Mobil (< 768px)   | Einfache Liste: Panels feste Höhe, kein Hover-Effekt, immer aufgeklappt oder per Tap |

---

## 6. Accessibility

- `<div>` Panels → besser `<a>` oder `role="button" tabindex="0"` für Tastatur-Navigation
- `:focus-visible` Styles wie `:hover` setzen
- `aria-label` am Wrapper: `"Themen-Navigation"`
- Bilder: `aria-hidden="true"` (dekorativ), Text im DOM reicht
- Für Screen-Reader: verstecktes `<span class="sr-only">` mit Linkziel-Beschreibung

---

## 7. Contao Content Element – geplante Umsetzung

```
ContentElement: ImgNaviController
├── DCA: tl_content
│   ├── imgNaviItems (multiColumnWizard oder eigene Palette)
│   │   ├── image        (singleSRC / Dateiauswahl)
│   │   ├── title        (text)
│   │   ├── text         (textarea)
│   │   ├── url          (url)
│   │   └── linkTitle    (text)
│   └── imgNaviHeight    (text, default: 600px)
│
├── Template: ce_img_navi.html5 / ce_img_navi.html.twig
└── Assets:
    ├── img-navi.css
    └── img-navi.js  (optional, für Fallback/Touch)
```

---

## 8. Offene Fragen / Entscheidungen

- [ ] Horizontale oder vertikale Anordnung als Standard?
- [ ] Anzahl Panels fix (4) oder konfigurierbar (2–6)?
- [ ] Twig-Template oder HTML5-Template?
- [ ] Touch-Support: Tap statt Hover auf Mobilgeräten?
- [ ] Bild-Vorladen (preload) für Performance?
