<!-- View formulaire du mot de passe oublié -->
<div class="d-flex justify-content-center my-auto">

    <div class="card p-md-3 rounded-start-1 rounded-end-0">
        <div class="card-body">
            <h5 class="card-title text-center mb-2">Récupération du mot de passe</h5>
            <p class="text-muted text-center mb-4">Entrez votre adresse mail UPHF pour recevoir un lien de réinitialisation.</p>

            <form id="sendMailForm" method="post" class="row g-3">
                <div class="col-12">
                    <label for="email" class="form-label">Adresse e‑mail</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="prenom.nom@uphf.fr" required aria-describedby="emailHelp">
                    <div id="emailHelp" class="form-text">Un e‑mail contenant les instructions vous sera envoyé.</div>
                </div>

                <div class="col-12 d-flex flex-row gap-2 justify-content-between">
                    <a class="btn btn-secondary" href="/">Retour</a>
                    <button type="submit" class="btn btn-uphf">Envoyer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="rounded-end-1 card-uphf d-none d-md-flex align-items-start justify-content-start" style="width: 30%; min-width:220px;"></div>
</div>