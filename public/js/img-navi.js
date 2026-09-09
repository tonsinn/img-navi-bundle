/*!
 * tonsinn/img-navi-bundle – Steuerung der Bildnavigation.
 *
 * Vanilla JS, ohne Abhängigkeiten, mehrfach ausführbar (Guard über
 * data-imgnav-init sowie eine einzige globale Instanz).
 *
 * Sobald dieses Skript ein Element initialisiert hat, setzt es
 * data-imgnav-init auf den Wrapper. Die Hover-Regeln in img-navi.css sind
 * dann abgeschaltet und der Zustand läuft ausschließlich über .is-active –
 * nur so lassen sich ein vorab geöffnetes Panel und das Offenbleiben nach
 * dem Verlassen umsetzen. Ohne JavaScript greift der CSS-Hover als Fallback.
 *
 * Verhalten:
 *  - Zeigergeräte: Überfahren öffnet ein Panel. Beim Verlassen kehrt die
 *    Navigation zum Startpanel zurück – oder bleibt stehen, wenn
 *    data-imgnav-sticky gesetzt ist.
 *  - Touch-Geräte / schmale Bildschirme: Der erste Tap öffnet das Panel (ein
 *    Klick auf den Button wird dabei unterdrückt), der zweite Tap auf den
 *    Button folgt dem Link. Tap daneben schließt bzw. kehrt zum Startpanel
 *    zurück.
 *  - Tastatur: Fokus auf den Button öffnet das Panel, Escape schließt es.
 *
 * Nach dynamischem Nachladen von Inhalten: window.ImgNavi.init(container)
 */
(function () {
    'use strict';

    if (window.ImgNavi) {
        return;
    }

    var hoverNone = window.matchMedia('(hover: none)');
    var narrow = window.matchMedia('(max-width: 767.98px)');
    var roots = [];

    function isTapMode() {
        return hoverNone.matches || narrow.matches;
    }

    function setActive(root, panel) {
        root.panels.forEach(function (candidate) {
            candidate.classList.toggle('is-active', candidate === panel);
        });

        root.container.classList.toggle('has-active', Boolean(panel));
    }

    /**
     * Zustand nach dem Verlassen bzw. einem Tap daneben: Startpanel wieder
     * öffnen, alles schließen – oder unverändert lassen, wenn das Panel laut
     * Einstellung offen bleiben soll.
     */
    function restore(root) {
        if (root.sticky) {
            return;
        }

        setActive(root, root.initialPanel);
    }

    function blurInside(root) {
        var active = document.activeElement;

        if (active && active !== document.body && root.el.contains(active)) {
            active.blur();
        }
    }

    function initRoot(el) {
        if (el.hasAttribute('data-imgnav-init')) {
            return;
        }

        var panels = Array.prototype.slice.call(el.querySelectorAll('[data-imgnav-panel]'));
        var initial = parseInt(el.getAttribute('data-imgnav-initial'), 10);

        var root = {
            el: el,
            container: el.querySelector('.imgnav__panels') || el,
            panels: panels,
            sticky: el.hasAttribute('data-imgnav-sticky'),
            initialPanel: initial > 0 ? panels[initial - 1] || null : null
        };

        roots.push(root);

        panels.forEach(function (panel) {
            // Zeigergeräte: Überfahren öffnet das Panel
            panel.addEventListener('mouseenter', function () {
                if (isTapMode()) {
                    return;
                }

                setActive(root, panel);
            });

            panel.addEventListener('click', function (event) {
                if (!isTapMode()) {
                    return;
                }

                if (panel.classList.contains('is-active')) {
                    // bereits offen: ein Tap auf den Button folgt dem Link
                    return;
                }

                var link = event.target.closest ? event.target.closest('a') : null;

                if (link && panel.contains(link)) {
                    // erster Tap öffnet nur
                    event.preventDefault();
                }

                setActive(root, panel);
            });

            panel.addEventListener('focusin', function () {
                if (isTapMode()) {
                    // Touch: das Öffnen übernimmt der Click-Handler
                    return;
                }

                setActive(root, panel);
            });
        });

        el.addEventListener('mouseleave', function () {
            if (isTapMode()) {
                return;
            }

            restore(root);
        });

        el.addEventListener('focusout', function (event) {
            if (!event.relatedTarget || !el.contains(event.relatedTarget)) {
                restore(root);
            }
        });

        // Ab hier steuert das Skript den Zustand, der CSS-Hover ist abgeschaltet
        el.setAttribute('data-imgnav-init', '');
        setActive(root, root.initialPanel);
    }

    function initAll(scope) {
        var nodes = (scope || document).querySelectorAll('[data-imgnav]');

        for (var i = 0; i < nodes.length; i++) {
            initRoot(nodes[i]);
        }
    }

    document.addEventListener('click', function (event) {
        roots.forEach(function (root) {
            if (!root.el.contains(event.target)) {
                restore(root);
                blurInside(root);
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }

        // Escape ist eine bewusste Geste: schließt auch bei "geöffnet lassen"
        roots.forEach(function (root) {
            setActive(root, root.initialPanel);
            blurInside(root);
        });
    });

    window.ImgNavi = { init: initAll };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initAll();
        });
    } else {
        initAll();
    }
})();
