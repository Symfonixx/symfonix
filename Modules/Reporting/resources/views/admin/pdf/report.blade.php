<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }} — {{ $from }} to {{ $to }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin-top: 20px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .meta { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
        .kpi-grid { display: table; width: 100%; margin-bottom: 16px; }
        .kpi-item { display: table-cell; width: 33%; padding: 8px; border: 1px solid #eee; text-align: center; }
        .kpi-value { font-size: 16px; font-weight: bold; }
        .kpi-label { font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">{{ $from }} — {{ $to }}</div>

    <h2>{{ __('reporting::report.menu.reports') }} — KPIs</h2>
    <div class="kpi-grid">
        @foreach($report['kpis'] ?? [] as $key => $kpi)
            <div class="kpi-item">
                <div class="kpi-label">{{ __('reporting::report.kpis.'.$key) }}</div>
                <div class="kpi-value">{{ number_format($kpi['value'] ?? 0, 2) }}</div>
            </div>
        @endforeach
    </div>

    @if(! empty($report['tables']))
        @foreach($report['tables'] as $tableName => $rows)
            @if(count($rows) > 0)
                <h2>{{ ucfirst(str_replace('_', ' ', $tableName)) }}</h2>
                <table>
                    <thead>
                    <tr>
                        @foreach(array_keys((array) $rows[0]) as $col)
                            <th>{{ ucfirst(str_replace('_', ' ', $col)) }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rows as $row)
                        <tr>
                            @foreach((array) $row as $val)
                                <td>{{ is_array($val) ? json_encode($val) : $val }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    @endif
</body>
</html>
