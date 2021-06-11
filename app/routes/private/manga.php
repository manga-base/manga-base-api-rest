<?php

use App\Lib\Respuesta;
use App\Model\Autor;
use App\Model\BaseMangas;
use App\Model\Demografia;
use App\Model\Editorial;
use App\Model\Estado;
use App\Model\Genero;
use App\Model\Manga;
use App\Model\MangaAutor;
use App\Model\MangaGenero;
use App\Model\MangaRevista;
use App\Model\Revista;

$app->group('/private-manga/', function () {

    $this->get(
        '',
        function ($req, $res, $args) {
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->admin === 0) {
                return $res->withJson(Respuesta::set(false, 'No eres administrador! ಠ_ಠ'));
            }

            try {
                $datos['estados'] = Estado::all();
                $datos['demografias'] = Demografia::all();
                $datos['autores'] = Autor::select('autor.idAutor', 'autor.nombre')->get();
                $datos['revistas'] = Revista::select('revista.idRevista', 'revista.nombre')->get();
                $datos['generos'] = Genero::select('genero.idGenero', 'genero.genero')->get();
                $datos['editoriales'] = Editorial::all();
                return $res->withJson(Respuesta::set(true, '', $datos));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );

    $this->post(
        '',
        function ($req, $res, $args) {
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->admin === 0) {
                return $res->withJson(Respuesta::set(false, 'No eres administrador! ಠ_ಠ'));
            }

            $body = $req->getParsedBody();
            $files = $req->getUploadedFiles();
            if (
                !isset($files['foto']) ||
                !isset($body["tituloPreferido"]) ||
                !isset($body["tituloJA"]) ||
                !isset($body["tituloRōmaji"]) ||
                !isset($body["argumento"]) ||
                !isset($body["añoDePublicacion"]) ||
                !isset($body["estado"]) ||
                !isset($body["demografia"])
            ) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos.', $body));
            }

            $foto = $files['foto']->file;
            if ($foto == '' || !is_uploaded_file($foto)) {
                return $res->withJson(Respuesta::set(false, 'El formato del archivo enviado no es correcto.'));
            }

            $tituloPreferido = $body["tituloPreferido"];
            $tituloJA = $body["tituloJA"];
            $tituloRōmaji = $body["tituloRōmaji"];
            $argumento = $body["argumento"];
            $añoDePublicacion = $body["añoDePublicacion"];
            $estado = $body["estado"];
            $demografia = $body["demografia"];

            try {
                $manga = new Manga();
                $manga->tituloPreferido = $tituloPreferido;
                $manga->tituloJA = $tituloJA;
                $manga->tituloRōmaji = $tituloRōmaji;
                $manga->argumento = $argumento;
                $manga->añoDePublicacion = $añoDePublicacion;
                $manga->idEstado = $estado;
                $manga->nota = 0;
                $manga->idDemografia = $demografia;
                foreach (["tituloES", "tituloEN", "capitulos", "volumenes", "añoDeFinalizacion"] as $v) {
                    if (isset($body[$v])) {
                        $manga[$v] = $body[$v];
                    }
                }
                $manga->save();

                $image_name = "manga-" . $manga->id . ".jpg";
                $dest_name = '/var/www/rest.mangabase.tk/public/upload/images/manga/' . $image_name;
                if (!move_uploaded_file($foto, $dest_name)) {
                    return $res->withJson(Respuesta::set(false, 'Algo ha fallado guardando la imagen en su directorio.'));
                }

                $manga->foto = $image_name;
                $manga->save();

                if (isset($body["autores"])) {
                    foreach ($body["autores"] as $idAutor) {
                        $autor = Autor::find($idAutor);
                        if ($autor) {
                            $mangaAutor = new MangaAutor();
                            $mangaAutor->idManga = $manga->id;
                            $mangaAutor->idAutor = $autor->idAutor;
                            $mangaAutor->save();
                        }
                    }
                }

                if (isset($body["revistas"])) {
                    foreach ($body["revistas"] as $idRevista) {
                        $revista = Revista::find($idRevista);
                        if ($revista) {
                            $mangaRevista = new MangaRevista();
                            $mangaRevista->idManga = $manga->id;
                            $mangaRevista->idRevista = $revista->idRevista;
                            $mangaRevista->save();
                        }
                    }
                }

                if (isset($body["generos"])) {
                    foreach ($body["generos"] as $idGenero) {
                        $genero = Genero::find($idGenero);
                        if ($genero) {
                            $mangaGenero = new MangaGenero();
                            $mangaGenero->idManga = $manga->id;
                            $mangaGenero->idGenero = $genero->idGenero;
                            $mangaGenero->save();
                        }
                    }
                }

                $newManga = BaseMangas::find($manga->id);
                $newManga['autores'] = Autor::getAutoresMangaArray($manga->id);
                $newManga['revistas'] = Revista::getRevistasMangaArray($manga->id);
                $newManga['generos'] = Genero::getGenerosMangaArray($manga->id);

                return $res->withJson(Respuesta::set(true, 'Manga insertado correctamente.', $newManga));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );

    $this->put(
        '{id}',
        function ($req, $res, $args) {
            $decodetToken = $req->getAttribute('decoded_token_data');
            if ($decodetToken['usuario']->admin === 0) {
                return $res->withJson(Respuesta::set(false, 'No eres administrador! ಠ_ಠ'));
            }

            $body = $req->getParsedBody();
            $files = $req->getUploadedFiles();
            if (
                !isset($body["tituloPreferido"]) ||
                !isset($body["tituloJA"]) ||
                !isset($body["tituloRōmaji"]) ||
                !isset($body["argumento"]) ||
                !isset($body["añoDePublicacion"]) ||
                !isset($body["estado"]) ||
                !isset($body["demografia"])
            ) {
                return $res->withJson(Respuesta::set(false, 'Faltan campos22.', $body));
            }

            $tituloPreferido = $body["tituloPreferido"];
            $tituloJA = $body["tituloJA"];
            $tituloRōmaji = $body["tituloRōmaji"];
            $argumento = $body["argumento"];
            $añoDePublicacion = $body["añoDePublicacion"];
            $estado = $body["estado"];
            $demografia = $body["demografia"];

            try {
                $manga = Manga::find($args["id"]);
                if (!$manga) {
                    return $res->withJson(Respuesta::set(false, 'El manga que intentas editar no existe.'));
                }
                $manga->tituloPreferido = $tituloPreferido;
                $manga->tituloJA = $tituloJA;
                $manga->tituloRōmaji = $tituloRōmaji;
                $manga->argumento = $argumento;
                $manga->añoDePublicacion = $añoDePublicacion;
                $manga->idEstado = $estado;
                $manga->nota = 0;
                $manga->idDemografia = $demografia;
                foreach (["tituloES", "tituloEN", "capitulos", "volumenes", "añoDeFinalizacion"] as $v) {
                    if (isset($body[$v])) {
                        $manga[$v] = $body[$v];
                    }
                }
                $manga->save();

                if (isset($files['foto'])) {
                    $foto = $files['foto']->file;
                    if ($foto == '' || !is_uploaded_file($foto)) {
                        return $res->withJson(Respuesta::set(false, 'El formato del archivo enviado no es correcto.'));
                    }

                    $image_name = "manga-" . $manga->id . ".jpg";
                    $dest_name = '/var/www/rest.mangabase.tk/public/upload/images/manga/' . $image_name;
                    if (!move_uploaded_file($foto, $dest_name)) {
                        return $res->withJson(Respuesta::set(false, 'Algo ha fallado guardando la imagen en su directorio.'));
                    }

                    $manga->foto = $image_name;
                    $manga->save();
                }

                if (isset($body["autores"])) {
                    $mangasAutoresExistentes = MangaAutor::where('idManga', $manga->id)->get();
                    foreach ($mangasAutoresExistentes as $mangaAutor) {
                        $mangaAutor->delete();
                    }
                    foreach ($body["autores"] as $idAutor) {
                        $autor = Autor::find($idAutor);
                        if ($autor) {
                            $mangaAutor = new MangaAutor();
                            $mangaAutor->idManga = $manga->id;
                            $mangaAutor->idAutor = $autor->idAutor;
                            $mangaAutor->save();
                        }
                    }
                }

                if (isset($body["revistas"])) {
                    $mangasRevistasExistentes = MangaRevista::where('idManga', $manga->id)->get();
                    foreach ($mangasRevistasExistentes as $mangaRevista) {
                        $mangaRevista->delete();
                    }
                    foreach ($body["revistas"] as $idRevista) {
                        $revista = Revista::find($idRevista);
                        if ($revista) {
                            $mangaRevista = new MangaRevista();
                            $mangaRevista->idManga = $manga->id;
                            $mangaRevista->idRevista = $revista->idRevista;
                            $mangaRevista->save();
                        }
                    }
                }

                if (isset($body["generos"])) {
                    $mangasGenerosExistentes = MangaGenero::where('idManga', $manga->id)->get();
                    foreach ($mangasGenerosExistentes as $mangaGenero) {
                        $mangaGenero->delete();
                    }
                    foreach ($body["generos"] as $idGenero) {
                        $genero = Genero::find($idGenero);
                        if ($genero) {
                            $mangaGenero = new MangaGenero();
                            $mangaGenero->idManga = $manga->id;
                            $mangaGenero->idGenero = $genero->idGenero;
                            $mangaGenero->save();
                        }
                    }
                }

                $newManga = BaseMangas::find($manga->id);
                $newManga['autores'] = Autor::getAutoresMangaArray($manga->id);
                $newManga['revistas'] = Revista::getRevistasMangaArray($manga->id);
                $newManga['generos'] = Genero::getGenerosMangaArray($manga->id);

                return $res->withJson(Respuesta::set(true, 'Manga insertado correctamente.', $newManga));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );
});
