import './App.css';
import React, { useEffect, useState } from 'react';
import PokemonList from './components/PokemonList';


function App() {


return (
<div className="bg-gradient-to-r from-purple-500 via-blue-600 to-indigo-700 text-white min-h-screen flex items-center justify-center">

  <div className="text-center p-5 shadow-lg bg-opacity-75 bg-[#282c34] min-h-screen w-full md:mx-[15%] mx-[5%]">
    <div className='md:flex w-full items-center justify-center'>
      <div className='flex justify-center'>
        <img src="/poke-img/pokeball.png" alt="pokeball" className="w-16 h-16 md:mr-5 " />
      </div>
      <h1 className=" font-extrabold tracking-wide mb-2 text-2xl">Pokémon Web APP</h1>
    </div>

    <div className='mt-2'>
      <PokemonList />
    </div>


  </div>



</div>

  )
}

export default App;
