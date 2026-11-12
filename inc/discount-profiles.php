<?php
/** Dated direct discounts. Stored legacy options remain the permanent default. */
function ds_discount_fields()
{
    $fields = array('discount_cod', 'discount_shop', 'discount_min', 'type_promotion', 'promotion', 'type_promotion_2', 'promotion_2', 'dsmart_custom_discount_date');
    foreach (array('mo', 'tu', 'we', 'th', 'fr', 'sa', 'su') as $day) {
        $fields[] = 'time_discount_shop_' . $day;
        $fields[] = 'time_discount_shop_2_' . $day;
    }
    return $fields;
}

function ds_discount_valid_date($date)
{
    if (!is_string($date)) return false;
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    return $parsed && $parsed->format('Y-m-d') === $date;
}

/** No active custom profile deliberately means no discount, never default fallback. */
function ds_discount_option($key, $timestamp = null)
{
    $profiles = get_option('ds_discount_profiles');
    if (!is_array($profiles) || !$profiles) return get_option($key);
    $today = (new DateTimeImmutable('@' . ($timestamp === null ? time() : (int) $timestamp)))
        ->setTimezone(new DateTimeZone('Europe/Berlin'))->format('Y-m-d');
    foreach ($profiles as $profile) {
        if (!is_array($profile) || !ds_discount_valid_date($profile['from'] ?? null) || !ds_discount_valid_date($profile['to'] ?? null)) continue;
        if ($today >= $profile['from'] && $today <= $profile['to']) {
            return $profile[$key] ?? ($key === 'dsmart_custom_discount_date' ? array() : '');
        }
    }
    return $key === 'dsmart_custom_discount_date' ? array() : '';
}

function ds_save_discount_profiles()
{
    if (!isset($_POST['ds_discount_profiles_present'], $_POST['ds_discount_profiles_nonce']) ||
        !current_user_can('manage_options') || !wp_verify_nonce($_POST['ds_discount_profiles_nonce'], 'ds_discount_profiles')) return;
    $rows = isset($_POST['ds_discount_profiles']) ? wp_unslash($_POST['ds_discount_profiles']) : array();
    if (!is_array($rows)) return;
    $profiles = array();
    foreach ($rows as $row) {
        if (!is_array($row) || !ds_discount_valid_date($row['from'] ?? null) || !ds_discount_valid_date($row['to'] ?? null) || $row['from'] > $row['to']) {
            echo '<div class="notice notice-error"><p>Rabatt-Konfigurationen nicht gespeichert: Bitte gültige Von-/Bis-Daten angeben.</p></div>';
            return;
        }
        $profile = array('from' => $row['from'], 'to' => $row['to'], 'name' => sanitize_text_field(is_scalar($row['name'] ?? '') ? ($row['name'] ?? '') : ''));
        foreach (ds_discount_fields() as $key) {
            if ($key === 'dsmart_custom_discount_date') continue;
            $value = isset($row[$key]) && is_scalar($row[$key]) ? (string) $row[$key] : '';
            if (in_array($key, array('discount_cod', 'discount_shop'), true)) $value = $value === 'on' ? 'on' : '';
            elseif (in_array($key, array('promotion', 'promotion_2', 'discount_min'), true)) $value = (string) max(0, (float) $value);
            elseif (strpos($key, 'type_promotion') === 0) $value = $value === 'number' ? 'number' : '%';
            else $value = sanitize_text_field($value);
            $profile[$key] = $value;
        }
        $profile['dsmart_custom_discount_date'] = array();
        $exceptions = isset($row['special_dates']) && is_string($row['special_dates']) ? $row['special_dates'] : '';
        foreach (preg_split('/\R/', $exceptions) as $line) {
            if (trim($line) === '') continue;
            $parts = array_map('trim', explode('|', $line, 2));
            if (count($parts) !== 2 || !ds_discount_valid_date($parts[0])) {
                echo '<div class="notice notice-error"><p>Rabatt-Konfigurationen nicht gespeichert: Sondertage im Format JJJJ-MM-TT | HH:MM-HH:MM eingeben.</p></div>';
                return;
            }
            $profile['dsmart_custom_discount_date'][] = array('date' => date('d-m-Y', strtotime($parts[0])), 'time' => sanitize_text_field($parts[1]));
        }
        $profiles[] = $profile;
    }
    update_option('ds_discount_profiles', $profiles, false);
}

function ds_render_discount_profile($index, $profile)
{
    $prefix = 'ds_discount_profiles[' . $index . ']';
    $input = function ($key, $label, $type = 'text') use ($prefix, $profile) {
        echo '<label style="display:block;margin:8px 0">' . esc_html($label) . ' <input type="' . esc_attr($type) . '" name="' . esc_attr($prefix . '[' . $key . ']') . '" value="' . esc_attr($profile[$key] ?? '') . '"' . ($type === 'date' ? ' required' : '') . ($type === 'number' ? ' min="0" step="0.01"' : '') . '></label>';
    };
    echo '<details open class="ds-discount-profile" style="border:1px solid #ccd0d4;padding:12px;margin:12px 0"><summary><strong>Eigene Rabatt-Konfiguration</strong></summary>';
    $input('name', 'Name'); $input('from', 'Von (einschließlich)', 'date'); $input('to', 'Bis (einschließlich)', 'date');
    $input('discount_min', 'Mindestbestellwert', 'number');
    foreach (array('' => array('Lieferung', 'discount_cod'), '_2' => array('Abholung', 'discount_shop')) as $suffix => $service) {
        echo '<h3>' . esc_html($service[0]) . '</h3><label><input type="checkbox" name="' . esc_attr($prefix . '[' . $service[1] . ']') . '" value="on" ' . checked($profile[$service[1]] ?? '', 'on', false) . '> Rabatt aktivieren</label>';
        echo '<p><select name="' . esc_attr($prefix . '[type_promotion' . $suffix . ']') . '"><option value="%" ' . selected($profile['type_promotion' . $suffix] ?? '%', '%', false) . '>Prozente</option><option value="number" ' . selected($profile['type_promotion' . $suffix] ?? '', 'number', false) . '>Fester Betrag</option></select></p>';
        $input('promotion' . $suffix, 'Rabattwert', 'number');
        foreach (array('mo' => 'Montag', 'tu' => 'Dienstag', 'we' => 'Mittwoch', 'th' => 'Donnerstag', 'fr' => 'Freitag', 'sa' => 'Samstag', 'su' => 'Sonntag') as $day => $label) $input('time_discount_shop' . $suffix . '_' . $day, $label);
    }
    $lines = array();
    foreach (($profile['dsmart_custom_discount_date'] ?? array()) as $exception) {
        $date = DateTimeImmutable::createFromFormat('!d-m-Y', $exception['date']);
        if ($date) $lines[] = $date->format('Y-m-d') . ' | ' . $exception['time'];
    }
    echo '<p>Zeiten: HH:MM-HH:MM, mehrere Zeiträume mit Komma trennen. Leere Wochentage: kein Rabatt.</p><label>Sondertage (ersetzen die Wochenzeiten für Lieferung und Abholung):<br><textarea rows="4" class="widefat" name="' . esc_attr($prefix . '[special_dates]') . '" placeholder="2026-12-24 | 12:00-14:00,18:00-20:00">' . esc_textarea(implode("\n", $lines)) . '</textarea></label><p><button type="button" class="button ds-delete-discount">Konfiguration löschen</button></p></details>';
}

function ds_render_discount_profiles()
{
    wp_nonce_field('ds_discount_profiles', 'ds_discount_profiles_nonce');
    echo '<input type="hidden" name="ds_discount_profiles_present" value="1"><h2>Direkt Rabatt – Konfigurationen</h2><p>Ohne eigene Konfiguration gilt der Standard ohne Datumsbegrenzung. Sobald eine eigene Konfiguration existiert, wird der Standard ignoriert – auch außerhalb der Datumsbereiche. Bei Überschneidungen gilt die erste passende Konfiguration. Datumsbereiche gelten einschließlich beider Tage (Europe/Berlin), bezogen auf den Bestellzeitpunkt.</p><div id="ds-discount-profiles">';
    $profiles = get_option('ds_discount_profiles');
    foreach (is_array($profiles) ? $profiles : array() as $index => $profile) ds_render_discount_profile($index, $profile);
    echo '</div><button type="button" class="button" id="ds-add-discount">Rabatt-Konfiguration hinzufügen</button><p>Änderungen mit dem Speichern-Button dieser Seite übernehmen.</p><template id="ds-discount-template">';
    ds_render_discount_profile('__INDEX__', array());
    echo '</template>';
    ?>
    <script>
    (function () {
        var list = document.getElementById('ds-discount-profiles');
        var next = list.children.length;
        document.getElementById('ds-add-discount').addEventListener('click', function () {
            list.insertAdjacentHTML('beforeend', document.getElementById('ds-discount-template').innerHTML.replace(/__INDEX__/g, String(next++)));
        });
        list.addEventListener('click', function (event) {
            if (event.target.classList.contains('ds-delete-discount')) event.target.closest('.ds-discount-profile').remove();
        });
        list.closest('form').addEventListener('submit', function (event) {
            var valid = true;
            list.querySelectorAll('.ds-discount-profile').forEach(function (panel) {
                var from = panel.querySelector('input[name$="[from]"]');
                var to = panel.querySelector('input[name$="[to]"]');
                to.setCustomValidity(from.value > to.value ? 'Bis muss am oder nach dem Von-Datum liegen.' : '');
                if (!from.checkValidity() || !to.checkValidity()) { panel.open = true; valid = false; }
            });
            if (!valid) { event.preventDefault(); list.closest('form').reportValidity(); }
        });
    })();
    </script>
    <?php
}
