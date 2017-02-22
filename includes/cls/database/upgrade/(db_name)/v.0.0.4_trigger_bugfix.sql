-- multi_query
CREATE TRIGGER `hours_set_type_status_on_update` BEFORE UPDATE ON `hours` FOR EACH ROW BEGIN
  IF(NEW.hour_end IS NOT NULL) THEN
    SET NEW.hour_diff     = TIME_TO_SEC(TIMEDIFF(NEW.hour_end, NEW.hour_start)) / 60;
    SET NEW.hour_accepted = NEW.hour_diff - IFNULL(NEW.hour_minus, 0) + IFNULL(NEW.hour_plus, 0);
  END IF;

  CASE NEW.hour_type
  WHEN 'base' THEN
      SET NEW.hour_accepted = NEW.hour_diff;
      SET NEW.hour_status = 'filter';

  WHEN 'wplus' THEN
      SET NEW.hour_accepted = NEW.hour_diff + IFNULL(NEW.hour_plus, 0);
      SET NEW.hour_status = 'filter';

  WHEN 'wminus' THEN
      SET NEW.hour_accepted = NEW.hour_diff - IFNULL(NEW.hour_minus, 0);
      SET NEW.hour_status = 'filter';

  WHEN 'all' THEN
      SET NEW.hour_accepted = NEW.hour_diff - IFNULL(NEW.hour_minus, 0) + IFNULL(NEW.hour_plus, 0);
      SET NEW.hour_status = 'active';

    ELSE
      SET NEW.hour_accepted = '0';
      SET NEW.hour_status = 'deactive';

    END CASE;
END