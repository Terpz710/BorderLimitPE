-- #!mysql

-- #{ table
    -- #{ world_borders
        CREATE TABLE IF NOT EXISTS world_borders (
            world VARCHAR(255) PRIMARY KEY,
            min_x INT NOT NULL,
            max_x INT NOT NULL,
            min_z INT NOT NULL,
            max_z INT NOT NULL
        );
    -- #}
-- #}

-- #{ world_borders
    -- #{ create
        -- # :world string
        -- # :min_x int
        -- # :max_x int
        -- # :min_z int
        -- # :max_z int
        INSERT INTO world_borders (world, min_x, max_x, min_z, max_z)
        VALUES (:world, :min_x, :max_x, :min_z, :max_z)
        ON DUPLICATE KEY UPDATE min_x = VALUES(min_x), max_x = VALUES(max_x), min_z = VALUES(min_z), max_z = VALUES(max_z);
    -- #}

    -- #{ get
        -- # :world string
        SELECT min_x, max_x, min_z, max_z FROM world_borders WHERE world = :world LIMIT 1;
    -- #}

    -- #{ remove
        -- # :world string
        DELETE FROM world_borders WHERE world = :world;
    -- #}

    -- #{ get_all
        SELECT * FROM world_borders;
    -- #}
-- #}