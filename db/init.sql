-- Crear Tablas
CREATE TABLE pokemons (
    pokemon_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    type_1 VARCHAR(50) NOT NULL,
    type_2 VARCHAR(50) NULL
);


CREATE TABLE estadisticas (
    stat_id INT AUTO_INCREMENT PRIMARY KEY,
    pokemon_id INT,
    attack INT NOT NULL,
    defense INT NOT NULL,
    speed INT NOT NULL,
    FOREIGN KEY (pokemon_id) REFERENCES pokemons(pokemon_id) ON DELETE CASCADE
);


CREATE TABLE movimientos (
    move_id INT AUTO_INCREMENT PRIMARY KEY,
    pokemon_id INT,
    move_name VARCHAR(255) NOT NULL,
    FOREIGN KEY (pokemon_id) REFERENCES pokemons(pokemon_id) ON DELETE CASCADE
);


CREATE TABLE habilidades (
    ability_id INT AUTO_INCREMENT PRIMARY KEY,
    pokemon_id INT,
    ability_name VARCHAR(255) NOT NULL,
    FOREIGN KEY (pokemon_id) REFERENCES pokemons(pokemon_id) ON DELETE CASCADE
);


CREATE TABLE pokemon_tipos (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    type_name VARCHAR(50) NOT NULL UNIQUE 
)