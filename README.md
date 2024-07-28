# SAÉ 1.03

L'équipe : Marius, Mattéo, Raphaël, Stanislas

## Info

Version PHP `sae103-php` : 7.4.33

À supprimer

- Enum (8.1)
- Named arguments (8.0)

## [Parser Markdown $\to$ HTML](<Implémentation gendoc-user.md>)

L'objectif est de créer un parser **robuste** et **versatile**, qui respecte le standard sous toutes les formes qu'il peut prendre.

En effet le standard défini dans le sujet n'est pas exhaustif et laisse beaucoup à l'interprétation.

On ne sait pas ce que M. Quiniou nous réserve dans ses `*.md` de test.

Blocs supportés :

- Code $\to$ `pre > code`
- Listes $\to$ `ul`
- Paragraphes $\to$ `p`
- Tableaux $\to$ `table`
- Titres 1-4 $\to$ `h1-4`

Formatage inline :

- Code inline $\to$ `code`
- Gras $\to$ `b`
- Italique $\to$ `i`
- Lien $\to$ `a`

### Choix effectués

### Entrée/sortie

Le Markdown est lu depuis l'entrée standard du script.

L'HTML généré est écrit dans la sortie standard.

Pourquoi utiliser l'entrée standard pour le Markdown plutôt qu'un nom de fichier ?

- Robustesse : c'est le shell qui se charge de lire le fichier, le programme n'a donc pas à prendre en charge une erreur de fichier introuvable ou inaccessible. Cela simplifie le programme.
- Versatilité : en passant par l'entrée standard, le Markdown peut être fourni au script dans un fichier (avec une redirection), depuis la sortie d'une autre commande (avec les pipes), ou encore dans une chaîne de caractères (avec les *Here Strings* en Bash). On est plus confiné au fichiers uniquement.

#### Code de sortie

code|signification
-|-
0|succès
1|ligne invalide dans le fichier de configuration

#### Formatage du HTML généré

Ce n'est pas requis dans le sujet, donc aucun formatage du HTML généré n'est effectué.

#### Blocs non séparés

**Ligne blanche** : une ligne vide ou uniquement consituée d'espaces

Pour ce qui est des blocs non séparés par des lignes blanches, uniquement les titres sont supportés :

```md
bonjour
# Titre
au revoir
```

Devient :

```html
<p>bonjour</p>
<h1>Titre</h1>
<p>au revoir</p>
```

Et non pas :

```html
<p>bonjour
# Titre
au revoir</p>
```

Ce comportement est également implémenté avec les listes et les blocs de code.
