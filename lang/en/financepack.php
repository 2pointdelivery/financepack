<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Account Categories
    |--------------------------------------------------------------------------
    */

    'account_categories' => [
        'asset' => 'Asset',
        'liability' => 'Liability',
        'equity' => 'Equity',
        'revenue' => 'Revenue',
        'expense' => 'Expense',
    ],

    'account_categories_plural' => [
        'asset' => 'Assets',
        'liability' => 'Liabilities',
        'equity' => 'Equity',
        'revenue' => 'Revenue',
        'expense' => 'Expenses',
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Types
    |--------------------------------------------------------------------------
    */

    'account_types' => [
        'current_asset' => 'Current Asset',
        'non_current_asset' => 'Non-Current Asset',
        'contra_asset' => 'Contra Asset',
        'current_liability' => 'Current Liability',
        'non_current_liability' => 'Non-Current Liability',
        'contra_liability' => 'Contra Liability',
        'equity' => 'Equity',
        'contra_equity' => 'Contra Equity',
        'operating_revenue' => 'Operating Revenue',
        'non_operating_revenue' => 'Non-Operating Revenue',
        'contra_revenue' => 'Contra Revenue',
        'uncategorized_revenue' => 'Uncategorized Revenue',
        'operating_expense' => 'Operating Expense',
        'non_operating_expense' => 'Non-Operating Expense',
        'contra_expense' => 'Contra Expense',
        'uncategorized_expense' => 'Uncategorized Expense',
    ],

    'account_types_plural' => [
        'current_asset' => 'Current Assets',
        'non_current_asset' => 'Non-Current Assets',
        'contra_asset' => 'Contra Assets',
        'current_liability' => 'Current Liabilities',
        'non_current_liability' => 'Non-Current Liabilities',
        'contra_liability' => 'Contra Liabilities',
        'equity' => 'Equity',
        'contra_equity' => 'Contra Equity',
        'operating_revenue' => 'Operating Revenue',
        'non_operating_revenue' => 'Non-Operating Revenue',
        'contra_revenue' => 'Contra Revenue',
        'uncategorized_revenue' => 'Uncategorized Revenue',
        'operating_expense' => 'Operating Expenses',
        'non_operating_expense' => 'Non-Operating Expenses',
        'contra_expense' => 'Contra Expenses',
        'uncategorized_expense' => 'Uncategorized Expense',
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Types
    |--------------------------------------------------------------------------
    */

    'transaction_types' => [
        'deposit' => 'Deposit',
        'withdrawal' => 'Withdrawal',
        'journal' => 'Journal',
        'transfer' => 'Transfer',
    ],

    /*
    |--------------------------------------------------------------------------
    | Journal Entry Types
    |--------------------------------------------------------------------------
    */

    'journal_entry_types' => [
        'debit' => 'Debit',
        'credit' => 'Credit',
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Statuses
    |--------------------------------------------------------------------------
    */

    'invoice_statuses' => [
        'draft' => 'Draft',
        'unsent' => 'Unsent',
        'sent' => 'Sent',
        'viewed' => 'Viewed',
        'partial' => 'Partial',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'overpaid' => 'Overpaid',
        'void' => 'Void',
    ],

    /*
    |--------------------------------------------------------------------------
    | Bill Statuses
    |--------------------------------------------------------------------------
    */

    'bill_statuses' => [
        'draft' => 'Draft',
        'open' => 'Open',
        'partial' => 'Partial',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'void' => 'Void',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    */

    'payment_methods' => [
        'cash' => 'Cash',
        'check' => 'Check',
        'bank_transfer' => 'Bank Transfer',
        'credit_card' => 'Credit Card',
        'debit_card' => 'Debit Card',
        'paypal' => 'PayPal',
        'stripe' => 'Stripe',
        'other' => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Bank Account Types
    |--------------------------------------------------------------------------
    */

    'bank_account_types' => [
        'checking' => 'Checking',
        'savings' => 'Savings',
        'credit_card' => 'Credit Card',
    ],

    /*
    |--------------------------------------------------------------------------
    | Report Labels
    |--------------------------------------------------------------------------
    */

    'reports' => [
        'balance_sheet' => 'Balance Sheet',
        'income_statement' => 'Income Statement',
        'trial_balance' => 'Trial Balance',
        'cash_flow_statement' => 'Cash Flow Statement',
        'account_balances' => 'Account Balances',
        'account_transactions' => 'Account Transactions',
        'aging_report' => 'Aging Report',
        'profit_and_loss' => 'Profit & Loss',

        'as_of' => 'As of',
        'from' => 'From',
        'to' => 'To',
        'period' => 'Period',
        'date_range' => 'Date Range',

        'total_assets' => 'Total Assets',
        'total_liabilities' => 'Total Liabilities',
        'total_equity' => 'Total Equity',
        'total_revenue' => 'Total Revenue',
        'total_expenses' => 'Total Expenses',
        'net_income' => 'Net Income',
        'net_loss' => 'Net Loss',
        'total_debits' => 'Total Debits',
        'total_credits' => 'Total Credits',
        'retained_earnings' => 'Retained Earnings',

        'code' => 'Code',
        'name' => 'Name',
        'description' => 'Description',
        'debit_balance' => 'Debit Balance',
        'credit_balance' => 'Credit Balance',
        'net_movement' => 'Net Movement',
        'starting_balance' => 'Starting Balance',
        'ending_balance' => 'Ending Balance',
        'beginning_balance' => 'Beginning Balance',
        'amount' => 'Amount',
        'debit' => 'Debit',
        'credit' => 'Credit',
        'balance' => 'Balance',

        'operating_activities' => 'Operating Activities',
        'investing_activities' => 'Investing Activities',
        'financing_activities' => 'Financing Activities',
        'net_cash_flow' => 'Net Cash Flow',

        'current' => 'Current',
        'over_periods' => 'Over Periods',
        'total' => 'Total',

        'pre_closing' => 'Pre-Closing',
        'post_closing' => 'Post-Closing',

        'current_assets' => 'Current Assets',
        'non_current_assets' => 'Non-Current Assets',
        'contra_assets' => 'Contra Assets',
        'current_liabilities' => 'Current Liabilities',
        'non_current_liabilities' => 'Non-Current Liabilities',
        'contra_liabilities' => 'Contra Liabilities',
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Messages
    |--------------------------------------------------------------------------
    */

    'validation' => [
        'account_required' => 'The account field is required.',
        'account_invalid' => 'The selected account is invalid.',
        'amount_required' => 'The amount field is required.',
        'amount_must_be_positive' => 'The amount must be greater than zero.',
        'amount_must_be_negative' => 'The amount must be less than zero.',
        'company_required' => 'The company field is required.',
        'company_invalid' => 'The selected company is invalid.',
        'date_required' => 'The date field is required.',
        'date_invalid' => 'The date format is invalid.',
        'date_must_be_future' => 'The date must be in the future.',
        'date_must_be_past' => 'The date must be in the past.',
        'description_required' => 'The description field is required.',
        'journal_entries_not_balanced' => 'Journal entries are not balanced. Total debits must equal total credits.',
        'journal_entries_required' => 'At least one journal entry is required.',
        'transaction_type_invalid' => 'The selected transaction type is invalid.',
        'currency_invalid' => 'The selected currency is invalid.',
        'invoice_number_unique' => 'The invoice number has already been taken.',
        'bill_number_unique' => 'The bill number has already been taken.',
        'invoice_not_approvable' => 'This invoice cannot be approved.',
        'bill_not_approvable' => 'This bill cannot be approved.',
        'invoice_not_payable' => 'This invoice cannot accept payments.',
        'bill_not_payable' => 'This bill cannot accept payments.',
        'payment_exceeds_balance' => 'The payment amount exceeds the outstanding balance.',
        'account_already_archived' => 'This account is already archived.',
        'account_has_transactions' => 'This account has transactions and cannot be deleted.',
        'company_data_isolation' => 'You do not have access to this company data.',
    ],

    /*
    |--------------------------------------------------------------------------
    | General Labels
    |--------------------------------------------------------------------------
    */

    'general' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'create' => 'Create',
        'update' => 'Update',
        'view' => 'View',
        'search' => 'Search',
        'filter' => 'Filter',
        'export' => 'Export',
        'import' => 'Import',
        'print' => 'Print',
        'download' => 'Download',
        'back' => 'Back',
        'next' => 'Next',
        'previous' => 'Previous',
        'yes' => 'Yes',
        'no' => 'No',
        'confirm' => 'Confirm',
        'actions' => 'Actions',
        'status' => 'Status',
        'date' => 'Date',
        'reference' => 'Reference',
        'notes' => 'Notes',
        'all' => 'All',
        'none' => 'None',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'archived' => 'Archived',
        'default' => 'Default',
        'currency' => 'Currency',
        'company' => 'Company',
        'companies' => 'Companies',
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Labels
    |--------------------------------------------------------------------------
    */

    'accounts' => [
        'title' => 'Accounts',
        'create' => 'Create Account',
        'edit' => 'Edit Account',
        'delete' => 'Delete Account',
        'single' => 'Account',
        'code' => 'Account Code',
        'name' => 'Account Name',
        'type' => 'Account Type',
        'category' => 'Account Category',
        'description' => 'Description',
        'opening_balance' => 'Opening Balance',
        'current_balance' => 'Current Balance',
        'is_active' => 'Active',
        'is_archived' => 'Archived',
        'bank_account' => 'Bank Account',
        'parent_account' => 'Parent Account',
        'sub_accounts' => 'Sub-Accounts',
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Labels
    |--------------------------------------------------------------------------
    */

    'transactions' => [
        'title' => 'Transactions',
        'create' => 'Create Transaction',
        'edit' => 'Edit Transaction',
        'delete' => 'Delete Transaction',
        'single' => 'Transaction',
        'description' => 'Description',
        'amount' => 'Amount',
        'type' => 'Type',
        'date' => 'Date',
        'posted_at' => 'Posted At',
        'account' => 'Account',
        'bank_account' => 'Bank Account',
        'contact' => 'Contact',
        'reference' => 'Reference',
        'notes' => 'Notes',
        'pending' => 'Pending',
        'reviewed' => 'Reviewed',
        'is_payment' => 'Payment',
        'payment_method' => 'Payment Method',
        'payment_channel' => 'Payment Channel',
        'journal_entries' => 'Journal Entries',
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Labels
    |--------------------------------------------------------------------------
    */

    'invoices' => [
        'title' => 'Invoices',
        'create' => 'Create Invoice',
        'edit' => 'Edit Invoice',
        'delete' => 'Delete Invoice',
        'single' => 'Invoice',
        'number' => 'Invoice Number',
        'date' => 'Invoice Date',
        'due_date' => 'Due Date',
        'client' => 'Client',
        'subtotal' => 'Subtotal',
        'tax' => 'Tax',
        'discount' => 'Discount',
        'total' => 'Total',
        'amount_paid' => 'Amount Paid',
        'amount_due' => 'Amount Due',
        'status' => 'Status',
        'notes' => 'Notes',
        'terms' => 'Terms',
        'footer' => 'Footer',
        'approve' => 'Approve Invoice',
        'record_payment' => 'Record Payment',
        'send' => 'Send Invoice',
        'void' => 'Void Invoice',
    ],

    /*
    |--------------------------------------------------------------------------
    | Bill Labels
    |--------------------------------------------------------------------------
    */

    'bills' => [
        'title' => 'Bills',
        'create' => 'Create Bill',
        'edit' => 'Edit Bill',
        'delete' => 'Delete Bill',
        'single' => 'Bill',
        'number' => 'Bill Number',
        'date' => 'Bill Date',
        'due_date' => 'Due Date',
        'vendor' => 'Vendor',
        'subtotal' => 'Subtotal',
        'tax' => 'Tax',
        'discount' => 'Discount',
        'total' => 'Total',
        'amount_paid' => 'Amount Paid',
        'amount_due' => 'Amount Due',
        'status' => 'Status',
        'notes' => 'Notes',
        'terms' => 'Terms',
        'footer' => 'Footer',
        'approve' => 'Approve Bill',
        'record_payment' => 'Record Payment',
        'void' => 'Void Bill',
    ],

    /*
    |--------------------------------------------------------------------------
    | Chart of Accounts
    |--------------------------------------------------------------------------
    */

    'chart_of_accounts' => [
        'title' => 'Chart of Accounts',
        'import' => 'Import Chart of Accounts',
        'export' => 'Export Chart of Accounts',
        'reset' => 'Reset Chart of Accounts',
    ],

    /*
    |--------------------------------------------------------------------------
    | Banking
    |--------------------------------------------------------------------------
    */

    'banking' => [
        'title' => 'Banking',
        'bank_accounts' => 'Bank Accounts',
        'create_bank_account' => 'Create Bank Account',
        'connect_bank' => 'Connect Bank Account',
        'sync_transactions' => 'Sync Transactions',
        'last_synced' => 'Last Synced',
        'balance' => 'Balance',
        'institution' => 'Institution',
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Company
    |--------------------------------------------------------------------------
    */

    'multi_company' => [
        'switch_company' => 'Switch Company',
        'no_companies' => 'No companies found',
        'access_denied' => 'You do not have access to this company.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Flash Messages
    |--------------------------------------------------------------------------
    */

    'messages' => [
        'account_created' => 'Account created successfully.',
        'account_updated' => 'Account updated successfully.',
        'account_deleted' => 'Account deleted successfully.',
        'account_archived' => 'Account archived successfully.',
        'account_restored' => 'Account restored successfully.',
        'transaction_created' => 'Transaction created successfully.',
        'transaction_updated' => 'Transaction updated successfully.',
        'transaction_deleted' => 'Transaction deleted successfully.',
        'invoice_created' => 'Invoice created successfully.',
        'invoice_updated' => 'Invoice updated successfully.',
        'invoice_approved' => 'Invoice approved successfully.',
        'invoice_sent' => 'Invoice sent successfully.',
        'invoice_voided' => 'Invoice voided successfully.',
        'payment_recorded' => 'Payment recorded successfully.',
        'bill_created' => 'Bill created successfully.',
        'bill_updated' => 'Bill updated successfully.',
        'bill_approved' => 'Bill approved successfully.',
        'bill_voided' => 'Bill voided successfully.',
        'bank_account_created' => 'Bank account created successfully.',
        'bank_account_updated' => 'Bank account updated successfully.',
        'bank_account_deleted' => 'Bank account deleted successfully.',
        'transactions_synced' => 'Transactions synced successfully.',
        'export_success' => 'Export completed successfully.',
        'import_success' => 'Import completed successfully.',
        'error_occurred' => 'An error occurred. Please try again.',
        'unauthorized' => 'You are not authorized to perform this action.',
    ],
];
