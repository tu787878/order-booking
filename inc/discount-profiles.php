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
    $field = function ($key, $type = 'text', $attributes = '') use ($prefix, $profile) {
        echo '<input class="widefat" type="' . esc_attr($type) . '" name="' . esc_attr($prefix . '[' . $key . ']') . '" value="' . esc_attr($profile[$key] ?? '') . '" ' . $attributes . '>';
    };
    echo '<details open class="ds-discount-profile"><summary><strong>Eigene Rabatt-Konfiguration</strong></summary><div class="form-group"><h2>Name</h2>';
    $field('name');
    echo '</div><div class="form-group"><h2>Gültigkeit</h2><label>Von ';
    $field('from', 'date', 'required');
    echo '</label><label>Bis ';
    $field('to', 'date', 'required');
    echo '</label></div><div class="form-group"><h2>Direkter Rabatt Abzug</h2><div class="radio-wrap">';
    foreach (array('discount_cod' => 'Lieferung', 'discount_shop' => 'Abholung') as $key => $label) {
        echo '<label><input class="ds-discount-service" type="checkbox" name="' . esc_attr($prefix . '[' . $key . ']') . '" data-service="' . esc_attr($key) . '" value="on" ' . checked($profile[$key] ?? '', 'on', false) . '> ' . esc_html($label) . '</label>';
    }
    echo '</div></div><div class="form-group"><h2>Mindestbestellwert (für direkt Rabatt)</h2>';
    $field('discount_min', 'number', 'min="0" step="0.01"');
    echo '</div>';
    foreach (array('' => array('Lieferung', 'discount_cod'), '_2' => array('Abholung', 'discount_shop')) as $suffix => $service) {
        $hidden = ($profile[$service[1]] ?? '') === 'on' ? '' : ' hidden';
        echo '<div class="form-group ds-discount-service-fields" data-service="' . esc_attr($service[1]) . '"' . $hidden . '><h2>Rabatt ' . esc_html($service[0]) . '</h2><div class="radio-wrap">';
        foreach (array('%' => 'Prozente', 'number' => 'Nummer') as $value => $label) {
            echo '<label><input type="radio" name="' . esc_attr($prefix . '[type_promotion' . $suffix . ']') . '" value="' . esc_attr($value) . '" ' . checked($profile['type_promotion' . $suffix] ?? '%', $value, false) . '> ' . esc_html($label) . '</label>';
        }
        echo '</div>';
        $field('promotion' . $suffix, 'number', 'min="0" step="0.01"');
        echo '</div><div class="form-group timepicker-group ds-discount-service-fields" data-service="' . esc_attr($service[1]) . '"' . $hidden . '><h4>Rabattzeit ' . esc_html($service[0]) . '</h4>';
        foreach (array('mo' => 'Montag', 'tu' => 'Dienstag', 'we' => 'Mittwoch', 'th' => 'Donnerstag', 'fr' => 'Freitag', 'sa' => 'Samstag', 'su' => 'Sonntag') as $day => $label) {
            echo '<label class="ds-discount-time-row"><span class="day-of-week">' . esc_html($label) . ':</span>';
            $field('time_discount_shop' . $suffix . '_' . $day, 'text', 'data-discount-times placeholder="Gültige Rabattzeiten" autocomplete="off"');
            echo ' <button type="button" class="button button-primary ds-discount-add-time">Rabatt Zeit einfügen</button></label>';
        }
        echo '</div>';
    }
    $lines = array();
    echo '<div class="form-group ds-discount-special"><h4>Benutzerdefinierte Rabatt Zeit</h4><table class="table"><thead><tr><th>Datum</th><th>Rabatt Zeit</th><th>Zeit hinzufügen</th><th>Aktion</th></tr></thead><tbody>';
    foreach (($profile['dsmart_custom_discount_date'] ?? array()) as $exception) {
        $date = DateTimeImmutable::createFromFormat('!d-m-Y', $exception['date']);
        if (!$date) continue;
        $lines[] = $date->format('Y-m-d') . ' | ' . $exception['time'];
        ds_render_discount_special_row($date->format('Y-m-d'), $exception['time']);
    }
    echo '</tbody></table><button type="button" class="button ds-discount-add-date">Rabatt Zeit einfügen</button><input type="hidden" data-discount-special-values name="' . esc_attr($prefix . '[special_dates]') . '" value="' . esc_attr(implode("\n", $lines)) . '"></div><p><button type="button" class="button ds-delete-discount">Konfiguration löschen</button></p></details>';
}

function ds_render_discount_special_row($date = '', $times = '')
{
    echo '<tr class="ds-discount-time-row"><td><input class="widefat" type="date" data-discount-special-date value="' . esc_attr($date) . '" required></td><td><input type="text" class="widefat" data-discount-times value="' . esc_attr($times) . '" required></td><td><button type="button" class="button button-primary ds-discount-add-time">Neue Gültige Rabattzeiten</button></td><td><button type="button" class="button ds-discount-delete-date">Löschen</button></td></tr>';
}

function ds_render_discount_profiles()
{
    wp_nonce_field('ds_discount_profiles', 'ds_discount_profiles_nonce');
    echo '<input type="hidden" name="ds_discount_profiles_present" value="1"><h2>Direkt Rabatt – Konfigurationen</h2><div id="ds-discount-profiles">';
    $profiles = get_option('ds_discount_profiles');
    foreach (is_array($profiles) ? $profiles : array() as $index => $profile) ds_render_discount_profile($index, $profile);
    echo '</div><button type="button" class="button" id="ds-add-discount">Rabatt-Konfiguration hinzufügen</button><template id="ds-discount-template">';
    ds_render_discount_profile('__INDEX__', array());
    echo '</template><template id="ds-discount-date-template">';
    ds_render_discount_special_row();
    echo '</template>';
    ?>
    <style>
    .ds-discount-profile { display: flow-root; border: 1px solid #ccd0d4; padding: 12px; margin: 12px 0; }
    .ds-discount-profile > p { clear: both; }
    .ds-discount-profile [hidden] { display: none !important; }
    .ds-discount-profile .ds-discount-special table { width: auto; }
    .ds-discount-profile .ds-discount-special th { width: 200px; }
    .ds-discount-default { border: 0; padding: 0; margin: 0; min-width: 0; display: flow-root; width: 100%; }
    .ds-discount-default:disabled { opacity: .5; pointer-events: none; }
    .ds-discount-default:disabled a { pointer-events: none; }
    #ds-discount-time-dialog { border: 1px solid #ccd0d4; padding: 24px; }
    #ds-discount-time-dialog::backdrop { background: rgba(0,0,0,.3); }
    </style>
    <dialog id="ds-discount-time-dialog"><h4>Rabattzeit hinzufügen</h4><input type="time" data-time-from required> <input type="time" data-time-to required><p><button type="button" class="button button-primary" data-time-save>Ok</button> <button type="button" class="button" data-time-cancel>Schließen</button></p></dialog>
    <script>
    (function () {
        var list = document.getElementById('ds-discount-profiles');
        var next = list.children.length;
        var form = list.closest('form');
        var dialog = document.getElementById('ds-discount-time-dialog');
        document.body.appendChild(dialog);
        var target = null;
        function updateDefault() {
            form.querySelectorAll('.ds-discount-default').forEach(function (fieldset) {
                fieldset.disabled = list.children.length > 0;
                fieldset.inert = list.children.length > 0;
            });
        }
        function syncDates(panel) {
            var lines = [];
            panel.querySelectorAll('.ds-discount-special tbody tr').forEach(function (row) {
                lines.push(row.querySelector('[data-discount-special-date]').value + ' | ' + row.querySelector('[data-discount-times]').value);
            });
            panel.querySelector('[data-discount-special-values]').value = lines.join('\n');
        }
        document.getElementById('ds-add-discount').addEventListener('click', function () {
            list.insertAdjacentHTML('beforeend', document.getElementById('ds-discount-template').innerHTML.replace(/__INDEX__/g, String(next++)));
            updateDefault();
        });
        list.addEventListener('input', function (event) { syncDates(event.target.closest('.ds-discount-profile')); });
        list.addEventListener('change', function (event) {
            if (event.target.classList.contains('ds-discount-service')) {
                event.target.closest('.ds-discount-profile').querySelectorAll('.ds-discount-service-fields').forEach(function (group) {
                    if (group.dataset.service === event.target.dataset.service) group.hidden = !event.target.checked;
                });
            }
        });
        list.addEventListener('click', function (event) {
            var panel = event.target.closest('.ds-discount-profile');
            if (!panel) return;
            if (event.target.classList.contains('ds-delete-discount')) { panel.remove(); updateDefault(); }
            if (event.target.classList.contains('ds-discount-add-date')) panel.querySelector('tbody').insertAdjacentHTML('beforeend', document.getElementById('ds-discount-date-template').innerHTML);
            if (event.target.classList.contains('ds-discount-delete-date')) { event.target.closest('tr').remove(); syncDates(panel); }
            if (event.target.classList.contains('ds-discount-add-time')) {
                target = event.target.closest('.ds-discount-time-row').querySelector('[data-discount-times]');
                dialog.querySelectorAll('input').forEach(function (input) { input.value = ''; });
                dialog.showModal();
            }
        });
        dialog.querySelector('[data-time-cancel]').addEventListener('click', function () { dialog.close(); });
        dialog.querySelector('[data-time-save]').addEventListener('click', function () {
            var from = dialog.querySelector('[data-time-from]');
            var to = dialog.querySelector('[data-time-to]');
            if (!from.reportValidity() || !to.reportValidity()) return;
            var range = from.value + '-' + to.value;
            target.value = target.value.trim() ? target.value.trim() + ',' + range : range;
            syncDates(target.closest('.ds-discount-profile'));
            dialog.close();
        });
        // The default fieldset follows this script in the page markup.
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', updateDefault);
        else updateDefault();
        form.addEventListener('submit', function (event) {
            var valid = true;
            list.querySelectorAll('.ds-discount-profile').forEach(function (panel) {
                syncDates(panel);
                var from = panel.querySelector('input[name$="[from]"]');
                var to = panel.querySelector('input[name$="[to]"]');
                to.setCustomValidity(from.value > to.value ? 'Bis muss am oder nach dem Von-Datum liegen.' : '');
                if (!from.checkValidity() || !to.checkValidity()) { panel.open = true; valid = false; }
            });
            if (!valid) { event.preventDefault(); form.reportValidity(); return; }
            // Disabled controls are not submitted. Restore them only for serialization.
            form.querySelectorAll('.ds-discount-default').forEach(function (fieldset) { fieldset.disabled = false; });
            setTimeout(updateDefault, 0);
        });
    })();
    </script>
    <?php
}
