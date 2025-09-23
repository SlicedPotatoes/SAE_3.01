<form class="filter-bar" action="" method="GET">
    <label>
        Date début:
        <input id="startDate" type="date"/>
    </label>

    <label>
        Date fin:
        <input id="endDate" type="date"/>
    </label>

    <label>
        Etat:
        <select id="state">
            <option>A</option>
            <option>B</option>
        </select>
    </label>

    <div class="checkbox-container">
        <input id="exam" type="checkbox">
        <label for="exam">Examen</label>
    </div>

    <button type="submit">Appliquer</button>
</form>