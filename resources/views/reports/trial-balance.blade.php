<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Balance</title>
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
        .section-title { font-size: 12pt; font-weight: 700; color: #2c3e50; border-bottom: 1.5px solid #bdc3c7; padding-bottom: 6px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        table { width: 100%; border-collapse: collapse; }
        table th { text-align: left; font-weight: 600; padding: 5px 8px; font-size: 9pt; color: #fff; background-color: #2c3e50; }
        table th.amount-header { text-align: right; }
        table td { padding: 5px 8px; font-size: 10pt; border-bottom: 1px solid #f5f6fa; }
        table td.amount { text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
        table td.account-name { padding-left: 24px; }
        table tr.category-row { background-color: #f8f9fa; }
        table tr.category-row td { font-weight: 700; color: #2c3e50; border-bottom: 2px solid #bdc3c7; padding-top: 8px; padding-bottom: 8px; }
        .total-row { font-weight: 700; border-top: 2.5px solid #2c3e50; }
        .total-row td { padding-top: 8px; padding-bottom: 8px; font-size: 11pt; color: #2c3e50; }
        .balanced { background-color: #eafaf1; }
        .balanced td { color: #27ae60; }
        .unbalanced { background-color: #fdedec; }
        .unbalanced td { color: #e74c3c; }
        .status-badge { display: inline-block; padding: 2px 10px; border-radius: 3px; font-size: 8pt; font-weight: 600; text-transform: uppercase; }
        .badge-balanced { background-color: #eafaf1; color: #27ae60; border: 1px solid #27ae60; }
        .badge-unbalanced { background-color: #fdedec; color: #e74c3c; border: 1px solid #e74c3c; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #ecf0f1; text-align: center; font-size: 8pt; color: #bdc3c7; }
        @media print { .page { padding: 20px 30px; } }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>Trial Balance</h1>
            <div class="company-name">{{ $company->name ?? 'Company Name' }}</div>
            <div class="period">
                @if(isset($startDate) && isset($endDate))
                    For the period {{ $startDate->format('F j, Y') }} &mdash; {{ $endDate->format('F j, Y') }}
                @else
                    As of {{ now()->format('F j, Y') }}
                @endif
            </div>
            <div class="subtitle">
                @php
                    $totalDebits = 0;
                    $totalCredits = 0;
                    foreach ($categories as $category) {
                        foreach ($category['accounts'] ?? [] as $account) {
                            $debit = (float)($account->balance->debitBalance ?? $account['balance']['debit_balance'] ?? 0);
                            $credit = (float)($account->balance->creditBalance ?? $account['balance']['credit_balance'] ?? 0);
                            $totalDebits += $debit;
                            $totalCredits += $credit;
                        }
                    }
                    $isBalanced = abs($totalDebits - $totalCredits) < 0.01;
                @endphp
                <span class="status-badge {{ $isBalanced ? 'badge-balanced' : 'badge-unbalanced' }}">
                    {{ $isBalanced ? 'Balanced' : 'Unbalanced' }}
                </span>
            </div>
        </div>

        <div class="section">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%">Account</th>
                        <th style="width: 15%">Code</th>
                        <th style="width: 15%">Type</th>
                        <th class="amount-header" style="width: 20%">Debit</th>
                        <th class="amount-header" style="width: 20%">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr class="category-row">
                            <td colspan="5">{{ $category['name'] ?? 'Account' }}</td>
                        </tr>

                        @foreach($category['accounts'] ?? [] as $account)
                            @php
                                $debit = (float)($account->balance->debitBalance ?? $account['balance']['debit_balance'] ?? 0);
                                $credit = (float)($account->balance->creditBalance ?? $account['balance']['credit_balance'] ?? 0);
                            @endphp
                            <tr>
                                <td class="account-name">{{ $account->accountName ?? $account['accountName'] ?? '' }}</td>
                                <td>{{ $account->accountCode ?? $account['accountCode'] ?? '' }}</td>
                                <td>{{ $account->accountType ?? $account['accountType'] ?? '' }}</td>
                                <td class="amount">{{ $debit > 0 ? number_format($debit, 2) : '' }}</td>
                                <td class="amount">{{ $credit > 0 ? number_format($credit, 2) : '' }}</td>
                            </tr>
                        @endforeach

                        @php
                            $catDebit = 0;
                            $catCredit = 0;
                            foreach ($category['accounts'] ?? [] as $account) {
                                $catDebit += (float)($account->balance->debitBalance ?? $account['balance']['debit_balance'] ?? 0);
                                $catCredit += (float)($account->balance->creditBalance ?? $account['balance']['credit_balance'] ?? 0);
                            }
                        @endphp
                        <tr class="subtotal-row">
                            <td colspan="3" style="padding-left: 24px;">Subtotal - {{ $category['name'] ?? '' }}</td>
                            <td class="amount">{{ $catDebit > 0 ? number_format($catDebit, 2) : '' }}</td>
                            <td class="amount">{{ $catCredit > 0 ? number_format($catCredit, 2) : '' }}</td>
                        </tr>
                    @endforeach

                    <tr class="total-row {{ $isBalanced ? 'balanced' : 'unbalanced' }}">
                        <td colspan="3">TOTAL</td>
                        <td class="amount">{{ number_format($totalDebits, 2) }}</td>
                        <td class="amount">{{ number_format($totalCredits, 2) }}</td>
                    </tr>

                    @if(!$isBalanced)
                        <tr>
                            <td colspan="3" style="font-style: italic; color: #e74c3c;">Difference</td>
                            <td class="amount" style="color: #e74c3c;">{{ number_format(abs($totalDebits - $totalCredits), 2) }}</td>
                            <td></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }} &mdash; {{ $company->name ?? '' }} &mdash; FinancePack</p>
        </div>
    </div>
</body>
</html>
