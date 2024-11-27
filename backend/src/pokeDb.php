<?php

class PokeDb {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Obtener los tipos de Pokémon
    public function getPokemonTypes() {
        $query = "SELECT * FROM pokemon_tipos";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function getFilteredPokemonsByTypeStatAndOrder($type = null, $stat = null, $order = 'asc', $limit = 8, $offset = 0) {
        // Validación del valor de $order
        if (!in_array(strtolower($order), ['asc', 'desc'])) {
            $order = 'asc';  // Valor predeterminado si no es válido
        }

        // Validación del valor de $stat
        $validStats = ['attack', 'defense', 'speed'];
        if (!in_array(strtolower($stat), $validStats)) {
            $stat = 'attack';  // Valor predeterminado si no es válido
        }
        
        $query = "
            SELECT 
                    p.pokemon_id, 
                    p.name, 
                    p.image, 
                    p.type_1, 
                    p.type_2,
                    MAX(s.attack) AS attack,
                    MAX(s.defense) AS defense,
                    MAX(s.speed) AS speed
                FROM pokemons p
                LEFT JOIN estadisticas s ON p.pokemon_id = s.pokemon_id
                WHERE p.type_1 = ?
                GROUP BY p.pokemon_id
                ORDER BY MAX(s.$stat) $order
                LIMIT ? OFFSET ?        
              ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sii', $type, $limit, $offset);
        $stmt->execute();

        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $results;
    }


    public function getFilteredPokemonsByType($type = null, $limit = 8, $offset = 0) {
        $query = "
            SELECT 
                    p.pokemon_id, 
                    p.name, 
                    p.image, 
                    p.type_1, 
                    p.type_2,
                    MAX(s.attack) AS attack,
                    MAX(s.defense) AS defense,
                    MAX(s.speed) AS speed
                FROM pokemons p
                LEFT JOIN estadisticas s ON p.pokemon_id = s.pokemon_id
                WHERE p.type_1 = ?
                GROUP BY p.pokemon_id
                LIMIT ? OFFSET ?        
              ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sii', $type, $limit, $offset);
        $stmt->execute();

        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $results;
    }



    public function getFilteredPokemons($limit = 10, $offset = 0) {
        $query = "
            SELECT 
                    p.pokemon_id, 
                    p.name, 
                    p.image, 
                    p.type_1, 
                    p.type_2,
                    MAX(s.attack) AS attack,
                    MAX(s.defense) AS defense,
                    MAX(s.speed) AS speed
                FROM pokemons p
                LEFT JOIN estadisticas s ON p.pokemon_id = s.pokemon_id
                GROUP BY p.pokemon_id
                LIMIT ? OFFSET ?        
              ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();

        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $results;
    }



    public function getFilteredPokemonsByStatAndOrder($stat = null, $order = 'asc', $limit = 8, $offset = 0) {
        // Validación del valor de $order
        if (!in_array(strtolower($order), ['asc', 'desc'])) {
            $order = 'asc';  // Valor predeterminado si no es válido
        }

        // Validación del valor de $stat
        $validStats = ['attack', 'defense', 'speed'];
        if (!in_array(strtolower($stat), $validStats)) {
            $stat = 'attack';  // Valor predeterminado si no es válido
        }
        
        $query = "
            SELECT 
                    p.pokemon_id, 
                    p.name, 
                    p.image, 
                    p.type_1, 
                    p.type_2,
                    MAX(s.attack) AS attack,
                    MAX(s.defense) AS defense,
                    MAX(s.speed) AS speed
                FROM pokemons p
                LEFT JOIN estadisticas s ON p.pokemon_id = s.pokemon_id
                GROUP BY p.pokemon_id
                ORDER BY MAX(s.$stat) $order
                LIMIT ? OFFSET ?        
              ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();

        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $results;
    }



    // Obtener todos los Pokémon con límite y desplazamiento
    public function getPokemons($limit = 10, $offset = 0) {
        $query = "SELECT * FROM pokemons LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);  
        $stmt->execute();
        $result = $stmt->get_result(); 
        return $result->fetch_all(MYSQLI_ASSOC); 
    }



    // Obtener detalles de un Pokémon por ID o nombre
    public function getPokemonByIdOrName($nameOrId) {
        $query = "SELECT * FROM pokemons WHERE pokemon_id = ? OR name = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('is', $nameOrId, $nameOrId); 
        $stmt->execute();
        $result = $stmt->get_result(); 
        return $result->fetch_assoc();
    }



    // Obtener las estadísticas de un Pokémon
    public function getPokemonStats($pokemonId) {
        $query = "SELECT * FROM estadisticas WHERE pokemon_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $pokemonId); 
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }



    // Obtener los movimientos de un Pokémon
    public function getPokemonMoves($pokemonId) {
        $query = "SELECT * FROM movimientos WHERE pokemon_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $pokemonId);  // 'i' significa entero
        $stmt->execute();
        $result = $stmt->get_result();  // Obtener el conjunto de resultados
        return $result->fetch_all(MYSQLI_ASSOC);  // Devuelve todos los resultados como un array asociativo
    }



    // Obtener las habilidades de un Pokémon
    public function getPokemonAbilities($pokemonId) {
        $query = "SELECT * FROM habilidades WHERE pokemon_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $pokemonId);  // 'i' significa entero
        $stmt->execute();
        $result = $stmt->get_result();  // Obtener el conjunto de resultados
        return $result->fetch_all(MYSQLI_ASSOC);  // Devuelve todos los resultados como un array asociativo
    }



    // Obtener Pokémon por tipo
    public function getPokemonByType($type) {
        $query = "SELECT p.* FROM pokemons p
                  JOIN pokemons_types pt ON p.pokemon_id = pt.pokemon_id
                  WHERE pt.type_1 = ? OR pt.type_2 = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $type, $type);  
        $stmt->execute();
        $result = $stmt->get_result(); 
        return $result->fetch_all(MYSQLI_ASSOC);  
    }



    // Métodos para insertar los datos en la base de datos

    // ------> Insertar todos los tipos de pokémon
    public function insertAllPokemonTypes($types) {
        $values = [];
        $placeholders = [];

        foreach ($types as $type) {
            $values[] = $type;
            $placeholders[] = "(?)";
        }

        $query = "INSERT INTO pokemon_tipos (type_name) VALUES " . implode(", ", $placeholders);

        $stmt = $this->db->prepare($query);
        $stmt->bind_param(str_repeat('s', count($types)), ...$values);
        $stmt->execute();
    }



    // ------> Insertar todos los Pokémon
    private function insertAllPokemons($pokemons) {
        $values = [];
        $placeholders = [];
    
        foreach ($pokemons as $pokemon) {
            $values[] = $pokemon['name'];
            $values[] = $pokemon['image'];
            $values[] = $pokemon['type_1'];
            $values[] = $pokemon['type_2'];
            $placeholders[] = "(?, ?, ?, ?)";
        }
    
        $query = "INSERT INTO pokemons (name, image, type_1, type_2) VALUES " . implode(", ", $placeholders);
    
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(str_repeat('ssss', count($pokemons)), ...$values);
        $stmt->execute();
    }



    // ------> Insertar las estadísticas de los Pokémon
    private function insertAllStats($statsBatch) {
    $values = [];
    $placeholders = [];

    foreach ($statsBatch as $stats) {
        $values[] = $stats['pokemon_id'];
        $values[] = $stats['attack'];
        $values[] = $stats['defense'];
        $values[] = $stats['speed'];
        $placeholders[] = "(?, ?, ?, ?)";
    }

    $query = "INSERT INTO estadisticas (pokemon_id, attack, defense, speed) VALUES " . implode(", ", $placeholders);

    $stmt = $this->db->prepare($query);
    $stmt->bind_param(str_repeat('iiii', count($statsBatch)), ...$values);
    $stmt->execute();
    }
    
    

    // ------> Insertar los movimientos de los Pokémon
    private function insertAllMoves($movesBatch) {
        $values = [];
        $placeholders = [];

        foreach ($movesBatch as $move) {
            $values[] = $move['pokemon_id'];
            $values[] = $move['move_name'];
            $placeholders[] = "(?, ?)";
        }

        $query = "INSERT INTO movimientos (pokemon_id, move_name) VALUES " . implode(", ", $placeholders);

        $stmt = $this->db->prepare($query);
        $stmt->bind_param(str_repeat('is', count($movesBatch)), ...$values);
        $stmt->execute();
    }



    // ------> Insertar las habilidades de los Pokémon
    private function insertAllAbilities($abilitiesBatch) {
        $values = [];
        $placeholders = [];
    
        foreach ($abilitiesBatch as $ability) {
            $values[] = $ability['pokemon_id'];
            $values[] = $ability['ability_name'];
            $placeholders[] = "(?, ?)";
        }
    
        $query = "INSERT INTO habilidades (pokemon_id, ability_name) VALUES " . implode(", ", $placeholders);
    
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(str_repeat('is', count($abilitiesBatch)), ...$values);
        $stmt->execute();
    }



    // Insertar todos los datos de los Pokémon
    public function insertAllPokemonData($pokemonDetailsList) {
        $statsBatch = [];
        $movesBatch = [];
        $abilitiesBatch = [];
    
        foreach ($pokemonDetailsList as $pokemonDetails) {
            $this->insertAllPokemons([$pokemonDetails]);  
            $pokemonId = $this->db->insert_id;  
    
            $statsBatch[] = [
                'pokemon_id' => $pokemonId,
                'attack' => $pokemonDetails['stats']['attack'],
                'defense' => $pokemonDetails['stats']['defense'],
                'speed' => $pokemonDetails['stats']['speed']
            ];
    
            foreach ($pokemonDetails['moves'] as $move) {
                $movesBatch[] = ['pokemon_id' => $pokemonId, 'move_name' => $move];
            }
    
            foreach ($pokemonDetails['abilities'] as $ability) {
                $abilitiesBatch[] = ['pokemon_id' => $pokemonId, 'ability_name' => $ability];
            }
        }
    
        $this->insertAllStats($statsBatch);
        $this->insertAllMoves($movesBatch);
        $this->insertAllAbilities($abilitiesBatch);
    }
    
    
    
    
    
    


}

?>
