@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div id="errors" class="alert alert-danger d-none" role="alert">
                </div>
                <form id="form">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                               placeholder="Enter email" name="email">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
                            else.
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputName">Name</label>
                        <input type="text" class="form-control" id="exampleInputName" aria-describedby="nameHelp"
                               placeholder="Name" name="name">
                        <small id="nameHelp" class="form-text text-muted">We'll never share your email with anyone
                            else.
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password"
                               name="password">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
        <div class="row my-5">
            <div class="col-12">
                <h2>Tableau qui n'affiche les nouveaux ajouts en fetch</h2>
                <p>C'est dans ce tableau qu'on affichera dynamiquement le retour du fetch.</p>
                <table class="table table-dark">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                    </tr>
                    </thead>
                    <tbody id="tableBody">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row my-5">
            <div class="col-12">
                <h2>Tableau global</h2>
                <p>Ce tableau reprend toutes les inscriptions avec une pagination.</p>
                <table class="table table-dark">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <th scope="row">{{ $user->id }}</th>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
@section('scripts-footer')
    <script>
        // On attend le chargement du document
        document.addEventListener("DOMContentLoaded", function () {
            (function () {
                "use strict";
                // Ici le JS pour le fetch (SANS JQUERY)
                let form = document.querySelector('#form');

                form.addEventListener('submit', function (e) {
                    // Au cas si le preventDefault ne marche pas (ex: IE10)
                    e.preventDefault ? e.preventDefault() : (e.returnValue = false);

                    // on récupère le token sinon erreur 419
                    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // on récupère les valeurs
                    let email = document.querySelector('#exampleInputEmail1').value;
                    let name = document.querySelector('#exampleInputName').value;
                    let password = document.querySelector('#exampleInputPassword1').value;

                    // On récupère la div error
                    let errorElement = document.querySelector('#errors');

                    // On récupère le tableau
                    let table = document.querySelector('#tableBody');

                    if (window.fetch) {

                        // exécuter ma requête fetch ici
                        // @link : https://developer.mozilla.org/fr/docs/Web/API/Fetch_API/Using_Fetch
                        let myInit = {
                            method: 'POST',
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json, text-plain, */*",
                                "X-Requested-With": "XMLHttpRequest",
                                // Ici je redonne le token ) mon en-tête
                                // sinon j'aurai une erreur Laravel
                                "X-CSRF-TOKEN": token,

                            },
                            // dans body je transmet mes données que j'encode en JSON
                            body: JSON.stringify({
                                name: name,
                                email: email,
                                password: password
                            })
                        };

                        fetch('', myInit)
                            .then(function (response) {
                                // Je vérifie que j'ai bien un retour code : 200

                                if (response.ok) {
                                    // Si c'est ok je capte la réponse
                                    // je la transforme transform en json()
                                    // puis je passe à la suite de mon script
                                    // Il est obligatoire de passer en deux then() deux étapes
                                    return response.json();
                                } else {
                                    console.log('Mauvaise réponse du réseau');
                                }
                            })
                            .then(function (data) {
                                // Une fois que j'ai passé le test je récupère les informations
                                // Je vide toujours les erreurs au chargement
                                errorElement.textContent = '';
                                if (data[0] === 'Errors') {
                                    newError(data);
                                } else {
                                    // Si je n'ai pas d'erreur je lance ma fonction
                                    newTab(data);
                                }

                            })
                            .catch(function (error) {
                                // Ici l'erreur si le fetch n'a pas pu fonctionner
                                console.log('Il y a eu un problème avec l\'opération fetch: ' + error.message);
                            });

                    } // Fin de Fetch
                    else {
                        // Execute ajax
                        // @link: https://api.jquery.com/jquery.ajax/
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': token
                            }
                        });
                        $.ajax({
                            url: "",
                            method: "POST",
                            data: {
                                name: name,
                                email: email,
                                password: password
                            },
                            dataType: 'json',
                            success: function (data) {
                                // Si on a un code 200
                                // puis si j'ai errors ou rien
                                if (data[0] === 'Errors') {
                                    newError(data);
                                } else {
                                    newTab(data);
                                }
                            },
                            error: function (errors) {
                                console.log(errors);
                            }
                        });
                    } // Fin de jQuery

                    /********************************************************************
                     J'ai créé deux fonctions qui me permettent d'injecter les données
                     ********************************************************************/
                    let newTab = function(data) {
                        // Si je n'ai pas d'erreur je vide le formulaire
                        form.reset();
                        errorElement.classList.add('d-none');

                        table.insertAdjacentHTML('beforeend',
                            '<tr>' +
                            '<th scope="row">' + data.id + '</th>' +
                            '<td>' + data.name + '</td>' +
                            '<td>' + data.email + '</td>' +
                            '</tr>');
                    };

                    let newError = function(errors) {
                        errorElement.textContent = '';
                        errorElement.classList.remove('d-none');
                        // Je fais volontairement passer le compteur à 1
                        // Pour ne pas afficher le Errors juste les messages
                        for (let i = 1; i < errors.length; i++) {
                            errorElement.insertAdjacentHTML('beforeend', errors[i] + '<br>');
                        }
                    };

                }); // Fin de l'écouteur

            })(); // Fin de mon script
        });
    </script>
@endsection

