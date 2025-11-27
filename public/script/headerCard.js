/**
 * Script pour gérer le changement du cookie "cardOpen" a l'ouverture / fermeture des cards pour le profil étudiant
 */

const headerCard = document.getElementById("showCard");

if(headerCard) {
    headerCard.addEventListener('show.bs.collapse', () => {
        const date = new Date();
        date.setTime(date.getTime() + (24*60*60*1000));
        document.cookie = "cardOpen=true; expires=" + date.toUTCString() + ";";
        console.log(document.cookie);
    });
    headerCard.addEventListener('hide.bs.collapse', () => {
        const date = new Date();
        date.setTime(date.getTime() + (24*60*60*1000));
        document.cookie = "cardOpen=false; expires=" + date.toUTCString() + ";";
        console.log(document.cookie);
    });
}