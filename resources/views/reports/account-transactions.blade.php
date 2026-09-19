<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Transactions</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; font-size: 10pt; color: #1a1a1a; line-height: 1.4; }
        .page { width: 100%; padding: 40px 50px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2c3e50; padding-bottom: 20px; }
        .header h1 { font-size: 22pt; color: #2c3e50; margin-bottom: 5px; font-weight: 700; }
        .header .subtitle { font-size: 11pt; color: #7f8c8d; }
        .header .company-name { font-size: 14pt; color: #34495e; margin-top: 8px; font-weight: 600; }
        .header .period { font-size: 10pt; color: #95a5a6; margin-top: 4px; }
        .account-info { background-color: #f8f9fa; border: 1px solid #ecf0f1; border-radius: 4px; padding: 15px; margin-bottom: 25px; }
        .account-info .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .account-info .info-item { }
        .account-info .info-label { font-size: 8pt; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.5px; }
        .account-info .info-value { font-size: 11pt; font-weight: 600; color: #2c3e50; }
        .summary-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px; }
        .summary-card { background-color: #fff; border: 1px solid #ecf0f1; border-radius: 4px; padding: 12px; text-align: center; }
        .summary-card .card-label { font-size: 8pt; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .summary-card .card-value { font-size: 13pt; font-weight: 700; font-family: 'DejaVu Sans Mono', monospace; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 13pt; font-weight: 700; color: #2c3e50; border-bottom: 1.5px solid #bdc3c7; padding-bottom: 6px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        table { width: 100%; border-collapse: collapse; }
        table th { text-align: left; font-weight: 600; padding: 6px 8px; font-size: 9pt; color: #fff; background-color: #2c3e50; }
        table th.amount-header { text-align: right; }
        table td { padding: 6px 8px; font-size: 10pt; border-bottom: 1px solid #f5f6fa; }
        table td.amount { text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
        table td.date { font-family: 'DejaVu Sans Mono', monospace; white-space: nowrap; }
        table td.description { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        table tr:nth-child(even) { background-color: #fafbfc; }
        table tr:hover { background-color: #f0f4f8; }
        .debit { color: #27ae60; }
        .credit { color: #e74c3c; }
        .total-row { font-weight: 700; border-top: 2px solid #2c3e50; background-color: #f8f9fa !important; }
        .total-row td { padding-top: 8px; padding-bottom: 8px; font-size: 10pt; }
        .no-data { text-align: center; padding: 40px; color: #95a5a6; font-style: italic; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #ecf0f1; text-align: center; font-size: 8pt; color: #bdc3c7; }
        @media print { .page { padding: 20px 30px; } }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>Account Transactions</h1>
            <div class="company-name">{{ $company->name ?? 'Company Name' }}</div>
            <div class="period">
                @if(isset($startDate) && isset($endDate))
                    {{ $startDate->format('F j, Y') }} &mdash; {{ $endDate->format('F j, Y') }}
                @else
                    {{ now()->startOfMonth()->format('F j, Y') }} &mdash; {{ now()->format('F j, Y') }}
                @endif
            </div>
            <div class="subtitle">Detailed Transaction History</div>
        </div>

        @if(isset($account))
            <div class="account-info">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Account Name</div>
                        <div class="info-value">{{ $account->name ?? $account['name'] ?? '' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Account Code</div>
                        <div class="info-value">{{ $account->code ?? $account['code'] ?? '' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Account Type</div>
                        <div class="info-value">{{ $account->type ?? $account['type'] ?? '' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Category</div>
                        <div class="info-value">{{ $account->category ?? $account['category'] ?? '' }}</div>
                    </div>
                </div>
            </div>
        @endif

        @php
            $totalDebit = 0;
            $totalCredit = 0;
            foreach ($transactions ?? [] as $transaction) {
                $totalDebit += (float)($transaction->debit ?? $transaction['debit'] ?? 0);
                $totalCredit += (float)($transaction->credit ?? $transaction['credit'] ?? 0);
            }
        @endphp

        @if(isset($transactions) && count($transactions) > 0)
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="card-label">Opening Balance</div>
                    <div class="card-value">{{ number_format((float)($openingBalance ?? 0), 2) }}</div>
                </div>
                <div class="summary-card">
                    <div class="card-label">Total Debits</div>
                    <div class="card-value debit">{{ number_format($totalDebit, 2) }}</div>
                </div>
                <div class="summary-card">
                    <div class="card-label">Total Credits</div>
                    <div class="card-value credit">{{ number_format($totalCredit, 2) }}</div>
                </div>
                <div class="summary-card">
                    <div class="card-label">Closing Balance</div>
                    <div class="card-value">{{ number_format((float)($closingBalance ?? ($openingBalance ?? 0) + $totalDebit - $totalCredit), 2) }}</div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Transaction Details</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 12%">Date</th>
                            <th style="width: 12%">Reference</th>
                            <th style="width: 36%">Description</th>
                            <th class="amount-header" style="width: 15%">Debit</th>
                            <th class="amount-header" style="width: 15%">Credit</th>
                            <th class="amount-header" style="width: 15%">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $runningBalance = (float)($openingBalance ?? 0); @endphp

                        @foreach($transactions as $transaction)
                            @php
                                $debit = (float)($transaction->debit ?? $transaction['debit'] ?? 0);
                                $credit = (float)($transaction->credit ?? $transaction['credit'] ?? 0);
                                $runningBalance += $debit - $credit;
                            @endphp
                            <tr>
                                <td class="date">{{ \Carbon\Carbon::parse($transaction->date ?? $transaction['date'] ?? now())->format('M d, Y') }}</td>
                                <td>{{ $transaction->reference ?? $transaction['reference'] ?? '' }}</td>
                                <td class="description" title="{{ $transaction->description ?? $transaction['description'] ?? '' }}">
                                    {{ $transaction->description ?? $transaction['description'] ?? '' }}
                                </td>
                                <td class="amount debit">{{ $debit > 0 ? number_format($debit, 2) : '' }}</td>
                                <td class="amount credit">{{ $credit > 0 ? number_format($credit, 2) : '' }}</td>
                                <td class="amount">{{ number_format($runningBalance, 2) }}</td>
                            </tr>
                        @endforeach

                        <tr class="total-row">
                            <td colspan="3">Totals</td>
                            <td class="amount debit">{{ number_format($totalDebit, 2) }}</td>
                            <td class="amount credit">{{ number_format($totalCredit, 2) }}</td>
                            <td class="amount">{{ number_format($runningBalance, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">
                <p>No transactions found for this account in the selected period.</p>
            </div>
        @endif

        <div class="footer">
            <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }} &mdash; {{ $company->name ?? '' }} &mdash; FinancePack</p>
        </div>
    </div>
</body>
</html>
