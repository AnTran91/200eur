INSERT INTO `order_delivery_time` (id, global, selected_by_default, time, unit, order_delivery_code, app_type)
VALUES (1, 1, 1, 48, 'time.hour', NULL, 'application.type.emmobilier'),
       (2, 1, 0, 24, 'time.hour', NULL, 'application.type.emmobilier'),
       (3, 0, 0, 1, 'time.week', NULL, 'application.type.emmobilier'),
       (4, 1, 0, 24, 'time.hour', '24_hours', 'application.type.immosquare'),
       (5, 1, 0, 48, 'time.hour', '48_hours', 'application.type.immosquare');