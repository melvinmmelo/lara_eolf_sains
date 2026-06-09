ALTER TABLE equipment ADD branch_code VARCHAR(191) NOT NULL;

UPDATE equipment SET branch_code = 'EFTO-CAG';

-- ALTER TABLE prices ALTER COLUMN p_quant INTEGER NULL;
-- ALTER TABLE prices ALTER COLUMN p_unit VARCHAR(10) NULL;

-- Per-customer price level override (nullable). When null, orders fall back to
-- the branch's designated default CUSTOMER price level (pricelevels.is_default).
ALTER TABLE customers ADD COLUMN pricelevel_id BIGINT UNSIGNED NULL AFTER branch_code;
ALTER TABLE customers ADD INDEX customers_pricelevel_id_index (pricelevel_id);

-- Marks the single default CUSTOMER price level for a branch (enforced as
-- one-per-branch in PricelevelsController).
ALTER TABLE pricelevels ADD COLUMN is_default TINYINT(1) NOT NULL DEFAULT 0;
