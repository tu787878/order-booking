"""Read-only native chart and worksheet relationship checks for a test export."""
import sys
from zipfile import ZipFile
from xml.etree import ElementTree as ET
import posixpath
from openpyxl import load_workbook

for filename in sys.argv[1:]:
    # Excel requires defined names to be unique within their worksheet scope.
    with ZipFile(filename) as archive:
        ns = {'s': 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'}
        book = ET.fromstring(archive.read('xl/workbook.xml'))
        sheets = [item.get('name') for item in book.findall('s:sheets/s:sheet', ns)]
        names = book.findall('s:definedNames/s:definedName', ns)
        scopes = [(item.get('name'), item.get('localSheetId')) for item in names]
        assert len(scopes) == len(set(scopes)), 'Duplicate scoped Excel names'
        for item in names:
            if item.get('name') == '_xlnm._FilterDatabase':
                index = int(item.get('localSheetId'))
                assert item.text.startswith("'" + sheets[index] + "'!"), 'Filter points to the wrong worksheet'
    wb = load_workbook(filename)
    charts = wb['Übersicht']._charts
    assert [type(c).__name__ for c in charts] == ['PieChart', 'BarChart', 'LineChart']
    assert len(wb['Übersicht']._images) == 0
    assert wb['Übersicht']['A2'].value == 'Bestellungen'
    assert isinstance(wb['Übersicht']['B2'].value, (int, float))
    assert 'Hinweise' in wb.sheetnames
    assert wb['Produkte'].freeze_panes == 'A2'
    assert wb['Produkte'].auto_filter.ref
    for i, chart in enumerate(charts):
        assert chart.anchor.ext.cx == 440 * 9525
        assert chart.anchor.ext.cy == 240 * 9525
        assert chart.anchor.pos.y == i * 260 * 9525
    for chart in charts:
        series = chart.series[0]
        for ref, numeric in [(series.cat.strRef, False), (series.val.numRef, True)]:
            sheet, cells = ref.f.split('!')
            sheet = sheet.strip("'")
            actual = [row[0].value for row in wb[sheet][cells.replace('$', '')]]
            cached = [p.v for p in (ref.numCache.pt if numeric else ref.strCache.pt)]
            assert actual == cached, (sheet, actual, cached)
    assert wb['Produkte']['E2'].value == 33
    assert '€' in wb['Produkte']['E2'].number_format
    with ZipFile(filename) as z:
        assert not any(name.startswith('xl/media/') for name in z.namelist())
        for name in z.namelist():
            if name.endswith('.xml') or name.endswith('.rels'):
                tree = ET.fromstring(z.read(name))
                if name.endswith('.rels'):
                    base = posixpath.dirname(posixpath.dirname(name))
                    for relation in tree:
                        if relation.get('TargetMode') == 'External':
                            raise AssertionError('Unexpected external relationship')
                        target = relation.get('Target')
                        resolved = target.lstrip('/') if target.startswith('/') else posixpath.normpath(posixpath.join(base, target))
                        assert resolved in z.namelist(), (name, resolved)
        assert b'<f>' not in z.read('xl/worksheets/sheet2.xml')
    print(filename + ': three native charts, linked numeric ranges, EUR formatting and package relationships verified')
