-- Update service display order to match business priority
-- Safe to re-run; only affects the listed slugs
-- For phpMyAdmin or MySQL CLI
BEGIN;
-- End
UPDATE services
SET sort_order = CASE slug
    WHEN 'van-chuyen-duong-bo' THEN 1
    WHEN 'mua-hang-trung-quoc' THEN 2
    WHEN 'van-chuyen-hang-khong' THEN 3
    WHEN 'nhap-khau-uy-thac' THEN 4
    WHEN 'van-chuyen-duong-bien' THEN 5
    ELSE sort_order
END
WHERE slug IN (
    'van-chuyen-duong-bo',
    'mua-hang-trung-quoc',
    'van-chuyen-hang-khong',
    'nhap-khau-uy-thac',
    'van-chuyen-duong-bien'
);

COMMIT;

-- Optional: verify
-- SELECT id, title, slug, sort_order FROM services ORDER BY sort_order ASC, created_at DESC;

