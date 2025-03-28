CREATE TABLE deactivation (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) UNSIGNED NOT NULL,
  deactivated_until DATE NOT NULL,
  deactivation_reason VARCHAR(255) NOT NULL,
  status ENUM('Active', 'Deactivated') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (id),
  UNIQUE KEY (user_id),
  CONSTRAINT deactivation_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id)
);
