<?php
require_once __DIR__ . '/../../../core/libs/fpdf/fpdf.php';
require_once __DIR__ . '/../../models/Equipment.php';
require_once __DIR__ . '/../../models/EquipmentManagement.php';

class EquipmentReportController {

    /** Get period label for PDF titles */
    private function getPeriodLabel($year = null, $month = null) {
        if ($year && $month) {
            return date('F', mktime(0, 0, 0, $month, 1)) . " $year";
        } elseif ($year) {
            return "Year $year";
        }
        return "All Time";
    }

    /** Get year/month from GET params */
    private function getFilters() {
        $year = $_GET['year'] ?? null;
        $month = $_GET['month'] ?? null;
        return [$year, $month];
    }

    /** Common PDF header */
    private function pdfHeader($pdf, $title, $periodLabel) {
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(75, 0, 130);
        $pdf->Cell(0, 12, $title, 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 6, 'University of Colombo - Sports E-Portal', 0, 1, 'C');
        $pdf->Cell(0, 6, 'Period: ' . $periodLabel . '  |  Generated: ' . date('F j, Y - g:i A'), 0, 1, 'C');
        $pdf->Ln(8);
    }

    /** Common PDF footer */
    private function pdfFooter($pdf) {
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->Cell(0, 5, '--- End of Report ---', 0, 1, 'C');
        $pdf->Cell(0, 5, 'UOC Sports E-Portal | Equipment Management System', 0, 1, 'C');
    }

    /** Truncate string */
    private function truncate($string, $length) {
        if (strlen($string) > $length) return substr($string, 0, $length - 2) . '..';
        return $string;
    }

    /** Section Header */
    private function sectionHeader($pdf, $title) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, $title, 0, 1, 'L');
        $pdf->SetDrawColor(75, 0, 130);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(4);
    }

    // ════════════════════════════════════════════════
    // 1. Equipment-wise Inventory Report
    // ════════════════════════════════════════════════
    public function equipmentInventoryPDF() {
        [$year, $month] = $this->getFilters();
        $model = new EquipmentManagement();
        $data = $model->getEquipmentInventoryReport($year, $month);
        $periodLabel = $this->getPeriodLabel($year, $month);

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);

        $this->pdfHeader($pdf, 'Equipment-wise Inventory Report', $periodLabel);

        // Summary
        $this->sectionHeader($pdf, 'Summary');
        $pdf->SetFont('Arial', '', 10);
        $s = $data['summary'];
        $pdf->Cell(60, 6, 'Total Equipment Types:', 0, 0); $pdf->Cell(30, 6, $s['total_equipment_types'], 0, 1);
        $pdf->Cell(60, 6, 'Total Stock:', 0, 0); $pdf->Cell(30, 6, $s['total_stock'], 0, 1);
        $pdf->Cell(60, 6, 'Usable Items:', 0, 0); $pdf->Cell(30, 6, $s['total_usable'], 0, 1);
        $pdf->Cell(60, 6, 'Damaged Items:', 0, 0); $pdf->Cell(30, 6, $s['total_damaged'], 0, 1);
        $pdf->Cell(60, 6, 'Sports Covered:', 0, 0); $pdf->Cell(30, 6, $s['sports_covered'], 0, 1);
        $pdf->Cell(60, 6, 'Overall Condition:', 0, 0); $pdf->Cell(30, 6, $s['overall_condition'] . '%', 0, 1);
        $pdf->Ln(8);

        // Detail table
        $this->sectionHeader($pdf, 'Equipment Inventory Details');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(75, 0, 130); $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(40, 8, 'Sport', 1, 0, 'C', true);
        $pdf->Cell(55, 8, 'Equipment Name', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Stock', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Usable', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Damaged', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Cond.%', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $fill = false;
        foreach ($data['equipment'] as $item) {
            $pdf->SetFillColor($fill ? 245 : 255, $fill ? 243 : 255, $fill ? 255 : 255);
            $pdf->Cell(40, 7, $this->truncate($item['sport_name'], 18), 1, 0, 'L', true);
            $pdf->Cell(55, 7, $this->truncate($item['equipment_name'], 25), 1, 0, 'L', true);
            $pdf->Cell(25, 7, $item['total_stock'], 1, 0, 'C', true);
            $pdf->Cell(25, 7, $item['usable'], 1, 0, 'C', true);
            $pdf->Cell(25, 7, $item['damaged'], 1, 0, 'C', true);
            $cond = $item['condition_percent'];
            if ($cond >= 80) $pdf->SetTextColor(0, 150, 0);
            elseif ($cond >= 50) $pdf->SetTextColor(200, 130, 0);
            else $pdf->SetTextColor(200, 0, 0);
            $pdf->Cell(20, 7, $cond . '%', 1, 1, 'C', true);
            $pdf->SetTextColor(0, 0, 0);
            $fill = !$fill;
        }

        $this->pdfFooter($pdf);
        $pdf->Output('D', 'Equipment_Inventory_' . ($year ?? 'AllTime') . ($month ? '_' . $month : '') . '.pdf');
        exit;
    }

    // ════════════════════════════════════════════════
    // 2. Supplier-wise Details & Analysis
    // ════════════════════════════════════════════════
    public function supplierDetailsPDF() {
        [$year, $month] = $this->getFilters();
        $model = new EquipmentManagement();
        $data = $model->getSupplierReport($year, $month);
        $periodLabel = $this->getPeriodLabel($year, $month);

        $pdf = new FPDF('L', 'mm', 'A4'); // Landscape for more columns
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);

        $this->pdfHeader($pdf, 'Supplier-wise Details & Analysis', $periodLabel);
        $this->sectionHeader($pdf, 'Supplier Summary');

        // Table header
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(75, 0, 130); $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(50, 8, 'Supplier', 1, 0, 'C', true);
        $pdf->Cell(60, 8, 'Address', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Telephone', 1, 0, 'C', true);
        $pdf->Cell(45, 8, 'Email', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'GRNs', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Items', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Total (Rs.)', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $fill = false;
        $grandTotal = 0;
        foreach ($data as $row) {
            $pdf->SetFillColor($fill ? 245 : 255, $fill ? 243 : 255, $fill ? 255 : 255);
            $pdf->Cell(50, 7, $this->truncate($row['supplier_name'], 24), 1, 0, 'L', true);
            $pdf->Cell(60, 7, $this->truncate($row['address'], 28), 1, 0, 'L', true);
            $pdf->Cell(30, 7, $row['telephone_1'], 1, 0, 'C', true);
            $pdf->Cell(45, 7, $this->truncate($row['email'] ?? '-', 20), 1, 0, 'L', true);
            $pdf->Cell(25, 7, $row['total_grns'], 1, 0, 'C', true);
            $pdf->Cell(25, 7, $row['total_items_supplied'], 1, 0, 'C', true);
            $pdf->Cell(35, 7, number_format($row['total_value'], 2), 1, 1, 'R', true);
            $grandTotal += $row['total_value'];
            $fill = !$fill;
        }
        // Grand total row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(235, 8, 'Grand Total:', 1, 0, 'R');
        $pdf->Cell(35, 8, 'Rs. ' . number_format($grandTotal, 2), 1, 1, 'R');

        $this->pdfFooter($pdf);
        $pdf->Output('D', 'Supplier_Report_' . ($year ?? 'AllTime') . ($month ? '_' . $month : '') . '.pdf');
        exit;
    }

    // ════════════════════════════════════════════════
    // 3. All Equipment Snapshot
    // ════════════════════════════════════════════════
    public function allEquipmentSnapshotPDF() {
        [$year, $month] = $this->getFilters();
        $model = new EquipmentManagement();
        $data = $model->getAllEquipmentSnapshot($year, $month);
        $periodLabel = $this->getPeriodLabel($year, $month);

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);

        $this->pdfHeader($pdf, 'All Equipment Snapshot', $periodLabel);
        $this->sectionHeader($pdf, 'Equipment Overview');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(75, 0, 130); $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(10, 8, '#', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Sport', 1, 0, 'C', true);
        $pdf->Cell(70, 8, 'Equipment Name', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Total Stock', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Usable', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $fill = false;
        $i = 0;
        foreach ($data as $row) {
            $i++;
            $pdf->SetFillColor($fill ? 245 : 255, $fill ? 243 : 255, $fill ? 255 : 255);
            $pdf->Cell(10, 7, $i, 1, 0, 'C', true);
            $pdf->Cell(50, 7, $this->truncate($row['sport_name'] ?? '-', 24), 1, 0, 'L', true);
            $pdf->Cell(70, 7, $this->truncate($row['equipment_name'], 32), 1, 0, 'L', true);
            $pdf->Cell(30, 7, $row['total_stock'], 1, 0, 'C', true);
            $pdf->Cell(30, 7, $row['usable'], 1, 1, 'C', true);
            $fill = !$fill;
        }

        $this->pdfFooter($pdf);
        $pdf->Output('D', 'Equipment_Snapshot_' . ($year ?? 'AllTime') . ($month ? '_' . $month : '') . '.pdf');
        exit;
    }

    // ════════════════════════════════════════════════
    // 4. Stock-wise Snapshot
    // ════════════════════════════════════════════════
    public function stockSnapshotPDF() {
        [$year, $month] = $this->getFilters();
        $model = new EquipmentManagement();
        $data = $model->getStockSnapshot($year, $month);
        $periodLabel = $this->getPeriodLabel($year, $month);

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);

        $this->pdfHeader($pdf, 'Stock-wise Snapshot', $periodLabel);
        $this->sectionHeader($pdf, 'Stock Entries');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(75, 0, 130); $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(25, 8, 'Stock ID', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Sport', 1, 0, 'C', true);
        $pdf->Cell(55, 8, 'Equipment', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Qty', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Usable', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Damaged', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Date Added', 1, 0, 'C', true);
        $pdf->Cell(60, 8, 'Remarks', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $fill = false;
        foreach ($data as $row) {
            $pdf->SetFillColor($fill ? 245 : 255, $fill ? 243 : 255, $fill ? 255 : 255);
            $pdf->Cell(25, 7, $row['stock_id'], 1, 0, 'C', true);
            $pdf->Cell(40, 7, $this->truncate($row['sport_name'], 18), 1, 0, 'L', true);
            $pdf->Cell(55, 7, $this->truncate($row['equipment_name'], 25), 1, 0, 'L', true);
            $pdf->Cell(20, 7, $row['quantity'], 1, 0, 'C', true);
            $pdf->Cell(20, 7, $row['usable'], 1, 0, 'C', true);
            $pdf->Cell(20, 7, $row['damaged'], 1, 0, 'C', true);
            $pdf->Cell(30, 7, $row['added_date'], 1, 0, 'C', true);
            $pdf->Cell(60, 7, $this->truncate($row['remarks'] ?? '-', 28), 1, 1, 'L', true);
            $fill = !$fill;
        }

        $this->pdfFooter($pdf);
        $pdf->Output('D', 'Stock_Snapshot_' . ($year ?? 'AllTime') . ($month ? '_' . $month : '') . '.pdf');
        exit;
    }

    // ════════════════════════════════════════════════
    // 5. Period Activity Snapshot (GRN/GIN/GCN)
    // ════════════════════════════════════════════════
    public function periodSnapshotPDF() {
        [$year, $month] = $this->getFilters();
        $model = new EquipmentManagement();
        $data = $model->getPeriodSnapshot($year, $month);
        $periodLabel = $this->getPeriodLabel($year, $month);

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);

        $this->pdfHeader($pdf, 'Activity Snapshot Report', $periodLabel);

        // ── Summary Cards ──
        $this->sectionHeader($pdf, 'Activity Overview');
        $pdf->SetFont('Arial', '', 10);
        $grn = $data['grn']; $gin = $data['gin']; $gcn = $data['gcn'];
        $pdf->Cell(50, 6, 'Goods Received (GRNs):', 0, 0); $pdf->Cell(40, 6, $grn['total_grns'] . ' notes, ' . $grn['total_received'] . ' items', 0, 0);
        $pdf->Cell(50, 6, 'Total Cost:', 0, 0); $pdf->Cell(40, 6, 'Rs. ' . number_format($grn['total_cost'], 2), 0, 1);
        $pdf->Cell(50, 6, 'Goods Issued (GINs):', 0, 0); $pdf->Cell(40, 6, $gin['total_gins'] . ' notes, ' . $gin['total_issued'] . ' items', 0, 1);
        $pdf->Cell(50, 6, 'Goods Condemned (GCNs):', 0, 0); $pdf->Cell(40, 6, $gcn['total_gcns'] . ' notes, ' . $gcn['total_condemned'] . ' items', 0, 1);
        $pdf->Ln(6);

        // ── GRN Details ──
        if (!empty($data['grn_details'])) {
            $this->sectionHeader($pdf, 'Goods Received Details');
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(75, 0, 130); $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(25, 7, 'Date', 1, 0, 'C', true);
            $pdf->Cell(45, 7, 'Equipment', 1, 0, 'C', true);
            $pdf->Cell(35, 7, 'Sport', 1, 0, 'C', true);
            $pdf->Cell(40, 7, 'Supplier', 1, 0, 'C', true);
            $pdf->Cell(15, 7, 'Qty', 1, 0, 'C', true);
            $pdf->Cell(20, 7, 'Unit', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'Price', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'PO No.', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'Invoice', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetTextColor(0, 0, 0);
            $fill = false;
            foreach ($data['grn_details'] as $row) {
                $pdf->SetFillColor($fill ? 245 : 255, $fill ? 243 : 255, $fill ? 255 : 255);
                $pdf->Cell(25, 6, $row['date'], 1, 0, 'C', true);
                $pdf->Cell(45, 6, $this->truncate($row['equipment_name'], 20), 1, 0, 'L', true);
                $pdf->Cell(35, 6, $this->truncate($row['sport_name'], 16), 1, 0, 'L', true);
                $pdf->Cell(40, 6, $this->truncate($row['supplier_name'], 18), 1, 0, 'L', true);
                $pdf->Cell(15, 6, $row['quantity'], 1, 0, 'C', true);
                $pdf->Cell(20, 6, $row['unit'], 1, 0, 'C', true);
                $pdf->Cell(25, 6, number_format($row['unit_price'], 2), 1, 0, 'R', true);
                $pdf->Cell(25, 6, $this->truncate($row['po_number'] ?? '-', 12), 1, 0, 'C', true);
                $pdf->Cell(30, 6, $this->truncate($row['invoice_no'] ?? '-', 14), 1, 1, 'C', true);
                $fill = !$fill;
            }
            $pdf->Ln(6);
        }

        // ── GIN Details ──
        if (!empty($data['gin_details'])) {
            $this->sectionHeader($pdf, 'Goods Issued Details');
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(75, 0, 130); $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(30, 7, 'Date', 1, 0, 'C', true);
            $pdf->Cell(60, 7, 'Equipment', 1, 0, 'C', true);
            $pdf->Cell(50, 7, 'Sport', 1, 0, 'C', true);
            $pdf->Cell(20, 7, 'Qty', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'Unit', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'Stock ID', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetTextColor(0, 0, 0);
            $fill = false;
            foreach ($data['gin_details'] as $row) {
                $pdf->SetFillColor($fill ? 245 : 255, $fill ? 243 : 255, $fill ? 255 : 255);
                $pdf->Cell(30, 6, $row['date'], 1, 0, 'C', true);
                $pdf->Cell(60, 6, $this->truncate($row['equipment_name'], 28), 1, 0, 'L', true);
                $pdf->Cell(50, 6, $this->truncate($row['sport_name'], 24), 1, 0, 'L', true);
                $pdf->Cell(20, 6, $row['quantity'], 1, 0, 'C', true);
                $pdf->Cell(30, 6, $row['unit'], 1, 0, 'C', true);
                $pdf->Cell(30, 6, $row['stock_id'], 1, 1, 'C', true);
                $fill = !$fill;
            }
            $pdf->Ln(6);
        }

        // ── GCN Details ──
        if (!empty($data['gcn_details'])) {
            $this->sectionHeader($pdf, 'Goods Condemned Details');
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(75, 0, 130); $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(40, 7, 'Date', 1, 0, 'C', true);
            $pdf->Cell(70, 7, 'Equipment', 1, 0, 'C', true);
            $pdf->Cell(50, 7, 'Sport', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'Qty', 1, 0, 'C', true);
            $pdf->Cell(35, 7, 'Stock ID', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetTextColor(0, 0, 0);
            $fill = false;
            foreach ($data['gcn_details'] as $row) {
                $pdf->SetFillColor($fill ? 245 : 255, $fill ? 243 : 255, $fill ? 255 : 255);
                $pdf->Cell(40, 6, substr($row['date'], 0, 10), 1, 0, 'C', true);
                $pdf->Cell(70, 6, $this->truncate($row['equipment_name'], 32), 1, 0, 'L', true);
                $pdf->Cell(50, 6, $this->truncate($row['sport_name'], 24), 1, 0, 'L', true);
                $pdf->Cell(30, 6, $row['quantity'], 1, 0, 'C', true);
                $pdf->Cell(35, 6, $row['stock_id'], 1, 1, 'C', true);
                $fill = !$fill;
            }
        }

        $this->pdfFooter($pdf);
        $pdf->Output('D', 'Activity_Snapshot_' . ($year ?? 'AllTime') . ($month ? '_' . $month : '') . '.pdf');
        exit;
    }

    // ════════════════════════════════════════════════
    // 6. Supplier Detail Report (single supplier)
    // ════════════════════════════════════════════════
    public function supplierDetailPDF() {
        [$year, $month] = $this->getFilters();
        $supplierId = $_GET['supplier_id'] ?? null;
        if (!$supplierId) { echo 'Supplier ID required'; exit; }

        $model = new EquipmentManagement();
        $data = $model->getSupplierDetailReport($supplierId, $year, $month);
        if (!$data) { echo 'Supplier not found'; exit; }
        $periodLabel = $this->getPeriodLabel($year, $month);

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);

        $this->pdfHeader($pdf, 'Supplier Detail Report', $periodLabel);

        // Supplier info
        $this->sectionHeader($pdf, 'Supplier Information');
        $pdf->SetFont('Arial', '', 10);
        $sup = $data['supplier'];
        $pdf->Cell(40, 6, 'Supplier:', 0, 0); $pdf->Cell(80, 6, $sup['supplier_name'], 0, 1);
        $pdf->Cell(40, 6, 'Address:', 0, 0); $pdf->Cell(80, 6, $sup['address'], 0, 1);
        $pdf->Cell(40, 6, 'Telephone:', 0, 0); $pdf->Cell(80, 6, $sup['telephone_1'] . ($sup['telephone_2'] ? ' / ' . $sup['telephone_2'] : ''), 0, 1);
        $pdf->Cell(40, 6, 'Email:', 0, 0); $pdf->Cell(80, 6, $sup['email'] ?? '-', 0, 1);
        $pdf->Ln(4);

        // Summary
        $this->sectionHeader($pdf, 'Summary');
        $s = $data['summary'];
        $pdf->Cell(40, 6, 'Total GRNs:', 0, 0); $pdf->Cell(30, 6, $s['total_grns'], 0, 1);
        $pdf->Cell(40, 6, 'Total Items:', 0, 0); $pdf->Cell(30, 6, $s['total_items'], 0, 1);
        $pdf->Cell(40, 6, 'Total Value:', 0, 0); $pdf->Cell(50, 6, 'Rs. ' . number_format($s['total_value'], 2), 0, 1);
        $pdf->Ln(6);

        // GRN detail table
        if (!empty($data['grns'])) {
            $this->sectionHeader($pdf, 'Delivery Records');
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(75, 0, 130); $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(22, 7, 'Date', 1, 0, 'C', true);
            $pdf->Cell(45, 7, 'Equipment', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'Sport', 1, 0, 'C', true);
            $pdf->Cell(50, 7, 'Description', 1, 0, 'C', true);
            $pdf->Cell(15, 7, 'Qty', 1, 0, 'C', true);
            $pdf->Cell(18, 7, 'Unit', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'Price', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'Total', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'PO No.', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'Invoice', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetTextColor(0, 0, 0);
            $fill = false;
            foreach ($data['grns'] as $row) {
                $pdf->SetFillColor($fill ? 245 : 255, $fill ? 243 : 255, $fill ? 255 : 255);
                $pdf->Cell(22, 6, $row['date'], 1, 0, 'C', true);
                $pdf->Cell(45, 6, $this->truncate($row['equipment_name'], 20), 1, 0, 'L', true);
                $pdf->Cell(30, 6, $this->truncate($row['sport_name'], 14), 1, 0, 'L', true);
                $pdf->Cell(50, 6, $this->truncate($row['description'] ?? '-', 24), 1, 0, 'L', true);
                $pdf->Cell(15, 6, $row['quantity'], 1, 0, 'C', true);
                $pdf->Cell(18, 6, $row['unit'], 1, 0, 'C', true);
                $pdf->Cell(25, 6, number_format($row['unit_price'], 2), 1, 0, 'R', true);
                $pdf->Cell(25, 6, number_format($row['quantity'] * $row['unit_price'], 2), 1, 0, 'R', true);
                $pdf->Cell(25, 6, $this->truncate($row['po_number'] ?? '-', 12), 1, 0, 'C', true);
                $pdf->Cell(25, 6, $this->truncate($row['invoice_no'] ?? '-', 12), 1, 1, 'C', true);
                $fill = !$fill;
            }
        }

        $this->pdfFooter($pdf);
        $safeName = preg_replace('/[^A-Za-z0-9]/', '_', $sup['supplier_name']);
        $pdf->Output('D', 'Supplier_' . $safeName . '_' . ($year ?? 'AllTime') . ($month ? '_' . $month : '') . '.pdf');
        exit;
    }

    // ════════════════════════════════════════════════
    // Legacy: existing generatePDF (kept for backward compat)
    // ════════════════════════════════════════════════
    public function generatePDF() {
        $this->equipmentInventoryPDF();
    }
}
