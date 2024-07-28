# gendoc-user.php

Objectif : traduire le Markdown saveur Quiniou de la documentation utilisateur en HTML.

## todo

Block length property and markup contain only contents? with proper \R for pre code?

> no because some generation functions suchs as *generate_table* need the markup

- ~~faire passer tous les tests~~
- ~~traduire le code en anglais~~
- ~~le découpage en sections est-il réellement nécessaire?~~

Discrepantcy between blocks and inline formatting:

```html
<p>Hello<code>return 0</code></p>
```

`p` = block<br>
`code` = inline formatting

## Issue: unwanted tag stripping

Stray tags should not be stripped from structures that semantically contain code :

- inline code
- code blocks

### Solution 1

Inline code : perform some regex voodoo trickery to get the desired result :

use preg_replace_callback to :

- escape tags
- replace consecutive spaces

### Solution 2

Inline code : define class `InlineFormattingType`

## Issue: how to prevent html tags in code for being intepreted by the browser?

Escape `&` to `&amp;` everywhere

Escape `<` to `&lt;` and `>` to `&gt;` in areas where HTML tags shouldn't be intepreted.

## Principe de base

Utiliser les expressions réguilères.

Flags :

- m : multiline (^/$ match start/end of line)
- s : singleline (dot matches newline)

Titre 2 -> `/^## (.+)/m` -> `<h2>$1</h2>`

Espaces consécutifs -> `/(?<= ) /` -> `&nbsp;`

## Structures complexes

### Listes

### Tableaux

Noeud de table -> `^\|(?:-+\|)+`

Soit *n* le nombre de colonne du tableau défini par le nombre de '|' moins 1 dans le noeud.

Pour toutes les lignes avant commençant par un '|', jusqu'à atteindre une ligne vide ou pleine d'espaces :
C'est une tr contenant *n* th.

Pour toutes les lignes après commençant par un '|', jusqu'à atteindre une ligne vide ou pleine d'espaces :
C'est une tr contenant *n* td.

Pour obtenir th/td :

1. Exploser la ligne par '|' (limite *n* + 1)
2. remplacer la ligne avec un implode sur les éléments obtenus : ``echo "<tr><td>" . implode("</td><td>", $cellules) . "</td></tr>"``

## Problème 1

Paragraphes : comment matcher?

Trop complexe de faire une regex pour matcher tout texte ne se situant pas dans un tag après les autres étapes

Irréaliste de faire une regex au début en excluant tout les autres types de tags

### Solution

**Bloc** : bloc de texte :

- débutant au début du fichier ou précédé par une ligne vide ou pleine d'espaces
- finissant à la fin du fichier ou suivi par une ligne vide ou pleine d'espaces

Le texte est divisé en blocs séparés par des lignes vides ou contenant uniquement des espaces.

#### 1. Séparer le document original en blocs

`/((?:.+\R)*?.+)(?:\R\s*\R|\z)/`

Groupe 1 : contenu du bloc

#### 2. Pour chaque bloc

##### 2.0 Détecter son type

Faire une énumeration.

##### 2.1 Appliquer le formatage inline

###### Tous blocs sauf Code

- code inline : ``/`([^`]*)`/`` $\to$ `<code>$1</code>`
- espaces consécutifs : `/(?<= ) /` $\to$ `&nbsp;`

###### Paragraphe et tableau

strip_tags allowing b and i

###### Pour tout bloc non paragraphe ou tableau

strip_tags

##### 2.2 Générer le bloc

- Titre1 : `/^# (.+)/m` $\to$ `<h1>$1</h1>`
- Titre2 : `/^## (.+)/m` $\to$ `<h2>$1</h2>`
- Titre3 : `/^### (.+)/m` $\to$ `<h3>$1</h3>`
- Titre4 : `/^#### (.+)/m` $\to$ `<h4>$1</h4>`
- Tableau : le contenu contient un noeud de tableau - faire une fonction
- Liste : le contenu commence par un '- ' - faire une fonction
- Code : `/```(.*)```/s` $\to$ `<pre><code>$1</code></pre>`
- Paragraphe : aucun de ci-dessus - enclore entre `<p>` et `</p>`

##### 2.3 Ajouter le contenu au résultat

## Problème 2

Si il n'y a pas de séparation entre les blocs, tout part en cacahuète

Exemple :

```md
# Titre1
## Titre2
### Titre3
#### Titre4
```

Donne l'HTML :

```html
<h1>Titre de niveau 1</h1>
## Titre de niveau 2
### Titre de niveau 3
#### Titre de niveau 4
```

> Pourquoi ?

Parce que les 4 titres sont considérés comme un seul bloc.

On applique ensuite les regex, on commence avec Titre1 qui matche le premier titre, le bloc est donc considéré comme un titre 1.

### Solution

On ne va pas modifier la regex de découpage de bloc, elle est déjà assez compliquée.

Plûtot, lorsqu'on détecte le type de bloc, détecter de nouveaux blocs en continuant à tester les regex jusqu'à arriver au bout de la chaîne.

Dans l'exemple précédent, *detecter* reçoit le bloc, et continue à lire avec un `while` tant qu'elle n'a pas épuisé tout le bloc.

La boucle s'arrêtera car on trouve un paragraphe à défaut d'avoir trouvé autre chose.

Et donc elle retourne un tableau de tableaux contenant 2 éléments chacun, d'abord le bloc puis le type.
