CREATE TABLE products (
  id INT PRIMARY KEY,
  name VARCHAR(255),
  description TEXT,
  rating FLOAT,
  price_current INT,
  price_old INT,
  discount_percent INT
);

CREATE TABLE product_colors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT,
  class_name VARCHAR(50)
);

CREATE TABLE product_sizes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT,
  size VARCHAR(20)
);

INSERT INTO products VALUES
(1, 'One Life Graphic T-shirt',
 'This graphic t-shirt which is perfect for any occasion.',
 4.5, 260, 300, 40);

INSERT INTO product_colors (product_id, class_name) VALUES
(1, 'color-option--1'),
(1, 'color-option--2'),
(1, 'color-option--3');

INSERT INTO product_sizes (product_id, size) VALUES
(1, 'Small'), (1, 'Medium'), (1, 'Large'), (1, 'X-Large');
