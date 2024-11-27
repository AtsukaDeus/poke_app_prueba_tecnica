import React, { useState, useEffect } from 'react';
import { FaArrowAltCircleLeft, FaArrowAltCircleRight } from 'react-icons/fa';
import PokemonDetailsModal from './PokemonDetails';

const PokemonList = () => {
  const [selectedPokemon, setSelectedPokemon] = useState(null);
  const [pokemons, setPokemons] = useState([]);
  const [offset, setOffset] = useState(0);
  const [limit, setLimit] = useState(8);
  const [isLoading, setIsLoading] = useState(false);
  const [selectedType, setSelectedType] = useState('');
  const [selectedStat, setSelectedStat] = useState('');
  const [orderStat, setOrderStat] = useState('');
  const [types, setTypes] = useState([]);
  const [isPageRecentlyloaded, setIsPageRecentlyLoaded] = useState(true);
  const statistics = ['attack', 'defense', 'speed'];

  const fetchPokemonTypes = async () => {
    try {
      const response = await fetch('http://localhost:8000/index.php?path=pokemon-types');
      const data = await response.json();
      setTypes(data);
    } catch (error) {
      console.error('Error fetching Pokémon types:', error);
    }
  };

  const fetchPokemons = async () => {
    setIsLoading(true);
    try {
      let endpointToFetch = 'pokemon-sort';
      let url = `http://localhost:8000/index.php?path=${endpointToFetch}&limit=${limit}&offset=${offset}`;
      if (selectedType) url += `&type=${selectedType.toLowerCase()}`;
      if (selectedStat) url += `&sort=${selectedStat}&order=${orderStat}`;
      
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data = await response.json();
      setPokemons(data);

    } catch (error) {
      console.error('Error fetching Pokémon:', error);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchPokemonTypes();
  }, []);



  const applyFilters = () => {
    if (selectedStat && !orderStat) {
      alert('Por favor, selecciona un orden (ascendente o descendente) para la estadística elegida.');
      return;
    }
    if (!selectedStat && orderStat) {
      alert('Por favor, selecciona una estadística para aplicar el orden.');
      return;
    }
  
    setOffset(0); // Resetea la paginación al aplicar filtros
    fetchPokemons();
  };

  const handleNextPage = () => {
    setOffset((prevOffset) => prevOffset + limit);
  };

  const handlePreviousPage = () => {
    if (offset > 0) {
      setOffset((prevOffset) => Math.max(prevOffset - limit, 0));
    }
  };

  useEffect(() => {
    fetchPokemons();
  }, [offset]);

  return (
    <div className="pokemon-list relative">
      {isLoading && (
        <div className="flex justify-center absolute top-0 left-0 right-0 h-[100%]">
          <div className="text-white rounded-lg bg-black bg-opacity-80 text-xl p-5 h-32">
            <div className="flex">
              <p className="mt-6 ml-6">Cargando pokémons...</p>
              <img src="/poke-img/pikachu.png" alt="pikachu" className="w-20 h-20 ml-5" />
            </div>
          </div>
        </div>
      )}

      <div className="grid md:grid-cols-4 grid-cols-1 gap-4 mb-4">
          <select
            value={selectedType}
            onChange={(e) => setSelectedType(e.target.value)}
            className="p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 mr-4"
          >
            <option value="">Todos los tipos</option>
            {types.map((type) => (
              <option key={type.type_name} value={type.type_name}>
                {type.type_name.charAt(0).toUpperCase() + type.type_name.slice(1)}
              </option>
            ))}
          </select>
          <select
            value={selectedStat}
            onChange={(e) => setSelectedStat(e.target.value)}
            className="p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 mr-4"
          >
            <option value="">Ordenar por estadística</option>
            {statistics.map((stat) => (
              <option key={stat} value={stat}>
                {stat.charAt(0).toUpperCase() + stat.slice(1)}
              </option>
            ))}
          </select>
          <select
            value={orderStat}
            onChange={(e) => setOrderStat(e.target.value)}
            className="p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500"
          >
            <option value="">Ordenar</option>
            <option value="asc">Ascendente</option>
            <option value="desc">Descendente</option>
          </select>
          <button
            onClick={applyFilters}
            className="ml-4 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
          >
            Aplicar
          </button>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        {pokemons.map((pokemon, index) => (
          <div
            key={index}
            className="bg-gray-800 rounded-lg shadow-lg p-5 flex flex-col items-center cursor-pointer"
            onClick={() => setSelectedPokemon(pokemon)}
          >
            <img
              src={pokemon.image}
              alt={pokemon.name}
              className="w-24 h-24 mb-4"
            />
            <h2 className="text-xl font-semibold">{pokemon.name}</h2>
            <div className="p-4 rounded-lg shadow-sm w-full max-w-xs mx-auto">
              <p className="font-semibold text-white mb-2">Tipo: 
                <span className="font-medium text-blue-500"> {pokemon.type_1}</span>
              </p>
              <p className="font-semibold text-white mb-2">Estadísticas:</p>
              <div className="flex justify-between text-sm text-gray-600">
                <div className="flex flex-col items-center">
                  <p className="font-semibold text-white">Attack</p>
                  <p>{pokemon.attack}</p>
                </div>
                <div className="flex flex-col items-center">
                  <p className="font-semibold text-white">Defense</p>
                  <p>{pokemon.defense}</p>
                </div>
                <div className="flex flex-col items-center">
                  <p className="font-semibold text-white">Speed</p>
                  <p>{pokemon.speed}</p>
                </div>
              </div>
            </div>

            
          </div>
        ))}
      </div>

      {selectedPokemon && (
        <PokemonDetailsModal pokemon={selectedPokemon} onClose={() => setSelectedPokemon(null)} />
      )}

      
      <div className='mt-20'>
        <div className="flex justify-center mt-4">
                <button
                    onClick={handlePreviousPage}
                    disabled={offset === 0}
                    className={`mr-4 px-4 py-2 rounded-full ${
                    offset === 0 ? 'bg-gray-400' : 'bg-red-500 hover:bg-red-600'
                    } text-white`}
                >
                    <FaArrowAltCircleLeft className='w-6 h-6'/>
                </button>
                <button
                    onClick={handleNextPage}
                    className="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-full"
                >
                    <FaArrowAltCircleRight className='w-6 h-6'/>
                </button>
        </div>
      </div>

 

    </div>
  );
};

export default PokemonList;
