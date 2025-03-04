-- #!sqlite

-- #{ table
    -- #{ world_borders
        CREATE TABLE IF NOT EXISTS world_borders (
            world TEXT PRIMARY KEY,
            min_x INTEGER NOT NULL,
            max_x INTEGER NOT NULL,
            min_z INTEGER NOT NULL,
            max_z INTEGER NOT NULL
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
        ON CONFLICT(world) DO UPDATE SET min_x = excluded.min_x, max_x = excluded.max_x, min_z = excluded.min_z, max_z = excluded.max_z;
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