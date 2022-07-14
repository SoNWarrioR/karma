CREATE TABLE `email_notify` (
                                `email` varchar(255) NOT NULL,
                                `created_date` datetime NOT NULL,
                                PRIMARY KEY (`email`,`created_date`),
                                UNIQUE KEY `email_notify_email_created_date_uindex` (`email`,`created_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `emails` (
                          `email` varchar(255) NOT NULL,
                          `checked` tinyint NOT NULL DEFAULT '0',
                          `valid` tinyint NOT NULL DEFAULT '0',
                          `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `users` (
                         `uuid` varchar(255) NOT NULL,
                         `username` varchar(255) NOT NULL DEFAULT '',
                         `email` varchar(255) NOT NULL DEFAULT '',
                         `validts` int(11) DEFAULT NULL,
                         `confirmed` tinyint NOT NULL DEFAULT '0',
                         `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         PRIMARY KEY (`uuid`,`created_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

