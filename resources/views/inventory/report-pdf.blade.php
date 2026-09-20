<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>USM Hospital Pharmacy - Inventory Valuation Report</title>
    <style>
        @page {
            margin: 12mm 10mm;
            size: a4 landscape;
        }

        * {
            font-family: 'DejaVu Sans', sans-serif;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        .currency {
            font-family: 'DejaVu Sans', sans-serif;
        }

        /* Header layout */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #047857;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .brand-title {
            font-size: 18px;
            font-weight: bold;
            color: #065f46;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .brand-subtitle {
            font-size: 11px;
            color: #475569;
            margin-top: 1px;
        }

        .report-badge {
            display: inline-block;
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-text {
            font-size: 9.5px;
            color: #64748b;
            text-align: right;
            line-height: 1.4;
        }

        .meta-text strong {
            color: #1e293b;
        }

        /* KPI & Financial Summaries */
        .kpi-section {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .kpi-section td {
            padding: 0 4px;
            vertical-align: top;
        }

        .kpi-card {
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 7px 9px;
            background-color: #f8fafc;
        }

        .kpi-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
        }

        .kpi-value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }

        .kpi-subtext {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* Highlighted financial cards */
        .card-stock-val {
            background-color: #eff6ff;
            border-color: #bfdbfe;
        }
        .card-stock-val .kpi-label { color: #1d4ed8; }
        .card-stock-val .kpi-value { color: #1e40af; }

        .card-sale-val {
            background-color: #eef2ff;
            border-color: #c7d2fe;
        }
        .card-sale-val .kpi-label { color: #4338ca; }
        .card-sale-val .kpi-value { color: #3730a3; }

        .card-profit {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
        }
        .card-profit .kpi-label { color: #15803d; }
        .card-profit .kpi-value { color: #166534; }

        /* Inventory Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .data-table th {
            background-color: #065f46;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.3px;
            padding: 6px 5px;
            border: 1px solid #047857;
            text-align: left;
        }

        .data-table th.text-right {
            text-align: right;
        }

        .data-table th.text-center {
            text-align: center;
        }

        .data-table td {
            padding: 5px 5px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .med-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 9.5px;
        }

        .med-generic {
            font-size: 8px;
            color: #64748b;
        }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            letter-spacing: 0.3px;
        }

        .badge-healthy {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .badge-low {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .badge-out {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        /* Table totals row */
        .totals-row td {
            background-color: #f1f5f9 !important;
            font-weight: bold;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            font-size: 9px;
            color: #0f172a;
            padding: 6px 5px;
        }

        /* Footer */
        .report-footer {
            margin-top: 16px;
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            color: #64748b;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
        }

        .report-footer td {
            vertical-align: top;
        }

        .sign-area {
            text-align: right;
            padding-top: 25px;
        }

        .sign-line {
            display: inline-block;
            width: 180px;
            border-top: 1px solid #475569;
            text-align: center;
            padding-top: 3px;
            font-size: 8.5px;
            color: #1e293b;
        }
    </style>
</head>
<body>

    <!-- Hospital & Report Header -->
    <table class="header-table">
        <tr>
            <td style="width: 58%;">
                <div class="brand-title">USM Hospital Pharmacy</div>
                <div class="brand-subtitle">University of Southern Mindanao &bull; Kabacan, Cotabato, Philippines</div>
                <div style="margin-top: 5px;">
                    <span class="report-badge">Official Inventory & Valuation Report</span>
                </div>
            </td>
            <td style="width: 42%;" class="meta-text">
                <div><strong>Report Date:</strong> {{ $generatedAt->format('F d, Y \a\t h:i A') }}</div>
                <div><strong>Generated By:</strong> {{ $generatedBy }}</div>
                <div><strong>Formulary Scope:</strong> Active Hospital Pharmacy Inventory</div>
            </td>
        </tr>
    </table>

    <!-- Executive Summary KPI & Valuation Cards -->
    <table class="kpi-section">
        <tr>
            <!-- KPI 1 -->
            <td style="width: 12.5%;">
                <div class="kpi-card">
                    <div class="kpi-label">Formulary</div>
                    <div class="kpi-value">{{ number_format($totalItems) }}</div>
                    <div class="kpi-subtext">Total Items</div>
                </div>
            </td>
            <!-- KPI 2 -->
            <td style="width: 12.5%;">
                <div class="kpi-card">
                    <div class="kpi-label">Healthy</div>
                    <div class="kpi-value" style="color: #15803d;">{{ number_format($inStockCount) }}</div>
                    <div class="kpi-subtext">Above reorder</div>
                </div>
            </td>
            <!-- KPI 3 -->
            <td style="width: 12.5%;">
                <div class="kpi-card">
                    <div class="kpi-label">Low Stock</div>
                    <div class="kpi-value" style="color: #b45309;">{{ number_format($lowStockCount) }}</div>
                    <div class="kpi-subtext">Need reorder</div>
                </div>
            </td>
            <!-- KPI 4 -->
            <td style="width: 12.5%;">
                <div class="kpi-card">
                    <div class="kpi-label">Out of Stock</div>
                    <div class="kpi-value" style="color: #b91c1c;">{{ number_format($outOfStockCount) }}</div>
                    <div class="kpi-subtext">Zero units</div>
                </div>
            </td>
            <!-- KPI 5 -->
            <td style="width: 12.5%;">
                <div class="kpi-card">
                    <div class="kpi-label">Expiring</div>
                    <div class="kpi-value" style="color: #7c3aed;">{{ number_format($expiringSoonCount) }}</div>
                    <div class="kpi-subtext">Within 30 days</div>
                </div>
            </td>
            <!-- Valuation Card 1: Stock Value -->
            <td style="width: 12.5%;">
                <div class="kpi-card card-stock-val">
                    <div class="kpi-label">Stock Value</div>
                    <div class="kpi-value">&#8369;{{ number_format($totalStockValue, 2) }}</div>
                    <div class="kpi-subtext">Qty &times; Cost Price</div>
                </div>
            </td>
            <!-- Valuation Card 2: Sale Value -->
            <td style="width: 12.5%;">
                <div class="kpi-card card-sale-val">
                    <div class="kpi-label">Sale Value</div>
                    <div class="kpi-value">&#8369;{{ number_format($totalSaleValue, 2) }}</div>
                    <div class="kpi-subtext">Qty &times; Selling Price</div>
                </div>
            </td>
            <!-- Valuation Card 3: Expected Profit -->
            <td style="width: 12.5%;">
                <div class="kpi-card card-profit">
                    <div class="kpi-label">Expected Profit</div>
                    <div class="kpi-value">&#8369;{{ number_format($expectedProfit, 2) }}</div>
                    <div class="kpi-subtext">Sale Val - Cost Val</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Detailed Inventory Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 2.5%;" class="text-center">#</th>
                <th style="width: 20%;">Medicine Name & Generic</th>
                <th style="width: 11%;">Category</th>
                <th style="width: 6%;" class="text-right">Stock</th>
                <th style="width: 5%;">Unit</th>
                <th style="width: 9%;" class="text-right">Cost Price</th>
                <th style="width: 9%;" class="text-right">Selling Price</th>
                <th style="width: 11%;" class="text-right">Stock Value</th>
                <th style="width: 11%;" class="text-right">Sale Value</th>
                <th style="width: 8.5%;" class="text-center">Nearest Expiry</th>
                <th style="width: 7%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($medicines as $index => $med)
                @php
                    $stock = $med->available_stock;
                    $costPrice = $med->cost_price;
                    $sellingPrice = $med->selling_price;
                    $stockVal = $med->stock_value;
                    $saleVal = $med->sale_value;
                    $nearestBatch = $med->stockBatches->first();
                    $expiry = $nearestBatch?->expiry_date?->format('M d, Y') ?? 'N/A';
                @endphp
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $index + 1 }}</td>
                    <td>
                        <div class="med-name">{{ $med->name }}</div>
                        <div class="med-generic">{{ $med->generic_name }}</div>
                    </td>
                    <td>{{ $med->category }}</td>
                    <td class="text-right" style="font-weight: bold; {{ $stock == 0 ? 'color: #dc2626;' : ($stock <= $med->reorder_level ? 'color: #d97706;' : 'color: #15803d;') }}">
                        {{ number_format($stock) }}
                    </td>
                    <td>{{ $med->unit }}</td>
                    <td class="text-right">&#8369;{{ number_format($costPrice, 2) }}</td>
                    <td class="text-right">&#8369;{{ number_format($sellingPrice, 2) }}</td>
                    <td class="text-right" style="font-weight: bold;">&#8369;{{ number_format($stockVal, 2) }}</td>
                    <td class="text-right" style="font-weight: bold;">&#8369;{{ number_format($saleVal, 2) }}</td>
                    <td class="text-center" style="font-size: 8px;">{{ $expiry }}</td>
                    <td class="text-center">
                        @if ($stock === 0)
                            <span class="badge badge-out">Out</span>
                        @elseif ($stock <= $med->reorder_level)
                            <span class="badge badge-low">Low</span>
                        @else
                            <span class="badge badge-healthy">Healthy</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 16px; color: #64748b;">
                        No medicines catalogued in the formulary.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td colspan="3" class="text-right">PORTFOLIO TOTALS:</td>
                <td class="text-right">{{ number_format($medicines->sum(fn ($m) => $m->available_stock)) }}</td>
                <td>units</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">&#8369;{{ number_format($totalStockValue, 2) }}</td>
                <td class="text-right">&#8369;{{ number_format($totalSaleValue, 2) }}</td>
                <td colspan="2" class="text-center" style="font-size: 8.5px; color: #166534;">
                    Profit: &#8369;{{ number_format($expectedProfit, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer & Signatures -->
    <table class="report-footer">
        <tr>
            <td style="width: 50%;">
                <div>USM Hospital Pharmacy Information System &bull; Confidential &bull; For Internal Hospital Administration Use Only</div>
                <div style="margin-top: 2px;">This automated inventory audit reflects live batches, purchase allocations, and catalog pricing.</div>
            </td>
            <td style="width: 50%;" class="sign-area">
                <div class="sign-line">
                    Prepared By: <strong>{{ $generatedBy }}</strong><br>
                    <span style="color: #64748b; font-size: 7.5px;">Pharmacy Custodian / Stock Manager</span>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
