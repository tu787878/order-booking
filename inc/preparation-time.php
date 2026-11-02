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

/** Add preparation once to an explicit selection; preserve the ASAP option. */
function ds_prepare_selected_time($option, $clock, $date = null)
{
    if ($clock === 'So schnell wie möglich') {
        return array('time' => $clock, 'date' => $date, 'minutes' => 0);
    }
    if (!is_string($clock) || !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/D', $clock)) {
        return false;
    }
    $zone = new DateTimeZone('Europe/Berlin');
    $date = $date ?: (new DateTimeImmutable('now', $zone))->format('d-m-Y');
    if (!is_string($date)) return false;
    $selected = DateTimeImmutable::createFromFormat('!d-m-Y H:i', $date . ' ' . $clock, $zone);
    if (!$selected || $selected->format('d-m-Y H:i') !== $date . ' ' . $clock) return false;
    $minutes = max(0, (int) ds_get_preparation_time($option, $selected->getTimestamp()));
    $ready = $selected->modify('+' . $minutes . ' minutes');
    return array('time' => $ready->format('H:i'), 'date' => $ready->format('d-m-Y'), 'minutes' => $minutes);
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
}

function ds_render_preparation_windows($option)
{
    static $script_rendered = false;
    $windows = ds_sanitize_preparation_windows(get_option($option . '_windows'));
    if (!$script_rendered) {
        wp_nonce_field('ds_preparation_windows', 'ds_preparation_nonce');
        ?>
        <script>
        document.addEventListener('click', function (event) {
            var button = event.target.closest('[data-preparation-add], [data-preparation-remove]');
            if (!button) return;
            var container = button.closest('[data-preparation-windows]');
            if (!container) return;
            if (button.hasAttribute('data-preparation-remove')) {
                button.closest('[data-preparation-row]').remove();
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
        });
        </script>
        <?php
        $script_rendered = true;
    }
    ?>
    <div data-preparation-windows data-next-index="<?php echo count($windows); ?>">
        <p><?php echo esc_html__('Individuelle Bearbeitungszeiten (täglich)', 'dsmart'); ?></p>
        <p><?php echo esc_html__('Außerhalb dieser Zeitfenster gilt die feste Bearbeitungszeit. Beginn inklusive, Ende exklusive. Bei Überschneidungen gilt das erste passende Zeitfenster. Die ausgewählte Uhrzeit bestimmt die Bearbeitungszeit, die zur Auswahl addiert wird. Zeiten gelten in Europe/Berlin; Zeitfenster über Mitternacht sind möglich.', 'dsmart'); ?></p>
        <div data-preparation-rows>
        <?php foreach ($windows as $index => $window) {
            ds_render_preparation_window_row($option, $index, $window);
        } ?>
        </div>
        <template><?php ds_render_preparation_window_row($option, '__INDEX__', array('from' => '', 'to' => '', 'minutes' => '')); ?></template>
        <button type="button" class="button" data-preparation-add><?php echo esc_html__('Zeitfenster hinzufügen', 'dsmart'); ?></button>
    </div>
    <?php
}

function ds_render_preparation_window_row($option, $index, $window)
{
    ?>
    <div data-preparation-row style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-bottom:8px">
    <?php foreach (array('from' => 'Von', 'to' => 'Bis', 'minutes' => 'Bearbeitungszeit (Minuten)') as $field => $label) {
        $name = $option . '_windows[' . $index . '][' . $field . ']'; ?>
        <label><?php echo esc_html__($label, 'dsmart'); ?>
            <input type="<?php echo $field === 'minutes' ? 'number' : 'time'; ?>" name="<?php echo esc_attr($name); ?>" data-name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($window[$field]); ?>" <?php echo $field === 'minutes' ? 'min="0" max="1440" step="1"' : ''; ?> required>
        </label>
    <?php } ?>
        <button type="button" class="button" data-preparation-remove><?php echo esc_html__('Entfernen', 'dsmart'); ?></button>
    </div>
    <?php
}
