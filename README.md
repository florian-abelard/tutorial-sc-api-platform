# api-platform-symfonycasts

## Description 

Application support pour le tutoriel 

https://symfonycasts.com/screencast/api-platform

## Démarrage

### Pré-requis

* docker
* docker-compose (version 3)

### Mise en place de l'environnement

```bash
# Récupération des sources github
git clone https://github.com/florian-abelard/api-platform-symfonycasts.git

# Initialisation du projet
make init

# Démarrer les containers docker
make up

# Créer et alimenter la base de données
make db-init

# Afficher toutes les commandes disponibles
make

## Accès

Accès interface web sur `http://localhost:8080`

Accès adminer sur `http://localhost:8081`

## Exemples d'appel à l'API

```bash

# Récupération de la liste des fromages
curl "http://localhost:8080/api/cheeses" -H "accept: application/ld+json" | jq

# Récupération d'un fromage
curl "http://localhost:8080/api/cheeses/2" -H "accept: application/ld+json" | jq

# Récupération des fromages avec un prix supérieur à 10€
curl "http://localhost:8080/api/cheeses?price[gt]=1000" -H "accept: application/ld+json" | jq

# Fitlre sur les propriétés retournées
curl "http://localhost:8080/api/users/3?properties[]=username&properties[cheeseListings][]=title" -H "accept: application/ld+json" | jq

# Récupération des fromages de Roxane
curl "http://localhost:8080/api/cheeses?owner.username=r0x" -H "accept: application/ld+json" | jq

```
