-- DragonStone Prototype Seed Data

INSERT INTO categories (name, description) VALUES
    ('Cleaning & Household Supplies', 'Low-waste cleaning staples and refillable solutions.'),
    ('Kitchen & Dining', 'Reusable, compostable, and recycled kitchen essentials.'),
    ('Lifestyle & Wellness', 'Sustainable wellness products for daily rituals.');

INSERT INTO products (category_id, sku, name, summary, description, price, subscription_eligible, sustainability_score)
VALUES
    (1, 'DS-CLN-REFPOD', 'Refillable Cleaning Pods', 'Concentrated pods with refillable glass bottle.', 'Multi-surface cleaning pods that dissolve in water to reduce single-use plastic.', 19.99, 1, 92),
    (1, 'DS-CLN-CHARBAG', 'Natural Air Purifying Bags', 'Charcoal-based air purifying bags for home spaces.', 'Reusable and compostable air purifying bags made from bamboo charcoal.', 24.00, 0, 88),
    (2, 'DS-KIT-BAMSET', 'Bamboo Kitchen Set', 'Utensil set made from FSC-certified bamboo.', 'Utensils, cutting board, and spatulas crafted from sustainable bamboo.', 34.50, 0, 90),
    (3, 'DS-LIF-ECOMAT', 'Eco Yoga Mat', 'Non-slip mat crafted from recycled rubber.', 'Durable, non-toxic yoga mat made entirely from post-consumer rubber waste.', 59.00, 1, 95);

INSERT INTO product_impact_metrics (product_id, metric_label, metric_value, baseline_comparison)
VALUES
    (1, 'Plastic Saved', '12 bottles/year', 'vs. traditional spray cleaners'),
    (1, 'Water Saved', '18L/year', 'vs. pre-filled cleaners'),
    (1, 'Carbon Footprint', '65% reduction', 'vs. baseline cleaning products'),
    (4, 'Carbon Footprint', '60% reduction', 'vs. traditional PVC mat'),
    (4, 'Recycled Content', '100% recycled rubber', 'Certified post-consumer materials');

INSERT INTO tags (label) VALUES
    ('Low Waste'),
    ('Carbon Neutral'),
    ('Vegan'),
    ('Recycled Materials'),
    ('Subscription Friendly');

INSERT INTO product_tags (product_id, tag_id) VALUES
    (1, 1),
    (1, 5),
    (1, 2),
    (2, 1),
    (3, 4),
    (4, 4),
    (4, 3),
    (4, 5);

INSERT INTO inventory_snapshots (product_id, quantity, snapshot_date, restock_eta)
VALUES
    (1, 150, CURRENT_DATE, CURRENT_DATE + INTERVAL 15 DAY),
    (2, 80, CURRENT_DATE, NULL),
    (3, 60, CURRENT_DATE, CURRENT_DATE + INTERVAL 7 DAY),
    (4, 45, CURRENT_DATE, CURRENT_DATE + INTERVAL 20 DAY);

INSERT INTO customers (first_name, last_name, email, password_hash, eco_points, city, country)
VALUES
    ('Mira', 'Lopez', 'mira@example.com', '$2y$10$dummyhash', 120, 'Cape Town', 'South Africa'),
    ('Jon', 'Snow', 'jon@example.com', '$2y$10$dummyhash', 80, 'Johannesburg', 'South Africa');

INSERT INTO orders (customer_id, order_reference, total, status, eco_points_awarded, placed_at)
VALUES
    (1, 'ORD-1001', 79.49, 'completed', 90, CURRENT_TIMESTAMP - INTERVAL 5 DAY),
    (2, 'ORD-1002', 59.00, 'paid', 60, CURRENT_TIMESTAMP - INTERVAL 2 DAY);

INSERT INTO order_items (order_id, product_id, quantity, unit_price, eco_points)
VALUES
    (1, 1, 2, 19.99, 40),
    (1, 3, 1, 34.50, 30),
    (2, 4, 1, 59.00, 60);

INSERT INTO payments (order_id, method, amount, status, processed_at)
VALUES
    (1, 'card', 79.49, 'captured', CURRENT_TIMESTAMP - INTERVAL 5 DAY),
    (2, 'card', 59.00, 'authorized', CURRENT_TIMESTAMP - INTERVAL 2 DAY);

INSERT INTO shipments (order_id, provider, tracking_number, shipped_at, delivered_at)
VALUES
    (1, 'EcoShip Logistics', 'EC123456789', CURRENT_TIMESTAMP - INTERVAL 4 DAY, CURRENT_TIMESTAMP - INTERVAL 1 DAY);

INSERT INTO subscriptions (customer_id, name, interval_unit, next_renewal, status)
VALUES
    (1, 'Home Essentials Bundle', 'monthly', CURRENT_DATE + INTERVAL 15 DAY, 'active');

INSERT INTO subscriptions_items (subscription_id, product_id, quantity, last_fulfilled)
VALUES
    (1, 1, 1, CURRENT_DATE - INTERVAL 15 DAY),
    (1, 4, 1, CURRENT_DATE - INTERVAL 15 DAY);

INSERT INTO challenges (title, description, start_date, end_date, eco_points_reward, status)
VALUES
    ('Plastic-Free Bathroom Sprint', 'Swap three bathroom products for plastic-free alternatives.', CURRENT_DATE - INTERVAL 7 DAY, CURRENT_DATE + INTERVAL 7 DAY, 80, 'active'),
    ('Community Compost Challenge', 'Share your home compost setup with the community.', CURRENT_DATE + INTERVAL 10 DAY, CURRENT_DATE + INTERVAL 24 DAY, 120, 'scheduled');

INSERT INTO community_posts (customer_id, challenge_id, title, body, status)
VALUES
    (1, 1, 'Bathroom Refresh Success', 'Swapped to compostable floss and bamboo toothbrushes.', 'approved'),
    (2, NULL, 'Refill Station Tips', 'How to organize weekly refill runs to reduce plastic waste.', 'pending');

INSERT INTO community_comments (post_id, customer_id, body)
VALUES
    (1, 2, 'This is inspiring! Any tips for storing refills?');

INSERT INTO ecopoint_rules (action_key, description, points, is_active)
VALUES
    ('order.completed', 'Completed order', 50, 1),
    ('challenge.post', 'Approved challenge submission', 80, 1),
    ('community.comment', 'Helpful community comment', 10, 1);

INSERT INTO ecopoint_transactions (customer_id, rule_id, source_type, source_reference, points)
VALUES
    (1, 1, 'order', 'ORD-1001', 90),
    (1, 2, 'challenge', 'POST-1', 80),
    (2, 1, 'order', 'ORD-1002', 60);

INSERT INTO admin_users (first_name, last_name, email, password_hash)
VALUES
    ('Aegon', 'Targaryen', 'aegon@dragonstone.eco', '$2y$12$r60BKdepGGD6rEbMZiMgG.K6LPCE/D/eXTP/KcQSAHmhsUxaoYgo6'),
    ('Visenya', 'Targaryen', 'visenya@dragonstone.eco', '$2y$12$r60BKdepGGD6rEbMZiMgG.K6LPCE/D/eXTP/KcQSAHmhsUxaoYgo6'),
    ('Rhaenys', 'Targaryen', 'rhaenys@dragonstone.eco', '$2y$12$r60BKdepGGD6rEbMZiMgG.K6LPCE/D/eXTP/KcQSAHmhsUxaoYgo6');

INSERT INTO roles (name, description) VALUES
    ('Operations Manager', 'Manages catalog, orders, subscriptions.'),
    ('Sustainability Lead', 'Oversees impact data, reporting, and EcoPoints.'),
    ('Community Manager', 'Moderates community content and challenges.');

INSERT INTO admin_roles (admin_id, role_id) VALUES
    (1, 1),
    (1, 2),
    (2, 2),
    (3, 3);

INSERT INTO permissions (code, label) VALUES
    ('catalog.manage', 'Manage product catalog'),
    ('orders.manage', 'Manage orders and shipments'),
    ('community.moderate', 'Moderate community submissions'),
    ('ecopoints.adjust', 'Adjust EcoPoints balances'),
    ('reports.view', 'View aggregated reports');

INSERT INTO role_permissions (role_id, permission_id) VALUES
    (1, 1),
    (1, 2),
    (1, 5),
    (2, 4),
    (2, 5),
    (3, 3),
    (3, 5);
