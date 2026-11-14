-- InfinityFree deployment for migration:
-- 2026_11_14_000000_add_birthday_extras_and_staff_to_pets_appointments
-- Back up the database first. Run this once in the deployed database via phpMyAdmin.

ALTER TABLE `pets`
    ADD COLUMN `birthday` DATE NULL;

ALTER TABLE `appointments`
    ADD COLUMN `service_extras` JSON NULL,
    ADD COLUMN `assigned_staff_id` BIGINT UNSIGNED NULL;

ALTER TABLE `appointments`
    ADD CONSTRAINT `appointments_assigned_staff_id_foreign`
        FOREIGN KEY (`assigned_staff_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Record the migration so Laravel will not try to add these columns again later.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT
    '2026_11_14_000000_add_birthday_extras_and_staff_to_pets_appointments',
    COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`;
