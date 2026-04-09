/**
 * Script de navigation des slides (userManual / holiday)
 *
 * Compatible:
 * - index simple: "1"
 * - index groupé: "manual-desktop:1", "manual-mobile:2"
 */
const slideGroups = new Map();

function registerGroup(name, selector) {
    const slides = document.querySelectorAll(selector);
    if (slides.length > 0) {
        slideGroups.set(name, {
            slides,
            currentSlide: 0
        });
    }
}

function parseSlideTarget(target) {
    const raw = String(target ?? "").trim();

    // Compatibilité historique: "1"
    if (!raw.includes(":")) {
        return {
            group: "default",
            index: Number(raw)
        };
    }

    // Nouveau format: "group:index"
    const [group, indexRaw] = raw.split(":");
    return {
        group,
        index: Number(indexRaw)
    };
}

/**
 * Permet de changer de slide
 * @param next index simple ou "group:index"
 */
export function showSlide(next) {
    const { group, index } = parseSlideTarget(next);
    const context = slideGroups.get(group);

    if (!context) {
        throw Error(`Le groupe de slides "${group}" n'existe pas`);
    }

    if (Number.isNaN(index) || index < 0 || index >= context.slides.length) {
        throw Error(`La slide ${next} n'existe pas`);
    }

    context.slides[context.currentSlide].style.setProperty("display", "none", "important");
    context.slides[index].style.removeProperty("display");

    context.currentSlide = index;
}

// Définition des actions possibles pour les boutons
const actions = {
    backToHome: () => (window.location.href = "/"),
    showSlide: (index) => showSlide(index)
};

function bindActions() {
    document.querySelectorAll("button[data-action]").forEach((el) => {
        el.addEventListener("click", () => {
            const action = actions[el.dataset.action];

            if (action === undefined) {
                throw Error(`L'action n'existe pas !`);
            }

            action(el.dataset.param);
        });
    });
}

function initSlides() {
    // Groupe par défaut (compatibilité éventuelle)
    registerGroup("default", ".slide:not([data-slide-group])");

    // Groupes userManual
    registerGroup("manual-desktop", ".slide[data-slide-group='manual-desktop']");
    registerGroup("manual-mobile", ".slide[data-slide-group='manual-mobile']");
}

function init() {
    initSlides();
    bindActions();
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
