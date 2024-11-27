<?php
header("Content-Type: application/json");

// Hablititando CORS
header("Access-Control-Allow-Origin: http://localhost:3000"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Para efectos del desarrollo de la prueba técnica no lo puse en un .env
$host = 'db';
$user = 'admin';
$password = 'admin';
$db = 'poke_database';

// Conexión a la base de datos MySQL
$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

require_once "pokeApi.php";
require_once "pokeHelpers.php";
require_once "pokeDb.php"; // Incluir la clase pokeDb

// Inicializar las clases PokeApi, PokeHelpers y PokeDb
$pokeApi = new PokeApi();
$pokeHelpers = new PokeHelpers();
$pokeDb = new PokeDb($conn);

// Obtener el parámetro `path` para determinar el endpoint
$path = $_GET['path'] ?? null;

if (!$path) {
    http_response_code(400);
    echo json_encode(["error" => "No path provided"]);
    exit;
}





// Rutas de la API
switch ($path) {
    case "pokemon":
        $nameOrId = $_GET['id'] ?? null;

        if (!$nameOrId) {
            http_response_code(400);
            echo json_encode(["error" => "No Pokemon ID or name provided"]);
            exit;
        }

        // Obteniendo los datos de un pokémon
        $pokemon = $pokeDb->getPokemonByIdOrName($nameOrId);
        $pokemon_id = $pokemon['pokemon_id'];
        $pokemonStats = $pokeDb->getPokemonStats($pokemon_id);
        $pokemonAbilities = $pokeDb->getPokemonAbilities($pokemon_id);
        $pokemonMoves = $pokeDb->getPokemonMoves($pokemon_id);

        // construir data a retornar
        $data = [
            'pokemon' => $pokemon,
            'stats' => $pokemonStats,
            'abilities' => $pokemonAbilities,
            'moves' => $pokemonMoves
        ];

        echo json_encode($data);
        exit;



    case "pokemon-list":
        $limit = $_GET['limit'] ?? 10;
        $offset = $_GET['offset'] ?? 0;
    
        $pokemons = $pokeDb->getPokemons($limit, $offset);
    
        if (empty($pokemons)) {
            $pokemonsFromApi = $pokeApi->fetchPokemonList($limit, $offset);
    
            if (isset($pokemonsFromApi['error'])) {
                http_response_code(500);
                echo json_encode($pokemonsFromApi);
                exit;
            }
    
            // Preparar los Pokémon que faltan para el almacenamiento por lotes
            $pokemonFormatted = [];
            foreach ($pokemonsFromApi['results'] as $pokemon) {
                $result = $pokeApi->fetchPokemon($pokemon['name']);

                // Obtener los detalles del Pokémon
                $pokemonDetails = [
                    'name' => $result['name'],
                    'image' => $result['sprites']['front_default'], 
                    'type_1' => $result['types'][0]['type']['name'], 
                    'type_2' => isset($result['types'][1]) ? $result['types'][1]['type']['name'] : null, 
                    'stats' => [
                        'attack' => $result['stats'][1]['base_stat'],
                        'defense' => $result['stats'][2]['base_stat'], 
                        'speed' => $result['stats'][5]['base_stat']  
                    ],
                    'moves' => array_map(function($move) {
                        return $move['move']['name']; 
                    }, $result['moves']),
                    'abilities' => array_map(function($ability) {
                        return $ability['ability']['name'];  
                    }, $result['abilities'])
                ];

                // Añadir a la lista de datos formateados
                $pokemonFormatted[] = $pokemonDetails;
            }
    
            $pokeDb->insertAllPokemonData($pokemonFormatted);
            $pokemons = $pokeDb->getPokemons($limit, $offset);
        }
    
        echo json_encode($pokemons);
        exit;
        
        
        
    case "pokemon-types":
        $types = $pokeDb->getPokemonTypes();
        if (empty($types)){
            $typesFromApi = $pokeApi->fetchPokemonType();
            if (isset($typesFromApi['error'])) {
                http_response_code(500);
                echo json_encode($typesFromApi);
                exit;
            }
            $types = array_map(function($type) {
                return $type['name'];
            }, $typesFromApi['results']);
            $pokeDb->insertAllPokemonTypes($types);
            $types = $pokeDb->getPokemonTypes();
        }

        echo json_encode($types);
        exit;
        

    case "pokemon-sort":
        // Parámetros desde la URL con valores predeterminados
        $limit = $_GET['limit'] ?? 10; 
        $offset = $_GET['offset'] ?? 0; 
        $type = $_GET['type'] ?? null; 
        $stat = $_GET['sort'] ?? null; 
        $order = strtolower($_GET['order'] ?? 'asc'); 
    
        $pokemonDetails = [];
        $pokemonFormatted = [];
        $pokemons = [];
    
        // Decidir qué método usar según los parámetros proporcionados
        if ($type && $stat && $order) {
            $pokemons = $pokeDb->getFilteredPokemonsByTypeStatAndOrder($type, $stat, $order, $limit, $offset);
        } elseif ($type) {
            $pokemons = $pokeDb->getFilteredPokemonsByType($type, $limit, $offset);
        } elseif ($stat && $order) {
            $pokemons = $pokeDb->getFilteredPokemonsByStatAndOrder($stat, $order, $limit, $offset);
        } else {
            $pokemons = $pokeDb->getFilteredPokemons($limit, $offset);
        }
    
        // Si no se encontraron resultados, intenta obtener los datos desde la API
        if (empty($pokemons)) {
            if ($type) {
                $result = $pokeApi->fetchPokemonByType($type);
                if (isset($result['error'])) {
                    http_response_code(500);
                    echo json_encode($result);
                    exit;
                }
    
                // Procesar la lista de Pokémon según el tipo
                $pokemonList = array_map(fn($pokemonData) => ['name' => $pokemonData['pokemon']['name']], $result['pokemon']);
                $pokemonDetails = $pokeHelpers->getPokemonDetails($pokemonList, $pokeApi);
    
                // Ordenar según el atributo y el orden solicitados
                if ($stat && $order) {
                    usort($pokemonDetails, fn($a, $b) => 
                        ($order === 'asc') 
                            ? ($a['stats'][$stat] ?? 0) <=> ($b['stats'][$stat] ?? 0)
                            : ($b['stats'][$stat] ?? 0) <=> ($a['stats'][$stat] ?? 0)
                    );
                }
    
                // Paginación de los Pokémon ordenados
                $paginatedPokemons = array_slice($pokemonDetails, $offset, $limit);
    
                foreach ($paginatedPokemons as $pokemon) {
                    $result = $pokeApi->fetchPokemon($pokemon['name']);
                    if (!$result || !is_array($result)) {
                        continue; // Si no hay resultado válido, pasar al siguiente
                    }
    
                    // Validar las claves esperadas y asignar valores predeterminados
                    $pokemonDetails = [
                        'name' => $result['name'],
                        'image' => $result['sprites']['front_default'], 
                        'type_1' => $result['types'][0]['type']['name'], 
                        'type_2' => isset($result['types'][1]) ? $result['types'][1]['type']['name'] : null, 
                        'stats' => [
                            'attack' => $result['stats'][1]['base_stat'],
                            'defense' => $result['stats'][2]['base_stat'], 
                            'speed' => $result['stats'][5]['base_stat']  
                        ],
                        'moves' => array_map(function($move) {
                            return $move['move']['name']; 
                        }, $result['moves']),
                        'abilities' => array_map(function($ability) {
                            return $ability['ability']['name'];  
                        }, $result['abilities'])
                    ];
    
                    // Añadir a la lista de datos formateados
                    $pokemonFormatted[] = $pokemonDetails;
                }
    
                // Insertar datos en la base de datos
                $pokeDb->insertAllPokemonData($pokemonFormatted);
    
                if ($type && $stat && $order) {
                    $pokemons = $pokeDb->getFilteredPokemonsByTypeStatAndOrder($type, $stat, $order, $limit, $offset);
                } elseif ($type) {
                    $pokemons = $pokeDb->getFilteredPokemonsByType($type, $limit, $offset);
                } elseif ($stat && $order) {
                    $pokemons = $pokeDb->getFilteredPokemonsByStatAndOrder($stat, $order, $limit, $offset);
                } else {
                    $pokemons = $pokeDb->getFilteredPokemons($limit, $offset);
                }

            } else {
                // Obtener datos genéricos desde la API
                $pokemonsFromApi = $pokeApi->fetchPokemonList($limit, $offset);
                if (isset($pokemonsFromApi['error'])) {
                    http_response_code(500);
                    echo json_encode($pokemonsFromApi);
                    exit;
                }
    
                foreach ($pokemonsFromApi['results'] as $pokemon) {
                    $result = $pokeApi->fetchPokemon($pokemon['name']);
                    if (!$result || !is_array($result)) {
                        continue; // Pasar si no hay resultado válido
                    }
    
                    // Validar las claves esperadas y asignar valores predeterminados
                    $pokemonDetails = [
                        'name' => $result['name'],
                        'image' => $result['sprites']['front_default'], 
                        'type_1' => $result['types'][0]['type']['name'], 
                        'type_2' => isset($result['types'][1]) ? $result['types'][1]['type']['name'] : null, 
                        'stats' => [
                            'attack' => $result['stats'][1]['base_stat'],
                            'defense' => $result['stats'][2]['base_stat'], 
                            'speed' => $result['stats'][5]['base_stat']  
                        ],
                        'moves' => array_map(function($move) {
                            return $move['move']['name']; 
                        }, $result['moves']),
                        'abilities' => array_map(function($ability) {
                            return $ability['ability']['name'];  
                        }, $result['abilities'])
                    ];
    
                    // Añadir a la lista de datos formateados
                    $pokemonFormatted[] = $pokemonDetails;
                }
    
                // Insertar los datos en la base de datos
                $pokeDb->insertAllPokemonData($pokemonFormatted);
    
                if ($type && $stat && $order) {
                    $pokemons = $pokeDb->getFilteredPokemonsByTypeStatAndOrder($type, $stat, $order, $limit, $offset);
                } elseif ($type) {
                    $pokemons = $pokeDb->getFilteredPokemonsByType($type, $limit, $offset);
                } elseif ($stat && $order) {
                    $pokemons = $pokeDb->getFilteredPokemonsByStatAndOrder($stat, $order, $limit, $offset);
                } else {
                    $pokemons = $pokeDb->getFilteredPokemons($limit, $offset);
                }
                
            }
        }
    
        echo json_encode($pokemons);
        exit;
    
        


}

// Cerrar conexión a la base de datos
$conn->close();
?>
