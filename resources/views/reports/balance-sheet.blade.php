<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Sheet</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; font-size: 10pt; color: #1a1a1a; line-height: 1.4; }
        .page { width: 100%; padding: 40px 50px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2c3e50; padding-bottom: 20px; }
        .header h1 { font-size: 22pt; color: #2c3e50; margin-bottom: 5px; font-weight: 700; }
        .header .subtitle { font-size: 11pt; color: #7f8c8d; }
        .header .company-name { font-size: 14pt; color: #34495e; margin-top: 8px; font-weight: 600; }
        .header .period { font-size: 10pt; color: #95a5a6; margin-top: 4px; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 13pt; font-weight: 700; color: #2c3e50; border-bottom: 1.5px solid #bdc3c7; padding-bottom: 6px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .type-group { margin-left: 15px; margin-bottom: 10px; }
        .type-title { font-size: 10pt; font-weight: 600; color: #34495e; margin-bottom: 6px; font-style: italic; }
        table { width: 100%; border-collapse: collapse; }
        table th { text-align: left; font-weight: 600; padding: 4px 8px; font-size: 9pt; color: #7f8c8d; border-bottom: 1px solid #ecf0f1; }
        table td { padding: 4px 8px; font-size: 10pt; border-bottom: 1px solid #f5f6fa; }
        table td.amount { text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
        table td.label { padding-left: 24px; }
        .total-row { font-weight: 700; border-top: 1.5px solid #bdc3c7; }
        .total-row td { padding-top: 6px; padding-bottom: 6px; }
        .subtotal-row { font-weight: 600; }
        .subtotal-row td { border-top: 1px solid #bdc3c7; }
        .grand-total { font-size: 12pt; border-top: 2.5px solid #2c3e50; }
        .grand-total td { padding-top: 8px; padding-bottom: 8px; font-weight: 700; color: #2c3e50; }
        .currency { font-size: 8pt; color: #95a5a6; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #ecf0f1; text-align: center; font-size: 8pt; color: #bdc3c7; }
        @media print { .page { padding: 20px 30px; } }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>Balance Sheet</h1>
            <div class="company-name">{{ $company->name ?? 'Company Name' }}</div>
            <div class="period">
                @if(isset($startDate) && isset($endDate))
                    As of {{ $endDate->format('F j, Y') }}
                @else
                    As of {{ now()->format('F j, Y') }}
                @endif
            </div>
            <div class="subtitle">Financial Position Statement</div>
        </div>

        @php
            $assets = collect($categories)->where('name', 'Assets')->first();
            $liabilities = collect($categories)->where('name', 'Liabilities')->first();
            $equity = collect($categories)->where('name', 'Equity')->first();
        @endphp

        @if($assets)
            <div class="section">
                <div class="section-title">Assets</div>

                @if(isset($assets['types']))
                    @foreach($assets['types'] as $type)
                        <div class="type-group">
                            <div class="type-title">{{ $type['name'] ?? 'Current' }}</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 60%">Account</th>
                                        <th style="width: 20%">Code</th>
                                        <th style="width: 20%; text-align: right">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($type['accounts'] ?? [] as $account)
                                        <tr>
                                            <td class="label">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                            <td>{{ $account->accountCode ?? $account['accountCode'] ?? '' }}</td>
                                            <td class="amount">{{ number_format((float)($account->balance->endingBalance ?? $account['balance']['ending_balance'] ?? 0), 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="subtotal-row">
                                        <td colspan="2">{{ $type['name'] ?? 'Subtotal' }}</td>
                                        <td class="amount">{{ number_format((float)($type['summary']['ending_balance'] ?? $type['total'] ?? 0), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif

                <table>
                    <tbody>
                        <tr class="total-row">
                            <td colspan="2">Total Assets</td>
                            <td class="amount">{{ number_format((float)($assets['total'] ?? $assets['summary']['ending_balance'] ?? 0), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        @if($liabilities)
            <div class="section">
                <div class="section-title">Liabilities</div>

                @if(isset($liabilities['types']))
                    @foreach($liabilities['types'] as $type)
                        <div class="type-group">
                            <div class="type-title">{{ $type['name'] ?? 'Current' }}</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 60%">Account</th>
                                        <th style="width: 20%">Code</th>
                                        <th style="width: 20%; text-align: right">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($type['accounts'] ?? [] as $account)
                                        <tr>
                                            <td class="label">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                            <td>{{ $account->accountCode ?? $account['accountCode'] ?? '' }}</td>
                                            <td class="amount">{{ number_format((float)($account->balance->endingBalance ?? $account['balance']['ending_balance'] ?? 0), 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="subtotal-row">
                                        <td colspan="2">{{ $type['name'] ?? 'Subtotal' }}</td>
                                        <td class="amount">{{ number_format((float)($type['summary']['ending_balance'] ?? $type['total'] ?? 0), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif

                <table>
                    <tbody>
                        <tr class="total-row">
                            <td colspan="2">Total Liabilities</td>
                            <td class="amount">{{ number_format((float)($liabilities['total'] ?? $liabilities['summary']['ending_balance'] ?? 0), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        @if($equity)
            <div class="section">
                <div class="section-title">Equity</div>

                @if(isset($equity['types']))
                    @foreach($equity['types'] as $type)
                        <div class="type-group">
                            <div class="type-title">{{ $type['name'] ?? '' }}</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 60%">Account</th>
                                        <th style="width: 20%">Code</th>
                                        <th style="width: 20%; text-align: right">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($type['accounts'] ?? [] as $account)
                                        <tr>
                                            <td class="label">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                            <td>{{ $account->accountCode ?? $account['accountCode'] ?? '' }}</td>
                                            <td class="amount">{{ number_format((float)($account->balance->endingBalance ?? $account['balance']['ending_balance'] ?? 0), 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="subtotal-row">
                                        <td colspan="2">{{ $type['name'] ?? 'Subtotal' }}</td>
                                        <td class="amount">{{ number_format((float)($type['summary']['ending_balance'] ?? $type['total'] ?? 0), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif

                <table>
                    <tbody>
                        <tr class="total-row">
                            <td colspan="2">Total Equity</td>
                            <td class="amount">{{ number_format((float)($equity['total'] ?? $equity['summary']['ending_balance'] ?? 0), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        @php
            $totalLiabilitiesAndEquity = (float)($liabilities['total'] ?? 0) + (float)($equity['total'] ?? 0);
        @endphp

        <div class="section">
            <table>
                <tbody>
                    <tr class="total-row grand-total">
                        <td>Total Liabilities &amp; Equity</td>
                        <td class="amount">{{ number_format($totalLiabilitiesAndEquity, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }} &mdash; {{ $company->name ?? '' }} &mdash; FinancePack</p>
        </div>
    </div>
</body>
</html>
