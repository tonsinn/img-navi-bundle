/*!
 * tonsinn/img-navi-bundle – Touch- und Tastaturbedienung für die Bildnavigation.
 *
 * Vanilla JS, ohne Abhängigkeiten, mehrfach ausführbar (Guard über
 * data-imgnav-init sowie eine einzige globale Instanz).
 *
 * Verhalten:
 *  - Geräte mit Zeiger (ab 768px): das Aufklappen macht CSS über :hover,
 *    Links funktionieren wie gewohnt – dieses Skript hält sich heraus.
 *  - Touch-Geräte oder schmale Bildschirme: der erste Tap klappt das Panel auf
 *    (ein Klick auf den Button wird dabei unterdrückt), der zweite Tap auf den
 *    Button folgt dem Link. Tap daneben oder Escape klappt wieder zu.
 *  - Tastatur: Fokus auf den Button klappt auf (CSS :focus-within), Verlassen zu.
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

        el.setAttribute('data-imgnav-init', '');

        var root = {
            el: el,
            container: el.querySelector('.imgnav__panels') || el,
            panels: Array.prototype.slice.call(el.querySelectorAll('[data-imgnav-panel]'))
        };

        roots.push(root);

        root.panels.forEach(function (panel) {
            panel.addEventListener('click', function (event) {
                if (!isTapMode()) {
                    // Gerät mit Zeiger: CSS erledigt das Aufklappen, Links bleiben normal
                    return;
                }

                if (panel.classList.contains('is-active')) {
                    // bereits offen: ein Tap auf den Button folgt dem Link
                    return;
                }

                var link = event.target.closest ? event.target.closest('a') : null;

                if (link && panel.contains(link)) {
                    // erster Tap klappt nur auf
                    event.preventDefault();
                }

                setActive(root, panel);
            });

            panel.addEventListener('focusin', function () {
                if (isTapMode()) {
                    // Touch: das Aufklappen übernimmt der Click-Handler
                    return;
                }

                setActive(root, panel);
            });
        });

        el.addEventListener('focusout', function (event) {
            if (!event.relatedTarget || !el.contains(event.relatedTarget)) {
                setActive(root, null);
            }
        });
    }

    function initAll(scope) {
        var nodes = (scope || document).querySelectorAll('[data-imgnav]');

        for (var i = 0; i < nodes.length; i++) {
            initRoot(nodes[i]);
        }
    }

    function deactivateAll() {
        roots.forEach(function (root) {
            setActive(root, null);
            blurInside(root);
        });
    }

    document.addEventListener('click', function (event) {
        roots.forEach(function (root) {
            if (!root.el.contains(event.target)) {
                setActive(root, null);
                blurInside(root);
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            deactivateAll();
        }
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
