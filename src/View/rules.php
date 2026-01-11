<?php
?>
<!-- Page unique : synthèse seulement (onglet 'Texte complet' supprimé) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
<link rel="stylesheet" href="/style/style.css">

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card shadow-sm">
        <div class="card-body p-4">

          <div class="d-flex align-items-start mb-3">
            <div>
              <h1 class="h3 mb-1">Gestion des absences à l’IUT de Maubeuge</h1>
              <p class="text-muted mb-0">Rappel synthétique des règles d'assiduité.</p>
            </div>
          </div>

          <!-- Synthèse : contenu principal -->
          <section id="principes" class="mb-4">
            <h2 class="h5">Principes généraux</h2>
            <p>La présence aux enseignements (cours, TD, TP, projets, contrôles continus, examens) est obligatoire pour l’ensemble des étudiant·e·s inscrit·e·s à l’IUT, sauf dérogation accordée par l’établissement.</p>
          </section>

          <section id="signaler" class="mb-4">
            <h2 class="h5">Signaler et justifier une absence</h2>
            <p>Toute absence doit être signalée dès que possible au département et justifiée selon les modalités et délais prévus par le règlement intérieur de l’IUT et le règlement de l'UPHF.</p>
          </section>

          <section id="consequences" class="mb-4">
            <h2 class="h5">Conséquences (résumé)</h2>
            <ul class="mb-2">
              <li>Les justificatifs doivent être transmis au responsable pédagogique dans les délais ; sinon, l'absence peut être considérée comme non excusée.</li>
              <li>Absences injustifiées ou répétées → pénalités académiques (malus, notes 0) et, en cas de cumul, risque de radiation.</li>
            </ul>
          </section>

          <div class="d-flex justify-content-between mt-4">
            <a href="/" class="btn btn-outline-secondary">Retour à l'accueil</a>
            <a href="https://recueildesactes.uphf.fr/browse/uphf/iut/reglmts-intrs-iut" class="btn btn-primary">Règlement de l'UPHF et de l'IUT</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Ajustements mineurs */
.h3 { color: #0b5ed7; }
.card-body h2 { color: #0d6efd; }
</style>
