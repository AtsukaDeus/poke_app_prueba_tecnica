<?php

class PokeHelpers {

    public function getPokemonDetails($pokemonList, $pokeApi) {
        $detailsList = [];
        foreach ($pokemonList as $pokemon) {
            $details = $pokeApi->fetchPokemon($pokemon['name']);
            if (!isset($details['error'])) {
                $detailsList[] = [
                    'name' => $details['name'],
                    'image' => $details['sprites']['front_default'] ?? '',
                    'stats' => [
                        'attack' => $details['stats'][1]['base_stat'] ?? 0,
                        'defense' => $details['stats'][2]['base_stat'] ?? 0,
                        'speed' => $details['stats'][5]['base_stat'] ?? 0,
                    ],
                ];
            }
        }
        return $detailsList;
    }
    

}
