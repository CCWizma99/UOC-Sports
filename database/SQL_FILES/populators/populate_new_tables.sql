-- Populate the 4 new equipment management tables
-- Run this against the uoc-sports database

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `address`, `telephone_1`, `telephone_2`, `email`) VALUES
(1, 'Lanka Sports Pvt Ltd', 'No. 45, Galle Road, Colombo 03', '0112345678', '0112345679', 'info@lankasports.lk'),
(2, 'Prozone Sports Equipment', 'No. 12, Kandy Road, Peradeniya', '0812234567', '', 'sales@prozone.lk'),
(3, 'Island Sports Traders', 'No. 78, High Level Road, Nugegoda', '0112889900', '0112889901', 'orders@islandsports.lk'),
(4, 'Champion Sporting Goods', 'No. 5, Stadium Road, Colombo 07', '0114567890', '', 'contact@champion.lk'),
(5, 'Sri Lanka Sports Authority Supplies', 'No. 100, Independence Square, Colombo 07', '0112678900', '0112678901', 'supplies@slsa.gov.lk'),
(6, 'Yonex Sri Lanka', 'No. 22, Duplication Road, Colombo 04', '0115678901', '', 'info@yonex.lk'),
(7, 'Nivia Sports Lanka', 'No. 88, Baseline Road, Colombo 09', '0116789012', '0116789013', 'orders@nivia.lk'),
(8, 'University Sports Store', 'University of Colombo, College House, Colombo 03', '0112158000', '', 'sports.store@uoc.lk');

INSERT INTO `good_received_notes` (`grn_id`, `sport_id`, `equipment_id`, `description`, `date`, `po_number`, `supplier_id`, `invoice_no`, `quantity`, `unit`, `unit_price`, `reference_info`, `stock_id`, `created_at`) VALUES
(1, 'BAD', 'EQ001', 'Badminton Rackets - Yonex Astrox 88D', '2025-06-15', 'PO-2025-001', 6, 'INV-YNX-0456', 20, 'Nos', 8500.00, 'Annual procurement', 'STK00001', '2025-06-15 10:00:00'),
(2, 'BAD', 'EQ002', 'Shuttlecocks - Yonex Mavis 350', '2025-06-15', 'PO-2025-001', 6, 'INV-YNX-0457', 200, 'Tubes', 1200.00, 'Annual procurement', 'STK00002', '2025-06-15 10:00:00'),
(3, 'CRI', 'EQ022', 'Cricket Bats - SG Profile Xtreme', '2025-07-20', 'PO-2025-005', 1, 'INV-LS-1123', 12, 'Nos', 12000.00, 'Inter-university tournament', 'STK00022', '2025-07-20 09:30:00'),
(4, 'CRI', 'EQ023', 'Cricket Balls - SG Test Red', '2025-07-20', 'PO-2025-005', 1, 'INV-LS-1124', 60, 'Nos', 950.00, 'Inter-university tournament', 'STK00023', '2025-07-20 09:30:00'),
(5, 'FOO', 'EQ007', 'Footballs - Nivia Ashtang', '2025-08-10', 'PO-2025-008', 7, 'INV-NIV-0089', 18, 'Nos', 3500.00, 'Seasonal stock', 'STK00007', '2025-08-10 14:00:00'),
(6, 'BAS', 'EQ014', 'Basketballs - Molten GG7X', '2025-09-01', 'PO-2025-012', 4, 'INV-CHP-0234', 10, 'Nos', 7500.00, 'Replacement stock', 'STK00014', '2025-09-01 11:00:00'),
(7, 'VOL', 'EQ004', 'Volleyballs - Mikasa MVA200', '2025-09-15', 'PO-2025-015', 3, 'INV-IST-0567', 15, 'Nos', 6800.00, 'Inter-faculty games', 'STK00004', '2025-09-15 13:30:00'),
(8, 'TEN', 'EQ011', 'Tennis Rackets - Wilson Blade', '2025-10-05', 'PO-2025-018', 2, 'INV-PRO-0345', 12, 'Nos', 15000.00, 'Varsity team', 'STK00011', '2025-10-05 10:15:00'),
(9, 'HOC', 'EQ017', 'Hockey Sticks - Malik Carbon', '2025-11-12', 'PO-2025-022', 1, 'INV-LS-1200', 20, 'Nos', 9500.00, 'National championship prep', 'STK00017', '2025-11-12 09:00:00'),
(10, 'ATH', 'EQ043', 'Javelins - Nordic Competition', '2025-12-01', 'PO-2025-025', 5, 'INV-SLSA-0078', 12, 'Nos', 22000.00, 'Track & Field equipment', 'STK00043', '2025-12-01 08:45:00'),
(11, 'BOX', 'EQ046', 'Boxing Gloves - Everlast Pro', '2025-12-09', 'PO-2025-028', 4, 'INV-CHP-0290', 16, 'Pairs', 4500.00, 'Boxing team', 'STK00046', '2025-12-09 11:20:00'),
(12, 'SWI', 'EQ028', 'Swimming Goggles - Speedo Fastskin', '2026-01-10', 'PO-2026-001', 3, 'INV-IST-0601', 25, 'Nos', 3200.00, 'New season stock', 'STK00028', '2026-01-10 09:00:00'),
(13, 'RUG', 'EQ026', 'Rugby Balls - Gilbert Match XV', '2026-01-20', 'PO-2026-003', 7, 'INV-NIV-0112', 10, 'Nos', 5500.00, 'Rugby season', 'STK00026', '2026-01-20 14:30:00'),
(14, 'TT', 'EQ031', 'Table Tennis Bats - Butterfly Timo Boll', '2026-02-05', 'PO-2026-005', 2, 'INV-PRO-0389', 20, 'Nos', 6200.00, 'TT club renewal', 'STK00031', '2026-02-05 10:00:00'),
(15, 'BAD', 'EQ003', 'Badminton Nets - Li-Ning Tournament', '2026-02-15', 'PO-2026-008', 6, 'INV-YNX-0512', 5, 'Nos', 4500.00, 'Net replacement', 'STK00003', '2026-02-15 09:15:00');

INSERT INTO `good_issue_notes` (`gin_id`, `sport_id`, `equipment_id`, `date`, `quantity`, `unit`, `stock_id`, `sport_manager_id`, `captain_id`, `equipment_manager_id`, `created_at`) VALUES
(1, 'BAD', 'EQ001', '2025-07-10', 5, 'Nos', 'STK00001', 'usr_694d89fa', '5Q1XZO2Y', 'usr_68f82fe0', '2025-07-10 08:00:00'),
(2, 'CRI', 'EQ022', '2025-08-05', 2, 'Nos', 'STK00022', 'usr_68f89be0', NULL, 'usr_68f89998', '2025-08-05 09:30:00'),
(3, 'FOO', 'EQ007', '2025-09-20', 4, 'Nos', 'STK00007', 'SPT004', NULL, 'usr_68f82fe0', '2025-09-20 10:00:00'),
(4, 'VOL', 'EQ004', '2025-10-15', 3, 'Nos', 'STK00004', 'usr_694d89fa', NULL, 'usr_68f89998', '2025-10-15 11:30:00'),
(5, 'TEN', 'EQ011', '2025-11-10', 4, 'Nos', 'STK00011', 'SPT004', '5Q1XZO2Y', 'usr_68f82fe0', '2025-11-10 14:00:00'),
(6, 'HOC', 'EQ017', '2025-12-01', 6, 'Nos', 'STK00017', 'usr_68f89be0', NULL, 'usr_68f89998', '2025-12-01 09:15:00'),
(7, 'BAS', 'EQ014', '2026-01-08', 3, 'Nos', 'STK00014', 'usr_694d89fa', '5Q1XZO2Y', 'usr_68f82fe0', '2026-01-08 10:45:00'),
(8, 'ATH', 'EQ042', '2026-01-15', 2, 'Nos', 'STK00042', 'SPT004', NULL, 'usr_68f89998', '2026-01-15 08:30:00'),
(9, 'CRI', 'EQ023', '2026-02-01', 10, 'Nos', 'STK00023', 'usr_68f89be0', '5Q1XZO2Y', 'usr_68f82fe0', '2026-02-01 11:00:00'),
(10, 'BAD', 'EQ002', '2026-02-18', 20, 'Tubes', 'STK00002', 'usr_694d89fa', NULL, 'usr_68f89998', '2026-02-18 09:00:00');

INSERT INTO `good_condemn_notes` (`gcn_id`, `sport_id`, `equipment_id`, `stock_id`, `quantity`, `created_at`) VALUES
(1, 'BAD', 'EQ001', 'STK00001', 3, '2025-09-01 10:00:00'),
(2, 'HOC', 'EQ017', 'STK00017', 8, '2025-10-15 14:00:00'),
(3, 'TEN', 'EQ011', 'STK00011', 2, '2025-11-20 09:30:00'),
(4, 'CRI', 'EQ022', 'STK00022', 1, '2025-12-10 11:00:00'),
(5, 'CHE', 'EQ040', 'STK00040', 12, '2026-01-05 08:45:00'),
(6, 'FOO', 'EQ009', 'STK00009', 3, '2026-01-22 10:15:00'),
(7, 'BAD', 'EQ002', 'STK00002', 15, '2026-02-10 13:00:00'),
(8, 'VOL', 'EQ006', 'STK00006', 5, '2026-02-19 09:00:00');
