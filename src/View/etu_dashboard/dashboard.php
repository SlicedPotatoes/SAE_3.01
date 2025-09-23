<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard Etudiant</title>
        <link href="../Style/style.css" rel="stylesheet">
    </head>
    <body>
        <div class="main-container">
            <ul class="tab">
                <li class="tab-item active">En cours</li>
                <li class="tab-item">Historique</li>
            </ul>
            <div class="tab-content">
                <?php
                    require "filter_bar.php";
                ?>
                <div class="abs-container">
                    <div class="abs">
                        <div class="date">
                            <div>Date du début <span>XX/XX/XX</span></div>
                            <div>Date de fin <span>XX/XX/XX</span></div>
                        </div>
                        <div class="tags">
                            <span class="tag approved">Validé</span>
                            <span class="tag rejected">Refusé</span>
                            <span class="tag under-review">En révision</span>
                            <span class="tag unjustified">Non-justifié</span>
                            <span class="tag pending">Attente</span>
                            <span class="tag exam">Examen</span>
                        </div>
                        <button>Sex</button>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <h5>Tab 2</h5>
                <?php
                    require "filter_bar.php";
                ?>
                <div class="abs-container"></div>
            </div>
        </div>

        <script src="../Script/request.js"></script>
        <script type="module">
            let tabs = document.querySelectorAll(".tab-item");
            let tabsContent = document.querySelectorAll(".tab-content");

            for(let i = 0; i < tabs.length; i++) {
                tabs[i].addEventListener("click", () => {
                    // Masquer le contenu de toutes les tabs
                    for(let j = 0; j < tabs.length; j++) {
                        tabs[j].classList.remove("active");
                        tabsContent[j].style.display = "none";
                    }
                    // Afficher le contenu de la tab sélectionner
                    tabs[i].classList.add("active");
                    tabsContent[i].style.display = "block";
                });
            }

            // Trigger l'event pour la tab1
            tabs[0].dispatchEvent(new Event("click"));

            // Test
            const tags = {
                "approved": "Validé",
                "rejected": "Refusé",
                "under-review": "En révision",
                "unjustified": "Non-justifié",
                "pending": "Attente"
            };

            let datas = await getAbs();

            for(let i = 0; i < datas.length; i++) {
                const absDiv = document.createElement("div");
                absDiv.classList.add("abs");

                // Dates
                const dateDiv = document.createElement("div");
                dateDiv.classList.add("date");
                const startDateDiv = document.createElement("div");
                startDateDiv.innerHTML = "Date du début " +  new Date(datas[0]['startDate']['date']).toLocaleDateString("fr-FR");
                const endDateDiv = document.createElement("div");
                endDateDiv.innerHTML = "Date du fin " +  new Date(datas[0]['endDate']['date']).toLocaleDateString("fr-FR");
                dateDiv.append(startDateDiv, endDateDiv);

                // Tags
                const tagsDiv = document.createElement("div");
                tagsDiv.classList.add("tags");
                const state = document.createElement("span");
                state.classList.add('tag', datas[i]["state"]);
                state.textContent = tags[datas[i]["state"]];
                tagsDiv.append(state);
                if(datas[i]["exam"]) {
                    const exam = document.createElement("span");
                    exam.classList.add('tag', 'exam');
                    exam.textContent = "Examen";
                    tagsDiv.append(exam);
                }

                // Button
                const button = document.createElement("button");
                button.textContent = "Voir";

                absDiv.append(dateDiv, tagsDiv, button);

                if(datas[i].state === "under-review" || datas[i].state === "pending") {
                    tabsContent[0].getElementsByClassName("abs-container")[0].append(absDiv);
                }
                else {
                    tabsContent[1].getElementsByClassName("abs-container")[0].append(absDiv);
                }
            }
        </script>
    </body>
</html>