CREATE TABLE ratings (
  id               INT(11) NOT NULL AUTO_INCREMENT,
  user_id          INT(10) UNSIGNED NOT NULL,
  order_stall_id   INT(11) NOT NULL,
  product_id       INT(11) NOT NULL,
  variations       VARCHAR(255)     DEFAULT NULL,
  rating_value     TINYINT(1) NOT NULL CHECK (rating_value BETWEEN 1 AND 5),
  comment          TEXT            DEFAULT NULL,
  created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (id),
  KEY idx_user (user_id),
  KEY idx_order_stall (order_stall_id),
  KEY idx_product (product_id),
  CONSTRAINT fk_ratings_user         FOREIGN KEY (user_id)          REFERENCES users(id)         ON DELETE CASCADE,
  CONSTRAINT fk_ratings_order_stall  FOREIGN KEY (order_stall_id)   REFERENCES order_stalls(id)  ON DELETE CASCADE,
  CONSTRAINT fk_ratings_product      FOREIGN KEY (product_id)       REFERENCES products(id)      ON DELETE CASCADE
);

CREATE TABLE rating_helpful (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  rating_id     INT NOT NULL,
  user_id       INT UNSIGNED NOT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_vote (rating_id, user_id),
  FOREIGN KEY (rating_id) REFERENCES ratings(id) ON DELETE CASCADE
);