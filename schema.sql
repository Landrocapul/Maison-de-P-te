CREATE DATABASE maison_de_pate;

USE maison_de_pate;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    fullname VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255),
    category VARCHAR(50),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    total DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample admin user
INSERT INTO users (username, fullname, email, password, role) VALUES ('admin', NULL, 'admin@gmail.com', '123456', 'admin');
-- Note: Password is now stored in plain text. Use a secure method in production.

INSERT INTO users (username, fullname, email, password, role) VALUES ('customer', NULL, 'customer@gmail.com', '123456', 'customer');

-- Insert sample products wildgrain
INSERT INTO products (name, description, price, image_path, category, stock) VALUES ('Croissants', 'Flaky, buttery croissants baked fresh daily.', 4.99, 'images/products/croissants.jpg', 'Pastry', 50);
INSERT INTO products (name, description, price, image_path, category, stock) VALUES ('Éclairs', 'Creamy éclairs with chocolate glaze.', 5.50, 'images/products/eclairs.jpg', 'Pastry', 30);
INSERT INTO products (name, description, price, image_path, category, stock) VALUES ('Cinnamon Rolls', 'Soft cinnamon rolls with icing.', 6.99, 'images/products/cinnamonroll.jpg', 'Pastry', 40);
INSERT INTO products (name, description, price, image_path, category, stock) VALUES ('Fresh Baked Bread', 'Artisanal bread loaf, perfect for sandwiches.', 7.99, 'images/products/freshbaked.jpeg', 'Bread', 20);
INSERT INTO products (name, description, price, image_path, category, stock) VALUES ('Baguettes', 'Traditional French baguettes with a crispy crust.', 3.99, 'images/products/baguettes.jpg', 'Bread', 25);
INSERT INTO products (name, description, price, image_path, category, stock) VALUES ('Macarons', 'Delicate French macarons in assorted flavors.', 2.50, 'images/products/Macarons.jpeg', 'Pastry', 60);
INSERT INTO products (name, description, price, image_path, category, stock) VALUES ('Mille-Feuille', 'Layered puff pastry with cream and icing.', 8.99, 'images/products/Mille-Feuille.jpg', 'Pastry', 15);
INSERT INTO products (name, description, price, image_path, category, stock) VALUES ('Pain au Chocolat', 'Buttery pastry filled with chocolate.', 5.99, 'images/products/Pain au Chocolat.jpg', 'Pastry', 35);
INSERT INTO products (name, description, price, image_path, category, stock) VALUES ('Tarte Tatin', 'Upside-down caramelized apple tart.', 9.50, 'images/products/Tarte Tatin.jpg', 'Pastry', 20);