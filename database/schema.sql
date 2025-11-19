-- DragonStone Prototype Database Schema
-- MySQL 8.x compatible

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS admin_roles;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS admin_users;
DROP TABLE IF EXISTS ecopoint_transactions;
DROP TABLE IF EXISTS ecopoint_rules;
DROP TABLE IF EXISTS community_comments;
DROP TABLE IF EXISTS community_posts;
DROP TABLE IF EXISTS challenges;
DROP TABLE IF EXISTS subscriptions_items;
DROP TABLE IF EXISTS subscriptions;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS shipments;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS inventory_snapshots;
DROP TABLE IF EXISTS product_tags;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS product_impact_metrics;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    sku VARCHAR(40) NOT NULL UNIQUE,
    name VARCHAR(120) NOT NULL,
    summary VARCHAR(255) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    subscription_eligible TINYINT(1) DEFAULT 0,
    sustainability_score TINYINT UNSIGNED DEFAULT 0,
    carbon_footprint_kg DECIMAL(8,2) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE product_impact_metrics (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    metric_label VARCHAR(120) NOT NULL,
    metric_value VARCHAR(120) NOT NULL,
    baseline_comparison VARCHAR(120) NULL,
    source_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE tags (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(40) NOT NULL UNIQUE
);

CREATE TABLE product_tags (
    product_id INT UNSIGNED NOT NULL,
    tag_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (product_id, tag_id),
    FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE inventory_snapshots (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    snapshot_date DATE NOT NULL,
    restock_eta DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(60) NOT NULL,
    last_name VARCHAR(60) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(40) NULL,
    city VARCHAR(120) NULL,
    country VARCHAR(120) NULL,
    eco_points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    order_reference VARCHAR(40) NOT NULL UNIQUE,
    subtotal DECIMAL(10,2) NOT NULL,
    discount_total DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    total_converted DECIMAL(10,2) NOT NULL DEFAULT 0,
    currency_code CHAR(3) NOT NULL DEFAULT 'USD',
    currency_rate DECIMAL(10,6) NOT NULL DEFAULT 1.000000,
    eco_points_awarded INT DEFAULT 0,
    eco_points_redeemed INT DEFAULT 0,
    status ENUM('pending','paid','shipped','completed','cancelled') DEFAULT 'pending',
    placed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    unit_price_display DECIMAL(10,2) NOT NULL DEFAULT 0,
    currency_code CHAR(3) NOT NULL DEFAULT 'USD',
    eco_points INT DEFAULT 0,
    FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE shipments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    provider VARCHAR(80) NOT NULL,
    tracking_number VARCHAR(80) NULL,
    shipped_at DATETIME NULL,
    delivered_at DATETIME NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    method ENUM('card','paypal','bank_transfer','manual') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    amount_converted DECIMAL(10,2) NOT NULL DEFAULT 0,
    currency_code CHAR(3) NOT NULL DEFAULT 'USD',
    status ENUM('pending','authorized','captured','failed','refunded') DEFAULT 'pending',
    processed_at DATETIME NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE subscriptions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    interval_unit ENUM('weekly','monthly','quarterly') NOT NULL,
    next_renewal DATE NOT NULL,
    last_processed DATE NULL,
    currency_code CHAR(3) NOT NULL DEFAULT 'USD',
    status ENUM('active','paused','cancelled') DEFAULT 'active',
    auto_renew TINYINT(1) DEFAULT 1,
    reward_points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE subscriptions_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subscription_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price_snapshot DECIMAL(10,2) NOT NULL DEFAULT 0,
    last_fulfilled DATE NULL,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE challenges (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    description TEXT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    eco_points_reward INT NOT NULL,
    status ENUM('scheduled','active','completed') DEFAULT 'scheduled'
);

CREATE TABLE community_posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    challenge_id INT UNSIGNED NULL,
    title VARCHAR(160) NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending','approved','flagged') DEFAULT 'pending',
    FOREIGN KEY (customer_id) REFERENCES customers(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (challenge_id) REFERENCES challenges(id)
        ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE community_comments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES community_posts(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE ecopoint_rules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    action_key VARCHAR(80) NOT NULL UNIQUE,
    description VARCHAR(160) NOT NULL,
    points INT NOT NULL,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE ecopoint_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    rule_id INT UNSIGNED NULL,
    source_type ENUM('order','subscription','challenge','manual','redemption') NOT NULL,
    source_reference VARCHAR(80) NULL,
    points INT NOT NULL,
    created_by_admin INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (rule_id) REFERENCES ecopoint_rules(id)
        ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(60) NOT NULL,
    last_name VARCHAR(60) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE,
    description VARCHAR(160) NULL
);

CREATE TABLE admin_roles (
    admin_id INT UNSIGNED NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (admin_id, role_id),
    FOREIGN KEY (admin_id) REFERENCES admin_users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(80) NOT NULL UNIQUE,
    label VARCHAR(120) NOT NULL
);

CREATE TABLE role_permissions (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Analytical Views
CREATE OR REPLACE VIEW vw_sales_by_category AS
SELECT
    c.name AS category_name,
    SUM(oi.quantity * oi.unit_price) AS revenue,
    SUM(oi.eco_points) AS eco_points_awarded,
    COUNT(DISTINCT o.id) AS order_count
FROM order_items oi
INNER JOIN orders o ON oi.order_id = o.id
INNER JOIN products p ON oi.product_id = p.id
INNER JOIN categories c ON p.category_id = c.id
GROUP BY c.id, c.name;

CREATE OR REPLACE VIEW vw_community_engagement AS
SELECT
    cp.id AS post_id,
    CONCAT(c.first_name, ' ', c.last_name) AS author,
    cp.status,
    cp.created_at,
    COUNT(cc.id) AS comment_count
FROM community_posts cp
LEFT JOIN customers c ON cp.customer_id = c.id
LEFT JOIN community_comments cc ON cp.id = cc.post_id
GROUP BY cp.id, cp.created_at, cp.status;

SET FOREIGN_KEY_CHECKS = 1;

