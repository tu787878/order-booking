<?php
/** Shared chart data and native DrawingML charts for the shop report. */
defined('ABSPATH') || exit;

function dsmart_analytics_charts($report) {
    return array(
        array('type' => 'pie', 'title' => 'Lieferung und Abholung', 'sheet' => 'Bestellarten', 'labels' => array('Lieferung', 'Abholung', 'Sonstige'), 'values' => array_values($report['methods'])),
        array('type' => 'bar', 'title' => 'Bestellungen nach Wochentag', 'sheet' => 'Wochentage', 'labels' => array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'), 'values' => $report['weekdays']),
        array('type' => 'line', 'title' => 'Bestellungen nach Uhrzeit', 'sheet' => 'Stunden', 'labels' => array_map(function ($hour) { return sprintf('%02d:00', $hour); }, range(0, 23)), 'values' => $report['hours'])
    );
}

function dsmart_analytics_xml($text) {
    return htmlspecialchars((string) $text, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

/** Render without third-party scripts; exact values remain available in the data table. */
function dsmart_analytics_svg($chart) {
    $xml = 'dsmart_analytics_xml';
    $values = $chart['values']; $labels = $chart['labels'];
    $out = '<svg viewBox="0 0 600 300" role="img" font-family="Arial, sans-serif" aria-label="' . $xml($chart['title']) . '"><title>' . $xml($chart['title']) . '</title>';
    if (!array_sum($values)) {
        return $out . '<text x="300" y="150" text-anchor="middle" fill="currentColor">Keine Bestellungen im gewählten Zeitraum</text></svg>';
    }
    if ($chart['type'] === 'pie') {
        $total = array_sum($values); $offset = 0; $colors = array('#2563eb', '#0d9488', '#a16207');
        foreach ($values as $i => $value) {
            $share = $value / $total;
            $out .= '<circle cx="170" cy="145" r="90" fill="none" stroke="' . $colors[$i] . '" stroke-width="50" stroke-dasharray="' . (2 * M_PI * 90 * $share) . ' ' . (2 * M_PI * 90 * (1 - $share)) . '" stroke-dashoffset="' . (-2 * M_PI * 90 * $offset) . '" transform="rotate(-90 170 145)"><title>' . $xml($labels[$i] . ': ' . $value) . '</title></circle>';
            $offset += $share;
            $y = 90 + $i * 50;
            $out .= '<rect x="315" y="' . ($y - 12) . '" width="14" height="14" fill="' . $colors[$i] . '"/><text x="340" y="' . $y . '" fill="currentColor">' . $xml($labels[$i] . ': ' . number_format($value, 0, ',', '.') . ' (' . number_format(100 * $share, 1, ',', '.') . ' %)') . '</text>';
        }
        $out .= '<text x="170" y="145" text-anchor="middle" fill="currentColor" font-size="26">' . $xml($total) . '</text><text x="170" y="170" text-anchor="middle" fill="currentColor">Bestellungen</text>';
    } else {
        $max = max(1, max($values)); $ceiling = max(4, ceil($max / 4) * 4);
        for ($i = 0; $i <= 4; $i++) {
            $y = 240 - $i * 50;
            $out .= '<path d="M50 ' . $y . ' H580" stroke="#cbd5e1"/><text x="42" y="' . ($y + 4) . '" text-anchor="end" fill="currentColor">' . $xml(number_format($ceiling * $i / 4, 0, ',', '.')) . '</text>';
        }
        $points = array(); $count = count($values);
        foreach ($values as $i => $value) {
            $step = 530 / $count; $x = 50 + ($i + .5) * $step; $y = 240 - 200 * $value / $ceiling;
            $title = '<title>' . $xml($labels[$i] . ': ' . $value . ' Bestellungen') . '</title>';
            if ($chart['type'] === 'bar') {
                $out .= '<rect x="' . ($x - $step * .3) . '" y="' . $y . '" width="' . ($step * .6) . '" height="' . (240 - $y) . '" rx="3" fill="#2563eb">' . $title . '</rect><text x="' . $x . '" y="' . ($y - 8) . '" text-anchor="middle" fill="currentColor">' . $xml($value) . '</text>';
            } else {
                $points[] = $x . ',' . $y;
                $out .= '<circle cx="' . $x . '" cy="' . $y . '" r="4" fill="#2563eb">' . $title . '</circle>';
            }
            if ($count === 7 || $i % 3 === 0 || $i === $count - 1) {
                $label = $count === 7 ? substr($labels[$i], 0, 2) : $labels[$i];
                $out .= '<text x="' . $x . '" y="265" text-anchor="middle" fill="currentColor">' . $xml($label) . '</text>';
            }
        }
        if ($points) { $out .= '<polyline points="' . implode(' ', $points) . '" fill="none" stroke="#2563eb" stroke-width="3"/>'; }
    }
    return $out . '</svg>';
}

/** Native chart series use cell references plus caches for initial display. */
function dsmart_analytics_chart_xml($chart) {
    $xml = 'dsmart_analytics_xml'; $count = count($chart['values']);
    $sheet = "'" . str_replace("'", "''", $chart['sheet']) . "'!";
    $categories = '<c:cat><c:strRef><c:f>' . $xml($sheet . '$A$2:$A$' . ($count + 1)) . '</c:f><c:strCache><c:ptCount val="' . $count . '"/>';
    $numbers = '<c:val><c:numRef><c:f>' . $xml($sheet . '$B$2:$B$' . ($count + 1)) . '</c:f><c:numCache><c:formatCode>0</c:formatCode><c:ptCount val="' . $count . '"/>';
    foreach ($chart['values'] as $i => $value) {
        $categories .= '<c:pt idx="' . $i . '"><c:v>' . $xml($chart['labels'][$i]) . '</c:v></c:pt>';
        $numbers .= '<c:pt idx="' . $i . '"><c:v>' . $xml($value) . '</c:v></c:pt>';
    }
    $categories .= '</c:strCache></c:strRef></c:cat>'; $numbers .= '</c:numCache></c:numRef></c:val>';
    $series = '<c:ser><c:idx val="0"/><c:order val="0"/><c:tx><c:v>Bestellungen</c:v></c:tx>';
    if ($chart['type'] !== 'pie') { $series .= '<c:spPr><a:solidFill><a:srgbClr val="2563EB"/></a:solidFill><a:ln w="28575"><a:solidFill><a:srgbClr val="2563EB"/></a:solidFill></a:ln></c:spPr>'; }
    if ($chart['type'] === 'line') { $series .= '<c:marker><c:symbol val="circle"/><c:size val="5"/></c:marker>'; }
    if ($chart['type'] === 'pie') {
        foreach (array('2563EB', '0D9488', 'A16207') as $i => $color) { $series .= '<c:dPt><c:idx val="' . $i . '"/><c:spPr><a:solidFill><a:srgbClr val="' . $color . '"/></a:solidFill></c:spPr></c:dPt>'; }
    }
    $series .= $categories . $numbers . '</c:ser>';
    if ($chart['type'] === 'pie') {
        $plot = '<c:pieChart><c:varyColors val="1"/>' . $series . '<c:dLbls><c:showLegendKey val="0"/><c:showVal val="0"/><c:showCatName val="0"/><c:showSerName val="0"/><c:showPercent val="1"/></c:dLbls></c:pieChart>';
    } else {
        $plot = $chart['type'] === 'bar' ? '<c:barChart><c:barDir val="col"/><c:grouping val="clustered"/><c:varyColors val="0"/>' . $series . '<c:gapWidth val="80"/>' : '<c:lineChart><c:grouping val="standard"/><c:varyColors val="0"/>' . $series;
        $plot .= '<c:axId val="100"/><c:axId val="200"/>' . ($chart['type'] === 'bar' ? '</c:barChart>' : '</c:lineChart>');
        $plot .= '<c:catAx><c:axId val="100"/><c:scaling><c:orientation val="minMax"/></c:scaling><c:axPos val="b"/><c:tickLblPos val="nextTo"/><c:crossAx val="200"/><c:crosses val="autoZero"/><c:auto val="1"/><c:lblAlgn val="ctr"/><c:lblOffset val="100"/></c:catAx>';
        $plot .= '<c:valAx><c:axId val="200"/><c:scaling><c:orientation val="minMax"/><c:min val="0"/></c:scaling><c:axPos val="l"/><c:majorGridlines/><c:numFmt formatCode="0" sourceLinked="0"/><c:tickLblPos val="nextTo"/><c:crossAx val="100"/><c:crosses val="autoZero"/><c:crossBetween val="between"/></c:valAx>';
    }
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><c:chartSpace xmlns:c="http://schemas.openxmlformats.org/drawingml/2006/chart" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><c:lang val="de-DE"/><c:chart><c:title><c:tx><c:rich><a:bodyPr/><a:lstStyle/><a:p><a:r><a:rPr lang="de-DE"/><a:t>' . $xml($chart['title']) . '</a:t></a:r></a:p></c:rich></c:tx><c:overlay val="0"/></c:title><c:plotArea><c:layout/>' . $plot . '</c:plotArea>' . ($chart['type'] === 'pie' ? '<c:legend><c:legendPos val="b"/><c:overlay val="0"/></c:legend>' : '') . '<c:plotVisOnly val="1"/><c:dispBlanksAs val="zero"/></c:chart></c:chartSpace>';
}

/** Attach editable charts to the first sheet of a newly generated report. */
function dsmart_analytics_attach_charts($filename, $charts) {
    $zip = new ZipArchive();
    if ($zip->open($filename) !== true) { throw new RuntimeException('Excel-Datei konnte nicht geöffnet werden.'); }
    try {
        $types = $zip->getFromName('[Content_Types].xml');
        // The bundled writer hardcodes header height; enlarge it for wrapped German labels.
        $worksheet_paths = array();
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $path = $zip->getNameIndex($index);
            if (preg_match('~^xl/worksheets/sheet[0-9]+\.xml$~', $path)) { $worksheet_paths[] = $path; }
        }
        foreach ($worksheet_paths as $path) {
            $content = $zip->getFromName($path);
            $content = preg_replace_callback('/<row\b[^>]*\br="1"[^>]*>/', function ($match) {
                return preg_replace('/\bht="[^"]*"/', 'ht="34"', $match[0]);
            }, $content, 1);
            if (!$zip->addFromString($path, $content)) { throw new RuntimeException('Excel-Kopfzeile konnte nicht gespeichert werden.'); }
            if ($path === 'xl/worksheets/sheet1.xml') { $sheet = $content; }
        }
        if ($types === false || $sheet === false) { throw new RuntimeException('Ungültige Excel-Datei.'); }
        $drawing = '<?xml version="1.0" encoding="UTF-8"?><xdr:wsDr xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">';
        $rels = '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        $add = function ($path, $content) use ($zip) { if (!$zip->addFromString($path, $content)) { throw new RuntimeException('Excel-Diagramm konnte nicht gespeichert werden.'); } };
        foreach ($charts as $i => $chart) {
            $id = $i + 1;
            // Fixed 440 × 240 px charts: row heights and wrapped notes cannot stretch them.
            $chart_y = $i * 260 * 9525;
            $drawing .= '<xdr:absoluteAnchor><xdr:pos x="4572000" y="' . $chart_y . '"/><xdr:ext cx="4191000" cy="2286000"/><xdr:graphicFrame macro=""><xdr:nvGraphicFramePr><xdr:cNvPr id="' . $id . '" name="Diagramm ' . $id . '"/><xdr:cNvGraphicFramePr/></xdr:nvGraphicFramePr><xdr:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/></xdr:xfrm><a:graphic><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/chart"><c:chart xmlns:c="http://schemas.openxmlformats.org/drawingml/2006/chart" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" r:id="rId' . $id . '"/></a:graphicData></a:graphic></xdr:graphicFrame><xdr:clientData/></xdr:absoluteAnchor>';
            $rels .= '<Relationship Id="rId' . $id . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/chart" Target="../charts/chart' . $id . '.xml"/>';
            $types = str_replace('</Types>', '<Override PartName="/xl/charts/chart' . $id . '.xml" ContentType="application/vnd.openxmlformats-officedocument.drawingml.chart+xml"/></Types>', $types);
            $add('xl/charts/chart' . $id . '.xml', dsmart_analytics_chart_xml($chart));
        }
        $add('xl/drawings/drawing1.xml', $drawing . '</xdr:wsDr>');
        $add('xl/drawings/_rels/drawing1.xml.rels', $rels . '</Relationships>');
        $add('xl/worksheets/_rels/sheet1.xml.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rIdCharts" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing" Target="../drawings/drawing1.xml"/></Relationships>');
        $add('xl/worksheets/sheet1.xml', str_replace('</worksheet>', '<drawing xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" r:id="rIdCharts"/></worksheet>', $sheet));
        $add('[Content_Types].xml', str_replace('</Types>', '<Override PartName="/xl/drawings/drawing1.xml" ContentType="application/vnd.openxmlformats-officedocument.drawing+xml"/></Types>', $types));
    } catch (Throwable $error) {
        $zip->close();
        throw $error;
    }
    if (!$zip->close()) { throw new RuntimeException('Excel-Datei konnte nicht abgeschlossen werden.'); }
}
