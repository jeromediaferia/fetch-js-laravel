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
                    e.preventDefault();

                    // on récupère le token sinon erreur 419
                    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    // on récupère les valeurs
                    let email = document.querySelector('#exampleInputEmail1').value;
                    let name = document.querySelector('#exampleInputName').value;
                    let password = document.querySelector('#exampleInputPassword1').value;

                    //Define your post url
                    let url = '/admin/part/store';//Define redirect if needed
                    let redirect = '/admin/part/list';//Select your form to clear data after sucessful post


                    if (window.fetch) {
                        // exécuter ma requête fetch ici


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
                                let isErrors = false;
                                for (let i = 0; i < data.length; i++) {
                                    if (data[i] === 'Errors') {
                                        isErrors = true;
                                        // Je supprime la valeur Errors qui ne me sert pas pour l'affichage
                                        data.splice(i, 1);
                                    }
                                }
                                let errorElement = document.querySelector('#errors');
                                // Je vide toujours les erreurs au chargement
                                errorElement.textContent = '';
                                if (isErrors) {
                                    errorElement.classList.remove('d-none');
                                    for (let i = 0; i < data.length; i++) {
                                        errorElement.insertAdjacentHTML('beforeend', data[i] + '<br>');
                                    }
                                } else {
                                    // Si je n'ai pas d'erreur je vide le formulaire
                                    form.reset();
                                    errorElement.classList.add('d-none');
                                    let table = document.querySelector('#tableBody');
                                    table.insertAdjacentHTML('beforeend',
                                        '<tr>' +
                                        '<th scope="row">' + data.id + '</th>' +
                                        '<td>' + data.name + '</td>' +
                                        '<td>' + data.email + '</td>' +
                                        '</tr>')
                                }

                            })
                            .catch(function (error) {
                                // Ici l'erreur si le fetch n'a pas pu fonctionner
                                console.log('Il y a eu un problème avec l\'opération fetch: ' + error.message);
                            });
                    } else {
                        // Faire quelque chose avec XMLHttpRequest?
                        console.log('Ici on le fera en jQuery');
                    }
                });
            })();
        });
    </script>
@endsection
