ALTER TABLE equipment ADD branch_code VARCHAR(191) NOT NULL;

UPDATE equipment SET branch_code = 'EFTO-CAG';

-- ALTER TABLE prices ALTER COLUMN p_quant INTEGER NULL;
-- ALTER TABLE prices ALTER COLUMN p_unit VARCHAR(10) NULL;
