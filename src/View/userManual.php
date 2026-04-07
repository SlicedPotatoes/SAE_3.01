<?php

use Uphf\GestionAbsence\Utils\Renderer;

Renderer::pushAsset('script', '<script type="module" src="/js/pages/holiday/main.js"></script>');
?>

<style>
    .manual-shell {
        min-height: 0;
    }

    /* Compatibilité avec .scroll-parent global (display:flex !important). */
    .manual-desktop {
        display: flex !important;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
    }

    .manual-mobile {
        display: none !important;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
    }

    @media (max-width: 767px) {
        .manual-desktop {
            display: none !important;
        }

        .manual-mobile {
            display: flex !important;
        }
    }

    .manual-shell .slide {
        flex: 1 1 auto;
        min-height: 0;
        overflow: auto;
    }

    /* Ajuste la barre actionsBar.php: gauche / centre / droite. */
    .manual-shell .d-grid.align-items-center.mt-auto {
        grid-template-columns: auto 1fr auto !important;
        align-items: center;
        column-gap: 0.75rem;
        width: 100%;
    }

    .manual-shell .d-grid.align-items-center.mt-auto .prev {
        justify-self: start;
        margin-right: 0 !important;
    }

    .manual-shell .d-grid.align-items-center.mt-auto .next {
        justify-self: end;
        margin-left: 0 !important;
    }

    .manual-shell .d-grid.align-items-center.mt-auto .pagination-dots {
        justify-self: center;
        align-items: center;
    }

    @media (max-width: 767px) {
        .manual-shell .d-grid.align-items-center.mt-auto {
            grid-template-columns: 1fr auto 1fr !important;
        }
    }

    .manual-illustration {
        max-height: 320px;
        width: 100%;
        object-fit: contain;
    }

    .manual-lightbox {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(0, 0, 0, 0.85);
    }

    .manual-lightbox.is-open {
        display: flex;
    }

    .manual-lightbox img {
        max-width: 95vw;
        max-height: 90vh;
        object-fit: contain;
    }

    .manual-lightbox-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        border: 0;
        border-radius: 50%;
        width: 2.25rem;
        height: 2.25rem;
    }

    .manual-clickable {
        cursor: zoom-in;
    }
</style>


<div class="card scroll-parent p-3 manual-shell">
    <h3 class="mb-3">Manuel d'utilisation</h3>

    <div class="manual-desktop scroll-parent">
        <?php
        require __DIR__ . '/Component/Pages/userManual/desktop/slide1.php';
        require __DIR__ . '/Component/Pages/userManual/desktop/slide2.php';
        require __DIR__ . '/Component/Pages/userManual/desktop/slide3.php';
        require __DIR__ . '/Component/Pages/userManual/desktop/slide4.php';
        require __DIR__ . '/Component/Pages/userManual/desktop/slide5.php';
        ?>
    </div>

    <div class="manual-mobile scroll-parent">
        <?php
        require __DIR__ . '/Component/Pages/userManual/mobile/slide1.php';
        require __DIR__ . '/Component/Pages/userManual/mobile/slide2.php';
        require __DIR__ . '/Component/Pages/userManual/mobile/slide3.php';
        require __DIR__ . '/Component/Pages/userManual/mobile/slide4.php';
        require __DIR__ . '/Component/Pages/userManual/mobile/slide5.php';
        ?>
    </div>
</div>

<div class="manual-lightbox" id="manualLightbox" aria-hidden="true">
    <button type="button" class="manual-lightbox-close" id="manualLightboxClose" aria-label="Fermer">X</button>
    <img id="manualLightboxImg" src="" alt="">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lightbox = document.getElementById('manualLightbox');
        const lightboxImg = document.getElementById('manualLightboxImg');
        const closeBtn = document.getElementById('manualLightboxClose');

        function openLightbox(src, altText) {
            lightboxImg.src = src;
            lightboxImg.alt = altText || '';
            lightbox.classList.add('is-open');
            lightbox.setAttribute('aria-hidden', 'false');
        }

        function closeLightbox() {
            lightbox.classList.remove('is-open');
            lightbox.setAttribute('aria-hidden', 'true');
            lightboxImg.src = '';
        }

        document.querySelectorAll('.manual-clickable').forEach((img) => {
            img.addEventListener('click', function () {
                openLightbox(this.src, this.alt);
            });
        });

        closeBtn.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) closeLightbox();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightbox.classList.contains('is-open')) {
                closeLightbox();
            }
        });
    });
</script>
