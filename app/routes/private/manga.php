<?php

use App\Lib\Respuesta;
use App\Model\Autor;
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
                $datos['autores'] = Autor::all();
                $datos['revistas'] = Revista::all();
                $datos['generos'] = Genero::all();
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

                return $res->withJson(Respuesta::set(true, 'Mensaje enviado correctamente.'));
            } catch (Exception $error) {
                return $res->withJson(Respuesta::set(false, $error->getMessage()));
            }
        }
    );
});
