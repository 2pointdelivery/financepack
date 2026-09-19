<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Categories de Comptes
    |--------------------------------------------------------------------------
    */

    'account_categories' => [
        'asset' => 'Actif',
        'liability' => 'Passif',
        'equity' => 'Capitaux',
        'revenue' => 'Produits',
        'expense' => 'Charges',
    ],

    'account_categories_plural' => [
        'asset' => 'Actifs',
        'liability' => 'Passifs',
        'equity' => 'Capitaux',
        'revenue' => 'Produits',
        'expense' => 'Charges',
    ],

    /*
    |--------------------------------------------------------------------------
    | Types de Compte
    |--------------------------------------------------------------------------
    */

    'account_types' => [
        'current_asset' => 'Actif Circulant',
        'non_current_asset' => 'Actif Non Circulant',
        'contra_asset' => 'Contra-Actif',
        'current_liability' => 'Passif Circulant',
        'non_current_liability' => 'Passif Non Circulant',
        'contra_liability' => 'Contra-Passif',
        'equity' => 'Capitaux Propres',
        'contra_equity' => 'Contra-Capitaux',
        'operating_revenue' => 'Produits d\'Exploitation',
        'non_operating_revenue' => 'Produits Non d\'Exploitation',
        'contra_revenue' => 'Contra-Produits',
        'uncategorized_revenue' => 'Produits Non Classes',
        'operating_expense' => 'Charge d\'Exploitation',
        'non_operating_expense' => 'Charge Non d\'Exploitation',
        'contra_expense' => 'Contra-Charge',
        'uncategorized_expense' => 'Charge Non Classee',
    ],

    'account_types_plural' => [
        'current_asset' => 'Actifs Circulants',
        'non_current_asset' => 'Actifs Non Circulants',
        'contra_asset' => 'Contra-Actifs',
        'current_liability' => 'Passifs Circulants',
        'non_current_liability' => 'Passifs Non Circulants',
        'contra_liability' => 'Contra-Passifs',
        'equity' => 'Capitaux Propres',
        'contra_equity' => 'Contra-Capitaux',
        'operating_revenue' => 'Produits d\'Exploitation',
        'non_operating_revenue' => 'Produits Non d\'Exploitation',
        'contra_revenue' => 'Contra-Produits',
        'uncategorized_revenue' => 'Produits Non Classes',
        'operating_expense' => 'Charges d\'Exploitation',
        'non_operating_expense' => 'Charges Non d\'Exploitation',
        'contra_expense' => 'Contra-Charges',
        'uncategorized_expense' => 'Charge Non Classee',
    ],

    /*
    |--------------------------------------------------------------------------
    | Types de Transaction
    |--------------------------------------------------------------------------
    */

    'transaction_types' => [
        'deposit' => 'Depot',
        'withdrawal' => 'Retrait',
        'journal' => 'Ecriture',
        'transfer' => 'Virement',
    ],

    /*
    |--------------------------------------------------------------------------
    | Types d\'Ecriture Comptable
    |--------------------------------------------------------------------------
    */

    'journal_entry_types' => [
        'debit' => 'Debit',
        'credit' => 'Credit',
    ],

    /*
    |--------------------------------------------------------------------------
    | Statuts de Facture
    |--------------------------------------------------------------------------
    */

    'invoice_statuses' => [
        'draft' => 'Brouillon',
        'unsent' => 'Non Envoyee',
        'sent' => 'Envoyee',
        'viewed' => 'Consultee',
        'partial' => 'Partielle',
        'paid' => 'Payee',
        'overdue' => 'En Retard',
        'overpaid' => 'Surpayee',
        'void' => 'Annulee',
    ],

    /*
    |--------------------------------------------------------------------------
    | Statuts de Facture Fournisseur
    |--------------------------------------------------------------------------
    */

    'bill_statuses' => [
        'draft' => 'Brouillon',
        'open' => 'Ouverte',
        'partial' => 'Partielle',
        'paid' => 'Payee',
        'overdue' => 'En Retard',
        'void' => 'Annulee',
    ],

    /*
    |--------------------------------------------------------------------------
    | Methodes de Paiement
    |--------------------------------------------------------------------------
    */

    'payment_methods' => [
        'cash' => 'Especes',
        'check' => 'Cheque',
        'bank_transfer' => 'Virement Bancaire',
        'credit_card' => 'Carte de Credit',
        'debit_card' => 'Carte de Debit',
        'paypal' => 'PayPal',
        'stripe' => 'Stripe',
        'other' => 'Autre',
    ],

    /*
    |--------------------------------------------------------------------------
    | Types de Compte Bancaire
    |--------------------------------------------------------------------------
    */

    'bank_account_types' => [
        'checking' => 'Courant',
        'savings' => 'Epargne',
        'credit_card' => 'Carte de Credit',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquettes des Rapports
    |--------------------------------------------------------------------------
    */

    'reports' => [
        'balance_sheet' => 'Bilan',
        'income_statement' => 'Compte de Resultat',
        'trial_balance' => 'Balance Generale',
        'cash_flow_statement' => 'Plan de Financement',
        'account_balances' => 'Soldes des Comptes',
        'account_transactions' => 'Transactions des Comptes',
        'aging_report' => 'Rapport d\'Anteriorite',
        'profit_and_loss' => 'Produits et Charges',

        'as_of' => 'Au',
        'from' => 'Du',
        'to' => 'Au',
        'period' => 'Periode',
        'date_range' => 'Periode',

        'total_assets' => 'Total des Actifs',
        'total_liabilities' => 'Total des Passifs',
        'total_equity' => 'Total des Capitaux',
        'total_revenue' => 'Total des Produits',
        'total_expenses' => 'Total des Charges',
        'net_income' => 'Resultat Net',
        'net_loss' => 'Perte Nette',
        'total_debits' => 'Total des Debits',
        'total_credits' => 'Total des Credits',
        'retained_earnings' => 'Resultat Non Distribue',

        'code' => 'Code',
        'name' => 'Nom',
        'description' => 'Description',
        'debit_balance' => 'Solde Debit',
        'credit_balance' => 'Solde Credit',
        'net_movement' => 'Mouvement Net',
        'starting_balance' => 'Solde Initial',
        'ending_balance' => 'Solde Final',
        'beginning_balance' => 'Solde d\'Ouverture',
        'amount' => 'Montant',
        'debit' => 'Debit',
        'credit' => 'Credit',
        'balance' => 'Solde',

        'operating_activities' => 'Activites d\'Exploitation',
        'investing_activities' => 'Activites d\'Investissement',
        'financing_activities' => 'Activites de Financement',
        'net_cash_flow' => 'Flux de Tresorerie Net',

        'current' => 'Courant',
        'over_periods' => 'Au-dela des Periodes',
        'total' => 'Total',

        'pre_closing' => 'Pre-Cloture',
        'post_closing' => 'Post-Cloture',

        'current_assets' => 'Actifs Circulants',
        'non_current_assets' => 'Actifs Non Circulants',
        'contra_assets' => 'Contra-Actifs',
        'current_liabilities' => 'Passifs Circulants',
        'non_current_liabilities' => 'Passifs Non Circulants',
        'contra_liabilities' => 'Contra-Passifs',
    ],

    /*
    |--------------------------------------------------------------------------
    | Messages de Validation
    |--------------------------------------------------------------------------
    */

    'validation' => [
        'account_required' => 'Le champ compte est obligatoire.',
        'account_invalid' => 'Le compte selectionne est invalide.',
        'amount_required' => 'Le champ montant est obligatoire.',
        'amount_must_be_positive' => 'Le montant doit etre superieur a zero.',
        'amount_must_be_negative' => 'Le montant doit etre inferieur a zero.',
        'company_required' => 'Le champ societe est obligatoire.',
        'company_invalid' => 'La societe selectionnee est invalide.',
        'date_required' => 'Le champ date est obligatoire.',
        'date_invalid' => 'Le format de date est invalide.',
        'date_must_be_future' => 'La date doit etre dans le futur.',
        'date_must_be_past' => 'La date doit etre dans le passe.',
        'description_required' => 'Le champ description est obligatoire.',
        'journal_entries_not_balanced' => 'Les ecritures comptables ne sont pas equilibrees. Le total des debits doit etre egal au total des credits.',
        'journal_entries_required' => 'Au moins une ecriture comptable est requise.',
        'transaction_type_invalid' => 'Le type de transaction selectionne est invalide.',
        'currency_invalid' => 'La devise selectionnee est invalide.',
        'invoice_number_unique' => 'Le numero de facture est deja utilise.',
        'bill_number_unique' => 'Le numero de facture fournisseur est deja utilise.',
        'invoice_not_approvable' => 'Cette facture ne peut pas etre approuvee.',
        'bill_not_approvable' => 'Cette facture fournisseur ne peut pas etre approuvee.',
        'invoice_not_payable' => 'Cette facture ne peut pas accepter de paiements.',
        'bill_not_payable' => 'Cette facture fournisseur ne peut pas accepter de paiements.',
        'payment_exceeds_balance' => 'Le montant du paiement depasse le solde impaye.',
        'account_already_archived' => 'Ce compte est deja archive.',
        'account_has_transactions' => 'Ce compte a des transactions et ne peut pas etre supprime.',
        'company_data_isolation' => 'Vous n\'avez pas acces aux donnees de cette societe.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquettes Generales
    |--------------------------------------------------------------------------
    */

    'general' => [
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'delete' => 'Supprimer',
        'edit' => 'Modifier',
        'create' => 'Creer',
        'update' => 'Mettre a Jour',
        'view' => 'Voir',
        'search' => 'Rechercher',
        'filter' => 'Filtrer',
        'export' => 'Exporter',
        'import' => 'Importer',
        'print' => 'Imprimer',
        'download' => 'Telecharger',
        'back' => 'Retour',
        'next' => 'Suivant',
        'previous' => 'Precedent',
        'yes' => 'Oui',
        'no' => 'Non',
        'confirm' => 'Confirmer',
        'actions' => 'Actions',
        'status' => 'Statut',
        'date' => 'Date',
        'reference' => 'Reference',
        'notes' => 'Notes',
        'all' => 'Tous',
        'none' => 'Aucun',
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'archived' => 'Archive',
        'default' => 'Par Defaut',
        'currency' => 'Devise',
        'company' => 'Societe',
        'companies' => 'Societes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquettes des Comptes
    |--------------------------------------------------------------------------
    */

    'accounts' => [
        'title' => 'Comptes',
        'create' => 'Creer un Compte',
        'edit' => 'Modifier le Compte',
        'delete' => 'Supprimer le Compte',
        'single' => 'Compte',
        'code' => 'Code du Compte',
        'name' => 'Nom du Compte',
        'type' => 'Type de Compte',
        'category' => 'Categorie de Compte',
        'description' => 'Description',
        'opening_balance' => 'Solde d\'Ouverture',
        'current_balance' => 'Solde Actuel',
        'is_active' => 'Actif',
        'is_archived' => 'Archive',
        'bank_account' => 'Compte Bancaire',
        'parent_account' => 'Compte Parent',
        'sub_accounts' => 'Sous-Comptes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquettes des Transactions
    |--------------------------------------------------------------------------
    */

    'transactions' => [
        'title' => 'Transactions',
        'create' => 'Creer une Transaction',
        'edit' => 'Modifier la Transaction',
        'delete' => 'Supprimer la Transaction',
        'single' => 'Transaction',
        'description' => 'Description',
        'amount' => 'Montant',
        'type' => 'Type',
        'date' => 'Date',
        'posted_at' => 'Enregistree le',
        'account' => 'Compte',
        'bank_account' => 'Compte Bancaire',
        'contact' => 'Contact',
        'reference' => 'Reference',
        'notes' => 'Notes',
        'pending' => 'En Attente',
        'reviewed' => 'Revisee',
        'is_payment' => 'Paiement',
        'payment_method' => 'Mode de Paiement',
        'payment_channel' => 'Canal de Paiement',
        'journal_entries' => 'Ecritures Comptables',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquettes des Factures
    |--------------------------------------------------------------------------
    */

    'invoices' => [
        'title' => 'Factures',
        'create' => 'Creer une Facture',
        'edit' => 'Modifier la Facture',
        'delete' => 'Supprimer la Facture',
        'single' => 'Facture',
        'number' => 'Numero de Facture',
        'date' => 'Date de Facture',
        'due_date' => 'Date d\'Echeance',
        'client' => 'Client',
        'subtotal' => 'Sous-Total',
        'tax' => 'Taxe',
        'discount' => 'Remise',
        'total' => 'Total',
        'amount_paid' => 'Montant Paye',
        'amount_due' => 'Montant Dû',
        'status' => 'Statut',
        'notes' => 'Notes',
        'terms' => 'Conditions',
        'footer' => 'Pied de Page',
        'approve' => 'Approuver la Facture',
        'record_payment' => 'Enregistrer le Paiement',
        'send' => 'Envoyer la Facture',
        'void' => 'Annuler la Facture',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquettes des Factures Fournisseur
    |--------------------------------------------------------------------------
    */

    'bills' => [
        'title' => 'Factures Fournisseur',
        'create' => 'Creer une Facture Fournisseur',
        'edit' => 'Modifier la Facture Fournisseur',
        'delete' => 'Supprimer la Facture Fournisseur',
        'single' => 'Facture Fournisseur',
        'number' => 'Numero de Facture',
        'date' => 'Date de Facture',
        'due_date' => 'Date d\'Echeance',
        'vendor' => 'Fournisseur',
        'subtotal' => 'Sous-Total',
        'tax' => 'Taxe',
        'discount' => 'Remise',
        'total' => 'Total',
        'amount_paid' => 'Montant Paye',
        'amount_due' => 'Montant Dû',
        'status' => 'Statut',
        'notes' => 'Notes',
        'terms' => 'Conditions',
        'footer' => 'Pied de Page',
        'approve' => 'Approuver la Facture Fournisseur',
        'record_payment' => 'Enregistrer le Paiement',
        'void' => 'Annuler la Facture Fournisseur',
    ],

    /*
    |--------------------------------------------------------------------------
    | Plan Comptable
    |--------------------------------------------------------------------------
    */

    'chart_of_accounts' => [
        'title' => 'Plan Comptable',
        'import' => 'Importer le Plan Comptable',
        'export' => 'Exporter le Plan Comptable',
        'reset' => 'Reinitialiser le Plan Comptable',
    ],

    /*
    |--------------------------------------------------------------------------
    | Banque
    |--------------------------------------------------------------------------
    */

    'banking' => [
        'title' => 'Banque',
        'bank_accounts' => 'Comptes Bancaires',
        'create_bank_account' => 'Creer un Compte Bancaire',
        'connect_bank' => 'Connecter un Compte Bancaire',
        'sync_transactions' => 'Synchroniser les Transactions',
        'last_synced' => 'Derniere Synchronisation',
        'balance' => 'Solde',
        'institution' => 'Institution',
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Societe
    |--------------------------------------------------------------------------
    */

    'multi_company' => [
        'switch_company' => 'Changer de Societe',
        'no_companies' => 'Aucune societe trouvee',
        'access_denied' => 'Vous n\'avez pas acces a cette societe.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Messages Flash
    |--------------------------------------------------------------------------
    */

    'messages' => [
        'account_created' => 'Compte cree avec succes.',
        'account_updated' => 'Compte mis a jour avec succes.',
        'account_deleted' => 'Compte supprime avec succes.',
        'account_archived' => 'Compte archive avec succes.',
        'account_restored' => 'Compte restaure avec succes.',
        'transaction_created' => 'Transaction creee avec succes.',
        'transaction_updated' => 'Transaction mise a jour avec succes.',
        'transaction_deleted' => 'Transaction supprimee avec succes.',
        'invoice_created' => 'Facture creee avec succes.',
        'invoice_updated' => 'Facture mise a jour avec succes.',
        'invoice_approved' => 'Facture approuvee avec succes.',
        'invoice_sent' => 'Facture envoyee avec succes.',
        'invoice_voided' => 'Facture annulee avec succes.',
        'payment_recorded' => 'Paiement enregistre avec succes.',
        'bill_created' => 'Facture fournisseur creee avec succes.',
        'bill_updated' => 'Facture fournisseur mise a jour avec succes.',
        'bill_approved' => 'Facture fournisseur approuvee avec succes.',
        'bill_voided' => 'Facture fournisseur annulee avec succes.',
        'bank_account_created' => 'Compte bancaire cree avec succes.',
        'bank_account_updated' => 'Compte bancaire mis a jour avec succes.',
        'bank_account_deleted' => 'Compte bancaire supprime avec succes.',
        'transactions_synced' => 'Transactions synchronisees avec succes.',
        'export_success' => 'Exportation terminee avec succes.',
        'import_success' => 'Importation terminee avec succes.',
        'error_occurred' => 'Une erreur s\'est produite. Veuillez reessayer.',
        'unauthorized' => 'Vous n\'etes pas autorise a effectuer cette action.',
    ],
];
