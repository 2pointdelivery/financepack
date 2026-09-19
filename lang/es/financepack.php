<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Categorias de Cuentas
    |--------------------------------------------------------------------------
    */

    'account_categories' => [
        'asset' => 'Activo',
        'liability' => 'Pasivo',
        'equity' => 'Capital',
        'revenue' => 'Ingresos',
        'expense' => 'Gastos',
    ],

    'account_categories_plural' => [
        'asset' => 'Activos',
        'liability' => 'Pasivos',
        'equity' => 'Capital',
        'revenue' => 'Ingresos',
        'expense' => 'Gastos',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de Cuenta
    |--------------------------------------------------------------------------
    */

    'account_types' => [
        'current_asset' => 'Activo Corriente',
        'non_current_asset' => 'Activo No Corriente',
        'contra_asset' => 'Activo Contra',
        'current_liability' => 'Pasivo Corriente',
        'non_current_liability' => 'Pasivo No Corriente',
        'contra_liability' => 'Pasivo Contra',
        'equity' => 'Capital',
        'contra_equity' => 'Capital Contra',
        'operating_revenue' => 'Ingresos Operativos',
        'non_operating_revenue' => 'Ingresos No Operativos',
        'contra_revenue' => 'Ingresos Contra',
        'uncategorized_revenue' => 'Ingresos Sin Categoria',
        'operating_expense' => 'Gasto Operativo',
        'non_operating_expense' => 'Gasto No Operativo',
        'contra_expense' => 'Gasto Contra',
        'uncategorized_expense' => 'Gasto Sin Categoria',
    ],

    'account_types_plural' => [
        'current_asset' => 'Activos Corrientes',
        'non_current_asset' => 'Activos No Corrientes',
        'contra_asset' => 'Activos Contra',
        'current_liability' => 'Pasivos Corrientes',
        'non_current_liability' => 'Pasivos No Corrientes',
        'contra_liability' => 'Pasivos Contra',
        'equity' => 'Capital',
        'contra_equity' => 'Capital Contra',
        'operating_revenue' => 'Ingresos Operativos',
        'non_operating_revenue' => 'Ingresos No Operativos',
        'contra_revenue' => 'Ingresos Contra',
        'uncategorized_revenue' => 'Ingresos Sin Categoria',
        'operating_expense' => 'Gastos Operativos',
        'non_operating_expense' => 'Gastos No Operativos',
        'contra_expense' => 'Gastos Contra',
        'uncategorized_expense' => 'Gasto Sin Categoria',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de Transaccion
    |--------------------------------------------------------------------------
    */

    'transaction_types' => [
        'deposit' => 'Deposito',
        'withdrawal' => 'Retiro',
        'journal' => 'Diario',
        'transfer' => 'Transferencia',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de Asiento Contable
    |--------------------------------------------------------------------------
    */

    'journal_entry_types' => [
        'debit' => 'Debito',
        'credit' => 'Credito',
    ],

    /*
    |--------------------------------------------------------------------------
    | Estados de Factura
    |--------------------------------------------------------------------------
    */

    'invoice_statuses' => [
        'draft' => 'Borrador',
        'unsent' => 'Sin Enviar',
        'sent' => 'Enviada',
        'viewed' => 'Vista',
        'partial' => 'Parcial',
        'paid' => 'Pagada',
        'overdue' => 'Vencida',
        'overpaid' => 'Saldado',
        'void' => 'Anulada',
    ],

    /*
    |--------------------------------------------------------------------------
    | Estados de Factura de Proveedor
    |--------------------------------------------------------------------------
    */

    'bill_statuses' => [
        'draft' => 'Borrador',
        'open' => 'Abierta',
        'partial' => 'Parcial',
        'paid' => 'Pagada',
        'overdue' => 'Vencida',
        'void' => 'Anulada',
    ],

    /*
    |--------------------------------------------------------------------------
    | Metodos de Pago
    |--------------------------------------------------------------------------
    */

    'payment_methods' => [
        'cash' => 'Efectivo',
        'check' => 'Cheque',
        'bank_transfer' => 'Transferencia Bancaria',
        'credit_card' => 'Tarjeta de Credito',
        'debit_card' => 'Tarjeta de Debito',
        'paypal' => 'PayPal',
        'stripe' => 'Stripe',
        'other' => 'Otro',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de Cuenta Bancaria
    |--------------------------------------------------------------------------
    */

    'bank_account_types' => [
        'checking' => 'Corriente',
        'savings' => 'Ahorro',
        'credit_card' => 'Tarjeta de Credito',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas de Informes
    |--------------------------------------------------------------------------
    */

    'reports' => [
        'balance_sheet' => 'Balance General',
        'income_statement' => 'Estado de Resultados',
        'trial_balance' => 'Balance de Comprobacion',
        'cash_flow_statement' => 'Estado de Flujo de Efectivo',
        'account_balances' => 'Saldos de Cuentas',
        'account_transactions' => 'Transacciones de Cuentas',
        'aging_report' => 'Informe de Antiguedad',
        'profit_and_loss' => 'Perdidas y Ganancias',

        'as_of' => 'Al',
        'from' => 'Desde',
        'to' => 'Hasta',
        'period' => 'Periodo',
        'date_range' => 'Rango de Fechas',

        'total_assets' => 'Total de Activos',
        'total_liabilities' => 'Total de Pasivos',
        'total_equity' => 'Total de Capital',
        'total_revenue' => 'Total de Ingresos',
        'total_expenses' => 'Total de Gastos',
        'net_income' => 'Ingreso Neto',
        'net_loss' => 'Perdida Neta',
        'total_debits' => 'Total de Debitos',
        'total_credits' => 'Total de Creditos',
        'retained_earnings' => 'Ganancias Retenidas',

        'code' => 'Codigo',
        'name' => 'Nombre',
        'description' => 'Descripcion',
        'debit_balance' => 'Saldo Debito',
        'credit_balance' => 'Saldo Credito',
        'net_movement' => 'Movimiento Neto',
        'starting_balance' => 'Saldo Inicial',
        'ending_balance' => 'Saldo Final',
        'beginning_balance' => 'Saldo Inicial',
        'amount' => 'Monto',
        'debit' => 'Debito',
        'credit' => 'Credito',
        'balance' => 'Saldo',

        'operating_activities' => 'Actividades Operativas',
        'investing_activities' => 'Actividades de Inversion',
        'financing_activities' => 'Actividades de Financiamiento',
        'net_cash_flow' => 'Flujo de Efectivo Neto',

        'current' => 'Corriente',
        'over_periods' => 'Mas de Periodos',
        'total' => 'Total',

        'pre_closing' => 'Pre-Cierre',
        'post_closing' => 'Post-Cierre',

        'current_assets' => 'Activos Corrientes',
        'non_current_assets' => 'Activos No Corrientes',
        'contra_assets' => 'Activos Contra',
        'current_liabilities' => 'Pasivos Corrientes',
        'non_current_liabilities' => 'Pasivos No Corrientes',
        'contra_liabilities' => 'Pasivos Contra',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mensajes de Validacion
    |--------------------------------------------------------------------------
    */

    'validation' => [
        'account_required' => 'El campo cuenta es obligatorio.',
        'account_invalid' => 'La cuenta seleccionada no es valida.',
        'amount_required' => 'El campo monto es obligatorio.',
        'amount_must_be_positive' => 'El monto debe ser mayor que cero.',
        'amount_must_be_negative' => 'El monto debe ser menor que cero.',
        'company_required' => 'El campo empresa es obligatorio.',
        'company_invalid' => 'La empresa seleccionada no es valida.',
        'date_required' => 'El campo fecha es obligatorio.',
        'date_invalid' => 'El formato de fecha no es valido.',
        'date_must_be_future' => 'La fecha debe ser en el futuro.',
        'date_must_be_past' => 'La fecha debe ser en el pasado.',
        'description_required' => 'El campo descripcion es obligatorio.',
        'journal_entries_not_balanced' => 'Los asientos contables no estan balanceados. El total de debitos debe ser igual al total de creditos.',
        'journal_entries_required' => 'Se requiere al menos un asiento contable.',
        'transaction_type_invalid' => 'El tipo de transaccion seleccionado no es valido.',
        'currency_invalid' => 'La moneda seleccionada no es valida.',
        'invoice_number_unique' => 'El numero de factura ya ha sido tomado.',
        'bill_number_unique' => 'El numero de factura de proveedor ya ha sido tomado.',
        'invoice_not_approvable' => 'Esta factura no puede ser aprobada.',
        'bill_not_approvable' => 'Esta factura de proveedor no puede ser aprobada.',
        'invoice_not_payable' => 'Esta factura no puede aceptar pagos.',
        'bill_not_payable' => 'Esta factura de proveedor no puede aceptar pagos.',
        'payment_exceeds_balance' => 'El monto del pago excede el saldo pendiente.',
        'account_already_archived' => 'Esta cuenta ya esta archivada.',
        'account_has_transactions' => 'Esta cuenta tiene transacciones y no puede ser eliminada.',
        'company_data_isolation' => 'No tiene acceso a los datos de esta empresa.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas Generales
    |--------------------------------------------------------------------------
    */

    'general' => [
        'save' => 'Guardar',
        'cancel' => 'Cancelar',
        'delete' => 'Eliminar',
        'edit' => 'Editar',
        'create' => 'Crear',
        'update' => 'Actualizar',
        'view' => 'Ver',
        'search' => 'Buscar',
        'filter' => 'Filtrar',
        'export' => 'Exportar',
        'import' => 'Importar',
        'print' => 'Imprimir',
        'download' => 'Descargar',
        'back' => 'Volver',
        'next' => 'Siguiente',
        'previous' => 'Anterior',
        'yes' => 'Si',
        'no' => 'No',
        'confirm' => 'Confirmar',
        'actions' => 'Acciones',
        'status' => 'Estado',
        'date' => 'Fecha',
        'reference' => 'Referencia',
        'notes' => 'Notas',
        'all' => 'Todos',
        'none' => 'Ninguno',
        'active' => 'Activo',
        'inactive' => 'Inactivo',
        'archived' => 'Archivado',
        'default' => 'Predeterminado',
        'currency' => 'Moneda',
        'company' => 'Empresa',
        'companies' => 'Empresas',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas de Cuentas
    |--------------------------------------------------------------------------
    */

    'accounts' => [
        'title' => 'Cuentas',
        'create' => 'Crear Cuenta',
        'edit' => 'Editar Cuenta',
        'delete' => 'Eliminar Cuenta',
        'single' => 'Cuenta',
        'code' => 'Codigo de Cuenta',
        'name' => 'Nombre de Cuenta',
        'type' => 'Tipo de Cuenta',
        'category' => 'Categoria de Cuenta',
        'description' => 'Descripcion',
        'opening_balance' => 'Saldo de Apertura',
        'current_balance' => 'Saldo Actual',
        'is_active' => 'Activa',
        'is_archived' => 'Archivada',
        'bank_account' => 'Cuenta Bancaria',
        'parent_account' => 'Cuenta Padre',
        'sub_accounts' => 'Sub-Cuentas',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas de Transacciones
    |--------------------------------------------------------------------------
    */

    'transactions' => [
        'title' => 'Transacciones',
        'create' => 'Crear Transaccion',
        'edit' => 'Editar Transaccion',
        'delete' => 'Eliminar Transaccion',
        'single' => 'Transaccion',
        'description' => 'Descripcion',
        'amount' => 'Monto',
        'type' => 'Tipo',
        'date' => 'Fecha',
        'posted_at' => 'Registrado En',
        'account' => 'Cuenta',
        'bank_account' => 'Cuenta Bancaria',
        'contact' => 'Contacto',
        'reference' => 'Referencia',
        'notes' => 'Notas',
        'pending' => 'Pendiente',
        'reviewed' => 'Revisado',
        'is_payment' => 'Pago',
        'payment_method' => 'Metodo de Pago',
        'payment_channel' => 'Canal de Pago',
        'journal_entries' => 'Asientos Contables',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas de Facturas
    |--------------------------------------------------------------------------
    */

    'invoices' => [
        'title' => 'Facturas',
        'create' => 'Crear Factura',
        'edit' => 'Editar Factura',
        'delete' => 'Eliminar Factura',
        'single' => 'Factura',
        'number' => 'Numero de Factura',
        'date' => 'Fecha de Factura',
        'due_date' => 'Fecha de Vencimiento',
        'client' => 'Cliente',
        'subtotal' => 'Subtotal',
        'tax' => 'Impuesto',
        'discount' => 'Descuento',
        'total' => 'Total',
        'amount_paid' => 'Monto Pagado',
        'amount_due' => 'Monto Pendiente',
        'status' => 'Estado',
        'notes' => 'Notas',
        'terms' => 'Terminos',
        'footer' => 'Pie de Pagina',
        'approve' => 'Aprobar Factura',
        'record_payment' => 'Registrar Pago',
        'send' => 'Enviar Factura',
        'void' => 'Anular Factura',
    ],

    /*
    |--------------------------------------------------------------------------
    | Etiquetas de Facturas de Proveedor
    |--------------------------------------------------------------------------
    */

    'bills' => [
        'title' => 'Facturas de Proveedor',
        'create' => 'Crear Factura de Proveedor',
        'edit' => 'Editar Factura de Proveedor',
        'delete' => 'Eliminar Factura de Proveedor',
        'single' => 'Factura de Proveedor',
        'number' => 'Numero de Factura',
        'date' => 'Fecha de Factura',
        'due_date' => 'Fecha de Vencimiento',
        'vendor' => 'Proveedor',
        'subtotal' => 'Subtotal',
        'tax' => 'Impuesto',
        'discount' => 'Descuento',
        'total' => 'Total',
        'amount_paid' => 'Monto Pagado',
        'amount_due' => 'Monto Pendiente',
        'status' => 'Estado',
        'notes' => 'Notas',
        'terms' => 'Terminos',
        'footer' => 'Pie de Pagina',
        'approve' => 'Aprobar Factura de Proveedor',
        'record_payment' => 'Registrar Pago',
        'void' => 'Anular Factura de Proveedor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Plan de Cuentas
    |--------------------------------------------------------------------------
    */

    'chart_of_accounts' => [
        'title' => 'Plan de Cuentas',
        'import' => 'Importar Plan de Cuentas',
        'export' => 'Exportar Plan de Cuentas',
        'reset' => 'Restablecer Plan de Cuentas',
    ],

    /*
    |--------------------------------------------------------------------------
    | Banca
    |--------------------------------------------------------------------------
    */

    'banking' => [
        'title' => 'Banca',
        'bank_accounts' => 'Cuentas Bancarias',
        'create_bank_account' => 'Crear Cuenta Bancaria',
        'connect_bank' => 'Conectar Cuenta Bancaria',
        'sync_transactions' => 'Sincronizar Transacciones',
        'last_synced' => 'Ultima Sincronizacion',
        'balance' => 'Saldo',
        'institution' => 'Institucion',
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Empresa
    |--------------------------------------------------------------------------
    */

    'multi_company' => [
        'switch_company' => 'Cambiar Empresa',
        'no_companies' => 'No se encontraron empresas',
        'access_denied' => 'No tiene acceso a esta empresa.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mensajes Flash
    |--------------------------------------------------------------------------
    */

    'messages' => [
        'account_created' => 'Cuenta creada exitosamente.',
        'account_updated' => 'Cuenta actualizada exitosamente.',
        'account_deleted' => 'Cuenta eliminada exitosamente.',
        'account_archived' => 'Cuenta archivada exitosamente.',
        'account_restored' => 'Cuenta restaurada exitosamente.',
        'transaction_created' => 'Transaccion creada exitosamente.',
        'transaction_updated' => 'Transaccion actualizada exitosamente.',
        'transaction_deleted' => 'Transaccion eliminada exitosamente.',
        'invoice_created' => 'Factura creada exitosamente.',
        'invoice_updated' => 'Factura actualizada exitosamente.',
        'invoice_approved' => 'Factura aprobada exitosamente.',
        'invoice_sent' => 'Factura enviada exitosamente.',
        'invoice_voided' => 'Factura anulada exitosamente.',
        'payment_recorded' => 'Pago registrado exitosamente.',
        'bill_created' => 'Factura de proveedor creada exitosamente.',
        'bill_updated' => 'Factura de proveedor actualizada exitosamente.',
        'bill_approved' => 'Factura de proveedor aprobada exitosamente.',
        'bill_voided' => 'Factura de proveedor anulada exitosamente.',
        'bank_account_created' => 'Cuenta bancaria creada exitosamente.',
        'bank_account_updated' => 'Cuenta bancaria actualizada exitosamente.',
        'bank_account_deleted' => 'Cuenta bancaria eliminada exitosamente.',
        'transactions_synced' => 'Transacciones sincronizadas exitosamente.',
        'export_success' => 'Exportacion completada exitosamente.',
        'import_success' => 'Importacion completada exitosamente.',
        'error_occurred' => 'Ocurrio un error. Por favor intente de nuevo.',
        'unauthorized' => 'No esta autorizado para realizar esta accion.',
    ],
];
