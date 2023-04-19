ALTER TABLE `tec_sales` ADD COLUMN `seq_id` INT NOT NULL DEFAULT 0;

UPDATE tec_sales SET seq_id = id WHERE seq_id = 0;