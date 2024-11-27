<?php

class PokeApi {
    private $baseUrl = "https://pokeapi.co/api/v2/";


    public function fetchAllPokemons(){
        $url = $this->baseUrl . "pokemon?limit=100000&offset=0";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                "error" => "Error connecting to PokeAPI: " . curl_error($ch)
            ];
        }

        curl_close($ch);

        return json_decode($response, true);
    }



    public function fetchPokemonType() {
        $url = $this->baseUrl . "type";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                "error" => "Error connecting to PokeAPI: " . curl_error($ch)
            ];
        }

        curl_close($ch);

        return json_decode($response, true);
    }


    public function fetchPokemon($nameOrId) {
        $url = $this->baseUrl . "pokemon/" . $nameOrId;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                "error" => "Error connecting to PokeAPI: " . curl_error($ch)
            ];
        }

        curl_close($ch);

        return json_decode($response, true);
    }



    public function fetchPokemonByType($type) {
        $url = $this->baseUrl . "type/$type";
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
    
        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                "error" => "Error connecting to PokeAPI: " . curl_error($ch)
            ];
        }
    
        curl_close($ch);
    
        return json_decode($response, true);
    }
    


        
    public function fetchPokemonList($limit = 10, $offset = 0) {
        $url = $this->baseUrl . "pokemon?limit=$limit&offset=$offset";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                "error" => "Error connecting to PokeAPI: " . curl_error($ch)
            ];
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (!isset($data['results']) || !is_array($data['results'])) {
            return [
                "error" => "Unexpected response format from PokeAPI."
            ];
        }

        return $data; 
    }

    
    
    


}

