ALTER TABLE `hours` ADD `hour_type` ENUM('nothing','base','wplus','wminus','all') NULL DEFAULT 'all';