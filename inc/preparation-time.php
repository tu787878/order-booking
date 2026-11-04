<?php
/** Daily preparation-time overrides, shared by both settings screens and checkout. */
function ds_sanitize_preparation_windows($rows)
{
    $windows = array();
    if (!is_array($rows)) {
        return $windows;
    }
    foreach ($rows as $row) {
        if (!is_array($row) || !isset($row['from'], $row['to'], $row['minutes']) ||
            !is_string($row['from']) || !is_string($row['to']) || !is_scalar($row['minutes'])) {
            continue;
        }
        if (!preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/D', $row['from']) ||
            !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/D', $row['to']) ||
            $row['from'] === $row['to'] ||
            !preg_match('/^[0-9]+$/D', (string) $row['minutes']) || (float) $row['minutes'] > 1440) {
            continue;
        }
        $windows[] = array('from' => $row['from'], 'to' => $row['to'], 'minutes' => (int) $row['minutes']);
    }
    return $windows;
}

/** Match the selected clock time; starts are inclusive, ends exclusive. */
function ds_get_preparation_time($option, $timestamp = null)
{
    $default = get_option($option);
    if ($timestamp === null) {
        return $default;
    }
    $now = new DateTimeImmutable('@' . (int) $timestamp);
    $clock = $now->setTimezone(new DateTimeZone('Europe/Berlin'))->format('H:i');
    foreach (ds_sanitize_preparation_windows(get_option($option . '_windows')) as $window) {
        $matches = $window['from'] < $window['to']
            ? $clock >= $window['from'] && $clock < $window['to']
            : $clock >= $window['from'] || $clock < $window['to'];
        if ($matches) {
            return $window['minutes'];
        }
    }
    return $default;
}

/** Add preparation gaps to opening ranges before rendering the time selector. */
function ds_preparation_closed_ranges($ranges, $option, $date, $now = null)
{
    $zone = new DateTimeZone('Europe/Berlin');
    $now = (new DateTimeImmutable('@' . ($now === null ? time() : $now)))->setTimezone($zone);
    $today = $now->format('d-m-Y') === $date;
    $current = (int) $now->format('H') * 60 + (int) $now->format('i');
    $windows = ds_sanitize_preparation_windows(get_option($option . '_windows'));
    $to_minute = function ($clock) {
        $parts = explode(':', $clock);
        return (int) $parts[0] * 60 + (int) $parts[1];
    };
    $closed = array_fill(0, 1440, false);
    foreach ((array) $ranges as $range) {
        $start = $to_minute($range[0]);
        $end = $to_minute($range[1]);
        for ($minute = 0; $minute < 1440; $minute++) {
            if ($start <= $end ? ($minute >= $start && $minute < $end) : ($minute >= $start || $minute < $end)) {
                $closed[$minute] = true;
            }
        }
    }
    $result = $closed;
    $segment_start = 0;
    $previous_rule = null;
    for ($minute = 0; $minute < 1440; $minute++) {
        if ($closed[$minute]) {
            $previous_rule = null;
            continue;
        }
        $rule = 'default';
        $preparation = max(0, (int) get_option($option));
        foreach ($windows as $index => $window) {
            $start = $to_minute($window['from']);
            $end = $to_minute($window['to']);
            if ($start < $end ? ($minute >= $start && $minute < $end) : ($minute >= $start || $minute < $end)) {
                $rule = $index;
                $preparation = $window['minutes'];
                break;
            }
        }
        if ($rule !== $previous_rule) {
            $segment_start = $minute;
            $previous_rule = $rule;
        }
        $base = $today ? max($segment_start, $current) : $segment_start;
        $result[$minute] = $minute < $base + $preparation;
    }
    $output = array();
    $start = null;
    $clock = function ($minute) { return sprintf('%02d:%02d', intdiv($minute, 60), $minute % 60); };
    for ($minute = 0; $minute <= 1440; $minute++) {
        $blocked = $minute < 1440 && $result[$minute];
        if ($blocked && $start === null) $start = $minute;
        if (!$blocked && $start !== null) {
            $output[] = array($clock($start), $clock($minute));
            $start = null;
        }
    }
    return $output;
}

function ds_sanitize_interval_windows($rows)
{
    return array_values(array_filter(ds_sanitize_preparation_windows($rows), function ($row) {
        return $row['minutes'] > 0;
    }));
}

function ds_save_preparation_windows()
{
    if (!isset($_POST['ds_preparation_nonce']) || !is_string($_POST['ds_preparation_nonce']) ||
        !wp_verify_nonce(wp_unslash($_POST['ds_preparation_nonce']), 'ds_preparation_windows')) {
        return;
    }
    foreach (array('delay_time', 'delay_delivery') as $option) {
        $key = $option . '_windows';
        update_option($key, ds_sanitize_preparation_windows(isset($_POST[$key]) ? wp_unslash($_POST[$key]) : array()), 'yes');
    }
    foreach (array('dsmart_delivery_time_step', 'dsmart_takeaway_time_step') as $option) {
        if (!isset($_POST[$option . '_windows_present'])) continue;
        $key = $option . '_windows';
        update_option($key, ds_sanitize_interval_windows(isset($_POST[$key]) ? wp_unslash($_POST[$key]) : array()), 'yes');
    }
}

function ds_render_preparation_windows($option, $interval = false)
{
    static $script_rendered = false;
    $windows = $interval ? ds_sanitize_interval_windows(get_option($option . '_windows')) : ds_sanitize_preparation_windows(get_option($option . '_windows'));
    if (!$script_rendered) {
        wp_nonce_field('ds_preparation_windows', 'ds_preparation_nonce');
        ?>
        <script>
        document.addEventListener('invalid', function (event) {
            var parent = event.target.parentElement;
            while (parent) {
                if (parent.tagName === 'DETAILS') parent.open = true;
                parent = parent.parentElement;
            }
        }, true);
        document.addEventListener('click', function (event) {
            var button = event.target.closest('[data-preparation-add], [data-preparation-remove]');
            if (!button) return;
            var container = button.closest('[data-preparation-windows]');
            if (!container) return;
            if (button.hasAttribute('data-preparation-remove')) {
                button.closest('[data-preparation-row]').remove();
                container.closest('.ds-window-editor').querySelector('.ds-window-count').textContent = container.querySelectorAll('[data-preparation-rows] > [data-preparation-row]').length;
                return;
            }
            var index = Number(container.getAttribute('data-next-index'));
            var template = container.querySelector('template');
            var row = template.content.cloneNode(true);
            row.querySelectorAll('input').forEach(function (input) {
                input.name = input.getAttribute('data-name').replace('__INDEX__', index);
            });
            container.querySelector('[data-preparation-rows]').appendChild(row);
            container.setAttribute('data-next-index', index + 1);
            container.closest('.ds-window-editor').querySelector('.ds-window-count').textContent = container.querySelectorAll('[data-preparation-rows] > [data-preparation-row]').length;
        });
        </script>
        <?php
        $script_rendered = true;
    }
    ?>
    <details class="ds-window-editor" <?php if (count($windows)) echo 'open'; ?>>
    <summary>Zeitfenster <span class="ds-window-count"><?php echo count($windows); ?></span></summary>
    <div data-preparation-windows data-next-index="<?php echo count($windows); ?>">
        <?php if ($interval) { ?><input type="hidden" name="<?php echo esc_attr($option . '_windows_present'); ?>" value="1"><?php } ?>
        <p class="ds-settings-hint">Täglich gültig. Außerhalb gilt der Standardwert.</p>
        <details class="ds-window-help"><summary>Hinweise</summary>        <p><?php echo esc_html__($interval ? 'Innerhalb der Zeitfenster werden die auswählbaren Uhrzeiten im angegebenen Abstand ab Beginn des Zeitfensters angeboten. Außerhalb gilt der feste Zeitabstand. Bei Überschneidungen gilt das erste passende Zeitfenster. Zeiten gelten in Europe/Berlin; Zeitfenster über Mitternacht sind möglich.' : 'Außerhalb dieser Zeitfenster gilt die feste Bearbeitungszeit. Beginn inklusive, Ende exklusive. Bei Überschneidungen gilt das erste passende Zeitfenster. Die Bearbeitungszeit bestimmt die erste verfügbare Uhrzeit je Zeitfenster. Die gewählte Liefer- / Abholzeit bleibt unverändert. Zeiten gelten in Europe/Berlin; Zeitfenster über Mitternacht sind möglich.', 'dsmart'); ?></p></details>
        <div data-preparation-rows>
        <?php foreach ($windows as $index => $window) {
            ds_render_preparation_window_row($option, $index, $window, $interval);
        } ?>
        </div>
        <template><?php ds_render_preparation_window_row($option, '__INDEX__', array('from' => '', 'to' => '', 'minutes' => ''), $interval); ?></template>
        <button type="button" class="button" data-preparation-add><?php echo esc_html__('Zeitfenster hinzufügen', 'dsmart'); ?></button>
    </div>
    </details>
    <?php
}

function ds_render_preparation_window_row($option, $index, $window, $interval = false)
{
    ?>
    <div data-preparation-row style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-bottom:8px">
    <?php foreach (array('from' => 'Von', 'to' => 'Bis', 'minutes' => ($interval ? 'Zeitabstand (Minuten)' : 'Bearbeitungszeit (Minuten)')) as $field => $label) {
        $name = $option . '_windows[' . $index . '][' . $field . ']'; ?>
        <label><?php echo esc_html__($label, 'dsmart'); ?>
            <input type="<?php echo $field === 'minutes' ? 'number' : 'time'; ?>" name="<?php echo esc_attr($name); ?>" data-name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($window[$field]); ?>" <?php echo $field === 'minutes' ? ($interval ? 'min="1" max="1440" step="1"' : 'min="0" max="1440" step="1"') : ''; ?> required>
        </label>
    <?php } ?>
        <button type="button" class="button" data-preparation-remove><?php echo esc_html__('Entfernen', 'dsmart'); ?></button>
    </div>
    <?php
}
