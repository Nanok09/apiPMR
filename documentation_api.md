# Ce fichier a pour but de clarifier le schéma pour interagir avec l'API

## Principe général :

On effectue uniquement des Requêtes à la page concernée (pour l'instant /libs/api.php). Ces requêtes utilisent la méthode POST et comprennent toutes un paramètre action.

action:

* get_list_places : effectue une recherche des terrains en fct de différents critères
* get_place_info : récupère les infos d'un terrain/lieu
* get_recommandations : récupère une liste de terrains/lieux recommandés

* address_research : à partir d'une adresse tapée par l'utilisateur, suggère une liste d'adresses associées à leurs coordonnées (latitude/longitude)

___

* add_note : ajoute une note à un terrain
* modify_note : modifie une note donnée
* delete_note : supprime une note donnée

___

* add_comment : ajoute un commentaire lié à un terrain
* modify_comment : le modifie
* delete_comment : le supprime

___

* add_creneau_dispo : pour l'utilisateur qui loue un terrain/lieu, ajoute un créneau disponible avec une certaine capacité que d'autres utilisateurs pourront réserver
* add_reservation : réserver un créneau
* delete_reservation : supprime une reservation faite

* get_creneaux_place : récupère tous les créneaux dispos et le nombre de places restantes pour un terrain/lieu entre 2 dates (pour affichage sur calendrier)
* get_capacite_creneau : récupère le nombre de places restantes pouvant être réservées sur un terrain pendant la totalité d'un créneau horaire

___

* send_message : envoyer un message sur le chat
* get_conversation : récupére les messages d'une conversation avec un autre utilisateur
* get_new_messages : récupère d'éventuels nouveaux messages reçus

___

* update_user : modifie les infos personelles de l'utilisateur

# Partie requete à l'API

Chaque action peut/doit recevoir une liste de parametres optionels:

* action = get_list_places:

  + sport = tennis -- chaine de caractere permettant d'identifier un sport parmis un certains set de sports. La chaine doit être pré déterminée, peut être il vaudrait mieux utiliser un genre d'id
  + user_location_lat =lat -- une chaine de caractere décrivant la position géographique de l'utilisateur (lattitude)
  + user_location_long=long -- une chaine de caractere décrivant la position géographique de l'utilisateur (longitude)
  + distance_max=5 -- une chaine de caractère représentant le nombre de km max autorisés pour les terrains à afficher
  + accept_public = no -- ne pas recevoir des propositions de terrains publics (par défaut on en recoit). 'no' est la seule valeur qu'il est possible de renseigner si on envoie le paramètre accept_public! 
  + accept_private = no -- ne pas recevoir des propositions de terrains (par défaut on en recoit).  'no' est la seule valeur qu'il est possible de renseigner si on envoie le paramètre accept_public!
  + prix_min=5 -- str représentant le prix minimal (par défaut 0)
  + prix_max=6 -- str représentant le prix max (par défaut infini)
  + max_results= 4 -- str représentant le nombre maximal de lieux différents à fournir en retour
  + start_time= hh:mm -str représentant l'heure et la minute du début de réservation (finir par :30 ou :00)
  + end_time= hh:mm -- str représentant l'heure et la minute de fin de réservation (finir par :30 ou :00)
  + date = yyyy-mm-dd  -- str représentant la date de recherche

* action = get_place_info

  + terrain_id = id -- str de l'id du terrain (paramètre obligatoire!)
  + start_time= hh:mm -str représentant l'heure et la minute du début de réservation (finir par :30 ou :00)
  + end_time= hh:mm -- str représentant l'heure et la minute de fin de réservation (finir par :30 ou :00)
  + date = yyyy-mm-dd  -- str représentant la date de recherche

* action = get_recommandations (plus tard)

  + user_location_lat=lat
  + user_location_long=long

* action = add_note

  + id_place=id -- str représentant l'id du terrain auquel la note est rattachée
  + note= note -- str représentant la note

* action = modify_note

  + id_place = id -- str représentant l'id du terrain auquel la note est rattachée
  + note= note -- str représentant la note

* action = delete_note

  + id_place = id -- str représentant l'id du terrain auquel la note est rattachée

* action = add_comment

  + id_place = id -- str représentant l'id du terrain auquel la note est rattachée
  + comment= comment -- str représentant le commentaire
  + pseudo = --str pseudo de l'utilisateur qui a posé le commentaire

* action = modify_comment

  + id_comment = id -- str représentant l'id du commentaire
  + comment= comment -- str représentant le commentaire

* action = delete_comment

  + id_comment = id -- str représentant l'id du commentaire

* action = address_research
  + address = addresse -- str représentant l'adresse à trouver^
  + user_location_lat =lat -- une chaine de caractere décrivant la position géographique de l'utilisateur (lattitude)
  + user_location_long =long -- une chaine de caractere décrivant la position géographique de l'utilisateur (longitude)
  + distance_max = 100 -- chaine de caractères représentant la distance maximale par rapport à l'utilisateur des adresses retournées. La distance est en km. 
  + max_results = 4 -- chaine de caractère représentant le nombre maximum d'adresses à retourner 

* action = add_creneau_dispo
  + id_place  = id du terrain
  + date = string(yyyy-mm-dd)
  + time_start = string(hh:mm)
  + time_end = string(hh:mm)
  + capacite = int

* action = add_reservation
  + id_place  = id du terrain
  + date = string(yyyy-mm-dd)
  + time_start = string(hh:mm)
  + time_end = string(hh:mm)
  + nb_personnes = int

* action = delete_reservation
  + id_reservation = int

* action = get_creneaux_place
  + id_place = int id du terrain
  + date_start = string(yyyy-mm-dd)
  + date_end = string(yyyy-mm-dd)

* action = get_capacite_creneau
  + id_place = int id du terrain
  + date = string(yyyy-mm-dd)
  + time_start = hh:mm
  + time_end = hh:mm

* action = update_user
  + email
  + nom
  + prenom

# Partie réponse de l'API

### Format général de la réponse :

``` javascript
{
    version: @float numéro_version,
    status: @int numéro_du statut,
    success: @bool success,
    data: {}
}
```

### Format de data en fonction des différentes actions demandées

* action = get_list_places

``` 

        data : [{
                coordinates: {lat:lat, long:long},
                name: @str,
                id: @int,
                adresse: @str,
                photos: [nom_fichier_1,...,nom_fichier_n],
                sport: @str,
                private: @bool,
                price: @float,
                note: @float (c'est la note moyenne des users),
                dispo: @int, false si private=false,
        },...,{}]
```

* action = get_place_info

``` 

        data = {
                coordinates: {lat:lat, long:long},
                address: @str,
                creator: @str,
                name: @str,
                id: @int,
                photos: [nom_fichier_1,...,nom_fichier_n],
                sport: @str,
                private: @bool,
                price: @float,
                note: {mean: @float (c'est la note moyenne des users), nb_notes: @int}
                dispo: @int, false si private=false,
                description: @str
                comments: [{
                        id_comment: @int
                        author: @str
                        content: @str
                        timestamp: @int
                },...,{}]
        }
```

* action = get_recommandations

  pareil que: action = get_list_places

* action = add_note

  data: Undefined

* action = modify_note

  data: Undefined

* action = delete_note

  data: Undefined

* action = add_comment

``` 

        data: {
                id_comment: @int
                timestamp: @int (unix timestamp en s)
        }
```

* action = modify_comment

``` 

        data: {
                timestamp: @int
        }
```

* action = delete_comment

  data: Undefined

* action = address_research

``` 

        data: [{
                coordinates: {lat:lat,long:long},
                address : @str (représentant le nom complet de l'adresse ),
        },...,{}]
```

* action = add_creneau_dispo

``` 

        data: {
                id_creneau: @int
        }
```

* action = add_reservation

``` 

        data: {
                id_reservation: @int
        }
```

* action = delete_reservation

  data: Undefined

* action = get_creneaux_place

(utilisé par fullcalendar pour afficher les creneaux dans un calendrier)

``` 

    data: [
            {
              start: string date (iso format),
              end: string date (iso format),
              title: string titre (contient la capacité restante / capacité totale),
              className: string classes des elements des evenements dans le calendrier
            },
            ...
          ]

```

* action = get_capacite_creneau

``` 

    data : {
      capacite: int
    }
```

# Exemples

* action = add_note / modify_note

``` javascript
$.post('libs/api.php', {
    action: "add_note",
    id_place: 2,
    note: 4
}, function(res) {
    console.log(res)
}, "json")
```

* action = delete_note

``` javascript
$.post('libs/api.php', {
    action: "delete_note",
    id_place: 1
}, function(res) {
    console.log(res)
}, "json")
```

* action = add_comment

``` javascript
$.post('libs/api.php', {
    action: "add_comment",
    id_place: 1,
    comment: "texte du commentaire"
}, function(res) {
    console.log(res)
}, "json")
```

 Réponse

``` javascript
{
    "version": 1.1,
    "success": true,
    "status": 200,
    "data": {
        "timestamp": 1618136846,
        "id_comment": "4"
    }
}
```

Pour transformer le timestamp en chaîne de caractères lisible :

``` javascript
new Date(res.data.timestamp * 1000).toLocaleString()
```
