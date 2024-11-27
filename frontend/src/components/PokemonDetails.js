
import React, { useEffect, useState } from 'react';

const PokemonDetailsModal = ({ pokemon, onClose }) => {
  const [pokemonDetails, setPokemonDetails] = useState(null);

  useEffect(() => {
    const fetchPokemonDetails = async () => {
      // Llamada a la API para obtener más detalles sobre el Pokémon
      const response = await fetch(`http://localhost:8000/index.php?path=pokemon&id=${pokemon.name}`);
      const data = await response.json();
      setPokemonDetails(data);
    };

    fetchPokemonDetails();
  }, [pokemon]);

  if (!pokemonDetails) {
    return (
        <div className={` flex justify-center absolute top-0 left-0 right-0 h-[100%]`}>
            <div className="text-white rounded-lg bg-black bg-opacity-80 text-xl p-5 rounded-lg h-32">
                <div className='flex'>
                    <p className='mt-6 ml-6'>
                        Cargando detalles...
                    </p>
                    <img src="/poke-img/pikachu.png" alt="pikachu" className="w-20 h-20 ml-5" />
                </div>
            </div>
        </div>
    );
  }

  return (
    <div className="absolute top-0 left-0 right-0 md:h-[100%] flex justify-center">
        <div className="bg-gray-900 h-auto text-white p-6 rounded-lg max-w-lg w-full relative overflow-y-auto">
            
            {/* Botón de Cierre */}
            <button
            className="absolute top-0 left-0 p-2 text-white bg-red-500 rounded-full mt-4 ml-4 md:w-10 w-6"
            onClick={onClose}
            >
            X
            </button>

            {/* Contenedor con contenido desplazable */}
            <div className=" max-h-auto pb-2">
                <h1 className="text-3xl font-bold mb-4 text-center">{pokemonDetails.pokemon.name}</h1>
                <img
                    src={pokemonDetails.pokemon.image}
                    alt={pokemonDetails.pokemon.name}
                    className="w-32 h-32 mb-4 mx-auto"
                />
                <h2 className="text-xl mb-2">Tipo:</h2>
                <ul className="mb-4">
                    <li>{pokemonDetails.pokemon.type_1}</li>
                </ul>

                <h2 className="text-xl mb-2">Habilidades:</h2>
                <ul className="mb-4">
                    {pokemonDetails.abilities.map((ability, index) => (
                    <li key={index} className="text-sm">{ability.ability_name}</li>
                    ))}
                </ul>

                <h2 className="text-xl mb-2">Estadísticas:</h2>
                <ul className="mb-4">
                    <li>Attack: {pokemonDetails.stats.attack}</li>
                    <li>Defense: {pokemonDetails.stats.defense}</li>
                    <li>Speed: {pokemonDetails.stats.speed}</li>
                </ul>

                <h2 className="text-xl mb-2">Movimientos:</h2>
                <ul className="text-sm">
                    {pokemonDetails.moves.slice(0, 10).map((move, index) => (
                    <li key={index}>{move.move_name}</li>
                    ))}
                </ul>
            </div>
        </div>
    </div>

  );
};

export default PokemonDetailsModal;

