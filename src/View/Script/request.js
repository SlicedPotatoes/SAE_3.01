const api = "http://" + window.location.host + "/SAE_3.01/src"

async function getAbs() {
    const res = await fetch(api + "/View/etu_dashboard/testAPI.php", {
        method: "GET",
    });

    if(!res.ok) throw new Error("HTTP " + res.status);

    return res.json();
}