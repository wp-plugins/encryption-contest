<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// This file is importing javascript for coundown to file kamzici-ukoly.php
// Script is from http://jecas.cz/odpocitavani
$one_year = __('year', 'encrypt');
$four_years = __('years', 'encrypt');
$five_years = __('years', 'encrypt');

$one_day = __('day', 'encrypt');
$four_days = __('days', 'encrypt');
$five_days = __('days', 'encrypt');

$one_hour = __('hour', 'encrypt');
$four_days = __('days', 'encrypt');
$five_days = __('days', 'encrypt');

$one_hour = __('hour', 'encrypt');
$four_hours = __('hours', 'encrypt');
$five_hours = __('hours', 'encrypt');

$one_minute = __('minute', 'encrypt');
$four_minutes = __('minutes', 'encrypt');
$five_minutes = __('minutes', 'encrypt');

$one_second = __('second', 'encrypt');
$four_seconds = __('seconds', 'encrypt');
$five_seconds = __('seconds', 'encrypt'); 

$content .= '    
<script>
var vterina = 1000;
var minuta = vterina * 60;
var hodina = minuta * 60;
var den = hodina * 24;
var rok = den * 365.24219;

var slova = {
    roku: ["'.$one_year.'", "'.$four_years.'", "'.$five_years.'"],
    dnu: ["'.$one_day.'", "'.$four_days.'", "'.$five_days.'"],
    hodin: ["'.$one_hour.'", "'.$four_hours.'", "'.$five_hours.'"],
    minut: ["'.$one_minute.'", "'.$four_minutes.'", "'.$five_minutes.'"],
    vterin: ["'.$one_second.'", "'.$four_seconds.'", "'.$five_seconds.'"]
};

function sklonovani(pocet, co) {
    if (pocet == 1) return slova[co][0];
    if (pocet < 5 && pocet > 0) return slova[co][1];
    return slova[co][2];
}

function odpocet(el) {
    var konec = new Date(el.getAttribute("data-konec"));
    var ted = new Date();
    var rozdil = konec - ted;
    if (rozdil < vterina) {
        el.innerHTML = el.getAttribute("data-hlaska");
        return;
    }
    var zbyva = {
        roku: Math.floor(rozdil / rok),
        dnu: Math.floor(rozdil % rok / den),
        hodin: Math.floor((rozdil % den) / hodina),
        minut: Math.floor((rozdil % hodina) / minuta),
        vterin: Math.floor((rozdil % minuta) / vterina)
    }

    var vypis = el.getAttribute("data-zbyva");
    for (co in zbyva) {
        var pocet = zbyva[co];
        if (pocet > 0) vypis += " " + pocet + " " + sklonovani(pocet, co);

    }

    el.innerHTML = vypis;
    setTimeout(function() {
      odpocet(el); 
    }, vterina);
}
</script>';