@extends('layouts.app')

@section('titulo')
    {{ $post->titulo }}
@endsection

@section('contenido')
    <div class="container mx-auto md:flex">
        <div class="md:w-1/2 px-3 md:p-0">
            <img src="{{ asset('uploads') . '/' . $post->imagen }}" alt="Imagen del post {{ $post->titulo }}">
            <div class="py-1 px-3 flex items-center gap-2">
            
                @auth

                    <livewire:like-post :post="$post" />
                 
                @endauth
                
            </div>

            <div class="py-1 px-3">
                <p class="mb-5">
                    {{ $post->descripcion }}
                </p>
                <a href=" {{ route('posts.index', $user) }} "class="font-bold">{{ $post->user->username }}</a>
                <p class="text-sm text-gray-500">
                    {{ $post->created_at->diffForHumans() }}
                </p>
                @auth
                    @if($post->user_id === auth()->user()->id)
                        <form action="{{ route('posts.destroy', $post) }}" method="POST">
                            @method('DELETE') <!-- Método spoofing --> 
                            @csrf
                            <input 
                            type="submit"
                            value="Eliminar publicación"
                            class="bg-red-500 hover:bg-red-600 p-2 rounded text-white font-bold mt-4 cursor-pointer"
                            >
                        </form>
                    @endif
                @endauth
            </div>
        </div>
        <div class="md:w-1/2 px-5 py-0">
            <div class="shadow bg-white p-5 my-10 md:m-0">
                {{-- <p class="text-xl font-bold text-center mb-4">Agrega un nuevo comentario</p> --}}
                
                @if(session('mensaje'))
                    <div class="bg-green-500 p-2 rounded-lg mb-6 text-white text-center uppercase font-bold">
                        {{ session('mensaje') }}
                    </div>
                @endif
                
                @auth
                    
                <form action="{{ route('comentarios.store', ['post' => $post, 'user' => $user]) }}" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label for="comentario" class="mb-2 block uppercase text-gray-500 font-bold">
                            Añadir comentario
                        </label>
                        <textarea
                            id="comentario"
                            name="comentario"
                            placeholder="Agregue un comentario"
                            class="border p-3 w-full rounded-lg @error('name') border-red-500 @enderror"
                            ></textarea>
                            @error('comentario')
                            <p class="bg-red-500 text-white my-2 rounded-lg text-sm p-2 text-center">
                                {{ str_replace('name', 'nombre', $message) }}
                            </p>
                            @enderror
                        </div>
                        
                        <input
                        type="submit"
                        value="Comentar"
                        class="bg-sky-600 hover:bg-sky-700 transition-colors cursor-pointer uppercase font-bold w-full p-3 text-white rounded-lg"
                        />
                    </form>
                @endauth

                <div class="bg-white shadow mb-5 mt-5 max-h-[31.8rem] overflow-y-scroll">
                    
                    @if($post->comentarios->count())
                        @foreach( $post->comentarios as $comentario )
                            <div class="p-5 border-gray-300 border-b">
                                <a href="{{ route('posts.index', $comentario->user) }}" class="font-bold hover:text-gray-700">
                                    {{ $comentario->user->username }}
                                </a>    
                                <p>{{ $comentario->comentario }}</p>
                                <p class="text-sm text-gray-500">{{ $comentario->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    @else
                        <p class="p-10 text-center">No hay comentario aún</p>
                    @endif
                </div>    
            </div>
        </div>
    </div>
@endsection