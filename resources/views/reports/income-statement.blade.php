<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Statement</title>
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
        .net-income { background-color: #eafaf1; }
        .net-income td { color: #27ae60; font-weight: 700; }
        .net-loss { background-color: #fdedec; }
        .net-loss td { color: #e74c3c; font-weight: 700; }
        .divider { border-top: 1px dashed #bdc3c7; margin: 8px 0; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #ecf0f1; text-align: center; font-size: 8pt; color: #bdc3c7; }
        @media print { .page { padding: 20px 30px; } }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>Income Statement</h1>
            <div class="company-name">{{ $company->name ?? 'Company Name' }}</div>
            <div class="period">
                @if(isset($startDate) && isset($endDate))
                    {{ $startDate->format('F j, Y') }} &mdash; {{ $endDate->format('F j, Y') }}
                @else
                    {{ now()->startOfMonth()->format('F j, Y') }} &mdash; {{ now()->format('F j, Y') }}
                @endif
            </div>
            <div class="subtitle">Profit &amp; Loss Statement</div>
        </div>

        @php
            $revenue = collect($categories)->where('name', 'Revenue')->first()
                ?? collect($categories)->where('name', 'Income')->first();
            $cogs = collect($categories)->where('name', 'Cost of Goods Sold')->first();
            $operatingExpenses = collect($categories)->where('name', 'Operating Expenses')->first();
            $nonOperating = collect($categories)->where('name', 'Non-Operating')->first();
        @endphp

        @if($revenue)
            <div class="section">
                <div class="section-title">Revenue</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60%">Account</th>
                            <th style="width: 20%">Code</th>
                            <th style="width: 20%; text-align: right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenue['accounts'] ?? [] as $account)
                            <tr>
                                <td class="label">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                <td>{{ $account->accountCode ?? $account['accountCode'] ?? '' }}</td>
                                <td class="amount">{{ number_format((float)($account->balance->endingBalance ?? $account['balance']['ending_balance'] ?? 0), 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="subtotal-row">
                            <td colspan="2">Total Revenue</td>
                            <td class="amount">{{ number_format((float)($revenue['total'] ?? $revenue['summary']['ending_balance'] ?? 0), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        @if($cogs)
            <div class="section">
                <div class="section-title">Cost of Goods Sold</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60%">Account</th>
                            <th style="width: 20%">Code</th>
                            <th style="width: 20%; text-align: right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cogs['accounts'] ?? [] as $account)
                            <tr>
                                <td class="label">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                <td>{{ $account->accountCode ?? $account['accountCode'] ?? '' }}</td>
                                <td class="amount">{{ number_format((float)($account->balance->endingBalance ?? $account['balance']['ending_balance'] ?? 0), 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="subtotal-row">
                            <td colspan="2">Total Cost of Goods Sold</td>
                            <td class="amount">{{ number_format((float)($cogs['total'] ?? $cogs['summary']['ending_balance'] ?? 0), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        @php
            $totalRevenue = (float)($revenue['total'] ?? $revenue['summary']['ending_balance'] ?? 0);
            $totalCogs = (float)($cogs['total'] ?? $cogs['summary']['ending_balance'] ?? 0);
            $grossProfit = $totalRevenue - $totalCogs;
        @endphp

        <div class="section">
            <table>
                <tbody>
                    <tr class="subtotal-row">
                        <td>Gross Profit</td>
                        <td class="amount">{{ number_format($grossProfit, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($operatingExpenses)
            <div class="section">
                <div class="section-title">Operating Expenses</div>

                @if(isset($operatingExpenses['types']))
                    @foreach($operatingExpenses['types'] as $type)
                        <div style="margin-left: 15px; margin-bottom: 10px;">
                            <div style="font-size: 10pt; font-weight: 600; color: #34495e; margin-bottom: 6px; font-style: italic;">{{ $type['name'] ?? 'General' }}</div>
                            <table>
                                <tbody>
                                    @foreach($type['accounts'] ?? [] as $account)
                                        <tr>
                                            <td class="label" style="width: 60%">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                            <td style="width: 20%">{{ $account->accountCode ?? $account['accountCode'] ?? '' }}</td>
                                            <td class="amount" style="width: 20%">{{ number_format((float)($account->balance->endingBalance ?? $account['balance']['ending_balance'] ?? 0), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif

                <table>
                    <tbody>
                        <tr class="subtotal-row">
                            <td>Total Operating Expenses</td>
                            <td class="amount">{{ number_format((float)($operatingExpenses['total'] ?? $operatingExpenses['summary']['ending_balance'] ?? 0), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        @php
            $totalOperatingExpenses = (float)($operatingExpenses['total'] ?? $operatingExpenses['summary']['ending_balance'] ?? 0);
            $operatingIncome = $grossProfit - $totalOperatingExpenses;
            $totalNonOperating = (float)($nonOperating['total'] ?? $nonOperating['summary']['ending_balance'] ?? 0);
            $netIncome = $operatingIncome - $totalNonOperating;
        @endphp

        @if($nonOperating)
            <div class="section">
                <div class="section-title">Non-Operating Items</div>
                <table>
                    <tbody>
                        @foreach($nonOperating['accounts'] ?? [] as $account)
                            <tr>
                                <td class="label" style="width: 60%">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                <td style="width: 20%">{{ $account->accountCode ?? $account['accountCode'] ?? '' }}</td>
                                <td class="amount" style="width: 20%">{{ number_format((float)($account->balance->endingBalance ?? $account['balance']['ending_balance'] ?? 0), 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="subtotal-row">
                            <td>Total Non-Operating</td>
                            <td class="amount">{{ number_format($totalNonOperating, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        <div class="section">
            <table>
                <tbody>
                    <tr class="total-row grand-total {{ $netIncome >= 0 ? 'net-income' : 'net-loss' }}">
                        <td>Net Income</td>
                        <td class="amount">{{ number_format($netIncome, 2) }}</td>
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
