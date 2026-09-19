<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Flow Statement</title>
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
        .activity-section { margin-bottom: 20px; }
        .activity-title { font-size: 11pt; font-weight: 700; color: #34495e; margin-bottom: 8px; padding-left: 10px; border-left: 3px solid #3498db; }
        .positive { color: #27ae60; }
        .negative { color: #e74c3c; }
        .summary-box { background-color: #f8f9fa; border: 1px solid #ecf0f1; border-radius: 4px; padding: 15px; margin-top: 15px; }
        .summary-box .summary-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .summary-box .summary-label { font-weight: 600; }
        .summary-box .summary-value { font-family: 'DejaVu Sans Mono', monospace; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #ecf0f1; text-align: center; font-size: 8pt; color: #bdc3c7; }
        @media print { .page { padding: 20px 30px; } }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>Cash Flow Statement</h1>
            <div class="company-name">{{ $company->name ?? 'Company Name' }}</div>
            <div class="period">
                @if(isset($startDate) && isset($endDate))
                    {{ $startDate->format('F j, Y') }} &mdash; {{ $endDate->format('F j, Y') }}
                @else
                    {{ now()->startOfMonth()->format('F j, Y') }} &mdash; {{ now()->format('F j, Y') }}
                @endif
            </div>
            <div class="subtitle">Statement of Cash Flows</div>
        </div>

        @php
            $operating = collect($categories)->where('name', 'Operating Activities')->first()
                ?? collect($categories)->where('name', 'Operating')->first();
            $investing = collect($categories)->where('name', 'Investing Activities')->first()
                ?? collect($categories)->where('name', 'Investing')->first();
            $financing = collect($categories)->where('name', 'Financing Activities')->first()
                ?? collect($categories)->where('name', 'Financing')->first();

            $totalOperating = (float)($operating['total'] ?? $operating['summary']['net_movement'] ?? 0);
            $totalInvesting = (float)($investing['total'] ?? $investing['summary']['net_movement'] ?? 0);
            $totalFinancing = (float)($financing['total'] ?? $financing['summary']['net_movement'] ?? 0);
            $netCashChange = $totalOperating + $totalInvesting + $totalFinancing;
        @endphp

        @if($operating)
            <div class="section">
                <div class="activity-section">
                    <div class="activity-title">Operating Activities</div>

                    @if(isset($operating['types']))
                        @foreach($operating['types'] as $type)
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 70%">{{ $type['name'] ?? '' }}</th>
                                        <th style="width: 30%; text-align: right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($type['accounts'] ?? [] as $account)
                                        @php
                                            $amount = (float)($account->balance->netMovement ?? $account['balance']['net_movement'] ?? 0);
                                        @endphp
                                        <tr>
                                            <td class="label">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                            <td class="amount {{ $amount >= 0 ? 'positive' : 'negative' }}">{{ number_format($amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    @else
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 70%">Description</th>
                                    <th style="width: 30%; text-align: right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($operating['accounts'] ?? [] as $account)
                                    @php
                                        $amount = (float)($account->balance->netMovement ?? $account['balance']['net_movement'] ?? 0);
                                    @endphp
                                    <tr>
                                        <td class="label">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                        <td class="amount {{ $amount >= 0 ? 'positive' : 'negative' }}">{{ number_format($amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <table>
                        <tbody>
                            <tr class="subtotal-row">
                                <td>Net Cash from Operating Activities</td>
                                <td class="amount {{ $totalOperating >= 0 ? 'positive' : 'negative' }}">{{ number_format($totalOperating, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($investing)
            <div class="section">
                <div class="activity-section">
                    <div class="activity-title">Investing Activities</div>

                    @if(isset($investing['types']))
                        @foreach($investing['types'] as $type)
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 70%">{{ $type['name'] ?? '' }}</th>
                                        <th style="width: 30%; text-align: right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($type['accounts'] ?? [] as $account)
                                        @php
                                            $amount = (float)($account->balance->netMovement ?? $account['balance']['net_movement'] ?? 0);
                                        @endphp
                                        <tr>
                                            <td class="label">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                            <td class="amount {{ $amount >= 0 ? 'positive' : 'negative' }}">{{ number_format($amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    @else
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 70%">Description</th>
                                    <th style="width: 30%; text-align: right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($investing['accounts'] ?? [] as $account)
                                    @php
                                        $amount = (float)($account->balance->netMovement ?? $account['balance']['net_movement'] ?? 0);
                                    @endphp
                                    <tr>
                                        <td class="label">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                        <td class="amount {{ $amount >= 0 ? 'positive' : 'negative' }}">{{ number_format($amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <table>
                        <tbody>
                            <tr class="subtotal-row">
                                <td>Net Cash from Investing Activities</td>
                                <td class="amount {{ $totalInvesting >= 0 ? 'positive' : 'negative' }}">{{ number_format($totalInvesting, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($financing)
            <div class="section">
                <div class="activity-section">
                    <div class="activity-title">Financing Activities</div>

                    @if(isset($financing['types']))
                        @foreach($financing['types'] as $type)
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 70%">{{ $type['name'] ?? '' }}</th>
                                        <th style="width: 30%; text-align: right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($type['accounts'] ?? [] as $account)
                                        @php
                                            $amount = (float)($account->balance->netMovement ?? $account['balance']['net_movement'] ?? 0);
                                        @endphp
                                        <tr>
                                            <td class="label">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                            <td class="amount {{ $amount >= 0 ? 'positive' : 'negative' }}">{{ number_format($amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    @else
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 70%">Description</th>
                                    <th style="width: 30%; text-align: right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($financing['accounts'] ?? [] as $account)
                                    @php
                                        $amount = (float)($account->balance->netMovement ?? $account['balance']['net_movement'] ?? 0);
                                    @endphp
                                    <tr>
                                        <td class="label">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                        <td class="amount {{ $amount >= 0 ? 'positive' : 'negative' }}">{{ number_format($amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <table>
                        <tbody>
                            <tr class="subtotal-row">
                                <td>Net Cash from Financing Activities</td>
                                <td class="amount {{ $totalFinancing >= 0 ? 'positive' : 'negative' }}">{{ number_format($totalFinancing, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="section">
            <div class="summary-box">
                <div class="summary-row">
                    <span class="summary-label">Net Cash from Operating Activities</span>
                    <span class="summary-value {{ $totalOperating >= 0 ? 'positive' : 'negative' }}">{{ number_format($totalOperating, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Net Cash from Investing Activities</span>
                    <span class="summary-value {{ $totalInvesting >= 0 ? 'positive' : 'negative' }}">{{ number_format($totalInvesting, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Net Cash from Financing Activities</span>
                    <span class="summary-value {{ $totalFinancing >= 0 ? 'positive' : 'negative' }}">{{ number_format($totalFinancing, 2) }}</span>
                </div>
            </div>

            <table style="margin-top: 15px;">
                <tbody>
                    <tr class="total-row grand-total">
                        <td>Net Change in Cash</td>
                        <td class="amount {{ $netCashChange >= 0 ? 'positive' : 'negative' }}">{{ number_format($netCashChange, 2) }}</td>
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
