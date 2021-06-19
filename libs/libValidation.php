<?php

// Validation de différentes entrées utilisateur

/**
 * Vérifie si une note est valide (entier entre 1 et 5)
 * @param int note
 * @return bool
 */
function is_valid_note($note)
{
    return (is_int($note) && $note > 0 && $note <= 5);
}

/**
 * Vérifie si une heure est valide (hh:mm) par créneaux de 30 minutes, et 23:59 = minuit
 * @param string time
 * @return bool
 */
function is_valid_time($time)
{
    if (strlen($time) == 5) {
        $heure = intval(substr($time, 0, 2));
        $delimiter = $time[2];
        $minutes = substr($time, 3, 2);
        if ($time == "23:59" ||
            ($heure >= 0 && $heure <= 23 && $delimiter == ":" && ($minutes == "00" || $minutes == "30"))) {
            return true;
        }
    }
    return false;
}

/**
 * Vérifie si une chaîne de caractères correspond bien à une date au format Y-m-d
 */
function is_valid_date($date)
{
    list($y, $m, $d) = explode('-', $date);
    if (checkdate($m, $d, $y)) {
        return true;
    }
    return false;
}
