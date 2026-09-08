"""Read-only native chart and worksheet relationship checks for a test export."""
import sys
from zipfile import ZipFile
from xml.etree import ElementTree as ET
import posixpath
from openpyxl import load_workbook

for filename in sys.argv[1:]:
    wb = load_workbook(filename)
    charts = wb['Übersicht']._charts
    assert [type(c).__name__ for c in charts] == ['PieChart', 'BarChart', 'LineChart']
    assert len(wb['Übersicht']._images) == 0
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
