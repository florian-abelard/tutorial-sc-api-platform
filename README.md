# api-platform-symfonycasts

## Description 

Application support pour le tutoriel 

https://symfonycasts.com/screencast/api-platform

## Accès

Accès interface web sur `http://localhost:8080`

Accès adminer sur `http://localhost:8081`

## Exempales d'appel de l'API

```bash

# Récupération de la liste des fromages
curl "http://localhost:8080/api/cheeses" -H "accept: application/ld+json" | jq

# Récupération d'un fromage
curl "http://localhost:8080/api/cheeses/2" -H "accept: application/ld+json" | jq

# Récupération des fromages avec un prix supérieur à 10€
curl "http://localhost:8080/api/cheeses?price[gt]=1000" -H "accept: application/ld+json" | jq

# Fitlre sur les propriétés retournées
curl "http://localhost:8080/api/users/3?properties[]=username&properties[cheeseListings][]=title" -H "accept: application/ld+json" | jq
```
