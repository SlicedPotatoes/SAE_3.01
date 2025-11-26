<div class="d-flex justify-content-center mx-auto my-auto overflow-hidden" style="max-width:900px;">
    <div class="card border p-3 rounded-start-4 rounded-end-0" style="flex:1 1 540px; max-width:540px;">
        <div class="card-body">
            <h5 class="card-title text-center mb-2">Récupération de mot de passe</h5>
            <p class="text-muted text-center mb-4">Entrez votre adresse mail HPUF pour recevoir un lien de réinitialisation.</p>

            <form id="sendMailForm" method="post" action="/password/lost" class="row g-3">
                <div class="col-12">
                    <label for="email" class="form-label">Adresse e‑mail</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Prenom.nom@uphf.fr" required aria-describedby="emailHelp">
                    <div id="emailHelp" class="form-text">Un e‑mail contenant les instructions vous sera envoyé.</div>
                </div>

                <div class="col-12 d-grid">
                    <button type="submit" class="btn btn-uphf">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
    <div class="rounded-end-4 card-uphf d-none d-md-flex align-items-start justify-content-start" style="width: 30%; min-width:220px;"></div>
</div>