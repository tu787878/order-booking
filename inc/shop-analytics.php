<?php
/** Shop-wide reporting, using historical order snapshots rather than current menu prices. */
defined('ABSPATH') || exit;
require_once __DIR__ . '/shop-analytics-charts.php';

function dsmart_analytics_allowed() {
    $user = wp_get_current_user();
    // Client manager accounts must not gain report access through product-edit rights.
    return is_user_logged_in()
        && !in_array('shop', (array) $user->roles, true)
        && current_user_can('manage_options');
}

function dsmart_analytics_filters($input) {
    $get = function ($key, $default = '') use ($input) {
        return isset($input[$key]) && is_scalar($input[$key]) ? sanitize_text_field(wp_unslash($input[$key])) : $default;
    };
    $today = new DateTimeImmutable('today', wp_timezone());
    $preset = $get('period', '30');
    $from = $today->modify('-29 days');
    $to = $today;
    if (in_array($preset, array('1', '7', '30', '365'), true)) {
        $from = $today->modify('-' . ((int) $preset - 1) . ' days');
    } elseif ($preset === 'yesterday') {
        $from = $today->modify('-1 day'); $to = $from;
    } elseif ($preset === 'year') {
        $from = $today->modify('first day of January last year');
        $to = $from->modify('last day of December');
    } elseif ($preset === 'custom') {
        $from = DateTimeImmutable::createFromFormat('!Y-m-d', $get('from'), wp_timezone());
        $to = DateTimeImmutable::createFromFormat('!Y-m-d', $get('to'), wp_timezone());
        if (!$from || !$to || $from->format('Y-m-d') !== $get('from') || $to->format('Y-m-d') !== $get('to') || $from > $to) {
            return new WP_Error('range', __('Bitte gültige Datumsangaben wählen. Das Startdatum darf nicht nach dem Enddatum liegen.', 'dsmart'));
        }
    } else { $preset = '30'; }
    $status = $get('status', 'completed');
    $method = $get('method', 'all');
    $sort = $get('sort', 'quantity');
    return array('period' => $preset, 'from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d'),
        'status' => in_array($status, array('completed', 'processing', 'cancelled', 'all'), true) ? $status : 'completed',
        'method' => in_array($method, array('all', 'shipping', 'direct'), true) ? $method : 'all',
        'search' => $get('search'), 'sort' => in_array($sort, array('name', 'quantity', 'orders', 'revenue', 'shipping', 'direct', 'last'), true) ? $sort : 'quantity',
        'direction' => $get('direction') === 'asc' ? 'asc' : 'desc');
}

function dsmart_analytics_money($currency, $value) {
    // EUR snapshots are already denominated in euros; do not convert them twice.
    $configured = get_option('dsmart_currency_rate');
    $rate = $configured !== '' && is_numeric($configured) ? (float) $configured : 1;
    return round((float) $value * (in_array((string) $currency, array('2', '€', 'EUR'), true) ? 1 : $rate), 2);
}

function dsmart_analytics_report($f) {
    $report = array('orders' => 0, 'quantity' => 0, 'revenue' => 0, 'products' => array(), 'days' => array(), 'hours' => array_fill(0, 24, 0), 'weekdays' => array_fill(0, 7, 0), 'heatmap' => array_fill(0, 7, array_fill(0, 24, 0)), 'methods' => array('shipping' => 0, 'direct' => 0, 'unknown' => 0));
    $meta = array();
    if ($f['status'] !== 'all') { $meta[] = array('key' => 'status', 'value' => $f['status']); }
    if ($f['method'] !== 'all') { $meta[] = array('key' => 'shipping_method', 'value' => $f['method']); }
    // Page through posts so WordPress does not load every order and its metadata at once.
    $page = 1;
    do {
        $query = new WP_Query(array('post_type' => 'orders', 'post_status' => 'publish', 'posts_per_page' => 250, 'paged' => $page++, 'orderby' => 'ID', 'order' => 'ASC', 'no_found_rows' => true, 'update_post_term_cache' => false,
            'date_query' => array(array('after' => $f['from'] . ' 00:00:00', 'before' => $f['to'] . ' 23:59:59', 'inclusive' => true)), 'meta_query' => $meta));
        foreach ($query->posts as $order) {
            $id = $order->ID;
            $method = get_post_meta($id, 'shipping_method', true);
            if (!isset($report['methods'][$method])) { $method = 'unknown'; }
            $date = new DateTimeImmutable($order->post_date, wp_timezone());
            $day = $date->format('Y-m-d'); $hour = (int) $date->format('G'); $weekday = (int) $date->format('N') - 1;
            $report['orders']++; $report['methods'][$method]++; $report['hours'][$hour]++; $report['weekdays'][$weekday]++; $report['heatmap'][$weekday][$hour]++;
            $currency = get_post_meta($id, 'currency', true);
            $total = (float) dsmart_analytics_money($currency, (float) get_post_meta($id, 'total', true));
            $report['revenue'] += $total;
            if (!isset($report['days'][$day])) { $report['days'][$day] = array('orders' => 0, 'revenue' => 0, 'shipping' => 0, 'direct' => 0, 'unknown' => 0); }
            $report['days'][$day]['orders']++; $report['days'][$day]['revenue'] += $total; $report['days'][$day][$method]++;
            $items = get_post_meta($id, 'item', true); $seen = array();
            foreach (is_array($items) ? $items : array() as $key => $item) {
                if (!is_array($item) || !isset($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) { continue; }
                $pid = isset($item['product_id']) ? (int) $item['product_id'] : (int) $key;
                $name = isset($item['title']) ? wp_strip_all_tags($item['title']) : __('Unbekanntes Produkt', 'dsmart');
                $key = $pid > 0 ? 'id:' . $pid : 'name:' . $name;
                if (!isset($report['products'][$key])) { $report['products'][$key] = array('name' => $name, 'quantity' => 0, 'orders' => 0, 'revenue' => 0, 'shipping' => 0, 'direct' => 0, 'unknown' => 0, 'last' => ''); }
                $p =& $report['products'][$key];
                $p['quantity'] += (float) $item['quantity']; $report['quantity'] += (float) $item['quantity'];
                // Snapshot price is the whole line total, including selected extras.
                $p['revenue'] += (float) dsmart_analytics_money($currency, isset($item['price']) && is_numeric($item['price']) ? (float) $item['price'] : 0);
                $p[$method] += (float) $item['quantity'];
                if (!isset($seen[$key])) { $p['orders']++; $seen[$key] = true; }
                $p['last'] = max($p['last'], $order->post_date);
                unset($p);
            }
        }
    } while (count($query->posts) === 250);
    ksort($report['days']);
    $report['products'] = array_values(array_filter($report['products'], function ($p) use ($f) { return $f['search'] === '' || stripos($p['name'], $f['search']) !== false; }));
    usort($report['products'], function ($a, $b) use ($f) {
        $cmp = $f['sort'] === 'name' ? strnatcasecmp($a['name'], $b['name']) : ($a[$f['sort']] <=> $b[$f['sort']]);
        return ($f['direction'] === 'asc' ? $cmp : -$cmp) ?: strnatcasecmp($a['name'], $b['name']);
    });
    return $report;
}

function dsmart_analytics_export() {
    if (!dsmart_analytics_allowed()) { wp_die(esc_html__('Keine Berechtigung für Shop-Berichte.', 'dsmart'), '', array('response' => 403)); }
    check_admin_referer('dsmart_analytics_export');
    $f = dsmart_analytics_filters($_GET);
    if (is_wp_error($f)) { wp_die(esc_html($f->get_error_message())); }
    if (!class_exists('ZipArchive')) { wp_die(esc_html__('Für den Excel-Export wird die PHP-ZIP-Erweiterung benötigt.', 'dsmart')); }
    $r = dsmart_analytics_report($f);
    require_once __DIR__ . '/PHPExcel/Classes/PHPExcel.php';
    $writer = new XLSXWriter();
    $writer->setTitle('Shop-Statistik'); $writer->setSubject('Bestellauswertung');
    $writer->setAuthor(''); $writer->setCompany(''); $writer->setDescription('Gefilterter Bestellbericht');
    $sheets = array(
        'Übersicht' => array(array('Kennzahl' => 'string', 'Wert' => 'GENERAL'), array(array('Von', $f['from']), array('Bis (einschließlich)', $f['to']), array('Zeitzone', wp_timezone_string()), array('Status', array('completed' => 'Abgeschlossen', 'processing' => 'In Bearbeitung', 'cancelled' => 'Storniert', 'all' => 'Alle Status')[$f['status']]), array('Bestellart', array('all' => 'Alle Bestellarten', 'shipping' => 'Lieferung', 'direct' => 'Abholung')[$f['method']]), array('Produktsuche (nur Produkttabelle)', $f['search']), array('Bestellungen', $r['orders']), array('Menge', $r['quantity']), array('Bestellumsatz', number_format($r['revenue'], 2, ',', '.') . ' €'), array('Durchschnittlicher Bestellwert', number_format($r['orders'] ? $r['revenue'] / $r['orders'] : 0, 2, ',', '.') . ' €'), array('Währung', 'EUR (€)'), array('Erläuterungen', 'Beliebtheit = Anteil der ausgewählten Bestellungen mit diesem Produkt. Produktumsätze entsprechen gespeicherten Positionsbeträgen vor Bestellrabatten und Gebühren. Maßgeblich ist der Bestellzeitpunkt. Die Suche filtert nur die Produkttabelle. Alle Beträge in Euro.'))),
        'Produkte' => array(array('Produkt' => 'string', 'Menge' => '0.##', 'Bestellungen' => 'integer', 'Beliebtheit' => '0.0%', 'Produktumsatz' => '#,##0.00 "€"', 'Liefermenge' => '0.##', 'Abholmenge' => '0.##', 'Sonstige Menge' => '0.##', 'Zuletzt bestellt' => 'string'), array()),
        'Tagesübersicht' => array(array('Datum' => 'string', 'Bestellungen' => 'integer', 'Bestellumsatz' => '#,##0.00 "€"', 'Lieferbestellungen' => 'integer', 'Abholbestellungen' => 'integer', 'Sonstige Bestellungen' => 'integer'), array()),
        'Stunden' => array(array('Stunde' => 'string', 'Bestellungen' => 'integer'), array()),
        'Wochentage' => array(array('Wochentag' => 'string', 'Bestellungen' => 'integer'), array()),
        'Bestellarten' => array(array('Bestellart' => 'string', 'Bestellungen' => 'integer'), array()),
        'Bestellaktivität' => array(array('Wochentag' => 'string', 'Stunde' => 'string', 'Bestellungen' => 'integer'), array())
    );
    // Keep the overview compact; detailed definitions belong on their own sheet.
    $overview = $sheets['Übersicht'][1];
    $sheets['Übersicht'][1] = array_merge(array_slice($overview, 6, 4), array_slice($overview, 0, 6), array($overview[10]));
    $sheets['Hinweise'] = array(array('Thema' => 'string', 'Erläuterung' => 'string'), array(
        array('Beliebtheit', 'Anteil der ausgewählten Bestellungen, die ein Produkt enthalten. Varianten werden zusammengefasst.'),
        array('Produktumsatz', 'Gespeicherte Positionsbeträge einschließlich Extras, vor Bestellrabatten und Liefergebühren. Alle Beträge in Euro.'),
        array('Zeitraum und Filter', 'Start- und Enddatum sind eingeschlossen. Maßgeblich ist der Bestellzeitpunkt. Die Produktsuche filtert nur das Blatt Produkte.'),
        array('Diagramme bearbeiten', 'Die Diagramme auf Übersicht sind mit Bestellarten, Wochentage und Stunden verknüpft. Änderungen an den vorhandenen Zahlenzellen werden im Diagramm übernommen.'),
        array('Tabellen verwenden', 'Die Datenblätter enthalten Filter und eine fixierte Kopfzeile. Mengen und Umsätze sind Zahlen und können in Excel weiter ausgewertet werden.')
    ));
    $widths = array(
        'Übersicht' => array(34, 30), 'Produkte' => array(38, 12, 16, 15, 19, 15, 15, 17, 23),
        'Tagesübersicht' => array(16, 16, 20, 22, 22, 24), 'Stunden' => array(16, 18),
        'Wochentage' => array(20, 18), 'Bestellarten' => array(20, 18),
        'Bestellaktivität' => array(20, 16, 18), 'Hinweise' => array(26, 90)
    );
    foreach (array('shipping' => 'Lieferung', 'direct' => 'Abholung', 'unknown' => 'Sonstige') as $method => $label) { $sheets['Bestellarten'][1][] = array($label, $r['methods'][$method]); }
    foreach ($r['products'] as $p) { $sheets['Produkte'][1][] = array($p['name'], $p['quantity'], $p['orders'], $r['orders'] ? $p['orders'] / $r['orders'] : 0, $p['revenue'], $p['shipping'], $p['direct'], $p['unknown'], $p['last']); }
    foreach ($r['days'] as $day => $data) { $sheets['Tagesübersicht'][1][] = array_merge(array($day), array_values($data)); }
    foreach ($r['hours'] as $hour => $count) { $sheets['Stunden'][1][] = array(sprintf('%02d:00', $hour), $count); }
    foreach (array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag') as $day => $name) {
        $sheets['Wochentage'][1][] = array($name, $r['weekdays'][$day]);
        foreach ($r['heatmap'][$day] as $hour => $count) { $sheets['Bestellaktivität'][1][] = array($name, sprintf('%02d:00', $hour), $count); }
    }
    foreach ($sheets as $name => $sheet) {
        $writer->writeSheetHeader($name, $sheet[0], array('widths' => $widths[$name], 'freeze_rows' => 1, 'auto_filter' => !in_array($name, array('Übersicht', 'Hinweise'), true), 'font' => 'Calibri', 'font-size' => 11, 'font-style' => 'bold', 'color' => '#FFFFFF', 'fill' => '#17365D', 'wrap_text' => true, 'valign' => 'center'));
        foreach ($sheet[1] as $i => $row) {
            // The bundled writer interprets leading '=' even in string columns.
            $row = array_map(function ($v) { return is_string($v) && preg_match('/^[\s]*[=+@-]/', $v) ? "'" . $v : $v; }, $row);
            $writer->writeSheetRow($name, $row, array('fill' => $i % 2 ? '#F1F5F9' : '#FFFFFF', 'wrap_text' => true, 'height' => $name === 'Hinweise' ? 62 : 28, 'font' => 'Calibri', 'font-size' => 11, 'valign' => 'center'));
        }
    }
    // Generate and validate privately before sending download headers.
    $filename = tempnam(sys_get_temp_dir(), 'shop-analytics-');
    if ($filename === false) { wp_die(esc_html__('Excel-Export konnte nicht erstellt werden.', 'dsmart')); }
    try {
        $writer->writeToFile($filename);
        dsmart_analytics_attach_charts($filename, dsmart_analytics_charts($r));
    } catch (Throwable $error) {
        if (is_file($filename)) { unlink($filename); }
        wp_die(esc_html__('Excel-Export konnte nicht erstellt werden. Bitte erneut versuchen.', 'dsmart'));
    }
    nocache_headers();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="shop-statistik-' . $f['from'] . '-' . $f['to'] . '.xlsx"');
    try { readfile($filename); } finally { if (is_file($filename)) { unlink($filename); } }
    exit;
}
add_action('admin_post_dsmart_analytics_export', 'dsmart_analytics_export');

/** Expose the same report in the WordPress product settings area. */
function dsmart_analytics_admin_menu() {
    if (!dsmart_analytics_allowed()) { return; }
    add_submenu_page('edit.php?post_type=product', 'Statistik & Analyse', 'Statistik & Analyse', 'manage_options', 'dsmart-shop-analytics', 'dsmart_analytics_admin_page');
}
add_action('admin_menu', 'dsmart_analytics_admin_menu');

function dsmart_analytics_admin_page() {
    if (!dsmart_analytics_allowed()) {
        wp_die(esc_html__('Keine Berechtigung für Shop-Berichte.', 'dsmart'), '', array('response' => 403));
    }
    echo '<div class="wrap">';
    require dirname(__DIR__) . '/templates-part/shop-statistics.php';
    echo '</div>';
}
